<?php

namespace App\Livewire\Posts;

use App\Models\Post;
use App\Models\Room;
use App\Models\MediaFile;
use App\Models\MediaRelation;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithFileUploads;

class PostCreate extends Component
{
    use WithFileUploads;

    public Room $room;
    public string $body = '';
    public array $media = [];      // マージ後の本体
    public array $newMedia = [];   // 選択直後のバッファ
    public int $formKey = 0;  // textareaリセット用
    public bool $showForm = false; // 新投稿トグル
    
    protected function rules(): array
    {
        return [
            // 画像だけでも投稿できるように
            'body'      => 'required_without:media|string|max:5000',
            'media'     => 'array|max:10',
            'media.*'   => 'file|max:10240|mimes:jpg,jpeg,png,webp,gif,mp4,mov,avi,webm',
            // ※ newMedia は save() では検証しない（updatedNewMedia で個別検証）
        ];
    }

    public function toggleForm(): void
    {
        $this->showForm = ! $this->showForm;
    }
    /**
     * ファイル選択直後に newMedia.* を検証してから media にマージ
     */
    public function updatedNewMedia(): void
    {
        // バッファが空でなければ個別検証
        if (!empty($this->newMedia)) {
            $this->validate([
                'newMedia'   => 'array',
                'newMedia.*' => 'file|max:10240|mimes:jpg,jpeg,png,webp,gif,mp4,mov,avi,webm',
            ]);

            // 合計枚数の上限チェック（任意）
            $total = count($this->media) + count($this->newMedia);
            if ($total > 9) {
                $this->addError('media', '一度に投稿できるファイルは最大9個までです。');
                // 破棄して早期 return
                $this->newMedia = [];
                return;
            }

            // マージ & バッファクリア
            $this->media = array_values(array_merge($this->media, $this->newMedia));
            $this->newMedia = [];
        }
    }

    public function removeMedia($index): void
    {
        if (isset($this->media[$index])) {
            unset($this->media[$index]);
            $this->media = array_values($this->media);
        }
    }

    public function moveUp($index): void
    {
        if ($index > 0) {
            [$this->media[$index - 1], $this->media[$index]] = [$this->media[$index], $this->media[$index - 1]];
        }
    }

    public function moveDown($index): void
    {
        if ($index < count($this->media) - 1) {
            [$this->media[$index + 1], $this->media[$index]] = [$this->media[$index], $this->media[$index + 1]];
        }
    }
    
    public function save(): void
    {
        $this->validate();
    
        DB::transaction(function () {
            // 1️⃣ 投稿本体を作成
            $post = Post::create([
                'room_id'          => $this->room->id,
                'user_id'          => Auth::id(),
                'post_type'        => 'post',
                'body'             => $this->body,
                'visibility'       => 'public',
                'status'           => 'published',
                'reaction_count'   => 0,
                'comment_count'    => 0,
                'last_activity_at' => now(),
            ]);
    
            // 2️⃣ 添付ファイル登録（MediaFile::uploadAndCreateで統一）
            if (!empty($this->media)) {
                $disk = config('filesystems.default');
    
                foreach ($this->media as $i => $file) {
                    // ファイルアップロード & MediaFile作成
                    $media = MediaFile::uploadAndCreate(
                        $file,
                        Auth::user(),
                        'post',
                        $disk,
                        'posts/' . $post->id
                    );
    
                    // 中間テーブル MediaRelation 登録
                    MediaRelation::create([
                        'media_file_id' => $media->id,
                        'mediable_type' => Post::class,
                        'mediable_id'   => $post->id,
                        'sort_order'    => $i,
                    ]);
                }
            }
    
            // 3️⃣ ルーム情報更新
            $this->room->increment('posts_count');
            $this->room->update(['last_posted_at' => now()]);
        });
    
        // 4️⃣ フォームリセット
        $this->reset(['body', 'media', 'newMedia']);
        $this->formKey++;
        $this->dispatch('post-created');
        $this->showForm = false;
    
        session()->flash('success', '投稿しました');
    }

    public function render()
    {
        return view('livewire.posts.post-create');
    }
}

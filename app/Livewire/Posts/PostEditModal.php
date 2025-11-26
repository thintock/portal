<?php

namespace App\Livewire\Posts;

use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Models\Post;
use App\Models\MediaFile;
use App\Models\MediaRelation;

class PostEditModal extends Component
{
    use WithFileUploads;

    public $postId;
    public $body = '';
    public $media = [];     // 既存＋新規をまとめる（配列 or UploadedFile）
    public $newMedia = [];  // アップロード直後のバッファ
    public $showModal = false;

    protected $listeners = ['open-post-edit' => 'open'];

    protected function rules()
    {
        return [
            'body'       => 'required_without:media|string|max:5000',
            'media'      => 'array|max:10',
            'media.*'    => 'nullable', // string(既存) or UploadedFile(新規)
            'newMedia'   => 'array',
            'newMedia.*' => 'file|max:1524000|mimes:jpg,jpeg,png,webp,gif,mp4,mov,avi,webm',
        ];
    }

    /**
     * モーダルを開く
     */
    public function open($postId)
    {
        $this->postId = $postId;
        $post = Post::with('mediaFiles')->findOrFail($postId);

        // 投稿本文
        $this->body = $post->body;

        // 既存メディアを配列形式でセット
        $this->media = $post->mediaFiles->map(fn($m) => [
            'id'   => $m->id,
            'path' => $m->path,
        ])->toArray();

        $this->newMedia = [];
        $this->showModal = true;
    }

    /**
     * 新規メディアが追加された際の処理
     */
    public function updatedNewMedia()
    {
        if (!empty($this->newMedia)) {
            $this->validateOnly('newMedia.*');

            $total = count($this->media) + count($this->newMedia);
            if ($total > 10) {
                $this->addError('media', '最大10個までです。');
                $this->newMedia = [];
                return;
            }

            // 新規ファイルを末尾に追加
            $this->media = array_merge($this->media, $this->newMedia);
            $this->newMedia = [];
        }
    }

    /**
     * メディア削除
     */
    public function removeMedia($index)
    {
        if (isset($this->media[$index])) {
            unset($this->media[$index]);
            $this->media = array_values($this->media);
        }
    }

    /**
     * 並べ替え（上）
     */
    public function moveUp($index)
    {
        if ($index > 0) {
            [$this->media[$index - 1], $this->media[$index]] = [$this->media[$index], $this->media[$index - 1]];
        }
    }

    /**
     * 並べ替え（下）
     */
    public function moveDown($index)
    {
        if ($index < count($this->media) - 1) {
            [$this->media[$index + 1], $this->media[$index]] = [$this->media[$index], $this->media[$index + 1]];
        }
    }

    /**
     * 保存処理
     */
    public function save()
    {
        $this->validate();
    
        $post = Post::findOrFail($this->postId);
    
        if ($post->user_id !== auth()->id()) {
            abort(403);
        }
    
        DB::transaction(function () use ($post) {
            // 1️⃣ 本文更新
            $post->update(['body' => $this->body]);
    
            // 2️⃣ 既存メディア関係を削除（MediaFileは削除しない）
            MediaRelation::where('mediable_type', Post::class)
                ->where('mediable_id', $post->id)
                ->delete();
    
            $disk = config('filesystems.default');
    
            // 3️⃣ 新しいメディアを再リンク・再登録
            foreach ($this->media as $index => $item) {
                if (is_array($item) && isset($item['id'])) {
                    // ✅ 既存MediaFileの再リンク
                    MediaRelation::create([
                        'mediable_type' => Post::class,
                        'mediable_id'   => $post->id,
                        'media_file_id' => $item['id'],
                        'sort_order'    => $index,
                    ]);
                } elseif (is_object($item)) {
                    // ✅ 新規アップロード（MediaFile::uploadAndCreate使用）
                    $media = MediaFile::uploadAndCreate(
                        $item,
                        Auth::user(),
                        'post',
                        $disk,
                        'posts/' . $post->id
                    );
    
                    MediaRelation::create([
                        'mediable_type' => Post::class,
                        'mediable_id'   => $post->id,
                        'media_file_id' => $media->id,
                        'sort_order'    => $index,
                    ]);
                }
            }
    
            // 4️⃣ 投稿の最終更新時刻を更新
            $post->update(['last_activity_at' => now()]);
        });
    
        // 5️⃣ モーダルを閉じて状態リセット
        $this->dispatch('post-updated', postId: $this->postId);
        $this->dispatch('post-updated-global');
        $this->reset(['newMedia']);
        $this->showModal = false;
    
        session()->flash('success', '投稿を編集しました');
    }

    public function render()
    {
        return view('livewire.posts.post-edit-modal');
    }
}

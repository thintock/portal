<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Models\Event;
use App\Models\MediaFile;
use App\Models\MediaRelation;

class EventImages extends Component
{
    use WithFileUploads;

    public $eventId;
    public $cover = null;          // カバー画像（既存または新規）
    public $gallery = [];          // 既存・新規の混在配列
    public $newGallery = [];       // 一時アップロード用
    public $hasChanges = false;    // 保存促進メッセージ表示用

    protected $listeners = ['refreshEventImages' => '$refresh'];

    /**
     * バリデーションルール
     */
    protected function rules()
    {
        return [
            'cover'        => 'nullable',
            'gallery'      => 'array|max:30',
            'gallery.*'    => 'nullable',
            'newGallery'   => 'array',
            'newGallery.*' => 'file|max:10240|mimes:jpg,jpeg,png,webp,gif',
        ];
    }

    /**
     * 初期ロード
     */
    public function mount(Event $event)
    {
        $this->eventId = $event->id;
        $this->reloadImages();
    }

    /**
     * イベントの画像を再読込
     */
    public function reloadImages()
    {
        $event = Event::find($this->eventId);

        // カバー画像の取得
        $cover = $event->mediaFiles()->where('type', 'event_cover')->first();
        $this->cover = $cover ? ['id' => $cover->id, 'path' => $cover->path] : null;

        // ギャラリー画像の取得
        $this->gallery = $event->mediaFiles()
            ->where('type', 'event_gallery')
            ->orderBy('media_relations.sort_order')
            ->get()
            ->map(fn($m) => ['id' => $m->id, 'path' => $m->path])
            ->toArray();

        $this->newGallery = [];
        $this->hasChanges = false;
    }

    /**
     * ギャラリー追加時
     */
    public function updatedNewGallery()
    {
        if (!empty($this->newGallery)) {
            $this->validateOnly('newGallery.*');

            $total = count($this->gallery) + count($this->newGallery);
            if ($total > 30) {
                $this->addError('gallery', 'ギャラリー画像は最大30枚までです。');
                $this->newGallery = [];
                return;
            }

            // 新規アップロード分を末尾に追加
            $this->gallery = array_merge($this->gallery, $this->newGallery);
            $this->newGallery = [];
            $this->hasChanges = true;
        }
    }

    /**
     * カバー削除処理
     */
    public function removeCover()
    {
        if ($this->cover && isset($this->cover['id'])) {
            $mediaId = $this->cover['id'];
    
            // メディア関係削除
            MediaRelation::where('media_file_id', $mediaId)->delete();
    
            // ファイル削除（物理削除）
            $media = MediaFile::find($mediaId);
            if ($media) {
                Storage::disk('public')->delete($media->path);
                $media->delete();
            }
        }
    
        // フロント側だけリフレッシュ
        $this->cover = null;
        $this->hasChanges = false;
        $this->reloadImages(); // ✅ ページ全体をリロードせず再描画
    }

    /**
     * ギャラリー削除処理
     */
    public function removeGallery($index)
    {
        if (!isset($this->gallery[$index])) return;
    
        $item = $this->gallery[$index];
    
        if (is_array($item) && isset($item['id'])) {
            $mediaId = $item['id'];
    
            // DB削除
            MediaRelation::where('media_file_id', $mediaId)->delete();
    
            // ファイル削除
            $media = MediaFile::find($mediaId);
            if ($media) {
                Storage::disk('public')->delete($media->path);
                $media->delete();
            }
        }
    
        // 配列更新（即時UI反映）
        unset($this->gallery[$index]);
        $this->gallery = array_values($this->gallery);
        $this->hasChanges = false;
    
        // ✅ Livewire内で再描画（リロード不要）
        $this->reloadImages();
    }

    /**
     * ギャラリー並べ替え（上）
     */
    public function moveUp($i)
    {
        if ($i > 0) {
            [$this->gallery[$i - 1], $this->gallery[$i]] = [$this->gallery[$i], $this->gallery[$i - 1]];
            $this->hasChanges = true;
        }
    }

    /**
     * ギャラリー並べ替え（下）
     */
    public function moveDown($i)
    {
        if ($i < count($this->gallery) - 1) {
            [$this->gallery[$i + 1], $this->gallery[$i]] = [$this->gallery[$i], $this->gallery[$i + 1]];
            $this->hasChanges = true;
        }
    }

    /**
     * 保存処理
     */
    public function save()
    {
        $this->validate();

        $event = Event::findOrFail($this->eventId);
        $disk = config('filesystems.default', 'public');

        DB::transaction(function () use ($event, $disk) {
            // 1️⃣ カバー処理
            MediaRelation::where('mediable_type', Event::class)
                ->where('mediable_id', $event->id)
                ->whereHas('mediaFile', fn($q) => $q->where('type', 'event_cover'))
                ->delete();

            if ($this->cover) {
                if (is_array($this->cover) && isset($this->cover['id'])) {
                    // 既存再リンク
                    MediaRelation::create([
                        'mediable_type' => Event::class,
                        'mediable_id'   => $event->id,
                        'media_file_id' => $this->cover['id'],
                        'sort_order'    => 0,
                    ]);
                } elseif (is_object($this->cover)) {
                    // 新規アップロード
                    $media = MediaFile::uploadAndCreate(
                        $this->cover,
                        $event,
                        'event_cover',
                        $disk,
                        'events/covers'
                    );

                    MediaRelation::create([
                        'mediable_type' => Event::class,
                        'mediable_id'   => $event->id,
                        'media_file_id' => $media->id,
                        'sort_order'    => 0,
                    ]);
                }
            }

            // 2️⃣ ギャラリー処理
            MediaRelation::where('mediable_type', Event::class)
                ->where('mediable_id', $event->id)
                ->whereHas('mediaFile', fn($q) => $q->where('type', 'event_gallery'))
                ->delete();

            foreach ($this->gallery as $i => $item) {
                if (is_array($item) && isset($item['id'])) {
                    MediaRelation::create([
                        'mediable_type' => Event::class,
                        'mediable_id'   => $event->id,
                        'media_file_id' => $item['id'],
                        'sort_order'    => $i,
                    ]);
                } elseif (is_object($item)) {
                    $media = MediaFile::uploadAndCreate(
                        $item,
                        $event,
                        'event_gallery',
                        $disk,
                        'events/gallery'
                    );

                    MediaRelation::create([
                        'mediable_type' => Event::class,
                        'mediable_id'   => $event->id,
                        'media_file_id' => $media->id,
                        'sort_order'    => $i,
                    ]);
                }
            }
        });

        session()->flash('success', '画像を保存しました。');
        $this->hasChanges = false;

        // 🔄 保存完了後にリフレッシュ
        session()->flash('success', '画像を保存しました。');
        $this->hasChanges = false;
        
        // ✅ Livewireコンポーネントのみ再描画（ページリロードしない）
        $this->reloadImages();
    }

    /**
     * レンダリング
     */
    public function render()
    {
        return view('livewire.admin.event-images');
    }
}

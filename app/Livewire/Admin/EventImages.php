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
    public $cover = null;       // カバー画像（単一）
    public $gallery = [];       // 既存＋新規を混在で管理
    public $newGallery = [];    // 新規アップロード用のバッファ

    protected function rules()
    {
        return [
            'cover'           => 'nullable', // string(既存) or UploadedFile(新規)
            'gallery'         => 'array|max:30',
            'gallery.*'       => 'nullable',
            'newGallery'      => 'array',
            'newGallery.*'    => 'file|max:10240|mimes:jpg,jpeg,png,webp,gif',
        ];
    }

    public function mount(Event $event)
    {
        $this->eventId = $event->id;

        // カバー画像
        $cover = $event->mediaFiles()->where('type', 'event_cover')->first();
        $this->cover = $cover ? ['id' => $cover->id, 'path' => $cover->path] : null;

        // ギャラリー画像
        $this->gallery = $event->mediaFiles()
            ->where('type', 'event_gallery')
            ->orderBy('media_relations.sort_order')
            ->get()
            ->map(fn($m) => ['id' => $m->id, 'path' => $m->path])
            ->toArray();

        $this->newGallery = [];
    }

    /**
     * 新規ギャラリー画像が追加されたとき
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

            // 新規を末尾にマージ
            $this->gallery = array_merge($this->gallery, $this->newGallery);
            $this->newGallery = [];
        }
    }

    /**
     * ギャラリー削除
     */
    public function removeGallery($index)
    {
        if (isset($this->gallery[$index])) {
            unset($this->gallery[$index]);
            $this->gallery = array_values($this->gallery);
        }
    }

    /**
     * 並べ替え（上）
     */
    public function moveUp($index)
    {
        if ($index > 0) {
            [$this->gallery[$index - 1], $this->gallery[$index]] = [$this->gallery[$index], $this->gallery[$index - 1]];
        }
    }

    /**
     * 並べ替え（下）
     */
    public function moveDown($index)
    {
        if ($index < count($this->gallery) - 1) {
            [$this->gallery[$index + 1], $this->gallery[$index]] = [$this->gallery[$index], $this->gallery[$index + 1]];
        }
    }

    /**
     * 保存
     */
    public function save()
    {
        $this->validate();

        $event = Event::findOrFail($this->eventId);
        $disk = config('filesystems.default', 'public');

        DB::transaction(function () use ($event, $disk) {

            /**
             * 1️⃣ カバー更新処理
             */
            // 既存レコード削除（MediaFileは残す）
            MediaRelation::where('mediable_type', Event::class)
                ->where('mediable_id', $event->id)
                ->whereHas('mediaFile', fn($q) => $q->where('type', 'event_cover'))
                ->delete();

            if ($this->cover) {
                if (is_array($this->cover) && isset($this->cover['id'])) {
                    // ✅ 既存の再リンク
                    MediaRelation::create([
                        'mediable_type' => Event::class,
                        'mediable_id'   => $event->id,
                        'media_file_id' => $this->cover['id'],
                        'sort_order'    => 0,
                    ]);
                } elseif (is_object($this->cover)) {
                    // ✅ 新規アップロード
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

            /**
             * 2️⃣ ギャラリー更新処理
             */
            MediaRelation::where('mediable_type', Event::class)
                ->where('mediable_id', $event->id)
                ->whereHas('mediaFile', fn($q) => $q->where('type', 'event_gallery'))
                ->delete();

            foreach ($this->gallery as $index => $item) {
                if (is_array($item) && isset($item['id'])) {
                    // ✅ 既存MediaFile
                    MediaRelation::create([
                        'mediable_type' => Event::class,
                        'mediable_id'   => $event->id,
                        'media_file_id' => $item['id'],
                        'sort_order'    => $index,
                    ]);
                } elseif (is_object($item)) {
                    // ✅ 新規アップロード
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
                        'sort_order'    => $index,
                    ]);
                }
            }
        });

        $this->dispatch('event-images-updated', eventId: $this->eventId);
        $this->reset('newGallery');
        session()->flash('success', '画像を更新しました。');
    }

    public function render()
    {
        return view('livewire.admin.event-images');
    }
}

<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Models\Announcement;
use App\Models\MediaFile;
use App\Models\MediaRelation;

class AnnouncementImages extends Component
{
    use WithFileUploads;

    public int $announcementId;

    /** @var array{id:int,path:string}|null| \Livewire\Features\SupportFileUploads\TemporaryUploadedFile */
    public $cover = null;

    /** @var array<int, array{id:int,path:string}|\Livewire\Features\SupportFileUploads\TemporaryUploadedFile> */
    public array $gallery = [];

    /** @var array<int, \Livewire\Features\SupportFileUploads\TemporaryUploadedFile> */
    public array $newGallery = [];

    public bool $hasChanges = false;

    // type を Event と同様に「cover / gallery」で分離
    public string $coverType   = 'announcement_cover';
    public string $galleryType = 'announcement_gallery';

    // 保存先（public disk 前提）
    public string $coverDir   = 'announcements/covers';
    public string $galleryDir = 'announcements/gallery';

    protected $listeners = ['refreshAnnouncementImages' => '$refresh'];

    protected function rules(): array
    {
        return [
            'cover'        => 'nullable',
            'gallery'      => 'array|max:30',
            'gallery.*'    => 'nullable',
            'newGallery'   => 'array',
            'newGallery.*' => 'file|max:10240|mimes:jpg,jpeg,png,webp,gif',
        ];
    }

    public function mount(Announcement $announcement): void
    {
        $this->announcementId = $announcement->id;
        $this->reloadImages();
    }

    public function reloadImages(): void
    {
        $announcement = Announcement::find($this->announcementId);

        // カバー
        $cover = $announcement->mediaFiles()->where('type', $this->coverType)->first();
        $this->cover = $cover ? ['id' => $cover->id, 'path' => $cover->path] : null;

        // ギャラリー
        $this->gallery = $announcement->mediaFiles()
            ->where('type', $this->galleryType)
            ->orderBy('media_relations.sort_order')
            ->get()
            ->map(fn ($m) => ['id' => $m->id, 'path' => $m->path])
            ->toArray();

        $this->newGallery = [];
        $this->hasChanges = false;
    }

    public function updatedNewGallery(): void
    {
        if (empty($this->newGallery)) return;

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

    public function removeCover(): void
    {
        if ($this->cover && is_array($this->cover) && isset($this->cover['id'])) {
            $mediaId = (int) $this->cover['id'];

            MediaRelation::where('media_file_id', $mediaId)->delete();

            $media = MediaFile::find($mediaId);
            if ($media) {
                // Event と合わせて物理削除をここで行う（MediaFile 側に寄せているならここは削ってOK）
                Storage::disk('public')->delete($media->path);
                $media->delete();
            }
        }

        $this->cover = null;
        $this->hasChanges = false;

        $this->reloadImages();
    }

    public function removeGallery(int $index): void
    {
        if (!isset($this->gallery[$index])) return;

        $item = $this->gallery[$index];

        if (is_array($item) && isset($item['id'])) {
            $mediaId = (int) $item['id'];

            MediaRelation::where('media_file_id', $mediaId)->delete();

            $media = MediaFile::find($mediaId);
            if ($media) {
                Storage::disk('public')->delete($media->path);
                $media->delete();
            }
        }

        unset($this->gallery[$index]);
        $this->gallery = array_values($this->gallery);
        $this->hasChanges = false;

        $this->reloadImages();
    }

    public function moveUp(int $i): void
    {
        if ($i > 0) {
            [$this->gallery[$i - 1], $this->gallery[$i]] = [$this->gallery[$i], $this->gallery[$i - 1]];
            $this->hasChanges = true;
        }
    }

    public function moveDown(int $i): void
    {
        if ($i < count($this->gallery) - 1) {
            [$this->gallery[$i + 1], $this->gallery[$i]] = [$this->gallery[$i], $this->gallery[$i + 1]];
            $this->hasChanges = true;
        }
    }

    public function save(): void
    {
        $this->validate();

        $announcement = Announcement::findOrFail($this->announcementId);
        $disk = config('filesystems.default', 'public');

        DB::transaction(function () use ($announcement, $disk) {

            // 1) カバー：既存 relation を coverType のみ削除して作り直し
            MediaRelation::where('mediable_type', Announcement::class)
                ->where('mediable_id', $announcement->id)
                ->whereHas('mediaFile', fn ($q) => $q->where('type', $this->coverType))
                ->delete();

            if ($this->cover) {
                if (is_array($this->cover) && isset($this->cover['id'])) {
                    MediaRelation::create([
                        'mediable_type' => Announcement::class,
                        'mediable_id'   => $announcement->id,
                        'media_file_id' => (int) $this->cover['id'],
                        'sort_order'    => 0,
                    ]);
                } elseif (is_object($this->cover)) {
                    $media = MediaFile::uploadAndCreate(
                        $this->cover,
                        $announcement,
                        $this->coverType,
                        $disk,
                        $this->coverDir
                    );

                    MediaRelation::create([
                        'mediable_type' => Announcement::class,
                        'mediable_id'   => $announcement->id,
                        'media_file_id' => $media->id,
                        'sort_order'    => 0,
                    ]);
                }
            }

            // 2) ギャラリー：既存 relation を galleryType のみ削除して作り直し
            MediaRelation::where('mediable_type', Announcement::class)
                ->where('mediable_id', $announcement->id)
                ->whereHas('mediaFile', fn ($q) => $q->where('type', $this->galleryType))
                ->delete();

            foreach ($this->gallery as $i => $item) {
                if (is_array($item) && isset($item['id'])) {
                    MediaRelation::create([
                        'mediable_type' => Announcement::class,
                        'mediable_id'   => $announcement->id,
                        'media_file_id' => (int) $item['id'],
                        'sort_order'    => $i,
                    ]);
                } elseif (is_object($item)) {
                    $media = MediaFile::uploadAndCreate(
                        $item,
                        $announcement,
                        $this->galleryType,
                        $disk,
                        $this->galleryDir
                    );

                    MediaRelation::create([
                        'mediable_type' => Announcement::class,
                        'mediable_id'   => $announcement->id,
                        'media_file_id' => $media->id,
                        'sort_order'    => $i,
                    ]);
                }
            }
        });

        session()->flash('success', '画像を保存しました。');
        $this->hasChanges = false;
        $this->reloadImages();
    }

    public function render()
    {
        return view('livewire.admin.announcement-images', [
            'cover'      => $this->cover,
            'gallery'    => $this->gallery,
            'hasChanges' => $this->hasChanges,
        ]);
    }
}

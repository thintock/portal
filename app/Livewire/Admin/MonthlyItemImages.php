<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Models\MonthlyItem;
use App\Models\MediaFile;
use App\Models\MediaRelation;

class MonthlyItemImages extends Component
{
    use WithFileUploads;

    public int $monthlyItemId;

    public $cover = null;        // カバー（既存配列 or 新規 UploadedFile）
    public array $gallery = [];  // 既存配列 + 新規 UploadedFile の混在
    public array $newGallery = [];
    public bool $hasChanges = false;

    protected $listeners = ['refreshMonthlyItemImages' => '$refresh'];

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

    public function mount(MonthlyItem $monthlyItem)
    {
        $this->monthlyItemId = (int) $monthlyItem->id;
        $this->reloadImages();
    }

    public function reloadImages(): void
    {
        $item = MonthlyItem::findOrFail($this->monthlyItemId);

        // カバー
        $cover = $item->mediaFiles()->where('type', 'monthly_item_cover')->first();
        $this->cover = $cover ? ['id' => $cover->id, 'path' => $cover->path] : null;

        // ギャラリー
        $this->gallery = $item->mediaFiles()
            ->where('type', 'monthly_item_gallery')
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

        // 末尾に追加
        $this->gallery = array_merge($this->gallery, $this->newGallery);
        $this->newGallery = [];
        $this->hasChanges = true;
    }

    public function removeCover(): void
    {
        if ($this->cover && is_array($this->cover) && isset($this->cover['id'])) {
            $mediaId = (int) $this->cover['id'];

            // relation 削除
            MediaRelation::where('media_file_id', $mediaId)->delete();

            // 物理削除 + media_files 削除
            $media = MediaFile::find($mediaId);
            if ($media) {
                $disk = config('filesystems.default', 'public');
                Storage::disk($disk)->delete($media->path);
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
                $disk = config('filesystems.default', 'public');
                Storage::disk($disk)->delete($media->path);
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
        if ($i <= 0) return;
        [$this->gallery[$i - 1], $this->gallery[$i]] = [$this->gallery[$i], $this->gallery[$i - 1]];
        $this->hasChanges = true;
    }

    public function moveDown(int $i): void
    {
        if ($i >= count($this->gallery) - 1) return;
        [$this->gallery[$i + 1], $this->gallery[$i]] = [$this->gallery[$i], $this->gallery[$i + 1]];
        $this->hasChanges = true;
    }

    public function save(): void
    {
        $this->validate();

        $item = MonthlyItem::findOrFail($this->monthlyItemId);
        $disk = config('filesystems.default', 'public');

        DB::transaction(function () use ($item, $disk) {

            // =========================
            // 1) カバー
            // =========================
            MediaRelation::where('mediable_type', MonthlyItem::class)
                ->where('mediable_id', $item->id)
                ->whereHas('mediaFile', fn ($q) => $q->where('type', 'monthly_item_cover'))
                ->delete();

            if ($this->cover) {
                if (is_array($this->cover) && isset($this->cover['id'])) {
                    // 既存を再リンク
                    MediaRelation::create([
                        'mediable_type' => MonthlyItem::class,
                        'mediable_id'   => $item->id,
                        'media_file_id' => (int) $this->cover['id'],
                        'sort_order'    => 0,
                    ]);
                } elseif (is_object($this->cover)) {
                    // 新規アップロード
                    $media = MediaFile::uploadAndCreate(
                        $this->cover,
                        $item,
                        'monthly_item_cover',
                        $disk,
                        'monthly-items/covers'
                    );

                    MediaRelation::create([
                        'mediable_type' => MonthlyItem::class,
                        'mediable_id'   => $item->id,
                        'media_file_id' => $media->id,
                        'sort_order'    => 0,
                    ]);
                }
            }

            // =========================
            // 2) ギャラリー
            // =========================
            MediaRelation::where('mediable_type', MonthlyItem::class)
                ->where('mediable_id', $item->id)
                ->whereHas('mediaFile', fn ($q) => $q->where('type', 'monthly_item_gallery'))
                ->delete();

            foreach ($this->gallery as $i => $g) {
                if (is_array($g) && isset($g['id'])) {
                    MediaRelation::create([
                        'mediable_type' => MonthlyItem::class,
                        'mediable_id'   => $item->id,
                        'media_file_id' => (int) $g['id'],
                        'sort_order'    => $i,
                    ]);
                } elseif (is_object($g)) {
                    $media = MediaFile::uploadAndCreate(
                        $g,
                        $item,
                        'monthly_item_gallery',
                        $disk,
                        'monthly-items/gallery'
                    );

                    MediaRelation::create([
                        'mediable_type' => MonthlyItem::class,
                        'mediable_id'   => $item->id,
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
        return view('livewire.admin.monthly-item-images');
    }
}

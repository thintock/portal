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

    // cover: 既存配列 or TemporaryUploadedFile or null
    public $cover = null;

    // gallery: 既存配列 + TemporaryUploadedFile の混在
    public array $gallery = [];

    // PostCreate と同じ「単発追加」
    public $newGallery = null;

    public bool $hasChanges = false;

    protected $listeners = ['refreshMonthlyItemImages' => '$refresh'];

    /**
     * ★ Livewire が確実に検出できる $rules プロパティ方式
     * validateOnly() がここを参照する
     */
    protected array $rules = [
        'cover'     => 'nullable|file|max:10240|mimes:jpg,jpeg,png,webp,gif',
        'gallery'   => 'array|max:30',
        'newGallery'=> 'nullable|file|max:10240|mimes:jpg,jpeg,png,webp,gif',
    ];

    protected array $validationAttributes = [
        'cover'      => 'カバー画像',
        'gallery'    => 'ギャラリー画像',
        'newGallery' => '追加画像',
    ];

    public function mount(MonthlyItem $monthlyItem): void
    {
        $this->monthlyItemId = (int) $monthlyItem->id;
        $this->reloadImages();
    }

    public function reloadImages(): void
    {
        $item = MonthlyItem::findOrFail($this->monthlyItemId);

        // cover
        $cover = $item->mediaFiles()->where('type', 'monthly_item_cover')->first();
        $this->cover = $cover ? ['id' => (int) $cover->id, 'path' => $cover->path] : null;

        // gallery
        $this->gallery = $item->mediaFiles()
            ->where('type', 'monthly_item_gallery')
            ->orderBy('media_relations.sort_order')
            ->get()
            ->map(fn ($m) => ['id' => (int) $m->id, 'path' => $m->path])
            ->toArray();

        $this->newGallery = null;
        $this->hasChanges = false;
        $this->resetErrorBag();
    }

    /**
     * cover は単発アップロードなので validateOnly('cover') でOK
     * ※ cover が既存配列のときは検証しない
     */
    public function updatedCover(): void
    {
        if (empty($this->cover) || is_array($this->cover)) return;

        $this->validateOnly('cover');
        $this->hasChanges = true;
    }

    /**
     * PostCreate と同じ：単発選択→検証→galleryへ追加→newGalleryをnullに戻す
     */
    public function updatedNewGallery(): void
    {
        if (empty($this->newGallery)) return;

        $this->resetErrorBag('newGallery');
        $this->resetErrorBag('gallery');

        // 単発検証（$rules 参照）
        $this->validateOnly('newGallery');

        // 上限チェック
        $total = count($this->gallery) + 1;
        if ($total > 30) {
            $this->addError('gallery', 'ギャラリー画像は最大30枚までです。');
            $this->newGallery = null;
            return;
        }

        // 末尾に追加
        $this->gallery[] = $this->newGallery;

        // バッファクリア（重要）
        $this->newGallery = null;

        $this->hasChanges = true;
    }

    public function removeCover(): void
    {
        if ($this->cover && is_array($this->cover) && isset($this->cover['id'])) {
            $mediaId = (int) $this->cover['id'];

            MediaRelation::where('media_file_id', $mediaId)->delete();

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

        $g = $this->gallery[$index];

        if (is_array($g) && isset($g['id'])) {
            $mediaId = (int) $g['id'];

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

        $this->hasChanges = true;
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
        // gallery の構造だけ確認（ファイル検証は追加時点で済ませる）
        $this->validateOnly('gallery');

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
                    MediaRelation::create([
                        'mediable_type' => MonthlyItem::class,
                        'mediable_id'   => $item->id,
                        'media_file_id' => (int) $this->cover['id'],
                        'sort_order'    => 0,
                    ]);
                } elseif (is_object($this->cover)) {
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

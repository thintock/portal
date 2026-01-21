<?php

namespace App\Livewire\MonthlyItems\Feedback;

use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

use App\Models\MonthlyItem;
use App\Models\FeedbackPost;
use App\Models\MediaFile;
use App\Models\MediaRelation;

class Edit extends Component
{
    use WithFileUploads;

    public MonthlyItem $monthlyItem;
    public FeedbackPost $feedbackPost;

    public string $title = '';
    public string $body  = '';

    /**
     * 既存 + 新規（混在）
     * - 既存: ['id' => int, 'path' => string]
     * - 新規: TemporaryUploadedFile
     */
    public array $images = [];
    public array $newImages = [];

    public bool $hasChanges = false;

    public function mount(MonthlyItem $monthlyItem): void
    {
        $this->monthlyItem = $monthlyItem;

        // 受付中以外は編集不可（方針により外してもOK）
        abort_unless($this->monthlyItem->isFeedbackOpen(), 403);

        $userId = Auth::id();

        // 「この月次の自分の投稿」を特定（1ユーザー=1投稿の前提）
        $post = FeedbackPost::query()
            ->where('monthly_item_id', $this->monthlyItem->id)
            ->where('user_id', $userId)
            ->firstOrFail();

        $this->feedbackPost = $post->load([
            'monthlyItem',
            'mediaFiles' => fn ($q) => $q
                ->where('media_files.type', 'feedback_image')
                ->orderBy('media_relations.sort_order'),
        ]);

        $this->title = (string) $this->feedbackPost->title;
        $this->body  = (string) $this->feedbackPost->body;

        $this->reloadImages();
    }

    private function reloadImages(): void
    {
        $this->feedbackPost->load([
            'mediaFiles' => fn ($q) => $q
                ->where('media_files.type', 'feedback_image')
                ->orderBy('media_relations.sort_order'),
        ]);

        $this->images = $this->feedbackPost->mediaFiles
            ->map(fn ($m) => ['id' => $m->id, 'path' => $m->path])
            ->toArray();

        $this->newImages = [];
        $this->hasChanges = false;
    }

    public function updatedNewImages(): void
    {
        if (empty($this->newImages)) return;

        $this->validate([
            'newImages'   => ['array'],
            'newImages.*' => ['file', 'max:10240', 'mimes:jpg,jpeg,png,webp,gif'],
        ], [], [
            'newImages'   => '画像',
            'newImages.*' => '画像',
        ]);

        $total = count($this->images) + count($this->newImages);
        if ($total > 10) {
            $this->addError('images', '画像は最大10枚までです。');
            $this->newImages = [];
            return;
        }

        $this->images = array_values(array_merge($this->images, $this->newImages));
        $this->newImages = [];
        $this->hasChanges = true;
    }

    public function removeImageAt(int $index): void
    {
        if (!isset($this->images[$index])) return;

        $item = $this->images[$index];

        // 既存画像（DB削除 + 物理削除）
        if (is_array($item) && isset($item['id'])) {
            $mediaId = (int) $item['id'];
            $disk = config('filesystems.default', 'public');

            DB::transaction(function () use ($mediaId, $disk) {
                MediaRelation::where('mediable_type', FeedbackPost::class)
                    ->where('mediable_id', $this->feedbackPost->id)
                    ->where('media_file_id', $mediaId)
                    ->delete();

                $media = MediaFile::find($mediaId);
                if ($media) {
                    Storage::disk($disk)->delete($media->path);
                    $media->delete();
                }
            });

            $this->reloadImages();
            session()->flash('success', '画像を削除しました。');
            return;
        }

        // 新規（未保存）なら配列から外すだけ
        unset($this->images[$index]);
        $this->images = array_values($this->images);
        $this->hasChanges = true;
    }

    public function moveUp(int $index): void
    {
        if ($index <= 0) return;
        if (!isset($this->images[$index], $this->images[$index - 1])) return;

        [$this->images[$index - 1], $this->images[$index]] = [$this->images[$index], $this->images[$index - 1]];
        $this->hasChanges = true;
    }

    public function moveDown(int $index): void
    {
        if (!isset($this->images[$index])) return;
        if ($index >= count($this->images) - 1) return;

        [$this->images[$index + 1], $this->images[$index]] = [$this->images[$index], $this->images[$index + 1]];
        $this->hasChanges = true;
    }

    public function save()
    {
        // 受付外は更新不可
        abort_unless($this->monthlyItem->isFeedbackOpen(), 403);

        $this->validate([
            'title'  => ['required', 'string', 'max:255'],
            'body'   => ['required', 'string', 'max:1000'],
            'images' => ['array', 'max:10'],
        ], [], [
            'title'  => 'タイトル',
            'body'   => '本文',
            'images' => '画像',
        ]);

        $disk = config('filesystems.default', 'public');

        DB::transaction(function () use ($disk) {
            $this->feedbackPost->update([
                'title' => $this->title,
                'body'  => $this->body,
            ]);

            // 並び順を完全に作り直す
            MediaRelation::where('mediable_type', FeedbackPost::class)
                ->where('mediable_id', $this->feedbackPost->id)
                ->whereHas('mediaFile', fn ($q) => $q->where('type', 'feedback_image'))
                ->delete();

            foreach ($this->images as $i => $item) {
                // 既存
                if (is_array($item) && isset($item['id'])) {
                    MediaRelation::create([
                        'mediable_type' => FeedbackPost::class,
                        'mediable_id'   => $this->feedbackPost->id,
                        'media_file_id' => (int) $item['id'],
                        'sort_order'    => $i,
                    ]);
                    continue;
                }

                // 新規
                if (is_object($item)) {
                    $media = MediaFile::uploadAndCreate(
                        $item,
                        $this->feedbackPost,
                        'feedback_image',
                        $disk,
                        'feedback-posts/images'
                    );

                    MediaRelation::create([
                        'mediable_type' => FeedbackPost::class,
                        'mediable_id'   => $this->feedbackPost->id,
                        'media_file_id' => $media->id,
                        'sort_order'    => $i,
                    ]);
                }
            }
        });

        return redirect()
            ->route('monthly-items.show', $this->monthlyItem)
            ->with('success', '更新しました。');
    }

    public function deletePost()
    {
        // 受付外は削除不可（要件次第で外してOK）
        abort_unless($this->monthlyItem->isFeedbackOpen(), 403);

        $disk = config('filesystems.default', 'public');

        DB::transaction(function () use ($disk) {
            $mediaFiles = $this->feedbackPost->mediaFiles()
                ->where('type', 'feedback_image')
                ->get();

            MediaRelation::where('mediable_type', FeedbackPost::class)
                ->where('mediable_id', $this->feedbackPost->id)
                ->whereIn('media_file_id', $mediaFiles->pluck('id'))
                ->delete();

            foreach ($mediaFiles as $m) {
                Storage::disk($disk)->delete($m->path);
                $m->delete();
            }

            $this->feedbackPost->delete();
        });

        return redirect()
            ->route('monthly-items.show', $this->monthlyItem)
            ->with('success', '投稿を削除しました。');
    }

    public function render()
    {
        return view('livewire.monthly-items.feedback.edit')
            ->layout('layouts.app', ['title' => 'フィードバック編集']);
    }
}

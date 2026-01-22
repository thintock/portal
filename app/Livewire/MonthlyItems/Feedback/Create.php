<?php

namespace App\Livewire\MonthlyItems\Feedback;

use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use App\Models\MonthlyItem;
use App\Models\FeedbackPost;
use App\Models\MediaFile;
use App\Models\MediaRelation;

class Create extends Component
{
    use WithFileUploads;

    public MonthlyItem $monthlyItem;

    public string $title = '';
    public string $body  = '';

    /** PostCreate と同じ思想 */
    public array $media = [];      // 本体（並び替え対象）
    public array $newMedia = [];   // 選択直後のバッファ（1件でも配列に寄せる）

    public function mount(MonthlyItem $monthlyItem): void
    {
        $this->monthlyItem = $monthlyItem;

        abort_unless($this->monthlyItem->isFeedbackOpen(), 403);

        $existing = FeedbackPost::query()
            ->where('monthly_item_id', $this->monthlyItem->id)
            ->where('user_id', auth()->id())
            ->first();

        if ($existing) {
            redirect()->route('monthly-items.feedback.edit', $this->monthlyItem)
                ->with('success', 'すでに投稿済みのため編集画面を開きました。');
        }
    }

    /**
     * PostCreate と同様：ファイル選択直後に newMedia.* を検証 → media にマージ
     */
    public function updatedNewMedia(): void
    {
        if (empty($this->newMedia)) return;

        $this->validate([
            'newMedia'   => 'array',
            'newMedia.*' => 'file|max:10240|mimes:jpg,jpeg,png,webp,gif',
        ], [], [
            'newMedia.*' => '画像',
        ]);

        $total = count($this->media) + count($this->newMedia);
        if ($total > 10) {
            $this->addError('media', '画像は最大10枚までです。');
            $this->newMedia = [];
            return;
        }

        $this->media = array_values(array_merge($this->media, $this->newMedia));
        $this->newMedia = [];
    }

    public function removeMedia(int $index): void
    {
        if (!isset($this->media[$index])) return;

        unset($this->media[$index]);
        $this->media = array_values($this->media);
    }

    public function moveUp(int $index): void
    {
        if ($index <= 0) return;
        [$this->media[$index - 1], $this->media[$index]] = [$this->media[$index], $this->media[$index - 1]];
    }

    public function moveDown(int $index): void
    {
        if ($index >= count($this->media) - 1) return;
        [$this->media[$index + 1], $this->media[$index]] = [$this->media[$index], $this->media[$index + 1]];
    }

    public function save()
    {
        abort_unless($this->monthlyItem->isFeedbackOpen(), 403);

        $userId = (int) Auth::id();

        $validated = $this->validate([
            'title' => ['required', 'string', 'max:255'],
            'body'  => ['required', 'string', 'max:1000'],
            'media' => ['array', 'max:10'],
            'media.*' => ['file', 'max:10240', 'mimes:jpg,jpeg,png,webp,gif'],
        ], [], [
            'title' => 'タイトル',
            'body'  => '本文',
            'media' => '画像',
        ]);

        $exists = FeedbackPost::where('monthly_item_id', $this->monthlyItem->id)
            ->where('user_id', $userId)
            ->exists();

        if ($exists) {
            return redirect()
                ->route('monthly-items.feedback.edit', $this->monthlyItem)
                ->with('success', 'すでに投稿済みのため編集画面を開きました。');
        }

        $disk = config('filesystems.default', 'public');

        DB::transaction(function () use ($validated, $userId, $disk) {
            $post = FeedbackPost::create([
                'monthly_item_id' => $this->monthlyItem->id,
                'user_id'         => $userId,
                'title'           => $validated['title'],
                'body'            => $validated['body'],
            ]);

            foreach ($this->media as $i => $file) {
                $media = MediaFile::uploadAndCreate(
                    $file,
                    $post,
                    'feedback_image',
                    $disk,
                    'feedback-posts/images'
                );

                MediaRelation::create([
                    'mediable_type' => FeedbackPost::class,
                    'mediable_id'   => $post->id,
                    'media_file_id' => $media->id,
                    'sort_order'    => $i,
                ]);
            }
        });

        return redirect()
            ->route('monthly-items.show', $this->monthlyItem)
            ->with('success', 'フィードバックを投稿しました。');
    }

    public function render()
    {
        return view('livewire.monthly-items.feedback.create')
            ->layout('layouts.app', ['title' => 'フィードバック投稿']);
    }
}

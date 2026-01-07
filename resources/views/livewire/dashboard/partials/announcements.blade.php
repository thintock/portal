<div class="space-y-4 w-full">
    <div class="max-w-3xl mx-auto">
        @if($announcements->isEmpty())
            <div class="p-4 bg-base-200 rounded-lg text-sm text-base-content/60">
                現在お知らせはありません。
            </div>
        @else
            <ul class="list bg-base-100 rounded-box shadow-sm">
                <li class="p-2 text-xs opacity-60 tracking-wide">お知らせ</li>

                @foreach($announcements as $a)
                    <li class="list-row py-2 hover:bg-white transition tracking-wide border-t border-base-200">
                        <a
                            href="{{ route('announcements.show', $a->slug) }}"
                            wire:navigate
                            class="flex items-center justify-between gap-4 w-full px-2 py-1"
                        >
                            {{-- タイトル --}}
                            <div class="min-w-0">
                                <p class="text-xs font-medium leading-snug line-clamp-2">
                                    {{ $a->title }}
                                </p>
                            </div>

                            {{-- 日付 --}}
                            <div class="text-xs text-base-content/60 whitespace-nowrap">
                                {{ $a->updated_at?->format('Y/m/d') }}
                            </div>
                        </a>
                    </li>
                @endforeach
            </ul>
        @endif
    </div>
</div>

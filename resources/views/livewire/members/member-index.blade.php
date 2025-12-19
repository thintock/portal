<div class="max-w-6xl mx-auto px-4 py-10 space-y-6">

    {{-- 検索 + ソート（PC / SP 対応） --}}
    <div class="space-y-3">
    
        {{-- ▼ 1行目：検索窓（PC/SP 共通） --}}
        <div>
            <input type="text"
                placeholder="メンバー検索"
                class="input input-bordered w-full"
                wire:model.live="search" />
        </div>
    
        {{-- ▼ 2〜3行目：スマホ（2×2 カラム） --}}
        <div class="grid grid-cols-2 gap-2 sm:hidden">
    
            {{-- 新しい順 --}}
            <button wire:click="$set('sort','newest')"
                class="btn btn-sm w-full {{ $sort === 'newest' ? 'btn-primary' : 'btn-outline' }}">
                入会日 新しい順
            </button>
    
            {{-- 古い順 --}}
            <button wire:click="$set('sort','oldest')"
                class="btn btn-sm w-full {{ $sort === 'oldest' ? 'btn-primary' : 'btn-outline' }}">
                入会日 古い順
            </button>
    
            {{-- 会員番号順 --}}
            <button wire:click="$set('sort','member_no')"
                class="btn btn-sm w-full {{ $sort === 'member_no' ? 'btn-primary' : 'btn-outline' }}">
                会員番号順
            </button>
    
            {{-- 名前順 --}}
            <button wire:click="$set('sort','name')"
                class="btn btn-sm w-full {{ $sort === 'name' ? 'btn-primary' : 'btn-outline' }}">
                名前順
            </button>
    
        </div>
    
        {{-- ▼ PC：横並び --}}
        <div class="hidden sm:flex gap-2">
    
            <button wire:click="$set('sort','newest')"
                class="btn btn-sm {{ $sort === 'newest' ? 'btn-primary' : 'btn-outline' }}">
                新しい順
            </button>
    
            <button wire:click="$set('sort','oldest')"
                class="btn btn-sm {{ $sort === 'oldest' ? 'btn-primary' : 'btn-outline' }}">
                古い順
            </button>
    
            <button wire:click="$set('sort','member_no')"
                class="btn btn-sm {{ $sort === 'member_no' ? 'btn-primary' : 'btn-outline' }}">
                会員番号順
            </button>
    
            <button wire:click="$set('sort','name')"
                class="btn btn-sm {{ $sort === 'name' ? 'btn-primary' : 'btn-outline' }}">
                名前順
            </button>
    
        </div>
    
    </div>


    {{-- 0件メッセージ --}}
    @if($members->count() === 0)
        <div class="p-8 bg-base-200 rounded-xl text-center text-base-content/60">
            条件に一致するメンバーが見つかりませんでした。
        </div>
    @endif

    {{-- PC用一覧 --}}
    <div class="hidden sm:grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">

        @foreach($members as $member)
            <div class="card bg-base-100 shadow hover:shadow-md transition p-4 flex items-center gap-4">

              {{-- アバター表示 --}}
              @php
                  $userModel = \App\Models\User::find($member->id);
                  $avatar = $member->mediaFiles()
                      ->where('media_files.type', 'avatar')
                      ->first();
              @endphp
              
              <div 
                    class="w-16 h-16 rounded-full overflow-hidden bg-base-200 flex items-center justify-center 
                           border-2 cursor-pointer transition transform hover:scale-105 hover:border-primary"
                    wire:click="$dispatch('show-membership-card', { userId: {{ $userModel->id }} })"
                    title="{{ $userModel->name ?? '未登録ユーザー' }} の会員証を表示"
                >
                    @if($avatar)
                        <img src="{{ Storage::url($avatar->path) }}"
                             alt="avatar"
                             class="w-full h-full object-cover">
                    @else
                        <span class="text-lg font-semibold text-gray-600">
                            {{ mb_substr($userModel->name ?? '？', 0, 1) }}
                        </span>
                    @endif
                </div>




                {{-- テキスト --}}
                <div class="flex-1 space-y-1">

                    {{-- 名前 + role badge --}}
                    <div class="flex items-center gap-2">
                        <h3 class="font-semibold text-lg">{{ $member->name }}</h3>

                        @if($member->role === 'admin')
                            <span class="badge badge-primary badge-sm">Official</span>
                        @elseif($member->role === 'guest')
                            <span class="badge badge-secondary badge-sm">ゲスト</span>
                        @endif
                    </div>

                    {{-- 入会日（admin/guest は無し） --}}
                    @if($member->joined_at)
                        <p class="text-sm text-base-content/70">
                            入会日：{{ \Carbon\Carbon::parse($member->joined_at)->format('Y/m/d') }}
                        </p>
                    @else
                        <p class="text-sm text-base-content/60">運営です</p>
                    @endif

                    {{-- 会員番号 --}}
                    @if($member->member_no)
                        <p class="text-sm text-base-content/70">
                            会員番号：{{ $member->member_no }}
                        </p>
                    @endif

                </div>
            </div>
        @endforeach
    </div>
    
    {{--　モバイル用一覧 --}}
    {{-- モバイル --}}
    <div class="sm:hidden divide-y divide-base-200">
    
        @foreach($members as $member)
            @php
                $avatar = $member->mediaFiles()
                    ->where('media_files.type', 'avatar')
                    ->first();
            @endphp
            
            <div class="flex items-center gap-3 px-3 py-3 active:bg-base-200 transition bg-base-100"
                wire:click="$dispatch('show-membership-card', { userId: {{ $member->id }} })">
            
                {{-- アバター --}}
                <div class="w-10 h-10 rounded-full overflow-hidden bg-base-200 flex items-center justify-center flex-shrink-0">
                    @if($avatar)
                        <img src="{{ Storage::url($avatar->path) }}" class="w-full h-full object-cover">
                    @else
                        <span class="text-sm font-semibold">
                            {{ mb_substr($member->name ?? '？', 0, 1) }}
                        </span>
                    @endif
                </div>
            
                {{-- 名前・補足 --}}
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2">
                        <p class="font-medium truncate">{{ $member->name }}</p>
                        @if($member->role === 'admin')
                            <span class="badge badge-primary badge-xs">Official</span>
                        @elseif($member->role === 'guest')
                            <span class="badge badge-secondary badge-xs">Guest</span>
                        @endif
                    </div>
            
                    <div class="text-xs text-base-content/60">
                        @if($member->role === 'admin')
                            運営です
                        @elseif($member->role === 'guest')
                            ゲストさん
                        @elseif($member->member_no)
                            会員番号:No.{{ $member->member_no }}
                            &nbsp;入会日：{{ \Carbon\Carbon::parse($member->joined_at)->format('Y/m/d') }}
                        @endif
                    </div>
                </div>
            
            </div>
        @endforeach
    
    </div>
    

    {{-- ページネーション --}}
    <div x-data="{
        observe() {
            const observer = new IntersectionObserver(entries => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        @this.call('loadMore')
                    }
                })
            })
            observer.observe(this.$el)
        }
    }"
    x-init="observe" class="h-10">
    </div>
</div>

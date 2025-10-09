<x-app-layout>
    <div class="p-1 py-10 bg-base-200">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-8">

            {{-- プロフィール情報 --}}
            <div class="card bg-white shadow-xl border border-base-300">
                <div class="card-body p-3 sm:p-6">
                    <div class="flex justify-between">
                        <h3 class="text-lg font-bold mb-4 text-primary">基本情報</h3>
                        {{-- 会員証ボタン --}}
                        <button onclick="membershipCard.showModal()" class="btn btn-outline btn-sm sm:btn-md">
                          会員証を表示
                        </button>
                    </div>
                    <p class="text-sm text-gray-500 mt-1">会員情報の確認と変更ができます。</p>  
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>
            @include('profile.partials.membership-card')
            {{-- パスワード更新 --}}
            <div class="card bg-white shadow-xl border border-base-300">
                <div class="card-body p-3 sm:p-6">
                    <h3 class="text-lg font-bold mb-4 text-primary">パスワードの変更</h3>
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            {{-- 退会 --}}
            <div class="card bg-white shadow-xl border border-error/30">
                <div class="card-body">
                    <h3 class="text-lg font-bold mb-4 text-error">アカウント削除</h3>
                    <p class="text-sm text-gray-600 mb-4">
                        アカウントを削除すると、全ての投稿・データが失われます。この操作は取り消せません。
                    </p>
                    @include('profile.partials.delete-user-form')
                </div>
            </div>

        </div>
    </div>
</x-app-layout>

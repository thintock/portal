<section>
  {{-- ヘッダー --}}
  <header class="mb-6">
    <h2 class="text-lg font-semibold text-gray-800 flex items-center space-x-3">
      <div class="badge badge-primary text-white">
        会員番号：{{ $user->member_number ?? '未発行' }}
      </div>
      <span>あなたのプロフィール</span>
    </h2>
    <p class="mt-2 text-sm text-gray-600">
      <span class="text-primary font-semibold">※</span> 印のついた項目は他のメンバーに公開されます。
    </p>
  </header>

  {{-- メール再送フォーム --}}
  <form id="send-verification" method="post" action="{{ route('verification.send') }}">
    @csrf
  </form>

  {{-- メインフォーム --}}
  <form method="post" action="{{ route('profile.update') }}" enctype="multipart/form-data"
        class="space-y-8 bg-base-100 p-3 rounded-2xl shadow-inner border border-base-200">
    @csrf
    @method('patch')

    {{-- プロフィール画像 --}}
    <div class="form-control">
      <x-input-label for="avatar" :value="__('プロフィール画像')" />
      <div class="flex flex-col sm:flex-row sm:items-center gap-4 mt-3">
        {{-- プレビュー画像 --}}
        <div class="avatar relative">
          <div class="w-24 rounded-full bg-primary ring ring-primary ring-offset-base-100 ring-offset-2 overflow-hidden">
            @if ($avatar)
              {{-- 登録済み画像 --}}
              <img id="avatar-preview"
                   src="{{ Storage::url($avatar->path) }}"
                   alt="プロフィール画像"
                   class="object-cover w-24 h-24 transition-all duration-300" />
            @else
              {{-- 未登録時：頭文字表示 --}}
              <div id="avatar-initial" class="w-24 h-24 text-white text-3xl font-semibold rounded-full
            flex items-center justify-center select-none">
                {{ mb_substr($user->name ?? '？', 0, 1) }}
              </div>
            @endif
          </div>
          
        </div>
    
        {{-- ファイルアップロード欄 --}}
        <div class="flex flex-col">
          
          <input id="avatar" name="avatar" type="file"
                 accept="image/*"
                 class="file-input file-input-bordered file-input-sm sm:file-input-md sm:w-auto" />
          
          @if ($avatar)
            <label class="label text-xs mt-2 text-gray-500">
              現在の画像を置き換える場合は新しい画像を選択してください。
            </label>
          @else
            <label class="label text-xs mt-2 text-gray-500">
              プロフィール画像をアップロードしてください。
            </label>
          @endif
          {{-- 保存前ラベル --}}
          <div id="unsaved-label" class="badge badge-warning opacity-0 transition-opacity duration-300">
            反映するには保存ボタンを押してください
          </div>
        </div>
      </div>
    
      <x-input-error class="mt-2" :messages="$errors->get('avatar')" />
    </div>
    
    {{-- ✅ 即時プレビュー & 「保存前」表示スクリプト --}}
    <script>
      document.addEventListener('DOMContentLoaded', function () {
        const input = document.getElementById('avatar');
        const preview = document.getElementById('avatar-preview');
        const unsavedLabel = document.getElementById('unsaved-label');
    
        input?.addEventListener('change', function (e) {
          const file = e.target.files[0];
          if (file && file.type.startsWith('image/')) {
            const reader = new FileReader();
            reader.onload = function (event) {
              preview.src = event.target.result;
              // ✅ 「保存前です」表示
              unsavedLabel.classList.remove('opacity-0');
              unsavedLabel.classList.add('opacity-100');
            };
            reader.readAsDataURL(file);
          }
        });
      });
    </script>


    {{-- 氏名 --}}
    <div class="divider text-sm text-gray-500">基本情報</div>
    <div class="grid sm:grid-cols-2 gap-4">
      <div>
        <x-input-label for="last_name" :value="__('姓')" />
        <x-text-input id="last_name" name="last_name" type="text"
          class="input input-bordered w-full mt-1 text-base bg-white"
          :value="old('last_name', $user->last_name)" required />
        <x-input-error class="mt-2" :messages="$errors->get('last_name')" />
      </div>

      <div>
        <x-input-label for="first_name" :value="__('名')" />
        <x-text-input id="first_name" name="first_name" type="text"
          class="input input-bordered w-full mt-1 text-base bg-white"
          :value="old('first_name', $user->first_name)" required />
        <x-input-error class="mt-2" :messages="$errors->get('first_name')" />
      </div>
    </div>

    {{-- フリガナ --}}
    <div class="grid sm:grid-cols-2 gap-4">
      <div>
        <x-input-label for="last_name_kana" :value="__('せい（かな）')" />
        <x-text-input id="last_name_kana" name="last_name_kana" type="text"
          class="input input-bordered w-full mt-1 text-base bg-white"
          :value="old('last_name_kana', $user->last_name_kana)" />
      </div>
      <div>
        <x-input-label for="first_name_kana" :value="__('めい（かな）')" />
        <x-text-input id="first_name_kana" name="first_name_kana" type="text"
          class="input input-bordered w-full mt-1 text-base bg-white"
          :value="old('first_name_kana', $user->first_name_kana)" />
      </div>
    </div>

    {{-- ニックネーム・SNS --}}
    <div class="divider text-sm text-gray-500">公開プロフィール</div>
    <div class="grid sm:grid-cols-2 gap-4">
      <div>
        <x-input-label for="name" :value="__('ニックネーム（公開されます）')" />
        <x-text-input id="name" name="name" type="text"
          class="input input-bordered w-full mt-1 text-base bg-white"
          :value="old('name', $user->name)" />
      </div>

      <div>
        <x-input-label for="instagram_id" :value="__('Instagram アカウント')" />
        <x-text-input id="instagram_id" name="instagram_id" type="text"
          class="input input-bordered w-full mt-1 text-base bg-white"
          placeholder="@bakerista_official"
          :value="old('instagram_id', $user->instagram_id)" />
      </div>
    </div>

    {{-- 住所 --}}
    <div class="divider text-sm text-gray-500">住所情報</div>
    <div class="grid sm:grid-cols-2 gap-4">
      <div>
        <x-input-label for="postal_code" :value="__('郵便番号')" />
        <x-text-input id="postal_code" name="postal_code" type="text"
          class="input input-bordered w-full mt-1 text-base bg-white"
          :value="old('postal_code', $user->postal_code)" />
      </div>

      <div>
        <x-input-label for="prefecture" :value="__('都道府県')" />
        <x-text-input id="prefecture" name="prefecture" type="text"
          class="input input-bordered w-full mt-1 text-base bg-white"
          :value="old('prefecture', $user->prefecture)" />
      </div>
    </div>

    <div>
      <x-input-label for="address1" :value="__('住所1')" />
      <x-text-input id="address1" name="address1" type="text"
        class="input input-bordered w-full mt-1 text-base bg-white"
        :value="old('address1', $user->address1)" />
    </div>
    <div>
      <x-input-label for="address2" :value="__('住所2（建物名など）')" />
      <x-text-input id="address2" name="address2" type="text"
        class="input input-bordered w-full mt-1 text-base bg-white"
        :value="old('address2', $user->address2)" />
    </div>

    {{-- 会社・電話 --}}
    <div class="divider text-sm text-gray-500">勤務先・連絡先</div>
    <div class="grid sm:grid-cols-2 gap-4">
      <div>
        <x-input-label for="company_name" :value="__('会社名')" />
        <x-text-input id="company_name" name="company_name" type="text"
          class="input input-bordered w-full mt-1 text-base bg-white"
          :value="old('company_name', $user->company_name)" />
      </div>
      <div>
        <x-input-label for="phone" :value="__('電話番号')" />
        <x-text-input id="phone" name="phone" type="tel"
          class="input input-bordered w-full mt-1 text-base bg-white"
          :value="old('phone', $user->phone)" />
      </div>
    </div>

    {{-- 通知設定 --}}
    <div class="divider text-sm text-gray-500">通知設定</div>
    <label for="email_notification" class="label cursor-pointer">
      <span class="label-text">メール通知を受け取る</span>
      <input id="email_notification" name="email_notification" type="checkbox" value="1"
        {{ old('email_notification', $user->email_notification) ? 'checked' : '' }}
        class="checkbox checkbox-primary ml-2" />
    </label>

    {{-- Email --}}
    <div class="divider text-sm text-gray-500">ログイン情報</div>
    <div>
      <x-input-label for="email" :value="__('Eメールアドレス')" />
      <x-text-input id="email" name="email" type="email"
        class="input input-bordered w-full mt-1 text-base bg-white"
        :value="old('email', $user->email)" required />
      <x-input-error class="mt-2" :messages="$errors->get('email')" />
    </div>

    {{-- 未認証時の警告 --}}
    @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
      <div class="alert alert-warning mt-3 text-sm">
        <span>メールアドレスが未確認です。</span>
        <button form="send-verification" class="link link-primary ml-2">
          確認メールを再送する
        </button>
      </div>
    @endif

    {{-- 保存ボタン --}}
    <div class="flex items-center gap-4 pt-4">
      <button type="submit"
        class="btn btn-primary shadow-md w-full sm:w-auto btn-sm sm:btn-md">
        保存する
      </button>

      @if (session('status') === 'profile-updated')
        <p x-data="{ show: true }" x-show="show" x-transition
           x-init="setTimeout(() => show = false, 2000)"
           class="text-sm text-success">保存しました。</p>
      @endif
    </div>
  </form>
</section>

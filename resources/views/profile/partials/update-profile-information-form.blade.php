<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Profile Information') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ __("Update your account's profile information and email address.") }}
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('patch')

        {{-- 氏名 --}}
        <div>
            <x-input-label for="name" :value="__('姓（Last Name）')" />
            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full"
                :value="old('name', $user->name)" required autofocus autocomplete="family-name" />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        <div>
            <x-input-label for="first_name" :value="__('名（First Name）')" />
            <x-text-input id="first_name" name="first_name" type="text" class="mt-1 block w-full"
                :value="old('first_name', $user->first_name)" autocomplete="given-name" />
            <x-input-error class="mt-2" :messages="$errors->get('first_name')" />
        </div>

        {{-- フリガナ --}}
        <div>
            <x-input-label for="last_name_kana" :value="__('姓（カナ）')" />
            <x-text-input id="last_name_kana" name="last_name_kana" type="text" class="mt-1 block w-full"
                :value="old('last_name_kana', $user->last_name_kana)" />
            <x-input-error class="mt-2" :messages="$errors->get('last_name_kana')" />
        </div>

        <div>
            <x-input-label for="first_name_kana" :value="__('名（カナ）')" />
            <x-text-input id="first_name_kana" name="first_name_kana" type="text" class="mt-1 block w-full"
                :value="old('first_name_kana', $user->first_name_kana)" />
            <x-input-error class="mt-2" :messages="$errors->get('first_name_kana')" />
        </div>

        {{-- ニックネーム --}}
        <div>
            <x-input-label for="display_name" :value="__('表示名（ニックネーム）')" />
            <x-text-input id="display_name" name="display_name" type="text" class="mt-1 block w-full"
                :value="old('display_name', $user->display_name)" />
            <x-input-error class="mt-2" :messages="$errors->get('display_name')" />
        </div>

        {{-- SNS --}}
        <div>
            <x-input-label for="instagram_id" :value="__('Instagram アカウント')" />
            <x-text-input id="instagram_id" name="instagram_id" type="text" class="mt-1 block w-full"
                :value="old('instagram_id', $user->instagram_id)" />
            <x-input-error class="mt-2" :messages="$errors->get('instagram_id')" />
        </div>

        {{-- 会社名 --}}
        <div>
            <x-input-label for="company_name" :value="__('会社名')" />
            <x-text-input id="company_name" name="company_name" type="text" class="mt-1 block w-full"
                :value="old('company_name', $user->company_name)" />
            <x-input-error class="mt-2" :messages="$errors->get('company_name')" />
        </div>

        {{-- 郵便番号・住所 --}}
        <div>
            <x-input-label for="postal_code" :value="__('郵便番号')" />
            <x-text-input id="postal_code" name="postal_code" type="text" class="mt-1 block w-full"
                :value="old('postal_code', $user->postal_code)" />
            <x-input-error class="mt-2" :messages="$errors->get('postal_code')" />
        </div>

        <div>
            <x-input-label for="prefecture" :value="__('都道府県')" />
            <x-text-input id="prefecture" name="prefecture" type="text" class="mt-1 block w-full"
                :value="old('prefecture', $user->prefecture)" />
            <x-input-error class="mt-2" :messages="$errors->get('prefecture')" />
        </div>

        <div>
            <x-input-label for="address1" :value="__('住所1')" />
            <x-text-input id="address1" name="address1" type="text" class="mt-1 block w-full"
                :value="old('address1', $user->address1)" />
            <x-input-error class="mt-2" :messages="$errors->get('address1')" />
        </div>

        <div>
            <x-input-label for="address2" :value="__('住所2')" />
            <x-text-input id="address2" name="address2" type="text" class="mt-1 block w-full"
                :value="old('address2', $user->address2)" />
            <x-input-error class="mt-2" :messages="$errors->get('address2')" />
        </div>

        <div>
            <x-input-label for="address3" :value="__('住所3')" />
            <x-text-input id="address3" name="address3" type="text" class="mt-1 block w-full"
                :value="old('address3', $user->address3)" />
            <x-input-error class="mt-2" :messages="$errors->get('address3')" />
        </div>

        {{-- 国 --}}
        <div>
            <x-input-label for="country" :value="__('国 (例: JP, US)')" />
            <x-text-input id="country" name="country" type="text" class="mt-1 block w-full"
                :value="old('country', $user->country)" />
            <x-input-error class="mt-2" :messages="$errors->get('country')" />
        </div>

        {{-- 電話番号 --}}
        <div>
            <x-input-label for="phone" :value="__('電話番号')" />
            <x-text-input id="phone" name="phone" type="tel" class="mt-1 block w-full"
                :value="old('phone', $user->phone)" autocomplete="tel" />
            <x-input-error class="mt-2" :messages="$errors->get('phone')" />
        </div>

        {{-- 通知設定 --}}
        <div class="flex items-center">
            <x-input-label for="email_notification" :value="__('メール通知を受け取る')" class="mr-2" />
            <input id="email_notification" name="email_notification" type="checkbox" value="1"
                {{ old('email_notification', $user->email_notification) ? 'checked' : '' }}>
            <x-input-error class="mt-2" :messages="$errors->get('email_notification')" />
        </div>

        {{-- Email --}}
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full"
                :value="old('email', $user->email)" required autocomplete="username" />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div>
                    <p class="text-sm mt-2 text-gray-800">
                        {{ __('Your email address is unverified.') }}
                        <button form="send-verification"
                            class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            {{ __('Click here to re-send the verification email.') }}
                        </button>
                    </p>
                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 font-medium text-sm text-green-600">
                            {{ __('A new verification link has been sent to your email address.') }}
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Save') }}</x-primary-button>

            @if (session('status') === 'profile-updated')
                <p x-data="{ show: true }" x-show="show" x-transition
                   x-init="setTimeout(() => show = false, 2000)"
                   class="text-sm text-gray-600">{{ __('Saved.') }}</p>
            @endif
        </div>
    </form>
</section>

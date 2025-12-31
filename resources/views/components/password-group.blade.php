@props([
    'passwordName' => 'password',
    'passwordConfirmationName' => null,
    'passwordId' => null,
    'passwordConfirmationId' => null,
    'passwordLabel' => null,
    'passwordConfirmationLabel' => null,
    'passwordPlaceholder' => '••••••••',
    'passwordConfirmationPlaceholder' => null,
    'passwordAutocomplete' => 'new-password',
    'passwordConfirmationAutocomplete' => 'new-password',
    'passwordModel' => null,
    'passwordConfirmationModel' => null,
])

@php
    $passwordConfirmationName ??= $passwordName . '_confirmation';
    $passwordId ??= $passwordName;
    $passwordConfirmationId ??= $passwordConfirmationName;
    $passwordLabel ??= __('Password');
    $passwordConfirmationLabel ??= __('Confirm Password');
    $passwordConfirmationPlaceholder ??= __('Confirm password');
@endphp

<div {{ $attributes->class('space-y-6') }}
    x-data="{
        showPasswordStrength: false,
        passwordStrength: 0,
        passwordValue: '',
        confirmationMatches: false,
        updateStrength(value) {
            this.passwordValue = value ?? '';
            let strength = 0;
            if (this.passwordValue.length >= 8) strength++;
            if (/[a-z]/.test(this.passwordValue) && /[A-Z]/.test(this.passwordValue)) strength++;
            if (/\d/.test(this.passwordValue)) strength++;
            if (/[^a-zA-Z\d]/.test(this.passwordValue)) strength++;
            this.passwordStrength = strength;
            this.updateMatch();
        },
        updateMatch(value = null) {
            const confirmValue = value ?? (this.$refs.passwordConfirm?.value ?? '');
            this.confirmationMatches = confirmValue.length > 0 && confirmValue === this.passwordValue;
        }
    }">
    <div>
        <label for="{{ $passwordId }}" class="text-xs font-semibold uppercase tracking-[0.35em] text-base-300">
            {{ $passwordLabel }}
        </label>
        <div class="relative mt-3">
            <input
                id="{{ $passwordId }}"
                name="{{ $passwordName }}"
                type="password"
                placeholder="{{ $passwordPlaceholder }}"
                autocomplete="{{ $passwordAutocomplete }}"
                @focus="showPasswordStrength = true"
                @input="updateStrength($event.target.value)"
                @if ($passwordModel) wire:model.defer="{{ $passwordModel }}" @endif
                class="w-full rounded-2xl border border-base-700 bg-base-black/40 px-5 py-4 text-base text-base-50 placeholder:text-base-500 focus:border-indigo focus:ring-2 focus:ring-indigo/40"
            />
        </div>

        <div class="mt-3 space-y-2 overflow-hidden" x-show="showPasswordStrength"
            x-transition:enter="transition ease-out duration-250"
            x-transition:enter-start="opacity-0 -translate-y-2"
            x-transition:enter-end="opacity-100 translate-y-0">
            <div class="flex gap-1.5">
                <template x-for="index in 4" :key="index">
                    <div class="h-1.5 flex-1 rounded-full bg-base-800 overflow-hidden transition-all duration-300"
                        :class="passwordStrength >= index && ['bg-gradient-to-r from-red to-orange','bg-gradient-to-r from-orange to-yellow','bg-gradient-to-r from-yellow to-green-light','bg-gradient-to-r from-green to-green-light'][index-1]">
                    </div>
                </template>
            </div>
            <p class="text-xs transition-all duration-200"
                :class="{
                    'text-red': passwordStrength <= 1,
                    'text-orange': passwordStrength === 2,
                    'text-yellow': passwordStrength === 3,
                    'text-green-light': passwordStrength === 4
                }">
                <span x-show="passwordStrength === 0">{{ __('Enter a password') }}</span>
                <span x-show="passwordStrength === 1">{{ __('Weak password') }}</span>
                <span x-show="passwordStrength === 2">{{ __('Fair password') }}</span>
                <span x-show="passwordStrength === 3">{{ __('Good password') }}</span>
                <span x-show="passwordStrength === 4">{{ __('Strong password! 🎉') }}</span>
            </p>
        </div>

        @error($passwordName)
            <p class="mt-2 text-sm text-red">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="{{ $passwordConfirmationId }}" class="text-xs font-semibold uppercase tracking-[0.35em] text-base-300">
            {{ $passwordConfirmationLabel }}
        </label>
        <div class="relative mt-3">
            <input
                id="{{ $passwordConfirmationId }}"
                name="{{ $passwordConfirmationName }}"
                type="password"
                placeholder="{{ $passwordConfirmationPlaceholder }}"
                autocomplete="{{ $passwordConfirmationAutocomplete }}"
                x-ref="passwordConfirm"
                @input="updateMatch($event.target.value)"
                @if ($passwordConfirmationModel) wire:model.defer="{{ $passwordConfirmationModel }}" @endif
                class="w-full rounded-2xl border border-base-700 bg-base-black/40 px-5 py-4 text-base text-base-50 placeholder:text-base-500 focus:border-indigo focus:ring-2 focus:ring-indigo/40"
            />
            <div class="absolute right-3 top-1/2 -translate-y-1/2 opacity-0 scale-0 transition-all duration-200"
                :class="confirmationMatches && 'opacity-100 scale-100'">
                <svg class="w-5 h-5 text-green" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
            </div>
        </div>
        @error($passwordConfirmationName)
            <p class="mt-2 text-sm text-red">{{ $message }}</p>
        @enderror
    </div>
</div>

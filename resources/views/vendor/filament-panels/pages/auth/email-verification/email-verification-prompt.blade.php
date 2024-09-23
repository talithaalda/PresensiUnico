<x-filament-panels::page.simple>
    <p class="text-center text-sm text-gray-500 dark:text-gray-400">
        {{ __('filament-panels::pages/auth/email-verification/email-verification-prompt.messages.notification_sent', [
            'email' => filament()->auth()->user()->getEmailForVerification(),
        ]) }}
        <span>Please also check your <b class="text-black dark:text-white">spam or junk</b> folder if you haven't
            received the
            email.</span>
    </p>

    <p class="text-center text-sm text-gray-500 dark:text-gray-400">
        {{ __('filament-panels::pages/auth/email-verification/email-verification-prompt.messages.notification_not_received') }}

        {{ $this->resendNotificationAction }}
    </p>
</x-filament-panels::page.simple>

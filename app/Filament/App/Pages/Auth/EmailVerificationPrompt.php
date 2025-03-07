<?php

namespace App\Filament\App\Pages\Auth;

use Filament\Pages\Auth\EmailVerification\EmailVerificationPrompt as BaseEmailVerificationPrompt;
use App\Notifications\VerifyEmailNotification as VerifyEmail;
use Filament\Facades\Filament;
use Filament\Actions\Action;
use DanHarrin\LivewireRateLimiting\Exceptions\TooManyRequestsException;
use Exception;
use Filament\Notifications\Notification;

class EmailVerificationPrompt extends BaseEmailVerificationPrompt
{
    public function mount(): void
    {
        /** @var MustVerifyEmail $user */
        $user = Filament::auth()->user();

        if ($user->hasVerifiedEmail()) {
            redirect()->intended(Filament::getUrl());
        }
    }
    public function resendNotificationAction(): Action
    {
        return Action::make('resendNotification')
            ->link()
            ->label(__('filament-panels::pages/auth/email-verification/email-verification-prompt.actions.resend_notification.label') . '.')
            ->action(function (): void {
                try {
                    $this->rateLimit(2);
                } catch (TooManyRequestsException $exception) {
                    Notification::make()
                        ->title(__('filament-panels::pages/auth/email-verification/email-verification-prompt.notifications.notification_resend_throttled.title', [
                            'seconds' => $exception->secondsUntilAvailable,
                            'minutes' => ceil($exception->secondsUntilAvailable / 60),
                        ]))
                        ->body(array_key_exists('body', __('filament-panels::pages/auth/email-verification/email-verification-prompt.notifications.notification_resend_throttled') ?: []) ? __('filament-panels::pages/auth/email-verification/email-verification-prompt.notifications.notification_resend_throttled.body', [
                            'seconds' => $exception->secondsUntilAvailable,
                            'minutes' => ceil($exception->secondsUntilAvailable / 60),
                        ]) : null)
                        ->danger()
                        ->send();

                    return;
                }

                $user = Filament::auth()->user();

                if (! method_exists($user, 'notify')) {
                    $userClass = $user::class;

                    throw new Exception("Model [{$userClass}] does not have a [notify()] method.");
                }

                $notification = new VerifyEmail();
                $notification->url = Filament::getVerifyEmailUrl($user);

                $user->notify($notification);

                Notification::make()
                    ->title(__('filament-panels::pages/auth/email-verification/email-verification-prompt.notifications.notification_resent.title'))
                    ->success()
                    ->send();
            });
    }
}

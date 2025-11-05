<?php

namespace Shopen\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Auth\Notifications\ResetPassword as BaseResetPassword;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\View;

class ResetPassword extends BaseResetPassword
{
    use Queueable;

    public function toMail($notifiable): MailMessage
    {
        $resetUrl = url(route('password.reset', [
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ], false));

        $view = 'emails.auth.reset-password';
        $view = View::exists($view) ? $view : "shopen::$view";

        return (new MailMessage)
            ->subject('Resetowanie hasła w ' . config('app.name'))
            ->view($view, [
                'user' => $notifiable,
                'resetUrl' => $resetUrl,
            ]);
    }
}
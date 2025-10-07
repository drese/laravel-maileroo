<?php

namespace Drese\LaravelMaileroo\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static string sendFormNotification(array $formData, string $recipient, ?string $subject = null)
 * @method static string sendEmail(array $data)
 * 
 * @see \Drese\LaravelMaileroo\MailerooFormService
 */
class Maileroo extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \Drese\LaravelMaileroo\MailerooFormService::class;
    }
}
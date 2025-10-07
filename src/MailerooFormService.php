<?php

namespace Drese\LaravelMaileroo;

use Maileroo\EmailAddress;
use Maileroo\MailerooClient;

class MailerooFormService
{
    public function __construct(
        protected MailerooClient $client
    ) {}

    /**
     * Send form notification email
     */
    public function sendFormNotification(array $formData, string $recipient, ?string $subject = null): string
    {
        $payload = [
            'from' => new EmailAddress(
                config('maileroo.from.address'),
                config('maileroo.from.name')
            ),
            'to' => new EmailAddress($recipient),
            'subject' => $subject ?? ($formData['subject'] ?? 'New Form Submission'),
            'html' => $this->formatFormDataAsHtml($formData),
            'plain' => $this->formatFormDataAsPlain($formData),
        ];

        return $this->client->sendBasicEmail($payload);
    }

    /**
     * Send a custom email using the SDK
     */
    public function sendEmail(array $data): string
    {
        // Convert string addresses to EmailAddress objects if needed
        if (is_string($data['from'] ?? null)) {
            $data['from'] = new EmailAddress($data['from']);
        }

        if (is_string($data['to'] ?? null)) {
            $data['to'] = new EmailAddress($data['to']);
        } elseif (is_array($data['to'] ?? null)) {
            $data['to'] = array_map(
                fn($to) => is_string($to) ? new EmailAddress($to) : $to,
                $data['to']
            );
        }

        return $this->client->sendBasicEmail($data);
    }

    /**
     * Format form data as HTML for email
     */
    protected function formatFormDataAsHtml(array $data): string
    {
        $html = '<h2>Form Submission</h2><table style="border-collapse: collapse; width: 100%;">';
        
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $value = json_encode($value);
            }
            
            $key = ucwords(str_replace('_', ' ', $key));
            $html .= sprintf(
                '<tr><td style="padding: 8px; border: 1px solid #ddd;"><strong>%s:</strong></td><td style="padding: 8px; border: 1px solid #ddd;">%s</td></tr>',
                htmlspecialchars($key),
                nl2br(htmlspecialchars((string) $value))
            );
        }
        
        $html .= '</table>';
        
        return $html;
    }

    /**
     * Format form data as plain text for email
     */
    protected function formatFormDataAsPlain(array $data): string
    {
        $plain = "Form Submission\n\n";
        
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $value = json_encode($value);
            }
            
            $key = ucwords(str_replace('_', ' ', $key));
            $plain .= sprintf("%s: %s\n", $key, $value);
        }
        
        return $plain;
    }
}
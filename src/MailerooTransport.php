<?php

namespace Drese\LaravelMaileroo;

use Maileroo\Attachment;
use Maileroo\EmailAddress;
use Maileroo\MailerooClient;
use Symfony\Component\Mailer\SentMessage;
use Symfony\Component\Mailer\Transport\AbstractTransport;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\MessageConverter;

class MailerooTransport extends AbstractTransport
{
    public function __construct(
        protected MailerooClient $client
    ) {
        parent::__construct();
    }

    protected function doSend(SentMessage $message): void
    {
        $email = MessageConverter::toEmail($message->getOriginalMessage());
        
        $payload = [
            'from' => $this->convertToEmailAddress($email->getFrom()[0]),
            'to' => array_map(
                fn($addr) => $this->convertToEmailAddress($addr),
                $email->getTo()
            ),
            'subject' => $email->getSubject(),
        ];

        // Add CC if present
        if ($cc = $email->getCc()) {
            $payload['cc'] = array_map(
                fn($addr) => $this->convertToEmailAddress($addr),
                $cc
            );
        }

        // Add BCC if present
        if ($bcc = $email->getBcc()) {
            $payload['bcc'] = array_map(
                fn($addr) => $this->convertToEmailAddress($addr),
                $bcc
            );
        }

        // Add reply-to if present
        if ($replyTo = $email->getReplyTo()) {
            $payload['reply_to'] = $this->convertToEmailAddress($replyTo[0]);
        }

        // Set email body
        if ($html = $email->getHtmlBody()) {
            $payload['html'] = $html;
        }
        
        if ($text = $email->getTextBody()) {
            $payload['plain'] = $text;
        }

        // Add attachments if present
        if ($attachments = $email->getAttachments()) {
            $payload['attachments'] = [];
            foreach ($attachments as $attachment) {
                $payload['attachments'][] = Attachment::fromContent(
                    $attachment->getFilename() ?? 'attachment',
                    $attachment->getBody(),
                    $attachment->getMediaType(),
                    false,
                    false
                );
            }
        }

        // Send via Maileroo SDK
        $this->client->sendBasicEmail($payload);
    }

    protected function convertToEmailAddress(Address $address): EmailAddress
    {
        $name = $address->getName();
        
        // Convert empty string to null for Maileroo SDK
        if ($name === '' || $name === null) {
            $name = null;
        }
        
        return new EmailAddress(
            $address->getAddress(),
            $name
        );
    }

    public function __toString(): string
    {
        return 'maileroo';
    }
}
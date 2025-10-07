# Laravel Maileroo

A Laravel wrapper package for [Maileroo](https://maileroo.com/?r=drese) (*affiliate link) that provides seamless integration with Laravel's Mail system and form submissions. This package wraps the Maileroo SDK to make email sending simple and Laravel-friendly.

## Features

- ✅ Seamless Laravel Mail integration
- ✅ Works with Mailables, Notifications, and Queued emails
- ✅ Form submission helper with auto-formatted HTML tables
- ✅ Direct SDK access when needed
- ✅ Support for CC, BCC, and Reply-To
- ✅ Attachment support
- ✅ Auto-discovery for Laravel

## Requirements

- PHP 8.2 or higher
- Laravel 10.x, 11.x, or 12.x

## Create a Maileroo Account

If you do not already have one, create a [Maileroo Account here](https://maileroo.com/?r=drese) (*affiliate link). Once created, you will need to generate an API key and have it handy when setting up your environment variables.

Maileroo offers a generous free tier to get you going. 

## Installation

Install the package via Composer:

```bash
composer require drese/laravel-maileroo
```

### Publish Configuration

```bash
php artisan vendor:publish --tag=maileroo-config
```

### Configure Environment

Add the following to your `.env` file:

```env
MAILEROO_API_KEY=your-api-key-here
MAIL_MAILER=maileroo
MAILEROO_TIMEOUT=30
MAIL_FROM_ADDRESS=hello@example.com
MAIL_FROM_NAME="John Smith"
```

### Update Mail Configuration

Add Maileroo to the `mailers` array in `config/mail.php`:

```php
'mailers' => [
    'maileroo' => [
        'transport' => 'maileroo',
    ],
    // ... other mailers
],
```

## Usage

### Transactional Emails

Use Laravel's standard Mail facade with any Mailable:

```php
use Illuminate\Support\Facades\Mail;
use App\Mail\WelcomeEmail;

Mail::to('user@example.com')->send(new WelcomeEmail($user));
```

Or send via Notifications:

```php
$user->notify(new InvoicePaid($invoice));
```

### Standard Mailable Example

Mailables work automatically with the Maileroo transport:

```php
namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Queue\SerializesModels;

class WelcomeEmail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public $user
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address('welcome@example.com', 'Welcome Team'),
            subject: 'Welcome to Our Platform!',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.welcome',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
```

**Simple Email via Maileroo Facade:**

```php
use Drese\LaravelMaileroo\Facades\Maileroo;

Maileroo::sendEmail([
    'from' => 'sender@example.com',
    'to' => 'recipient@example.com',
    'subject' => 'Hello',
    'html' => '<h1>Hello World!</h1>',
    'plain' => 'Hello World!',
]);
```


### Form Submissions

The package includes a convenient form submission helper that automatically formats form data into an HTML table.

**Using the Facade:**

```php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Drese\LaravelMaileroo\Facades\Maileroo;

class ContactController extends Controller
{
    public function submit(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'email' => 'required|email',
            'subject' => 'required|string',
            'message' => 'required|string',
        ]);

        $referenceId = Maileroo::sendFormNotification(
            $validated,
            'admin@example.com',
            'New Contact: ' . $validated['subject']
        );

        return back()->with('success', "Message sent! Reference: {$referenceId}");
    }
}
```

**Using Dependency Injection:**

```php
use Drese\LaravelMaileroo\MailerooFormService;

class ContactController extends Controller
{
    public function submit(Request $request, MailerooFormService $maileroo)
    {
        $validated = $request->validate([
            'name' => 'required',
            'email' => 'required|email',
            'message' => 'required',
        ]);

        $referenceId = $maileroo->sendFormNotification(
            $validated, 
            'admin@example.com',
            'New Contact Form Submission'
        );

        return back()->with('success', "Message sent! Reference: {$referenceId}");
    }
}
```

### Direct SDK Access

For advanced use cases, you can access the Maileroo SDK directly.


**Advanced Example with Attachments:**

```php
namespace App\Services;

use Maileroo\MailerooClient;
use Maileroo\EmailAddress;
use Maileroo\Attachment;

class EmailService
{
    public function __construct(
        protected MailerooClient $client
    ) {}

    public function sendInvoice($invoice)
    {
        return $this->client->sendBasicEmail([
            'from' => new EmailAddress('billing@example.com', 'Billing Team'),
            'to' => new EmailAddress($invoice->customer->email, $invoice->customer->name),
            'subject' => 'Invoice #' . $invoice->number,
            'html' => view('emails.invoice', compact('invoice'))->render(),
            'attachments' => [
                Attachment::fromFile($invoice->pdf_path, 'application/pdf')
            ],
            'tags' => [
                'type' => 'invoice',
                'customer_id' => $invoice->customer_id,
            ],
            'reference_id' => 'invoice-' . $invoice->id,
        ]);
    }
}
```

**Scheduled Emails:**

```php
public function scheduleEmail()
{
    return $this->client->sendBasicEmail([
        'from' => new EmailAddress('sender@example.com'),
        'to' => new EmailAddress('recipient@example.com'),
        'subject' => 'Scheduled Email',
        'html' => '<p>This will be sent later</p>',
        'scheduled_at' => date('c', strtotime('+1 day')),
    ]);
}
```

**Bulk Emails:**

```php
public function sendBulk()
{
    return $this->client->sendBulkEmails([
        'subject' => 'Newsletter',
        'html' => '<h1>Hello {{name}}!</h1>',
        'messages' => [
            [
                'from' => new EmailAddress('newsletter@example.com'),
                'to' => new EmailAddress('user1@example.com'),
                'template_data' => ['name' => 'John'],
            ],
            [
                'from' => new EmailAddress('newsletter@example.com'),
                'to' => new EmailAddress('user2@example.com'),
                'template_data' => ['name' => 'Jane'],
            ],
        ],
    ]);
}
```

## License

The MIT License (MIT). Please see the [License File](LICENSE) for more information.

## Support

This package is provided as-is in the hope that it will benefit the Laravel community. It is offered without warranty and without commitment to ongoing support.
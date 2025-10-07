<?php

declare(strict_types=1);

namespace Drese\LaravelMaileroo\Tests;

use Drese\LaravelMaileroo\MailerooFormService;
use Drese\LaravelMaileroo\MailerooServiceProvider;
use Maileroo\MailerooClient;
use Orchestra\Testbench\TestCase;
use PHPUnit\Framework\Attributes\Test;

class MailerooFormServiceTest extends TestCase
{
    protected function getPackageProviders($app): array
    {
        return [MailerooServiceProvider::class];
    }

    #[Test]
    public function it_formats_form_data_as_html(): void
    {
        $client = $this->createMock(MailerooClient::class);
        $service = new MailerooFormService($client);

        $formData = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'message' => 'Test message',
        ];

        $reflection = new \ReflectionClass($service);
        $method = $reflection->getMethod('formatFormDataAsHtml');
        $method->setAccessible(true);

        $html = $method->invoke($service, $formData);

        $this->assertStringContainsString('John Doe', $html);
        $this->assertStringContainsString('john@example.com', $html);
        $this->assertStringContainsString('Test message', $html);
        $this->assertStringContainsString('<table', $html);
    }

    #[Test]
    public function it_formats_form_data_as_plain_text(): void
    {
        $client = $this->createMock(MailerooClient::class);
        $service = new MailerooFormService($client);

        $formData = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
        ];

        $reflection = new \ReflectionClass($service);
        $method = $reflection->getMethod('formatFormDataAsPlain');
        $method->setAccessible(true);

        $plain = $method->invoke($service, $formData);

        $this->assertStringContainsString('John Doe', $plain);
        $this->assertStringContainsString('john@example.com', $plain);
        $this->assertStringContainsString('Form Submission', $plain);
    }
}

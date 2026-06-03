<?php

declare(strict_types=1);

namespace Tests\Unit\Mail;

use App\Mail\AccountDestroyed;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AccountDestroyedTest extends TestCase
{
    #[Test]
    public function it_should_have_correct_envelope_subject(): void
    {
        $mailable = new AccountDestroyed(
            reason: 'No longer needed',
            activeSince: '2024-01-15',
        );

        $this->assertEquals('Account deleted', $mailable->envelope()->subject);

        $rendered = $mailable->render();

        $this->assertStringContainsString('No longer needed', $rendered);
        $this->assertStringContainsString('2024-01-15', $rendered);
    }
}

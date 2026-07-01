<?php

declare(strict_types=1);

namespace Tests\Unit\Mail;

use App\Mail\NewLoginDetected;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class NewLoginDetectedTest extends TestCase
{
    #[Test]
    public function it_should_have_correct_envelope_subject(): void
    {
        $mailable = new NewLoginDetected(
            device: 'Rachel iPhone 15',
        );

        $this->assertEquals('New sign-in to your account', $mailable->envelope()->subject);

        $rendered = $mailable->render();

        $this->assertStringContainsString('Rachel iPhone 15', $rendered);
    }
}

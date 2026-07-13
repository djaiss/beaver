<?php

declare(strict_types=1);

namespace Tests\Unit\Models;

use App\Models\EmailSent;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_has_many_emails_sent(): void
    {
        $user = $this->createUser();
        EmailSent::factory()->create([
            'user_id' => $user->id,
        ]);

        $this->assertTrue($user->emailsSent()->exists());
    }

    #[Test]
    public function it_gets_the_initials(): void
    {
        $ross = User::factory()->create([
            'first_name' => 'Ross',
            'last_name' => 'Geller',
        ]);

        $this->assertEquals('RG', $ross->initials());
    }

    #[Test]
    public function it_gets_the_full_name(): void
    {
        $user = User::factory()->create([
            'first_name' => 'Ross',
            'last_name' => 'Geller',
        ]);

        $this->assertEquals('Ross Geller', $user->getFullName());
    }

    #[Test]
    public function it_encrypts_the_two_factor_secret_and_recovery_codes_at_rest(): void
    {
        $secret = 'JBSWY3DPEHPK3PXP';
        $recoveryCodes = ['ABC123', 'DEF456'];

        $user = User::factory()->create([
            'two_factor_secret' => $secret,
            'two_factor_recovery_codes' => $recoveryCodes,
        ]);

        // The values are decrypted transparently when read through the model.
        $this->assertSame($secret, $user->fresh()->two_factor_secret);
        $this->assertSame($recoveryCodes, $user->fresh()->two_factor_recovery_codes);

        // But the raw database values are encrypted, not plaintext.
        $raw = DB::table('users')->where('id', $user->id)->first();
        $this->assertNotSame($secret, $raw->two_factor_secret);
        $this->assertStringNotContainsString($secret, (string) $raw->two_factor_secret);
        $this->assertStringNotContainsString('ABC123', (string) $raw->two_factor_recovery_codes);
    }
}

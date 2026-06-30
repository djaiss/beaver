<?php

declare(strict_types=1);

namespace Tests\Unit\Models;

use App\Models\EmailSent;
use App\Models\Member;
use App\Models\User;
use App\Models\Vault;
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
    public function it_has_many_memberships(): void
    {
        $user = $this->createUser();
        Member::factory()->create([
            'user_id' => $user->id,
        ]);

        $this->assertTrue($user->memberships()->exists());
    }

    #[Test]
    public function it_has_many_vaults_through_memberships(): void
    {
        $user = $this->createUser();
        $vault = Vault::factory()->create();
        Member::factory()->create([
            'user_id' => $user->id,
            'vault_id' => $vault->id,
        ]);

        $this->assertTrue($user->vaults()->exists());
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
    public function it_checks_if_user_is_part_of_vault(): void
    {
        $user = $this->createUser();
        $vault = Vault::factory()->create();

        $this->assertFalse($user->isPartOfVault($vault));

        Member::factory()->create([
            'user_id' => $user->id,
            'vault_id' => $vault->id,
        ]);
        $this->assertTrue($user->isPartOfVault($vault));
    }

    #[Test]
    public function it_gets_the_member_object_for_the_given_user(): void
    {
        $ross = Member::factory()->create([]);

        $this->assertInstanceOf(
            Member::class,
            $ross->user->memberOf($ross->vault),
        );
    }

    #[Test]
    public function it_fails_to_get_the_member_object_if_user_is_not_part_of_the_vault(): void
    {
        $ross = Member::factory()->create([]);
        $vault = Vault::factory()->create([]);

        $this->assertNull(
            $ross->user->memberOf($vault),
        );
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

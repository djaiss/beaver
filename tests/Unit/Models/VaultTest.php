<?php

declare(strict_types=1);

namespace Tests\Unit\Models;

use App\Models\Member;
use App\Models\Vault;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class VaultTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_has_many_memberships(): void
    {
        $vault = Vault::factory()->create();
        Member::factory()->create([
            'vault_id' => $vault->id,
        ]);

        $this->assertTrue($vault->members()->exists());
    }

    #[Test]
    public function it_gets_avatar(): void
    {
        $vault = Vault::factory()->create();

        $avatar = $vault->getAvatar();

        $this->assertStringStartsWith('data:image/svg+xml;base64,', $avatar);
    }
}

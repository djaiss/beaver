<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\App\Vault\Adminland;

use App\Enums\PermissionEnum;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AdminlandControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_shows_the_adminland(): void
    {
        $user = $this->createUser();
        $vault = $this->createVault();
        $this->assignUserToVault(
            user: $user,
            vault: $vault,
            role: PermissionEnum::Owner->value,
        );

        $response = $this->actingAs($user)->get('/vaults/'.$vault->id.'/adminland');

        $response->assertStatus(200);
    }

    #[Test]
    public function it_restricts_adminland(): void
    {
        $user = $this->createUser();
        $vault = $this->createVault();
        $this->assignUserToVault(
            user: $user,
            vault: $vault,
            role: PermissionEnum::Viewer->value,
        );

        $response = $this->actingAs($user)->get('/vaults/'.$vault->id.'/adminland');

        $response->assertStatus(403);
    }
}

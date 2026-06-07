<?php

declare(strict_types=1);

namespace Tests\Unit\Models;

use App\Models\Gender;
use App\Models\MaritalStatus;
use App\Models\Member;
use App\Models\Person;
use App\Models\RelationshipType;
use App\Models\RelationshipTypeCategory;
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
    public function it_has_many_genders(): void
    {
        $vault = Vault::factory()->create();
        Gender::factory()->create([
            'vault_id' => $vault->id,
        ]);

        $this->assertTrue($vault->genders()->exists());
    }

    #[Test]
    public function it_has_many_persons(): void
    {
        $vault = Vault::factory()->create();
        Person::factory()->create([
            'vault_id' => $vault->id,
        ]);

        $this->assertTrue($vault->persons()->exists());
    }

    #[Test]
    public function it_has_many_marital_statuses(): void
    {
        $vault = Vault::factory()->create();
        MaritalStatus::factory()->create([
            'vault_id' => $vault->id,
        ]);

        $this->assertTrue($vault->maritalStatuses()->exists());
    }

    #[Test]
    public function it_has_many_relationship_type_categories(): void
    {
        $vault = Vault::factory()->create();
        RelationshipTypeCategory::factory()->create(['vault_id' => $vault->id]);

        $this->assertTrue($vault->relationshipTypeCategories()->exists());
    }

    #[Test]
    public function it_has_many_relationship_types(): void
    {
        $vault = Vault::factory()->create();
        RelationshipType::factory()->create(['vault_id' => $vault->id]);

        $this->assertTrue($vault->relationshipTypes()->exists());
    }

    #[Test]
    public function it_gets_avatar(): void
    {
        $vault = Vault::factory()->create();

        $avatar = $vault->getAvatar();

        $this->assertStringStartsWith('data:image/svg+xml;base64,', $avatar);
    }
}

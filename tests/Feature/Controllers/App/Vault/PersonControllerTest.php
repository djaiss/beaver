<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\App\Vault;

use App\Enums\PermissionEnum;
use App\Models\Gender;
use App\Models\Person;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class PersonControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_shows_the_new_person_page(): void
    {
        $user = $this->createUser();
        $vault = $this->createVault();
        $this->assignUserToVault($user, $vault, PermissionEnum::Editor->value);
        $secondGender = Gender::factory()->create([
            'vault_id' => $vault->id,
            'name' => 'Second',
            'position' => 2,
        ]);
        $firstGender = Gender::factory()->create([
            'vault_id' => $vault->id,
            'name' => 'First',
            'position' => 1,
        ]);

        $response = $this->actingAs($user)->get('/vaults/'.$vault->id.'/persons/new');

        $response->assertOk();
        $response->assertViewIs('app.vault.person.create');
        $response->assertViewHas('vault', $vault);
        $response->assertViewHas(
            'genders',
            fn ($genders): bool => $genders->all() === [
                $firstGender->id => 'First',
                $secondGender->id => 'Second',
            ],
        );
    }

    #[Test]
    public function it_creates_a_person(): void
    {
        Queue::fake();

        $user = $this->createUser();
        $vault = $this->createVault();
        $this->assignUserToVault($user, $vault, PermissionEnum::Editor->value);
        $gender = Gender::factory()->create([
            'vault_id' => $vault->id,
        ]);

        $response = $this->actingAs($user)->post('/vaults/'.$vault->id.'/persons', [
            'gender_id' => $gender->id,
            'marital_status' => 'married',
            'kids_status' => 'has_kids',
            'first_name' => 'Regis',
            'middle_name' => 'John',
            'last_name' => 'Smith',
            'nickname' => 'RJ',
            'maiden_name' => 'Brown',
            'suffix' => 'Jr.',
            'prefix' => 'Mr.',
        ]);

        $person = Person::query()->where('vault_id', $vault->id)->firstOrFail();

        $response->assertRedirect(route('vault.person.index', $vault->id));
        $response->assertSessionHas('status', __('app/person.new.created'));
        $this->assertSame('Regis', $person->first_name);
        $this->assertSame('Smith', $person->last_name);
        $this->assertSame($gender->id, $person->gender_id);
        $this->assertSame($person->id.'-regis-smith', $person->slug);
    }

    #[Test]
    public function it_shows_the_temporary_blank_person_page(): void
    {
        $user = $this->createUser();
        $vault = $this->createVault();
        $this->assignUserToVault($user, $vault, PermissionEnum::Editor->value);

        $response = $this->actingAs($user)->get('/vaults/'.$vault->id.'/persons');

        $response->assertOk();
        $response->assertViewIs('app.vault.person.index');
    }

    #[Test]
    public function it_rejects_a_gender_from_another_vault(): void
    {
        $user = $this->createUser();
        $vault = $this->createVault();
        $otherVault = $this->createVault('Other vault');
        $this->assignUserToVault($user, $vault, PermissionEnum::Editor->value);
        $gender = Gender::factory()->create([
            'vault_id' => $otherVault->id,
        ]);

        $response = $this->actingAs($user)
            ->from('/vaults/'.$vault->id.'/persons/new')
            ->post('/vaults/'.$vault->id.'/persons', [
                'gender_id' => $gender->id,
                'first_name' => 'Regis',
            ]);

        $response->assertRedirect('/vaults/'.$vault->id.'/persons/new');
        $response->assertSessionHasErrors('gender_id');
        $this->assertDatabaseCount('persons', 0);
    }

    #[Test]
    public function it_prevents_a_viewer_from_creating_a_person(): void
    {
        $user = $this->createUser();
        $vault = $this->createVault();
        $this->assignUserToVault($user, $vault, PermissionEnum::Viewer->value);

        $response = $this->actingAs($user)->post('/vaults/'.$vault->id.'/persons', [
            'first_name' => 'Regis',
        ]);

        $response->assertNotFound();
        $this->assertDatabaseCount('persons', 0);
    }
}

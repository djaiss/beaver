<?php

declare(strict_types = 1);

namespace Tests\Feature\Controllers\App\Vault\Persons;

use App\Models\Person;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class PersonSearchControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_searches_listed_persons_by_name_case_insensitively(): void
    {
        $user = $this->createUser();
        $vault = $this->createVault();
        $this->assignUserToVault($user, $vault);
        $matchingPerson = Person::factory()->create([
            'vault_id' => $vault->id,
            'first_name' => 'Monica',
            'last_name' => 'Geller',
            'nickname' => null,
            'maiden_name' => null,
        ]);
        Person::factory()->create([
            'vault_id' => $vault->id,
            'first_name' => 'Ross',
            'last_name' => 'Geller',
            'nickname' => null,
            'maiden_name' => null,
        ]);

        $response = $this->actingAs($user)
            ->post(route('vault.person.search', $vault->id), [
                'term' => 'MON',
            ]);

        $response->assertOk();
        $response->assertViewIs('app.vault.person._list');
        $response->assertViewHas('vault', $vault);
        $response->assertViewHas(
            'persons',
            fn (Collection $persons): bool => $persons->pluck('id')->all() === [$matchingPerson->id],
        );
        $response->assertViewHas('person');
    }

    #[Test]
    public function it_searches_listed_persons_by_nickname(): void
    {
        $user = $this->createUser();
        $vault = $this->createVault();
        $this->assignUserToVault($user, $vault);
        $matchingPerson = Person::factory()->create([
            'vault_id' => $vault->id,
            'first_name' => 'Phoebe',
            'last_name' => 'Buffay',
            'nickname' => 'Pheebs',
            'maiden_name' => null,
        ]);

        $response = $this->actingAs($user)
            ->post(route('vault.person.search', $vault->id), [
                'term' => 'phee',
            ]);

        $response->assertOk();
        $response->assertViewHas(
            'persons',
            fn (Collection $persons): bool => $persons->pluck('id')->all() === [$matchingPerson->id],
        );
    }

    #[Test]
    public function it_searches_listed_persons_by_maiden_name(): void
    {
        $user = $this->createUser();
        $vault = $this->createVault();
        $this->assignUserToVault($user, $vault);
        $matchingPerson = Person::factory()->create([
            'vault_id' => $vault->id,
            'first_name' => 'Rachel',
            'last_name' => 'Green',
            'nickname' => null,
            'maiden_name' => 'Greene',
        ]);

        $response = $this->actingAs($user)
            ->post(route('vault.person.search', $vault->id), [
                'term' => 'greene',
            ]);

        $response->assertOk();
        $response->assertViewHas(
            'persons',
            fn (Collection $persons): bool => $persons->pluck('id')->all() === [$matchingPerson->id],
        );
    }

    #[Test]
    public function it_only_searches_persons_from_the_current_vault(): void
    {
        $user = $this->createUser();
        $vault = $this->createVault();
        $otherVault = $this->createVault('Other vault');
        $this->assignUserToVault($user, $vault);
        $matchingPerson = Person::factory()->create([
            'vault_id' => $vault->id,
            'first_name' => 'Chandler',
            'last_name' => 'Bing',
            'nickname' => null,
            'maiden_name' => null,
        ]);
        Person::factory()->create([
            'vault_id' => $otherVault->id,
            'first_name' => 'Chandler',
            'last_name' => 'Bing',
            'nickname' => null,
            'maiden_name' => null,
        ]);

        $response = $this->actingAs($user)
            ->post(route('vault.person.search', $vault->id), [
                'term' => 'chandler',
            ]);

        $response->assertOk();
        $response->assertViewHas(
            'persons',
            fn (Collection $persons): bool => $persons->pluck('id')->all() === [$matchingPerson->id],
        );
    }

    #[Test]
    public function it_excludes_unlisted_persons(): void
    {
        $user = $this->createUser();
        $vault = $this->createVault();
        $this->assignUserToVault($user, $vault);
        Person::factory()->create([
            'vault_id' => $vault->id,
            'first_name' => 'Joey',
            'last_name' => 'Tribbiani',
            'nickname' => null,
            'maiden_name' => null,
            'is_listed' => false,
        ]);

        $response = $this->actingAs($user)
            ->post(route('vault.person.search', $vault->id), [
                'term' => 'joey',
            ]);

        $response->assertOk();
        $response->assertViewHas(
            'persons',
            fn (Collection $persons): bool => $persons->isEmpty(),
        );
    }

    #[Test]
    public function it_returns_all_listed_persons_when_the_term_is_zero(): void
    {
        $user = $this->createUser();
        $vault = $this->createVault();
        $this->assignUserToVault($user, $vault);
        $persons = Person::factory()
            ->count(2)
            ->create([
                'vault_id' => $vault->id,
                'nickname' => null,
                'maiden_name' => null,
            ]);

        $response = $this->actingAs($user)
            ->post(route('vault.person.search', $vault->id), [
                'term' => '0',
            ]);

        $response->assertOk();
        $response->assertViewHas(
            'persons',
            fn (Collection $filteredPersons): bool => (
                $filteredPersons->pluck('id')->sort()->values()->all() === $persons
                    ->pluck('id')
                    ->sort()
                    ->values()
                    ->all()
            ),
        );
    }

    #[Test]
    public function it_requires_a_search_term(): void
    {
        $user = $this->createUser();
        $vault = $this->createVault();
        $this->assignUserToVault($user, $vault);

        $response = $this->actingAs($user)
            ->post(route('vault.person.search', $vault->id), [
                'term' => '',
            ]);

        $response->assertInvalid(['term' => 'required']);
    }

    #[Test]
    public function it_limits_the_search_term_to_255_characters(): void
    {
        $user = $this->createUser();
        $vault = $this->createVault();
        $this->assignUserToVault($user, $vault);

        $response = $this->actingAs($user)
            ->post(route('vault.person.search', $vault->id), [
                'term' => str_repeat('a', 256),
            ]);

        $response->assertInvalid(['term' => 'must not be greater than 255 characters']);
    }
}

<?php

declare(strict_types=1);

namespace Tests\Unit\Actions;

use App\Actions\CreateAccount;
use App\Models\Account;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class CreateAccountTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_creates_an_account_and_stamps_the_author(): void
    {
        $author = $this->createUser(['first_name' => 'Monica', 'last_name' => 'Geller']);

        $account = new CreateAccount(
            author: $author,
            name: 'Central Perk',
        )->execute();

        $this->assertInstanceOf(Account::class, $account);
        $this->assertSame('Central Perk', $account->name);
        $this->assertDatabaseHas('accounts', [
            'id' => $account->id,
            'created_by_id' => $author->id,
            'updated_by_id' => $author->id,
        ]);
        $this->assertSame('Monica Geller', $account->created_by_name);
        $this->assertSame('Monica Geller', $account->updated_by_name);
    }

    #[Test]
    public function it_sanitizes_the_name(): void
    {
        $author = $this->createUser();

        $account = new CreateAccount(
            author: $author,
            name: '<strong>Central Perk</strong>',
        )->execute();

        $this->assertSame('Central Perk', $account->name);
    }
}

<?php

declare(strict_types=1);

namespace Tests\Unit\Models\Concerns;

use App\Models\Account;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class HasAuthorTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_stamps_the_author_when_a_record_is_created(): void
    {
        $user = $this->createUser(['first_name' => 'Rachel', 'last_name' => 'Green']);

        $account = $this->actingAs($user)->createAccount();

        $account->refresh();

        $this->assertSame($user->id, $account->created_by_id);
        $this->assertSame('Rachel Green', $account->created_by_name);
        $this->assertSame($user->id, $account->updated_by_id);
        $this->assertSame('Rachel Green', $account->updated_by_name);
    }

    #[Test]
    public function it_refreshes_only_the_updater_when_a_record_is_updated(): void
    {
        $creator = $this->createUser(['first_name' => 'Rachel', 'last_name' => 'Green']);
        $editor = $this->createUser(['first_name' => 'Ross', 'last_name' => 'Geller']);

        $account = $this->actingAs($creator)->createAccount(name: 'Old name');

        $this->actingAs($editor);
        $account->name = 'Central Perk';
        $account->save();

        $account->refresh();

        $this->assertSame($creator->id, $account->created_by_id);
        $this->assertSame('Rachel Green', $account->created_by_name);
        $this->assertSame($editor->id, $account->updated_by_id);
        $this->assertSame('Ross Geller', $account->updated_by_name);
    }

    #[Test]
    public function it_keeps_the_author_name_snapshot_after_the_author_is_deleted(): void
    {
        $creator = $this->createUser(['first_name' => 'Rachel', 'last_name' => 'Green']);
        $editor = $this->createUser(['first_name' => 'Ross', 'last_name' => 'Geller']);

        $account = $this->actingAs($creator)->createAccount(name: 'Old name');

        $this->actingAs($editor);
        $account->name = 'Central Perk';
        $account->save();

        $creator->delete();

        $account->refresh();

        /*
         * The author id is wiped through the nullOnDelete foreign key when the
         * database enforces constraints. The sqlite testing connection leaves
         * foreign keys disabled, so we only assert on the encrypted name
         * snapshot, which is the behaviour that survives regardless of the
         * database in use.
         */
        $this->assertDatabaseMissing('users', ['id' => $creator->id]);
        $this->assertSame('Rachel Green', $account->created_by_name);
    }
}

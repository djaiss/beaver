<?php

declare(strict_types=1);

use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('creates, searches, renames and deletes tags inline', function () {
    $user = User::factory()->create();
    $this->actingAs($user);
    $tag = Tag::factory()->create(['account_id' => $user->account_id, 'name' => 'Signed']);

    $page = visit('/settings/tags');
    $page->assertSee('Tags')
        ->assertSee('1 tag total');

    // Creating a tag refreshes the list, the counter and clears the field.
    $page->fill('#name', 'First Issue')
        ->press('New tag')
        ->assertSee('Tag created')
        ->assertSee('2 tags total');

    expect(Tag::query()->count())->toBe(2);

    // Searching filters the rows client-side.
    $page->fill('[data-test="search-tags"]', 'zzz')
        ->assertSee('No tags match your search.');

    $page->fill('[data-test="search-tags"]', '');

    // The name is only editable once the rename button is pressed, and Enter commits it.
    $page->click('[data-test="edit-tag-'.$tag->id.'"]')
        ->type('[data-test="tag-name-'.$tag->id.'"]', 'Autographed')
        ->keys('[data-test="tag-name-'.$tag->id.'"]', 'Enter')
        ->assertSee('Tag updated');

    expect($tag->fresh()->name)->toBe('Autographed');

    // Deleting confirms first, then drops the row and updates the counter.
    $page->script('window.confirm = () => true');
    $page->click('[data-test="delete-tag-'.$tag->id.'"]')
        ->assertSee('Tag deleted')
        ->assertDontSee('Autographed')
        ->assertSee('1 tag total');

    $this->assertModelMissing($tag);

    $page->assertNoSmoke();
});

it('shows only the empty state when the last tag goes, and restores the list when one comes back', function () {
    $user = User::factory()->create();
    $this->actingAs($user);
    $tag = Tag::factory()->create(['account_id' => $user->account_id, 'name' => 'Signed']);

    $page = visit('/settings/tags');
    $page->assertSee('Updated')
        ->assertSee('1 tag total')
        ->assertDontSee('No tags yet');

    // Deleting the last tag leaves the empty state on its own: no column headers,
    // no counter and nothing to search.
    $page->script('window.confirm = () => true');
    $page->click('[data-test="delete-tag-'.$tag->id.'"]')
        ->assertSee('No tags yet')
        ->assertDontSee('Updated')
        ->assertDontSee('tags total')
        ->assertMissing('[data-test="search-tags"]');

    // Adding the first tag back brings the table with it.
    $page->fill('#name', 'First Issue')
        ->press('New tag')
        ->assertSee('1 tag total')
        ->assertSee('Updated')
        ->assertDontSee('No tags yet')
        ->assertPresent('[data-test="search-tags"]');

    $page->assertNoSmoke();
});

<?php

declare(strict_types=1);
use App\Models\Account;
use App\Models\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

uses(RefreshDatabase::class);

it('belongs to an account', function () {
    $account = $this->createAccount();
    $tag = Tag::factory()->create(['account_id' => $account->id]);

    expect($tag->account)->toBeInstanceOf(Account::class);
    expect($tag->account->id)->toBe($account->id);
});

it('encrypts the name at rest', function () {
    $tag = Tag::factory()->create(['name' => 'Signed']);

    $rawName = DB::table('tags')->where('id', $tag->id)->value('name');

    $this->assertNotSame('Signed', $rawName);
    expect($tag->fresh()->name)->toBe('Signed');
});

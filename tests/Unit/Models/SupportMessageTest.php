<?php

declare(strict_types=1);
use App\Models\SupportMessage;
use App\Models\SupportTicket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

uses(RefreshDatabase::class);

it('belongs to a ticket', function () {
    $message = SupportMessage::factory()->create();

    expect($message->ticket()->exists())->toBeTrue();
    expect($message->ticket)->toBeInstanceOf(SupportTicket::class);
});

it('belongs to a user', function () {
    $message = SupportMessage::factory()->create();

    expect($message->user()->exists())->toBeTrue();
    expect($message->user)->toBeInstanceOf(User::class);
});

it('encrypts the body at rest', function () {
    $message = SupportMessage::factory()->create([
        'body' => 'The import keeps timing out on my 4,300 records.',
    ]);

    expect($message->body)->toBe('The import keeps timing out on my 4,300 records.');

    $raw = DB::table('support_messages')->where('id', $message->id)->value('body');
    expect(decrypt($raw, false))->toBe('The import keeps timing out on my 4,300 records.');
});

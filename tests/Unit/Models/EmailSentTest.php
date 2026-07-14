<?php

declare(strict_types=1);
use App\Models\EmailSent;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('belongs to a user', function () {
    $user = $this->createUser();
    $emailSent = EmailSent::factory()->create([
        'user_id' => $user->id,
    ]);

    expect($emailSent->user()->exists())->toBeTrue();
});

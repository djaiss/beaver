<?php

declare(strict_types=1);
use App\Actions\CreateEmailSent;
use App\Models\EmailSent;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Str;

uses(RefreshDatabase::class);

it('creates an email sent', function () {
    Date::setTestNow(Date::create(2018, 1, 1));
    $user = $this->createUser();

    $emailSent = new CreateEmailSent(
        user: $user,
        uuid: 'd27cee22-b10f-46c4-a7dc-af3b46820d80',
        emailType: 'birthday_wishes',
        emailAddress: 'ross.geller@friends.com',
        subject: 'Happy Birthday!',
        body: 'Hope you have a great day!',
    )->execute();

    $this->assertDatabaseHas('emails_sent', [
        'id' => $emailSent->id,
        'user_id' => $user->id,
        'uuid' => 'd27cee22-b10f-46c4-a7dc-af3b46820d80',
        'sent_at' => '2018-01-01 00:00:00',
    ]);

    expect(mb_strlen((string) $emailSent->uuid))->toEqual(36);

    expect($emailSent)->toBeInstanceOf(EmailSent::class);
});

it('sanitizes the body and strips any links', function () {
    $user = $this->createUser();

    $emailSent = new CreateEmailSent(
        user: $user,
        uuid: null,
        emailType: 'birthday_wishes',
        emailAddress: 'ross.geller@friends.com',
        subject: 'Happy Birthday!',
        body: 'Hope you <a href="https://example.com">have a great day!</a>',
    )->execute();

    $this->assertDatabaseHas('emails_sent', [
        'id' => $emailSent->id,
    ]);
});

it('creates an email sent with a uuid', function () {
    $user = $this->createUser();
    $uuid = Str::uuid();

    $emailSent = new CreateEmailSent(
        user: $user,
        uuid: $uuid->toString(),
        emailType: 'birthday_wishes',
        emailAddress: 'ross.geller@friends.com',
        subject: 'Happy Birthday!',
        body: 'Hope you have a great day!',
    )->execute();

    $this->assertDatabaseHas('emails_sent', [
        'id' => $emailSent->id,
        'uuid' => $uuid->toString(),
    ]);
});

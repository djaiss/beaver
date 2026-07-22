<?php

declare(strict_types=1);
use App\Models\EmailSent;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Date;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->jsonStructure = [
        'type',
        'id',
        'attributes' => [
            'email_type',
            'email_address',
            'subject',
            'body',
            'sent_at',
            'delivered_at',
            'bounced_at',
            'created_at',
            'updated_at',
        ],
        'links' => [
            'self',
        ],
    ];
});

it('lists the emails of the current user', function () {
    Date::setTestNow('2025-06-30 12:00:00');
    $user = User::factory()->create();
    $email = EmailSent::factory()->create([
        'user_id' => $user->id,
        'email_type' => 'welcome',
        'email_address' => 'chandler.bing@friends.test',
        'subject' => 'Welcome to kollek',
        'body' => 'Could this BE any more of a welcome email?',
        'sent_at' => '2025-06-30 12:00:00',
        'delivered_at' => '2025-06-30 12:00:00',
        'bounced_at' => null,
    ]);

    $anotherUser = User::factory()->create();
    $anotherEmail = EmailSent::factory()->create([
        'user_id' => $anotherUser->id,
    ]);

    Sanctum::actingAs($user);

    $response = $this->json('GET', '/api/administration/emails');

    $response
        ->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonStructure([
            'data' => [
                '*' => $this->jsonStructure,
            ],
            'links',
            'meta',
        ])
        ->assertJsonPath('data.0.type', 'email')
        ->assertJsonPath('data.0.id', (string) $email->id)
        ->assertJsonPath('data.0.attributes.email_type', 'welcome')
        ->assertJsonPath('data.0.attributes.email_address', 'chandler.bing@friends.test')
        ->assertJsonPath('data.0.attributes.subject', 'Welcome to kollek')
        ->assertJsonPath('data.0.attributes.body', 'Could this BE any more of a welcome email?')
        ->assertJsonPath('data.0.attributes.sent_at', 1751284800)
        ->assertJsonPath('data.0.attributes.bounced_at', null)
        ->assertJsonMissing(['id' => (string) $anotherEmail->id]);
});

it('paginates the emails', function () {
    $user = User::factory()->create();

    EmailSent::factory()->count(15)->create([
        'user_id' => $user->id,
    ]);

    Sanctum::actingAs($user);

    $response = $this->json('GET', '/api/administration/emails');

    $response
        ->assertOk()
        ->assertJsonCount(10, 'data')
        ->assertJsonPath('meta.total', 15);
});

it('shows an email', function () {
    Date::setTestNow('2025-06-30 12:00:00');
    $user = User::factory()->create();
    $email = EmailSent::factory()->create([
        'user_id' => $user->id,
        'email_type' => 'welcome',
        'email_address' => 'monica.geller@friends.test',
        'subject' => 'Welcome to kollek',
        'body' => 'I know!',
        'sent_at' => '2025-06-30 12:00:00',
        'delivered_at' => '2025-06-30 12:00:00',
        'bounced_at' => null,
    ]);

    Sanctum::actingAs($user);

    $response = $this->json('GET', '/api/administration/emails/'.$email->id);

    $response
        ->assertOk()
        ->assertJsonStructure([
            'data' => $this->jsonStructure,
        ])
        ->assertJsonPath('data.type', 'email')
        ->assertJsonPath('data.id', (string) $email->id)
        ->assertJsonPath('data.attributes.email_type', 'welcome')
        ->assertJsonPath('data.attributes.email_address', 'monica.geller@friends.test')
        ->assertJsonPath('data.attributes.subject', 'Welcome to kollek')
        ->assertJsonPath('data.attributes.body', 'I know!')
        ->assertJsonPath('data.attributes.sent_at', 1751284800)
        ->assertJsonPath('data.attributes.delivered_at', 1751284800)
        ->assertJsonPath('data.attributes.bounced_at', null)
        ->assertJsonPath('data.attributes.created_at', 1751284800)
        ->assertJsonPath('data.attributes.updated_at', 1751284800)
        ->assertJsonPath('data.links.self', route('api.administration.emails.show', $email));
});

it('cannot show another users email', function () {
    $user = User::factory()->create();
    $anotherEmail = EmailSent::factory()->create();

    Sanctum::actingAs($user);

    $response = $this->json('GET', '/api/administration/emails/'.$anotherEmail->id);

    $response->assertNotFound();
});

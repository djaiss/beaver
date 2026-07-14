<?php

declare(strict_types=1);
use App\Models\CollectionType;
use App\Models\CustomField;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;

uses(RefreshDatabase::class);

it('moves a field down', function () {
    Queue::fake();

    $user = $this->createUser();
    $type = CollectionType::factory()->create(['account_id' => $user->account_id]);
    $first = CustomField::factory()->create(['type_id' => $type->id, 'position' => 1]);
    $second = CustomField::factory()->create(['type_id' => $type->id, 'position' => 2]);

    $this->actingAs($user)->put('/types/'.$type->id.'/fields/'.$first->id.'/order', ['direction' => 'down'])
        ->assertRedirect('/types/'.$type->id.'/edit');

    expect($first->refresh()->position)->toBe(2);
    expect($second->refresh()->position)->toBe(1);
});

it('validates the direction', function () {
    $user = $this->createUser();
    $type = CollectionType::factory()->create(['account_id' => $user->account_id]);
    $field = CustomField::factory()->create(['type_id' => $type->id, 'position' => 1]);

    $this->actingAs($user)->put('/types/'.$type->id.'/fields/'.$field->id.'/order', ['direction' => 'sideways'])
        ->assertSessionHasErrors('direction');
});

it('cannot reorder a field of another accounts type', function () {
    Queue::fake();

    $user = $this->createUser();
    $foreignType = CollectionType::factory()->create();
    $field = CustomField::factory()->create(['type_id' => $foreignType->id, 'position' => 1]);

    $this->actingAs($user)->put('/types/'.$foreignType->id.'/fields/'.$field->id.'/order', ['direction' => 'down'])
        ->assertNotFound();
});

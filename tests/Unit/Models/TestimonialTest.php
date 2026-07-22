<?php

declare(strict_types=1);

use App\Enums\TestimonialStatus;
use App\Models\Testimonial;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

uses(RefreshDatabase::class);

it('belongs to a user', function () {
    $testimonial = Testimonial::factory()->create();

    expect($testimonial->user()->exists())->toBeTrue()
        ->and($testimonial->user)->toBeInstanceOf(User::class);
});

it('casts the status to the enum', function () {
    $testimonial = Testimonial::factory()->published()->create();

    expect($testimonial->status)->toBe(TestimonialStatus::Published);
});

it('encrypts the name, link and body at rest', function () {
    $testimonial = Testimonial::factory()->create([
        'name' => 'Phoebe Buffay',
        'link' => 'https://example.com/phoebe',
        'body' => 'Smelly cat, smelly cat, what a catalogue you are.',
    ]);

    $raw = DB::table('testimonials')->where('id', $testimonial->id)->first();

    expect(decrypt($raw->name, false))->toBe('Phoebe Buffay')
        ->and(decrypt($raw->link, false))->toBe('https://example.com/phoebe')
        ->and(decrypt($raw->body, false))->toBe('Smelly cat, smelly cat, what a catalogue you are.');
});

it('only returns published testimonials from the published scope, newest first', function () {
    $older = Testimonial::factory()->published()->create(['published_at' => now()->subDays(5)]);
    $newer = Testimonial::factory()->published()->create(['published_at' => now()->subDay()]);
    Testimonial::factory()->create(['status' => TestimonialStatus::InReview]);
    Testimonial::factory()->rejected()->create();

    $published = Testimonial::query()->published()->get();

    expect($published)->toHaveCount(2)
        ->and($published->first()->is($newer))->toBeTrue()
        ->and($published->last()->is($older))->toBeTrue();
});

it('gives the uppercased first initial of the name', function () {
    $testimonial = Testimonial::factory()->create(['name' => 'joey tribbiani']);

    expect($testimonial->initial())->toBe('J');
});

it('returns the link only when it is a safe http scheme', function () {
    $safe = Testimonial::factory()->create(['link' => 'https://example.com/safe']);
    $sneaky = Testimonial::factory()->create(['link' => 'javascript:alert(1)']);
    $none = Testimonial::factory()->create(['link' => null]);

    expect($safe->safeLink())->toBe('https://example.com/safe')
        ->and($sneaky->safeLink())->toBeNull()
        ->and($none->safeLink())->toBeNull();
});

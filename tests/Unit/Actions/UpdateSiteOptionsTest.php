<?php

declare(strict_types=1);

use App\Actions\UpdateSiteOptions;
use App\Enums\UserActionEnum;
use App\Jobs\LogUserAction;
use App\Models\SiteOption;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;

uses(RefreshDatabase::class);

it('creates the one row the first time it is saved', function () {
    Queue::fake();
    $monica = $this->createUser(['is_instance_administrator' => true]);

    $siteOption = new UpdateSiteOptions(
        user: $monica,
        bannerEnabled: true,
        bannerVersion: 'v0.9',
        bannerUrl: 'https://kollek.test/changelog',
        bannerContent: ['en' => ['text' => 'Custom item types are here.', 'link_label' => 'Read the changelog']],
    )->execute();

    expect($siteOption)->toBeInstanceOf(SiteOption::class)
        ->and(SiteOption::query()->count())->toBe(1)
        ->and($siteOption->fresh()->banner_enabled)->toBeTrue()
        ->and($siteOption->fresh()->banner_content['en']['text'])->toBe('Custom item types are here.');
});

it('updates the existing row rather than adding another', function () {
    Queue::fake();
    $monica = $this->createUser(['is_instance_administrator' => true]);
    $existing = SiteOption::factory()->withBanner()->create();

    new UpdateSiteOptions(
        user: $monica,
        bannerEnabled: false,
        bannerVersion: null,
        bannerUrl: null,
        bannerContent: [],
    )->execute();

    expect(SiteOption::query()->count())->toBe(1)
        ->and($existing->fresh()->banner_enabled)->toBeFalse()
        ->and($existing->fresh()->banner_version)->toBeNull();
});

it('logs the update', function () {
    Queue::fake();
    $monica = $this->createUser(['is_instance_administrator' => true]);

    new UpdateSiteOptions(
        user: $monica,
        bannerEnabled: false,
        bannerVersion: null,
        bannerUrl: null,
        bannerContent: [],
    )->execute();

    Queue::assertPushed(LogUserAction::class, fn (LogUserAction $job): bool => $job->action === UserActionEnum::SiteOptionsUpdate);
});

it('forbids a user who does not administer the instance', function () {
    Queue::fake();
    $rachel = $this->createUser(['is_instance_administrator' => false]);

    expect(fn () => new UpdateSiteOptions(
        user: $rachel,
        bannerEnabled: true,
        bannerVersion: 'v0.9',
        bannerUrl: null,
        bannerContent: ['en' => ['text' => 'Custom item types are here.', 'link_label' => null]],
    )->execute())->toThrow(ModelNotFoundException::class);

    expect(SiteOption::query()->count())->toBe(0);
});

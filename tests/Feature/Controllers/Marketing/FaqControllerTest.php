<?php

declare(strict_types=1);
use App\ViewModels\MarketingFaq;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('renders the faq page', function () {
    config()->set('marketing.show', true);

    $response = $this->get(route('marketing.faq.index'));

    $response
        ->assertOk()
        ->assertSee('Everything worth asking before you trust us with a collection.')
        ->assertSee('The ten-second version')
        ->assertSee('What is the difference between an item and a copy?');
});

it('renders every section of the faq', function () {
    config()->set('marketing.show', true);

    $response = $this->get(route('marketing.faq.index'));

    $response->assertOk();

    foreach (app(MarketingFaq::class)->sections() as $section) {
        $response->assertSee($section['title']);
        $response->assertSee('id="'.$section['id'].'"', false);
    }
});

it('renders every question and its answer', function () {
    config()->set('marketing.show', true);

    $response = $this->get(route('marketing.faq.index'));

    $response->assertOk();

    foreach (app(MarketingFaq::class)->sections() as $section) {
        foreach ($section['items'] as $item) {
            $response->assertSee($item['question']);
            $response->assertSee($item['answer']);
        }
    }
});

it('serves the faq in every locale', function () {
    config()->set('marketing.show', true);

    foreach (config('docs.locales') as $meta) {
        $this->get(url($meta['url'].'/faq'))->assertOk();
    }
});

it('links to the faq from the header and the footer', function () {
    config()->set('marketing.show', true);

    $this->get(route('marketing.index'))
        ->assertOk()
        ->assertSee(route('marketing.faq.index'));
});

it('sends everyone to the login page when the marketing site is off', function () {
    config()->set('marketing.show', false);

    $this->get(route('marketing.faq.index'))->assertRedirect(route('login'));
});

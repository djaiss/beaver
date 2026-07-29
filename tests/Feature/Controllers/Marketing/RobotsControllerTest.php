<?php

declare(strict_types=1);

it('points crawlers at the sitemap when the public site is on', function () {
    config()->set('marketing.show', true);

    $response = $this->get('/robots.txt');

    $response
        ->assertOk()
        ->assertHeader('Content-Type', 'text/plain; charset=UTF-8');

    expect($response->getContent())->toBe(
        "User-agent: *\nAllow: /\n\nSitemap: ".route('marketing.sitemap.index')."\n"
    );
});

it('closes the whole host when the public site is off', function () {
    config()->set('marketing.show', false);

    $response = $this->get('/robots.txt');

    // Deliberately answered rather than redirected to the login page: a private
    // instance still has something to say to a crawler, and a 302 says nothing.
    $response->assertOk();

    expect($response->getContent())->toBe("User-agent: *\nDisallow: /\n");
});

<?php

declare(strict_types=1);
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    config()->set('marketing.show', true);
});

it('renders the about page', function () {
    $this->get(route('marketing.about.index'))
        ->assertOk()
        ->assertSee('Built by a team of seven.')
        ->assertSee('Meet the team')
        ->assertSee('Company facts')
        ->assertSee('The road to KolleK')
        ->assertSee('Why KolleK exists')
        ->assertSee('Our highly sophisticated development process')
        ->assertSee('Currently not on the roadmap');
});

it('lists the whole team and says which member is human', function () {
    $response = $this->get(route('marketing.about.index'))->assertOk();

    foreach (['Régis', 'Claude', 'ChatGPT', 'Gemini', 'Codex', 'Cursor', 'GitHub Copilot'] as $member) {
        $response->assertSee($member, false);
    }

    $response->assertSee('Only one member of this team is human.', false);
});

it('tells the whole story on the timeline', function () {
    $response = $this->get(route('marketing.about.index'))->assertOk();

    foreach (['1981', '1987', '1996', '2004', '2026', 'August 2, 2026'] as $date) {
        $response->assertSee($date, false);
    }

    $response->assertSee('Régis is born.', false)
        ->assertSee('KolleK launches.', false);
});

it('leaves no placeholder copy on the timeline', function () {
    $this->get(route('marketing.about.index'))
        ->assertOk()
        ->assertDontSee('[Birth year]')
        ->assertDontSee('Entries in brackets are placeholders until Régis checks the exact years.', false);
});

it('points the open source panel at the configured repository', function () {
    config()->set('marketing.github_url', 'https://github.com/example/kollek');

    $this->get(route('marketing.about.index'))
        ->assertOk()
        ->assertSee('https://github.com/example/kollek', false);
});

it('serves the about page in every locale', function () {
    foreach (config('docs.locales') as $meta) {
        $this->get(url($meta['url'].'/about'))->assertOk();
    }
});

it('translates the page', function () {
    $this->get('/fr/about')
        ->assertOk()
        ->assertSee('Construit par une équipe de sept.')
        ->assertSee('Rencontrez l\'équipe');
});

it('is linked from the footer', function () {
    $this->get(route('marketing.index'))
        ->assertOk()
        ->assertSee(route('marketing.about.index'));
});

it('sends everyone to the login page when the marketing site is off', function () {
    config()->set('marketing.show', false);

    $this->get(route('marketing.about.index'))->assertRedirect(route('login'));
});

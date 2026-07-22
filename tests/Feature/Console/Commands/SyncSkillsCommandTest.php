<?php

declare(strict_types=1);
use Illuminate\Support\Facades\File;

beforeEach(function () {
    $this->originalBasePath = $this->app->basePath();
    $this->temporaryBasePath = sys_get_temp_dir().'/kollek-sync-skills-'.bin2hex(random_bytes(8));

    File::makeDirectory($this->temporaryBasePath, 0755, true);
    File::ensureDirectoryExists($this->temporaryBasePath.'/scripts');
    File::copy(
        $this->originalBasePath.'/scripts/sync-skills.sh',
        $this->temporaryBasePath.'/scripts/sync-skills.sh',
    );
    $this->app->setBasePath($this->temporaryBasePath);
});
afterEach(function () {
    $this->app->setBasePath($this->originalBasePath);
    File::deleteDirectory($this->temporaryBasePath);

});

it('replaces agent and ai skills with github skills', function () {
    writeFile('.github/skills/actions/SKILL.md', 'actions skill');
    writeFile('.github/skills/actions/references/example.md', 'reference');
    writeFile('.github/skills/controllers/SKILL.md', 'controllers skill');
    writeFile('.agents/skills/stale/SKILL.md', 'stale agent skill');
    writeFile('.ai/skills/stale/SKILL.md', 'stale AI skill');

    $this->artisan('kollek:sync-skills')
        ->expectsOutput('Skills synchronized.')
        ->assertSuccessful();

    foreach (['.agents/skills', '.ai/skills'] as $targetDirectory) {
        $this->assertFileDoesNotExist(base_path($targetDirectory.'/stale/SKILL.md'));
        expect(File::get(base_path($targetDirectory.'/actions/SKILL.md')))->toBe('actions skill');
        expect(File::get(base_path($targetDirectory.'/actions/references/example.md')))->toBe('reference');
        expect(File::get(base_path($targetDirectory.'/controllers/SKILL.md')))->toBe('controllers skill');
    }
});

it('fails without removing existing skills when the source directory is missing', function () {
    writeFile('.agents/skills/existing/SKILL.md', 'agent skill');
    writeFile('.ai/skills/existing/SKILL.md', 'AI skill');

    $this->artisan('kollek:sync-skills')
        ->expectsOutputToContain('The source skills directory was not found')
        ->assertFailed();

    expect(File::get(base_path('.agents/skills/existing/SKILL.md')))->toBe('agent skill');
    expect(File::get(base_path('.ai/skills/existing/SKILL.md')))->toBe('AI skill');
});

it('stages source skills before removing linked target directories', function () {
    writeFile('.agents/skills/tailwindcss-development/SKILL.md', 'tailwind skill');
    File::ensureDirectoryExists(base_path('.github/skills'));
    File::link(
        base_path('.agents/skills/tailwindcss-development'),
        base_path('.github/skills/tailwindcss-development'),
    );

    $this->artisan('kollek:sync-skills')
        ->expectsOutput('Skills synchronized.')
        ->assertSuccessful();

    expect(is_link(base_path('.github/skills/tailwindcss-development')))->toBeTrue();
    expect(File::get(base_path('.github/skills/tailwindcss-development/SKILL.md')))->toBe('tailwind skill');
    expect(File::get(base_path('.agents/skills/tailwindcss-development/SKILL.md')))->toBe('tailwind skill');
    expect(File::get(base_path('.ai/skills/tailwindcss-development/SKILL.md')))->toBe('tailwind skill');
});

it('does not touch targets when another sync is running', function () {
    writeFile('.github/skills/actions/SKILL.md', 'new skill');
    writeFile('.agents/skills/existing/SKILL.md', 'agent skill');
    writeFile('.ai/skills/existing/SKILL.md', 'AI skill');
    File::makeDirectory(base_path('.sync-skills.lock'));

    $this->artisan('kollek:sync-skills')
        ->expectsOutputToContain('Another skills synchronization is already running')
        ->assertFailed();

    expect(File::get(base_path('.agents/skills/existing/SKILL.md')))->toBe('agent skill');
    expect(File::get(base_path('.ai/skills/existing/SKILL.md')))->toBe('AI skill');
});
function writeFile(string $relativePath, string $contents): void
{
    $path = base_path($relativePath);

    File::ensureDirectoryExists(dirname($path));
    File::put($path, $contents);
}

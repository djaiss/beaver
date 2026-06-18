<?php

declare(strict_types=1);

namespace Tests\Feature\Console\Commands;

use Illuminate\Support\Facades\File;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class SyncSkillsCommandTest extends TestCase
{
    private string $originalBasePath;

    private string $temporaryBasePath;

    protected function setUp(): void
    {
        parent::setUp();

        $this->originalBasePath = $this->app->basePath();
        $this->temporaryBasePath = sys_get_temp_dir().'/lifeos-sync-skills-'.bin2hex(random_bytes(8));

        File::makeDirectory($this->temporaryBasePath, 0755, true);
        File::ensureDirectoryExists($this->temporaryBasePath.'/scripts');
        File::copy(
            $this->originalBasePath.'/scripts/sync-skills.sh',
            $this->temporaryBasePath.'/scripts/sync-skills.sh',
        );
        $this->app->setBasePath($this->temporaryBasePath);
    }

    protected function tearDown(): void
    {
        $this->app->setBasePath($this->originalBasePath);
        File::deleteDirectory($this->temporaryBasePath);

        parent::tearDown();
    }

    #[Test]
    public function it_replaces_agent_and_ai_skills_with_github_skills(): void
    {
        $this->writeFile('.github/skills/actions/SKILL.md', 'actions skill');
        $this->writeFile('.github/skills/actions/references/example.md', 'reference');
        $this->writeFile('.github/skills/controllers/SKILL.md', 'controllers skill');
        $this->writeFile('.agents/skills/stale/SKILL.md', 'stale agent skill');
        $this->writeFile('.ai/skills/stale/SKILL.md', 'stale AI skill');

        $this->artisan('lifeos:sync-skills')
            ->expectsOutput('Skills synchronized.')
            ->assertSuccessful();

        foreach (['.agents/skills', '.ai/skills'] as $targetDirectory) {
            $this->assertFileDoesNotExist(base_path($targetDirectory.'/stale/SKILL.md'));
            $this->assertSame('actions skill', File::get(base_path($targetDirectory.'/actions/SKILL.md')));
            $this->assertSame(
                'reference',
                File::get(base_path($targetDirectory.'/actions/references/example.md')),
            );
            $this->assertSame('controllers skill', File::get(base_path($targetDirectory.'/controllers/SKILL.md')));
        }
    }

    #[Test]
    public function it_fails_without_removing_existing_skills_when_the_source_directory_is_missing(): void
    {
        $this->writeFile('.agents/skills/existing/SKILL.md', 'agent skill');
        $this->writeFile('.ai/skills/existing/SKILL.md', 'AI skill');

        $this->artisan('lifeos:sync-skills')
            ->expectsOutputToContain('The source skills directory was not found')
            ->assertFailed();

        $this->assertSame('agent skill', File::get(base_path('.agents/skills/existing/SKILL.md')));
        $this->assertSame('AI skill', File::get(base_path('.ai/skills/existing/SKILL.md')));
    }

    #[Test]
    public function it_stages_source_skills_before_removing_linked_target_directories(): void
    {
        $this->writeFile('.agents/skills/tailwindcss-development/SKILL.md', 'tailwind skill');
        File::ensureDirectoryExists(base_path('.github/skills'));
        File::link(
            base_path('.agents/skills/tailwindcss-development'),
            base_path('.github/skills/tailwindcss-development'),
        );

        $this->artisan('lifeos:sync-skills')
            ->expectsOutput('Skills synchronized.')
            ->assertSuccessful();

        $this->assertTrue(is_link(base_path('.github/skills/tailwindcss-development')));
        $this->assertSame(
            'tailwind skill',
            File::get(base_path('.github/skills/tailwindcss-development/SKILL.md')),
        );
        $this->assertSame(
            'tailwind skill',
            File::get(base_path('.agents/skills/tailwindcss-development/SKILL.md')),
        );
        $this->assertSame(
            'tailwind skill',
            File::get(base_path('.ai/skills/tailwindcss-development/SKILL.md')),
        );
    }

    #[Test]
    public function it_does_not_touch_targets_when_another_sync_is_running(): void
    {
        $this->writeFile('.github/skills/actions/SKILL.md', 'new skill');
        $this->writeFile('.agents/skills/existing/SKILL.md', 'agent skill');
        $this->writeFile('.ai/skills/existing/SKILL.md', 'AI skill');
        File::makeDirectory(base_path('.sync-skills.lock'));

        $this->artisan('lifeos:sync-skills')
            ->expectsOutputToContain('Another skills synchronization is already running')
            ->assertFailed();

        $this->assertSame('agent skill', File::get(base_path('.agents/skills/existing/SKILL.md')));
        $this->assertSame('AI skill', File::get(base_path('.ai/skills/existing/SKILL.md')));
    }

    private function writeFile(string $relativePath, string $contents): void
    {
        $path = base_path($relativePath);

        File::ensureDirectoryExists(dirname($path));
        File::put($path, $contents);
    }
}

<?php

declare(strict_types = 1);

namespace Tests\Unit\Services;

use App\Services\DocNavigationBuilder;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class DocNavigationBuilderTest extends TestCase
{
    private DocNavigationBuilder $builder;

    private string $tmpDir;

    protected function setUp(): void
    {
        parent::setUp();
        $this->builder = new DocNavigationBuilder;
        $temporaryDirectory = sys_get_temp_dir();
        $temporarySuffix = uniqid();
        $this->tmpDir = "{$temporaryDirectory}/doc_nav_test_{$temporarySuffix}";
        mkdir($this->tmpDir, 0o755, true);
    }

    protected function tearDown(): void
    {
        $this->removeDirectory($this->tmpDir);
        parent::tearDown();
    }

    // -------------------------------------------------------------------------
    // toLabel
    // -------------------------------------------------------------------------

    #[Test]
    public function it_converts_kebab_case_to_label(): void
    {
        $this->assertSame('Manage offices', $this->builder->toLabel('manage-offices'));
    }

    #[Test]
    public function it_preserves_uppercase_in_label(): void
    {
        $this->assertSame('API', $this->builder->toLabel('API'));
    }

    #[Test]
    public function it_strips_numeric_prefix_for_label(): void
    {
        $this->assertSame('Getting started', $this->builder->toLabel('01-getting-started'));
    }

    #[Test]
    public function it_strips_file_extension_for_label(): void
    {
        $this->assertSame('Manage', $this->builder->toLabel('01-manage.md'));
    }

    // -------------------------------------------------------------------------
    // stripPrefix
    // -------------------------------------------------------------------------

    #[Test]
    public function it_strips_numeric_prefix(): void
    {
        $this->assertSame('foo', $this->builder->stripPrefix('01-foo'));
    }

    #[Test]
    public function it_handles_name_without_prefix(): void
    {
        $this->assertSame('foo', $this->builder->stripPrefix('foo'));
    }

    #[Test]
    public function it_strips_double_digit_prefix(): void
    {
        $this->assertSame('bar-baz', $this->builder->stripPrefix('10-bar-baz'));
    }

    // -------------------------------------------------------------------------
    // build
    // -------------------------------------------------------------------------

    #[Test]
    public function it_builds_leaf_nodes_from_markdown_files(): void
    {
        touch("{$this->tmpDir}/01-getting-started.md");
        touch("{$this->tmpDir}/02-advanced.md");

        $nav = $this->builder->build('1.x', $this->tmpDir);

        $this->assertCount(2, $nav);
        $this->assertSame('Getting started', $nav[0]['label']);
        $this->assertSame('getting-started', $nav[0]['url']);
        $this->assertEmpty($nav[0]['children']);
    }

    #[Test]
    public function it_builds_section_nodes_from_directories(): void
    {
        mkdir("{$this->tmpDir}/01-offices");
        touch("{$this->tmpDir}/01-offices/01-index.md");
        touch("{$this->tmpDir}/01-offices/02-manage.md");

        $nav = $this->builder->build('1.x', $this->tmpDir);

        $this->assertCount(1, $nav);
        $this->assertSame('Offices', $nav[0]['label']);
        $this->assertNull($nav[0]['url']);
        $this->assertCount(2, $nav[0]['children']);
        $this->assertSame('Index', $nav[0]['children'][0]['label']);
        $this->assertSame('offices/index', $nav[0]['children'][0]['url']);
        $this->assertSame('Manage', $nav[0]['children'][1]['label']);
        $this->assertSame('offices/manage', $nav[0]['children'][1]['url']);
    }

    #[Test]
    public function it_always_sets_url_to_null_for_sections(): void
    {
        mkdir("{$this->tmpDir}/01-group");
        touch("{$this->tmpDir}/01-group/01-index.md");
        touch("{$this->tmpDir}/01-group/02-item.md");

        $nav = $this->builder->build('1.x', $this->tmpDir);

        $this->assertNull($nav[0]['url']);
        $this->assertCount(2, $nav[0]['children']);
    }

    #[Test]
    public function it_sorts_items_by_numeric_prefix(): void
    {
        touch("{$this->tmpDir}/03-third.md");
        touch("{$this->tmpDir}/01-first.md");
        touch("{$this->tmpDir}/02-second.md");

        $nav = $this->builder->build('1.x', $this->tmpDir);

        $this->assertSame('First', $nav[0]['label']);
        $this->assertSame('Second', $nav[1]['label']);
        $this->assertSame('Third', $nav[2]['label']);
    }

    #[Test]
    public function it_skips_files_starting_with_underscore(): void
    {
        touch("{$this->tmpDir}/_hidden.md");
        touch("{$this->tmpDir}/01-visible.md");

        $nav = $this->builder->build('1.x', $this->tmpDir);

        $this->assertCount(1, $nav);
        $this->assertSame('Visible', $nav[0]['label']);
    }

    #[Test]
    public function it_supports_blade_files(): void
    {
        touch("{$this->tmpDir}/01-intro.blade.php");

        $nav = $this->builder->build('1.x', $this->tmpDir);

        $this->assertCount(1, $nav);
        $this->assertSame('Intro', $nav[0]['label']);
        $this->assertSame('intro', $nav[0]['url']);
    }

    // -------------------------------------------------------------------------
    // resolve
    // -------------------------------------------------------------------------

    #[Test]
    public function it_resolves_a_markdown_leaf_file(): void
    {
        mkdir("{$this->tmpDir}/02-offices");
        touch("{$this->tmpDir}/02-offices/02-manage.md");

        $result = $this->builder->resolve('1.x', 'offices/manage', $this->tmpDir);

        $this->assertSame("{$this->tmpDir}/02-offices/02-manage.md", $result);
    }

    #[Test]
    public function it_resolves_a_prefixed_index_file_by_its_slug(): void
    {
        mkdir("{$this->tmpDir}/01-organizations");
        touch("{$this->tmpDir}/01-organizations/01-index.md");

        $result = $this->builder->resolve('1.x', 'organizations/index', $this->tmpDir);

        $this->assertSame("{$this->tmpDir}/01-organizations/01-index.md", $result);
    }

    #[Test]
    public function it_resolves_a_blade_file_by_its_slug(): void
    {
        mkdir("{$this->tmpDir}/04-API");
        touch("{$this->tmpDir}/04-API/01-index.blade.php");

        $result = $this->builder->resolve('1.x', 'api/index', $this->tmpDir);

        $this->assertSame("{$this->tmpDir}/04-API/01-index.blade.php", $result);
    }

    #[Test]
    public function it_returns_null_for_a_directory_path_with_no_file_segment(): void
    {
        mkdir("{$this->tmpDir}/01-organizations");
        touch("{$this->tmpDir}/01-organizations/01-index.md");

        $result = $this->builder->resolve('1.x', 'organizations', $this->tmpDir);

        $this->assertNull($result);
    }

    #[Test]
    public function it_returns_null_for_missing_path(): void
    {
        $result = $this->builder->resolve('1.x', 'nonexistent', $this->tmpDir);

        $this->assertNull($result);
    }

    private function removeDirectory(string $dir): void
    {
        if (!is_dir($dir)) {
            return;
        }

        $files = scandir($dir);

        if ($files !== false) {
            foreach ($files as $entry) {
                if ($entry === '.') {
                    continue;
                }
                if ($entry === '..') {
                    continue;
                }
                $path = "{$dir}/{$entry}";
                is_dir($path) ? $this->removeDirectory($path) : unlink($path);
            }
        }
        rmdir($dir);
    }
}

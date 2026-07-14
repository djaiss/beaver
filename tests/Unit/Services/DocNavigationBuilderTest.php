<?php

declare(strict_types=1);
use App\Services\DocNavigationBuilder;

beforeEach(function () {
    $this->builder = new DocNavigationBuilder;
    $this->tmpDir = sys_get_temp_dir().'/doc_nav_test_'.uniqid();
    mkdir($this->tmpDir, 0755, true);
});
afterEach(function () {
    removeDirectory($this->tmpDir);
});
it('converts kebab case to label', function () {
    expect($this->builder->toLabel('manage-offices'))->toBe('Manage offices');
});
it('preserves uppercase in label', function () {
    expect($this->builder->toLabel('API'))->toBe('API');
});
it('strips numeric prefix for label', function () {
    expect($this->builder->toLabel('01-getting-started'))->toBe('Getting started');
});
it('strips file extension for label', function () {
    expect($this->builder->toLabel('01-manage.md'))->toBe('Manage');
});
it('strips numeric prefix', function () {
    expect($this->builder->stripPrefix('01-foo'))->toBe('foo');
});
it('handles name without prefix', function () {
    expect($this->builder->stripPrefix('foo'))->toBe('foo');
});
it('strips double digit prefix', function () {
    expect($this->builder->stripPrefix('10-bar-baz'))->toBe('bar-baz');
});
it('builds leaf nodes from markdown files', function () {
    touch($this->tmpDir.'/01-getting-started.md');
    touch($this->tmpDir.'/02-advanced.md');

    $nav = $this->builder->build('1.x', $this->tmpDir);

    expect($nav)->toHaveCount(2);
    expect($nav[0]['label'])->toBe('Getting started');
    expect($nav[0]['url'])->toBe('getting-started');
    expect($nav[0]['children'])->toBeEmpty();
});
it('builds section nodes from directories', function () {
    mkdir($this->tmpDir.'/01-offices');
    touch($this->tmpDir.'/01-offices/01-index.md');
    touch($this->tmpDir.'/01-offices/02-manage.md');

    $nav = $this->builder->build('1.x', $this->tmpDir);

    expect($nav)->toHaveCount(1);
    expect($nav[0]['label'])->toBe('Offices');
    expect($nav[0]['url'])->toBeNull();
    expect($nav[0]['children'])->toHaveCount(2);
    expect($nav[0]['children'][0]['label'])->toBe('Index');
    expect($nav[0]['children'][0]['url'])->toBe('offices/index');
    expect($nav[0]['children'][1]['label'])->toBe('Manage');
    expect($nav[0]['children'][1]['url'])->toBe('offices/manage');
});
it('always sets url to null for sections', function () {
    mkdir($this->tmpDir.'/01-group');
    touch($this->tmpDir.'/01-group/01-index.md');
    touch($this->tmpDir.'/01-group/02-item.md');

    $nav = $this->builder->build('1.x', $this->tmpDir);

    expect($nav[0]['url'])->toBeNull();
    expect($nav[0]['children'])->toHaveCount(2);
});
it('sorts items by numeric prefix', function () {
    touch($this->tmpDir.'/03-third.md');
    touch($this->tmpDir.'/01-first.md');
    touch($this->tmpDir.'/02-second.md');

    $nav = $this->builder->build('1.x', $this->tmpDir);

    expect($nav[0]['label'])->toBe('First');
    expect($nav[1]['label'])->toBe('Second');
    expect($nav[2]['label'])->toBe('Third');
});
it('skips files starting with underscore', function () {
    touch($this->tmpDir.'/_hidden.md');
    touch($this->tmpDir.'/01-visible.md');

    $nav = $this->builder->build('1.x', $this->tmpDir);

    expect($nav)->toHaveCount(1);
    expect($nav[0]['label'])->toBe('Visible');
});
it('supports blade files', function () {
    touch($this->tmpDir.'/01-intro.blade.php');

    $nav = $this->builder->build('1.x', $this->tmpDir);

    expect($nav)->toHaveCount(1);
    expect($nav[0]['label'])->toBe('Intro');
    expect($nav[0]['url'])->toBe('intro');
});
it('resolves a markdown leaf file', function () {
    mkdir($this->tmpDir.'/02-offices');
    touch($this->tmpDir.'/02-offices/02-manage.md');

    $result = $this->builder->resolve('1.x', 'offices/manage', $this->tmpDir);

    expect($result)->toBe($this->tmpDir.'/02-offices/02-manage.md');
});
it('resolves a prefixed index file by its slug', function () {
    mkdir($this->tmpDir.'/01-organizations');
    touch($this->tmpDir.'/01-organizations/01-index.md');

    $result = $this->builder->resolve('1.x', 'organizations/index', $this->tmpDir);

    expect($result)->toBe($this->tmpDir.'/01-organizations/01-index.md');
});
it('resolves a blade file by its slug', function () {
    mkdir($this->tmpDir.'/04-API');
    touch($this->tmpDir.'/04-API/01-index.blade.php');

    $result = $this->builder->resolve('1.x', 'api/index', $this->tmpDir);

    expect($result)->toBe($this->tmpDir.'/04-API/01-index.blade.php');
});
it('returns null for a directory path with no file segment', function () {
    mkdir($this->tmpDir.'/01-organizations');
    touch($this->tmpDir.'/01-organizations/01-index.md');

    $result = $this->builder->resolve('1.x', 'organizations', $this->tmpDir);

    expect($result)->toBeNull();
});
it('returns null for missing path', function () {
    $result = $this->builder->resolve('1.x', 'nonexistent', $this->tmpDir);

    expect($result)->toBeNull();
});
function removeDirectory(string $dir): void
{
    if (! is_dir($dir)) {
        return;
    }
    foreach (scandir($dir) ?: [] as $entry) {
        if ($entry === '.') {
            continue;
        }
        if ($entry === '..') {
            continue;
        }
        $path = $dir.'/'.$entry;
        is_dir($path) ? removeDirectory($path) : unlink($path);
    }
    rmdir($dir);
}

<?php

declare(strict_types=1);

namespace App\Services;

class DocNavigationBuilder
{
    public function build(string $version, ?string $basePath = null): array
    {
        $dir = $basePath ?? resource_path("views/marketing/docs/{$version}");

        return $this->scanDirectory($dir, '');
    }

    public function resolve(string $version, string $urlPath, ?string $basePath = null): ?string
    {
        $dir = $basePath ?? resource_path("views/marketing/docs/{$version}");
        $segments = $urlPath !== '' ? explode('/', $urlPath) : [];

        return $this->resolveSegments($dir, $segments);
    }

    public function toLabel(string $name): string
    {
        $bare = (string) preg_replace('/\.(blade\.php|md)$/', '', $name);
        $stripped = $this->stripPrefix($bare);

        return ucfirst(str_replace('-', ' ', $stripped));
    }

    public function stripPrefix(string $name): string
    {
        return (string) preg_replace('/^\d+-/', '', $name);
    }

    private function scanDirectory(string $dir, string $urlPrefix): array
    {
        $items = [];

        foreach ($this->getSortedEntries($dir) as $entry) {
            $fullPath = "{$dir}/{$entry}";

            if (is_dir($fullPath)) {
                $slug = strtolower($this->stripPrefix($entry));
                $urlPath = $urlPrefix !== '' ? "{$urlPrefix}/{$slug}" : $slug;

                $items[] = [
                    'label' => $this->toLabel($entry),
                    'url' => null,
                    'children' => $this->scanDirectory($fullPath, $urlPath),
                ];

                continue;
            }

            if ($this->isDocFile($entry)) {
                $slug = $this->toSlug($entry);
                $urlPath = $urlPrefix !== '' ? "{$urlPrefix}/{$slug}" : $slug;

                $items[] = [
                    'label' => $this->toLabel($entry),
                    'url' => $urlPath,
                    'children' => [],
                ];
            }
        }

        return $items;
    }

    private function resolveSegments(string $currentDir, array $segments): ?string
    {
        if ($segments === []) {
            return null;
        }

        $segment = array_shift($segments);

        foreach ($this->getSortedEntries($currentDir) as $entry) {
            $fullPath = "{$currentDir}/{$entry}";

            if (is_dir($fullPath)) {
                if (strtolower($this->stripPrefix($entry)) === $segment) {
                    return $this->resolveSegments($fullPath, $segments);
                }

                continue;
            }

            if ($segments === [] && $this->isDocFile($entry) && $this->toSlug($entry) === $segment) {
                return $fullPath;
            }
        }

        return null;
    }

    private function toSlug(string $name): string
    {
        $bare = (string) preg_replace('/\.(blade\.php|md)$/', '', $name);

        return strtolower($this->stripPrefix($bare));
    }

    private function getSortedEntries(string $dir): array
    {
        if (! is_dir($dir)) {
            return [];
        }

        $entries = array_values(array_filter(
            scandir($dir),
            fn (string $e): bool => $e !== '.' && $e !== '..' && ! str_starts_with($e, '_'),
        ));

        usort($entries, function (string $a, string $b): int {
            $aNum = $this->extractPrefix($a);
            $bNum = $this->extractPrefix($b);

            if ($aNum !== $bNum) {
                return $aNum <=> $bNum;
            }

            return strcmp($a, $b);
        });

        return $entries;
    }

    private function extractPrefix(string $name): int
    {
        if (preg_match('/^(\d+)-/', $name, $matches)) {
            return (int) $matches[1];
        }

        return PHP_INT_MAX;
    }

    private function isDocFile(string $entry): bool
    {
        return str_ends_with($entry, '.md') || str_ends_with($entry, '.blade.php');
    }
}

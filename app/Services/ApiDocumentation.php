<?php

declare(strict_types=1);

namespace App\Services;

/**
 * Builds the API reference shown on the docs portal from the definition
 * files in resources/docs/api. Each file describes one sidebar group; the
 * service turns its sections into render-ready arrays with code samples,
 * highlighted JSON and a markdown version of every section.
 */
class ApiDocumentation
{
    /** @var list<array<string, mixed>>|null */
    private ?array $groups = null;

    public static function baseUrl(): string
    {
        return rtrim((string) config('app.url'), '/').'/api';
    }

    /**
     * Build a realistic paginated response body around a list of resources,
     * for use in the definition files.
     *
     * @param  list<array<string, mixed>>  $data
     * @return array<string, mixed>
     */
    public static function paginated(array $data, string $path): array
    {
        $url = self::baseUrl().$path;
        $total = count($data);

        return [
            'data' => $data,
            'links' => [
                'first' => $url.'?page=1',
                'last' => $url.'?page=1',
                'prev' => null,
                'next' => null,
            ],
            'meta' => [
                'current_page' => 1,
                'from' => 1,
                'last_page' => 1,
                'links' => [
                    ['url' => null, 'label' => '&laquo; Previous', 'active' => false],
                    ['url' => $url.'?page=1', 'label' => '1', 'active' => true],
                    ['url' => null, 'label' => 'Next &raquo;', 'active' => false],
                ],
                'path' => $url,
                'per_page' => 10,
                'to' => $total,
                'total' => $total,
            ],
        ];
    }

    /**
     * Get the sidebar groups with their render-ready sections.
     *
     * @return list<array<string, mixed>>
     */
    public function groups(): array
    {
        if ($this->groups !== null) {
            return $this->groups;
        }

        $files = glob(resource_path('docs/api').'/*.php') ?: [];
        sort($files);

        $this->groups = array_map(function (string $file): array {
            $group = require $file;

            $group['guide'] ??= false;
            $group['sections'] = array_map(
                fn (array $section): array => $this->buildSection($section),
                $group['sections'],
            );

            return $group;
        }, $files);

        return $this->groups;
    }

    /**
     * Get every section of the reference, in display order.
     *
     * @return list<array<string, mixed>>
     */
    public function sections(): array
    {
        return collect($this->groups())
            ->flatMap(fn (array $group): array => $group['sections'])
            ->values()
            ->all();
    }

    /**
     * @return array<string, mixed>|null
     */
    public function section(string $id): ?array
    {
        return collect($this->sections())->firstWhere('id', $id);
    }

    /**
     * Get the endpoints documented as real API routes. Guide sections show
     * illustrative requests and are not part of this list.
     *
     * @return list<array{method: string, path: string}>
     */
    public function documentedRoutes(): array
    {
        return collect($this->groups())
            ->reject(fn (array $group): bool => $group['guide'])
            ->flatMap(fn (array $group): array => $group['sections'])
            ->map(fn (array $section): array => [
                'method' => $section['method'],
                'path' => $section['path'],
            ])
            ->values()
            ->all();
    }

    /**
     * Get the sidebar structure: guide links and resource groups.
     *
     * @return array{guides: list<array{id: string, label: string}>, resources: list<array{name: string, items: list<array{id: string, label: string, method: string}>}>}
     */
    public function navigation(): array
    {
        $guides = [];
        $resources = [];

        foreach ($this->groups() as $group) {
            if ($group['guide']) {
                foreach ($group['sections'] as $section) {
                    $guides[] = ['id' => $section['id'], 'label' => $section['label']];
                }

                continue;
            }

            $resources[] = [
                'name' => $group['name'],
                'items' => collect($group['sections'])->map(fn (array $section): array => [
                    'id' => $section['id'],
                    'label' => $section['label'],
                    'method' => $section['method'],
                ])->all(),
            ];
        }

        return ['guides' => $guides, 'resources' => $resources];
    }

    /**
     * Get the whole reference as one markdown document.
     */
    public function markdown(): string
    {
        $blocks = [
            '# '.config('app.name').' API reference',
            'Base URL: `'.self::baseUrl().'`',
        ];

        foreach ($this->groups() as $group) {
            $blocks[] = '# '.$group['name'];

            foreach ($group['sections'] as $section) {
                $blocks[] = $section['markdown'];
            }
        }

        return implode("\n\n", $blocks)."\n";
    }

    /**
     * @param  array<string, mixed>  $section
     * @return array<string, mixed>
     */
    private function buildSection(array $section): array
    {
        $section['label'] ??= $section['title'];
        $section['kicker'] ??= 'V1';
        $section['auth'] ??= true;
        $section['examplePath'] ??= $section['path'];
        $section['body'] ??= [];
        $section['permissions'] ??= null;
        $section['pathParams'] ??= [];
        $section['queryParams'] ??= [];
        $section['bodyParams'] ??= [];
        $section['returns'] ??= null;
        $section['responseStatus'] ??= 200;
        $section['response'] ??= null;

        $body = $section['exampleBody'] ?? $this->exampleBody($section['bodyParams']);

        $samples = [
            'curl' => ['label' => 'cURL', 'code' => $this->curlSample($section, $body)],
            'javascript' => ['label' => 'JavaScript', 'code' => $this->javascriptSample($section, $body)],
            'php' => ['label' => 'PHP', 'code' => $this->phpSample($section, $body)],
        ];

        $section['samples'] = array_map(fn (array $sample): array => [
            'label' => $sample['label'],
            'code' => $sample['code'],
            'html' => $this->highlightCode($sample['code']),
        ], $samples);

        $section['responseJson'] = $section['response'] === null ? null : $this->prettyJson($section['response']);
        $section['responseHtml'] = $section['responseJson'] === null ? null : $this->highlightJson($section['responseJson']);
        $section['markdown'] = $this->sectionMarkdown($section, $samples['curl']['code']);

        return $section;
    }

    /**
     * Build the request body used in code samples from the body parameters
     * that define an example value.
     *
     * @param  list<array<string, mixed>>  $bodyParams
     * @return array<string, mixed>
     */
    private function exampleBody(array $bodyParams): array
    {
        $body = [];

        foreach ($bodyParams as $param) {
            if (array_key_exists('example', $param)) {
                $body[$param['name']] = $param['example'];
            }
        }

        return $body;
    }

    /**
     * @param  array<string, mixed>  $section
     * @param  array<string, mixed>  $body
     */
    private function curlSample(array $section, array $body): string
    {
        $lines = ['curl '.self::baseUrl().$section['examplePath']];

        if ($section['method'] !== 'GET') {
            $lines[] = '-X '.$section['method'];
        }

        if ($section['auth']) {
            $lines[] = '-H "Authorization: Bearer $API_KEY"';
        }

        $lines[] = '-H "Accept: application/json"';

        if ($body !== []) {
            $lines[] = '-H "Content-Type: application/json"';
            $lines[] = "-d '".$this->prettyJson($body)."'";
        }

        return implode(" \\\n  ", $lines);
    }

    /**
     * @param  array<string, mixed>  $section
     * @param  array<string, mixed>  $body
     */
    private function javascriptSample(array $section, array $body): string
    {
        $headers = [];

        if ($section['auth']) {
            $headers[] = '    "Authorization": "Bearer $API_KEY",';
        }

        $headers[] = '    "Accept": "application/json",';

        if ($body !== []) {
            $headers[] = '    "Content-Type": "application/json",';
        }

        $lines = [
            'const response = await fetch("'.self::baseUrl().$section['examplePath'].'", {',
            '  method: "'.$section['method'].'",',
            '  headers: {',
            ...$headers,
            '  },',
        ];

        if ($body !== []) {
            $json = str_replace("\n", "\n  ", $this->prettyJson($body));
            $lines[] = '  body: JSON.stringify('.$json.'),';
        }

        $lines[] = '});';

        if ($section['responseStatus'] !== 204) {
            $lines[] = '';
            $lines[] = 'const json = await response.json();';
        }

        return implode("\n", $lines);
    }

    /**
     * @param  array<string, mixed>  $section
     * @param  array<string, mixed>  $body
     */
    private function phpSample(array $section, array $body): string
    {
        $client = $section['auth'] ? 'Http::withToken($apiKey)' : 'Http::acceptJson()';
        $method = strtolower($section['method']);
        $url = "'".self::baseUrl().$section['examplePath']."'";

        $lines = [
            'use Illuminate\Support\Facades\Http;',
            '',
            '$response = '.$client,
        ];

        if ($section['auth']) {
            $lines[] = '    ->acceptJson()';
        }

        if ($body === []) {
            $lines[] = '    ->'.$method.'('.$url.');';
        } else {
            $lines[] = '    ->'.$method.'('.$url.', [';

            foreach ($body as $key => $value) {
                $lines[] = "        '".$key."' => ".$this->phpValue($value).',';
            }

            $lines[] = '    ]);';
        }

        if ($section['responseStatus'] !== 204) {
            $lines[] = '';
            $lines[] = '$json = $response->json();';
        }

        return implode("\n", $lines);
    }

    private function phpValue(mixed $value): string
    {
        if (is_string($value)) {
            return "'".$value."'";
        }

        if (is_bool($value)) {
            return $value ? 'true' : 'false';
        }

        if ($value === null) {
            return 'null';
        }

        if (is_array($value)) {
            return '['.implode(', ', array_map(fn (mixed $item): string => $this->phpValue($item), $value)).']';
        }

        return (string) $value;
    }

    private function prettyJson(mixed $value): string
    {
        return (string) json_encode($value, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }

    /**
     * Escape a code sample and wrap quoted strings in a highlight span.
     */
    private function highlightCode(string $code): string
    {
        $lines = array_map(function (string $line): string {
            $escaped = e($line);

            return (string) preg_replace(
                '/&quot;.*?&quot;|&#039;.*?&#039;/',
                '<span class="text-blue-600">$0</span>',
                $escaped,
            );
        }, explode("\n", $code));

        return implode("\n", $lines);
    }

    /**
     * Escape a pretty-printed JSON document and wrap keys and string values
     * in highlight spans.
     */
    private function highlightJson(string $json): string
    {
        $lines = array_map(function (string $line): string {
            $escaped = e($line);

            if (preg_match('/^(\s*)(&quot;[^;]*?&quot;)(\s*:\s*)(.*)$/', $escaped, $matches) === 1) {
                $value = (string) preg_replace(
                    '/&quot;.*?&quot;/',
                    '<span class="text-blue-600">$0</span>',
                    $matches[4],
                );

                return $matches[1].'<span class="font-medium text-gray-900">'.$matches[2].'</span>'.$matches[3].$value;
            }

            return (string) preg_replace(
                '/&quot;.*?&quot;/',
                '<span class="text-blue-600">$0</span>',
                $escaped,
            );
        }, explode("\n", $json));

        return implode("\n", $lines);
    }

    /**
     * @param  array<string, mixed>  $section
     */
    private function sectionMarkdown(array $section, string $curl): string
    {
        $lines = [
            '## '.$section['title'],
            '',
            '`'.$section['method'].' '.self::baseUrl().$section['path'].'`',
            '',
            $section['description'],
        ];

        foreach ($section['body'] as $paragraph) {
            $lines[] = '';
            $lines[] = $paragraph;
        }

        $lines[] = '';

        if ($section['permissions'] !== null) {
            $lines[] = '**Permissions:** '.$section['permissions'];
            $lines[] = '';
        }

        $groups = [
            'Path parameters' => 'pathParams',
            'Query parameters' => 'queryParams',
            'Body parameters' => 'bodyParams',
        ];

        foreach ($groups as $title => $key) {
            if ($section[$key] === []) {
                continue;
            }

            $lines[] = '### '.$title;
            $lines[] = '';

            foreach ($section[$key] as $param) {
                $line = '- `'.$param['name'].'` ('.$param['type'].', '.($param['required'] ? 'required' : 'optional').'): '.$param['description'];

                if (isset($param['default'])) {
                    $line .= ' Default: `'.$param['default'].'`.';
                }

                $lines[] = $line;
            }

            $lines[] = '';
        }

        if ($section['returns'] !== null) {
            $lines[] = '**Returns:** '.$section['returns'];
            $lines[] = '';
        }

        $lines[] = '### Example request';
        $lines[] = '';
        $lines[] = '```bash';
        $lines[] = $curl;
        $lines[] = '```';
        $lines[] = '';
        $lines[] = '### Example response';
        $lines[] = '';
        $lines[] = '`Status: '.$section['responseStatus'].'`';
        $lines[] = '';

        if ($section['response'] === null) {
            $lines[] = 'The response has no body.';
        } else {
            $lines[] = '```json';
            $lines[] = $this->prettyJson($section['response']);
            $lines[] = '```';
        }

        return implode("\n", $lines);
    }
}

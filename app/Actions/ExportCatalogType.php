<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\CatalogType;
use App\Models\CustomField;
use App\Models\CustomFieldGroup;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Database\Eloquent\ModelNotFoundException;

/**
 * Build a portable representation of a collection type: its groups, its fields
 * and their options. Structure only, never the items themselves. Only owners
 * and editors of its account may do so.
 */
class ExportCatalogType
{
    /**
     * Bumped whenever the shape below changes, so whoever reads the file can
     * tell which version of the format they are looking at.
     */
    public const int SCHEMA_VERSION = 1;

    public function __construct(
        private readonly User $user,
        private readonly CatalogType $catalogType,
    ) {}

    /**
     * @return array{schemaVersion: int, type: array{name: string, color: string, groups: list<array<string, mixed>>, standaloneFields: list<array<string, mixed>>}}
     */
    public function execute(): array
    {
        $this->validate();

        return $this->schema();
    }

    private function validate(): void
    {
        if (! $this->catalogType->account->allowsManagementBy($this->user)) {
            throw new ModelNotFoundException('Account not found');
        }
    }

    /**
     * The ordering is what makes an export reproducible, so it is applied here
     * rather than relying on whatever the caller happened to eager load.
     *
     * @return array{schemaVersion: int, type: array{name: string, color: string, groups: list<array<string, mixed>>, standaloneFields: list<array<string, mixed>>}}
     */
    private function schema(): array
    {
        $groups = $this->catalogType->customFieldGroups()
            ->with(['customFields' => fn ($query) => $query->orderBy('position')->orderBy('id')])
            ->orderBy('position')
            ->orderBy('id')
            ->get();

        $standaloneFields = $this->catalogType->ungroupedCustomFields()
            ->orderBy('position')
            ->orderBy('id')
            ->get();

        return [
            'schemaVersion' => self::SCHEMA_VERSION,
            'type' => [
                'name' => $this->catalogType->name,
                'color' => $this->catalogType->color,
                'groups' => $groups
                    ->map(fn (CustomFieldGroup $group): array => [
                        'name' => $group->name,
                        'fields' => $this->fields($group->customFields),
                    ])
                    ->values()
                    ->all(),
                'standaloneFields' => $this->fields($standaloneFields),
            ],
        ];
    }

    /**
     * @param  EloquentCollection<int, CustomField>  $fields
     * @return list<array<string, mixed>>
     */
    private function fields(EloquentCollection $fields): array
    {
        return $fields
            ->map(function (CustomField $field): array {
                $entry = [
                    'name' => $field->name,
                    'type' => $field->field_type->value,
                ];

                // Only select fields carry options, and even those start empty.
                if ($field->options !== null && $field->options !== []) {
                    $entry['options'] = array_values($field->options);
                }

                return $entry;
            })
            ->values()
            ->all();
    }
}

<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\DocumentType;
use App\Models\Account;
use App\Models\Copy;
use App\Models\Document;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Document>
 */
class DocumentFactory extends Factory
{
    protected $model = Document::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            // documentable_id resolves before account_id below, so the account is
            // read off the copy the document defaults to hanging from. A caller
            // attaching the document to another record passes its own account_id.
            'documentable_type' => 'copy',
            'documentable_id' => Copy::factory(),
            'account_id' => fn (array $attributes) => Copy::find($attributes['documentable_id'])?->item?->collection?->account_id
                ?? Account::factory(),
            'type' => DocumentType::Receipt,
            'name' => fake()->words(3, true),
            'path' => 'documents/1/'.fake()->uuid().'.pdf',
            'external_url' => null,
            'mime_type' => 'application/pdf',
            'size' => fake()->numberBetween(1024, 5_000_000),
            'description' => null,
            'issued_at' => fake()->date(),
            'reference_number' => null,
        ];
    }

    /**
     * A document that links to a file held elsewhere rather than storing one.
     */
    public function external(): static
    {
        return $this->state(fn (): array => [
            'path' => null,
            'external_url' => fake()->url(),
            'mime_type' => null,
            'size' => null,
        ]);
    }
}

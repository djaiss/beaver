<?php

declare(strict_types=1);

namespace App\Providers;

use App\Models\Copy;
use App\Models\InsuranceRecord;
use App\Models\Loan;
use App\Models\MaintenanceRecord;
use App\Models\ProvenanceEvent;
use App\Models\Transaction;
use App\Models\Valuation;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        $this->registerAuthorMacro();
        $this->registerMorphMap();
    }

    /**
     * Pins the polymorphic type strings that documents store, so a renamed or
     * moved model never changes what is written in documentable_type, and a raw
     * class name never leaks into the database or the API.
     *
     * This registers the map without enforcing it globally: the framework's own
     * morphs (Sanctum tokens are tokenable to a User) stay on their class names,
     * and only the documentable models resolve to these stable aliases.
     */
    private function registerMorphMap(): void
    {
        Relation::morphMap([
            'copy' => Copy::class,
            'transaction' => Transaction::class,
            'provenance_event' => ProvenanceEvent::class,
            'valuation' => Valuation::class,
            'insurance_record' => InsuranceRecord::class,
            'maintenance_record' => MaintenanceRecord::class,
            'loan' => Loan::class,
        ]);
    }

    /**
     * Adds a `author()` blueprint helper that stamps the standard authorship
     * columns (creator and last editor) on a table.
     */
    private function registerAuthorMacro(): void
    {
        Blueprint::macro('author', function (): void {
            /** @var Blueprint $this */
            $this->foreignId('created_by_id')->nullable()->constrained('users')->nullOnDelete();
            $this->text('created_by_name')->nullable();
            $this->foreignId('updated_by_id')->nullable()->constrained('users')->nullOnDelete();
            $this->text('updated_by_name')->nullable();
        });
    }
}

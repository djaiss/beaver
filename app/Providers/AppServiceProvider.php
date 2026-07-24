<?php

declare(strict_types=1);

namespace App\Providers;

use App\Models\Catalog;
use App\Models\Category;
use App\Models\Copy;
use App\Models\Document;
use App\Models\InsuranceRecord;
use App\Models\Item;
use App\Models\ItemPhoto;
use App\Models\Loan;
use App\Models\Location;
use App\Models\MaintenanceRecord;
use App\Models\ProvenanceEvent;
use App\Models\Series;
use App\Models\Set;
use App\Models\Tag;
use App\Models\Transaction;
use App\Models\Valuation;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        $this->registerAuthorMacro();
        $this->registerMorphMap();
        $this->registerDefaultUrlLocale();
    }

    /**
     * Every public URL carries a language prefix (getkollek.com/en/...), so the
     * {locale} route parameter needs a value even when a URL is generated
     * outside a localized request, such as a "back to the site" link on an auth
     * page. Default it to the default locale's prefix; the marketing locale
     * middleware overrides it per request to the locale actually being viewed.
     */
    private function registerDefaultUrlLocale(): void
    {
        $default = config('docs.default_locale');

        URL::defaults(['locale' => config("docs.locales.{$default}.url", $default)]);
    }

    /**
     * Pins the polymorphic type strings that documents and search tokens store,
     * so a renamed or moved model never changes what is written in
     * documentable_type or searchable_type, and a raw class name never leaks
     * into the database or the API.
     *
     * This registers the map without enforcing it globally: the framework's own
     * morphs (Sanctum tokens are tokenable to a User) stay on their class names,
     * and only these models resolve to the stable aliases.
     *
     * A Catalog is written as `collection`: the alias leaves the codebase, and
     * a collection is what a user calls it.
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
            'collection' => Catalog::class,
            'item' => Item::class,
            'photo' => ItemPhoto::class,
            'location' => Location::class,
            'set' => Set::class,
            'series' => Series::class,
            'category' => Category::class,
            'tag' => Tag::class,
            'document' => Document::class,
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

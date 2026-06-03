<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('countries', function (Blueprint $table): void {
            $table->id()->comment('primary key');
            $table->string('name', 100)->comment('country name');
            $table->string('iso2', 2)->unique()->comment('ISO 3166-1 alpha-2 country code');
            $table->string('iso3', 3)->comment('ISO 3166-1 alpha-3 country code');
            $table->string('phone_code', 10)->comment('international dialing code');
            $table->string('currency_code', 3)->comment('ISO 4217 currency code');
            $table->string('timezone_default', 100)->nullable()->comment('default timezone for the country');
            $table->boolean('is_active')->default(true)->comment('whether the country is active');
            $table->timestamps();
        });

        $json = File::get(database_path('data/countries.json'));
        $countries = json_decode($json, true);

        DB::table('countries')->upsert(
            $countries,
            ['iso2'], // unique key
            ['name', 'iso3', 'phone_code', 'currency_code', 'timezone_default', 'is_active'],
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('countries');
    }
};

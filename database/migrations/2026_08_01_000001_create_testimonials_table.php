<?php

declare(strict_types=1);

use App\Enums\TestimonialStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('testimonials', function (Blueprint $table): void {
            $table->id()->comment('primary key');
            // One testimonial per user; deleting the user removes it with them.
            $table->unsignedBigInteger('user_id')->unique()->comment('the member who wrote the testimonial');
            $table->text('name')->comment('public display name, encrypted');
            $table->text('link')->nullable()->comment('optional URL that makes the name clickable, encrypted');
            $table->text('body')->comment('the testimony itself, encrypted');
            $table->string('status', 20)->default(TestimonialStatus::InReview->value)->comment('a TestimonialStatus value');
            $table->timestamp('submitted_at')->nullable()->comment('when the member last submitted it for review');
            $table->timestamp('published_at')->nullable()->comment('null until an instance administrator publishes it');
            $table->timestamps();

            // The homepage lists the most recently published testimonials.
            $table->index(['status', 'published_at']);

            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('testimonials');
    }
};

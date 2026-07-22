<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\TestimonialStatus;
use App\Models\Testimonial;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Date;

class TestimonialSeeder extends Seeder
{
    /**
     * Seed a spread of testimonials across every status so the marketing wall has
     * something to show and the instance-admin moderation buckets are all worth
     * opening. Each testimonial belongs to its own user, since a user has at most
     * one.
     */
    public function run(): void
    {
        $seed = [
            ['name' => 'Marion Delacroix', 'link' => 'https://example.com/marion', 'status' => TestimonialStatus::Published,
                'body' => 'I moved 900 comics off a decade-old spreadsheet in an afternoon. Nested locations mean I finally know which box issue #300 is actually in.'],
            ['name' => 'A quiet vinyl hoarder', 'link' => null, 'status' => TestimonialStatus::Published,
                'body' => 'Custom item types sold me. Pressing, matrix number, sleeve grade: my LPs and my wine no longer share one dumb form.'],
            ['name' => 'Tomás Herrera', 'link' => 'https://example.com/tomas', 'status' => TestimonialStatus::Published,
                'body' => 'Self-hosted on a Raspberry Pi in one command. It is mine, offline, forever.'],
            ['name' => 'gridironkate', 'link' => 'https://example.com/kate', 'status' => TestimonialStatus::Published,
                'body' => 'Tracking each physical copy separately, condition, what I paid, what it is worth now, turned my card binder into something I can actually insure.'],
            ['name' => 'Priya Natarajan', 'link' => 'https://example.com/priya', 'status' => TestimonialStatus::Published,
                'body' => 'The export is real. I dumped everything to JSON, poked at it, imported it back clean. That is what data ownership is supposed to feel like.'],
            ['name' => 'Someone in the Discord', 'link' => null, 'status' => TestimonialStatus::Published,
                'body' => 'No subscription, no tracking, no nonsense. Paid once and never thought about it again.'],
            ['name' => 'Bev Okonkwo', 'link' => 'https://example.com/bev', 'status' => TestimonialStatus::InReview,
                'body' => 'The value-over-time chart is weirdly addictive. I check it more than I probably should.'],
            ['name' => 'FREE CRYPTO GIVEAWAY', 'link' => 'https://example.com/spam', 'status' => TestimonialStatus::InReview,
                'body' => 'Amazing app!!! Also message me to grow your portfolio, guaranteed returns, act now!!!'],
            ['name' => 'Ancien collectionneur', 'link' => null, 'status' => TestimonialStatus::Rejected,
                'body' => "Application formidable, je l'utilise tous les jours pour ma collection de timbres anciens et rares."],
            ['name' => 'Sara Okafor', 'link' => null, 'status' => TestimonialStatus::Draft,
                'body' => 'Still figuring out how to say how much I love the loan tracking. More soon.'],
        ];

        foreach ($seed as $entry) {
            $user = User::factory()->create();

            Testimonial::factory()
                ->for($user)
                ->create([
                    'name' => $entry['name'],
                    'link' => $entry['link'],
                    'body' => $entry['body'],
                    'status' => $entry['status'],
                    'submitted_at' => $entry['status'] === TestimonialStatus::Draft ? null : Date::now()->subDays(random_int(1, 60)),
                    'published_at' => $entry['status'] === TestimonialStatus::Published ? Date::now()->subDays(random_int(1, 45)) : null,
                ]);
        }
    }
}

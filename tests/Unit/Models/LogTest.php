<?php

declare(strict_types=1);

namespace Tests\Unit\Models;

use App\Models\Log;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class LogTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_belongs_to_a_vault(): void
    {
        $log = Log::factory()->create();

        $this->assertTrue($log->vault()->exists());
    }

    #[Test]
    public function it_belongs_to_a_user(): void
    {
        $log = Log::factory()->create();

        $this->assertTrue($log->user()->exists());
    }

    #[Test]
    public function it_gets_the_name_of_the_user(): void
    {
        $user = User::factory()->create([
            'first_name' => 'Ross',
            'last_name' => 'Geller',
        ]);
        $log = Log::factory()->create([
            'user_id' => $user->id,
            'user_name' => 'Joey Tribbiani',
        ]);

        $this->assertEquals('Ross Geller', $log->getUserName());

        $log->user_id = null;
        $log->save();

        $this->assertEquals('Joey Tribbiani', $log->refresh()->getUserName());
    }
}

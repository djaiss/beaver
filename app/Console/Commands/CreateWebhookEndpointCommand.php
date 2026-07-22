<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\User;
use App\Models\WebhookEndpoint;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class CreateWebhookEndpointCommand extends Command
{
    /**
     * @var string
     */
    protected $signature = 'kollek:create-webhook-endpoint
        {email : The email of the user who owns the endpoint}
        {url : The url the webhook is sent to}
        {--label= : An optional human readable name for the endpoint}';

    /**
     * @var string
     */
    protected $description = 'Register a webhook endpoint for a user from the command line';

    public function handle(): int
    {
        $user = User::query()
            ->where('email', $this->argument('email'))
            ->first();

        if ($user === null) {
            $this->error('No user found with this email address.');

            return self::FAILURE;
        }

        $secret = Str::random(64);

        $endpoint = WebhookEndpoint::query()->create([
            'user_id' => $user->id,
            'label' => $this->option('label'),
            'url' => $this->argument('url'),
            'secret' => $secret,
            'is_active' => true,
        ]);

        $this->info('Webhook endpoint created.');
        $this->line('Endpoint ID: '.$endpoint->id);
        $this->line('Signing secret: '.$secret);

        return self::SUCCESS;
    }
}

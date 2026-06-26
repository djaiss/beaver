<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Process;

class SyncSkillsCommand extends Command
{
    /**
     * @var string
     */
    protected $signature = 'lifeos:sync-skills';

    /**
     * @var string
     */
    protected $description = 'Synchronize GitHub skills to the agent and AI skill directories';

    public function handle(): int
    {
        $result = Process::timeout(60)->run([
            'bash',
            base_path('scripts/sync-skills.sh'),
            base_path(),
        ]);

        if ($result->failed()) {
            $message = trim($result->errorOutput());

            if ($message === '') {
                $message = trim($result->output());
            }

            if ($message !== '') {
                $this->error($message);
            }

            return self::FAILURE;
        }

        $this->info(trim($result->output()));

        return self::SUCCESS;
    }
}

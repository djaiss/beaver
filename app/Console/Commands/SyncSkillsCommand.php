<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Throwable;

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
        $sourcePath = base_path('.github/skills');

        if (! File::isDirectory($sourcePath)) {
            $this->error(sprintf('The source skills directory was not found at %s.', $sourcePath));

            return self::FAILURE;
        }

        $stagingPath = sys_get_temp_dir().'/lifeos-sync-skills-'.bin2hex(random_bytes(8));

        try {
            if (! File::copyDirectory($sourcePath, $stagingPath)) {
                $this->error(sprintf('The source skills directory could not be staged from %s.', $sourcePath));

                return self::FAILURE;
            }

            foreach (['.agents/skills', '.ai/skills'] as $targetDirectory) {
                $targetPath = base_path($targetDirectory);

                if (File::isDirectory($targetPath) && ! File::deleteDirectory($targetPath)) {
                    $this->error(sprintf('The skills directory could not be removed at %s.', $targetPath));

                    return self::FAILURE;
                }

                if (! File::copyDirectory($stagingPath, $targetPath)) {
                    $this->error(sprintf('The skills directory could not be copied to %s.', $targetPath));

                    return self::FAILURE;
                }
            }
        } catch (Throwable $throwable) {
            $this->error($throwable->getMessage());

            return self::FAILURE;
        } finally {
            File::deleteDirectory($stagingPath);
        }

        $this->info('Skills synchronized.');

        return self::SUCCESS;
    }
}

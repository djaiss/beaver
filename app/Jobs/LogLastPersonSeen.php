<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\Member;
use App\Models\Person;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Queue\Queueable;

class LogLastPersonSeen implements ShouldQueue
{
    use Queueable;

    private Member $member;

    public function __construct(
        public User $user,
        public Person $person,
    ) {}

    public function handle(): void
    {
        $this->validate();
        $this->update();
    }

    private function validate(): void
    {
        if ($this->user->isPartOfVault($this->person->vault) === false) {
            throw new ModelNotFoundException('Person not found');
        }

        $this->member = $this->user->memberOf($this->person->vault);
    }

    private function update(): void
    {
        $this->member->last_person_seen_id = $this->person->id;
        $this->member->save();
    }
}

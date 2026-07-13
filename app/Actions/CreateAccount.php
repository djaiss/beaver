<?php

declare(strict_types=1);

namespace App\Actions;

use App\Helpers\TextSanitizer;
use App\Models\Account;
use App\Models\User;

/**
 * Create an account (the top-level shared space). The author becomes the record
 * that created it; membership is added separately.
 */
class CreateAccount
{
    private Account $account;

    public function __construct(
        private readonly User $author,
        private string $name,
    ) {}

    public function execute(): Account
    {
        $this->sanitize();
        $this->create();

        return $this->account;
    }

    private function sanitize(): void
    {
        $this->name = TextSanitizer::plainText($this->name);
    }

    private function create(): void
    {
        $this->account = new Account(['name' => $this->name]);
        $this->account->created_by_id = $this->author->id;
        $this->account->created_by_name = $this->author->getFullName();
        $this->account->updated_by_id = $this->author->id;
        $this->account->updated_by_name = $this->author->getFullName();
        $this->account->save();
    }
}

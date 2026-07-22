<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Actions\CreateAccount;
use App\Actions\CreateUser;
use App\Enums\PermissionEnum;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Date;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create the admin account and its first user (the owner). This one also
        // administers the instance, so the panel has someone to sign in as.
        $adminUser = new CreateAccount(
            email: 'admin@admin.com',
            password: 'admin123',
            firstName: 'Monica',
            lastName: 'Geller',
        )->execute();
        $adminUser->email_verified_at = Date::now();
        $adminUser->is_instance_administrator = true;
        $adminUser->save();

        // Add a second user to the admin's account to demo a shared account.
        $secondUser = new CreateUser(
            account: $adminUser->account,
            email: 'ross@friends.com',
            password: 'ross123',
            firstName: 'Ross',
            lastName: 'Geller',
            role: PermissionEnum::Editor->value,
        )->execute();
        $secondUser->email_verified_at = Date::now();
        $secondUser->save();

        // Create a blank account and its owner for clean testing.
        $blankUser = new CreateAccount(
            email: 'blank@blank.com',
            password: 'blank123',
            firstName: 'Rachel',
            lastName: 'Green',
        )->execute();
        $blankUser->email_verified_at = Date::now();
        $blankUser->save();
    }
}

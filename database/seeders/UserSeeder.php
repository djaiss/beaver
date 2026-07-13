<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Actions\AddAccountMember;
use App\Actions\CreateUser;
use App\Actions\SignUp;
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
        // Create the admin user, owner of their own account.
        $adminUser = new SignUp(
            email: 'admin@admin.com',
            password: 'admin123',
            firstName: 'Monica',
            lastName: 'Geller',
        )->execute();
        $adminUser->email_verified_at = Date::now();
        $adminUser->save();

        // Add a second member to the admin's account to demo a shared account.
        $adminAccount = $adminUser->accounts()->firstOrFail();

        $secondUser = new CreateUser(
            email: 'ross@friends.com',
            password: 'ross123',
            firstName: 'Ross',
            lastName: 'Geller',
        )->execute();
        $secondUser->email_verified_at = Date::now();
        $secondUser->save();

        new AddAccountMember(
            account: $adminAccount,
            user: $secondUser,
            role: PermissionEnum::Editor->value,
            invitedBy: $adminUser,
        )->execute();

        // Create a blank user for clean testing, owner of their own account.
        $blankUser = new SignUp(
            email: 'blank@blank.com',
            password: 'blank123',
            firstName: 'Rachel',
            lastName: 'Green',
        )->execute();
        $blankUser->email_verified_at = Date::now();
        $blankUser->save();
    }
}

<?php

declare(strict_types=1);

namespace App\Enums;

enum UserActionEnum: string
{
    case AccountCreation = 'account_creation';
    case ApiKeyCreation = 'api_key_creation';
    case MagicLinkCreated = 'magic_link_created';
    case VaultCreation = 'vault_creation';
    case ApiKeyDeletion = 'api_key_deletion';
    case VaultDeletion = 'vault_deletion';
    case TwoFaQrCodeGeneration = '2fa_qr_code_generation';
    case VaultJoined = 'vault_joined';
    case TwoFaRemoval = '2fa_removal';
    case AutoDeleteAccountUpdate = 'auto_delete_account_update';
    case VaultUpdate = 'vault_update';
    case PersonalProfileUpdate = 'personal_profile_update';
    case UpdateUserPassword = 'update_user_password';
    case GenderCreation = 'gender_creation';
    case GenderUpdate = 'gender_update';
    case GenderDeletion = 'gender_deletion';
}

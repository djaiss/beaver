<?php

declare(strict_types=1);

namespace App\Enums;

enum UserActionEnum: string
{
    case AccountCreation = 'account_created';
    case ApiKeyCreation = 'api_key_created';
    case ApiKeyDeletion = 'api_key_deleted';
    case MagicLinkCreated = 'magic_link_created';
    case VaultCreation = 'vault_created';
    case VaultUpdate = 'vault_updated';
    case VaultDeletion = 'vault_deleted';
    case VaultJoined = 'vault_joined';
    case GenderCreation = 'gender_created';
    case GenderUpdate = 'gender_updated';
    case GenderDeletion = 'gender_deleted';
    case PersonCreation = 'person_created';
    case PersonUpdate = 'person_updated';
    case PersonDeletion = 'person_deleted';
    case PersonalProfileUpdate = 'user_profile_updated';
    case UpdateUserPassword = 'user_password_updated';
    case AutoDeleteAccountUpdate = 'user_auto_delete_updated';
    case TwoFaQrCodeGeneration = '2fa_qr_generated';
    case TwoFaRemoval = '2fa_removed';
}

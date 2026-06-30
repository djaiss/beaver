<?php

declare(strict_types=1);

namespace App\Enums;

enum UserActionEnum: string
{
    case AccountCreation = 'account_created';
    case ApiKeyCreation = 'api_key_created';
    case ApiKeyDeletion = 'api_key_deleted';
    case WebhookEndpointCreation = 'webhook_endpoint_created';
    case WebhookEndpointDeletion = 'webhook_endpoint_deleted';
    case MagicLinkCreated = 'magic_link_created';
    case VaultCreation = 'vault_created';
    case VaultUpdate = 'vault_updated';
    case VaultDeletion = 'vault_deleted';
    case VaultJoined = 'vault_joined';
    case GenderCreation = 'gender_created';
    case GenderUpdate = 'gender_updated';
    case GenderDeletion = 'gender_deleted';
    case RelationshipTypeCategoryCreation = 'relationship_type_category_created';
    case RelationshipTypeCategoryUpdate = 'relationship_type_category_updated';
    case RelationshipTypeCategoryDeletion = 'relationship_type_category_deleted';
    case RelationshipTypeCreation = 'relationship_type_created';
    case RelationshipTypeUpdate = 'relationship_type_updated';
    case RelationshipTypeDeletion = 'relationship_type_deleted';
    case PersonCreation = 'person_created';
    case PersonUpdate = 'person_updated';
    case PersonDeletion = 'person_deleted';
    case PersonalProfileUpdate = 'user_profile_updated';
    case UpdateUserPassword = 'user_password_updated';
    case AutoDeleteAccountUpdate = 'user_auto_delete_updated';
    case TwoFaQrCodeGeneration = '2fa_qr_generated';
    case TwoFaRemoval = '2fa_removed';

    public function translationKey(): string
    {
        return match ($this) {
            self::AccountCreation => 'Created an account',
            self::ApiKeyCreation => 'Created an API key',
            self::ApiKeyDeletion => 'Deleted an API key',
            self::WebhookEndpointCreation => 'Created a webhook endpoint',
            self::WebhookEndpointDeletion => 'Deleted a webhook endpoint',
            self::MagicLinkCreated => 'Sent a magic link',
            self::VaultCreation => 'Created a vault called :name',
            self::VaultUpdate => 'Updated the vault called :name',
            self::VaultDeletion => 'Deleted the vault called :name',
            self::VaultJoined => 'Joined vault called :name',
            self::GenderCreation => 'Created a gender called :name',
            self::GenderUpdate => 'Updated a gender called :name',
            self::GenderDeletion => 'Deleted the gender called :name',
            self::RelationshipTypeCategoryCreation => 'Created a relationship type category called :name',
            self::RelationshipTypeCategoryUpdate => 'Updated a relationship type category called :name',
            self::RelationshipTypeCategoryDeletion => 'Deleted the relationship type category called :name',
            self::RelationshipTypeCreation => 'Created a relationship type called :name',
            self::RelationshipTypeUpdate => 'Updated a relationship type called :name',
            self::RelationshipTypeDeletion => 'Deleted the relationship type called :name',
            self::PersonCreation => 'Created a person called :name',
            self::PersonUpdate => 'Updated a person called :name',
            self::PersonDeletion => 'Deleted the person called :name',
            self::PersonalProfileUpdate => 'Updated their personal profile',
            self::UpdateUserPassword => 'Updated their password',
            self::AutoDeleteAccountUpdate => 'Updated auto delete account setting to :status',
            self::TwoFaQrCodeGeneration => 'Generated 2FA QR code for setup',
            self::TwoFaRemoval => 'Removed 2FA from account',
        };
    }
}

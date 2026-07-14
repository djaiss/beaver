<?php

declare(strict_types=1);

namespace App\Enums;

enum UserActionEnum: string
{
    case AccountCreation = 'account_created';
    case AccountUpdate = 'account_updated';
    case AccountDeletion = 'account_deleted';
    case MemberInvited = 'member_invited';
    case InvitationAccepted = 'invitation_accepted';
    case MemberRemoved = 'member_removed';
    case MemberRoleUpdated = 'member_role_updated';
    case CollectionCreation = 'collection_created';
    case CollectionUpdate = 'collection_updated';
    case CollectionDeletion = 'collection_deleted';
    case CollectionTypeCreation = 'collection_type_created';
    case CollectionTypeUpdate = 'collection_type_updated';
    case CollectionTypeDeletion = 'collection_type_deleted';
    case CustomFieldCreation = 'custom_field_created';
    case CustomFieldUpdate = 'custom_field_updated';
    case CustomFieldDeletion = 'custom_field_deleted';
    case LocationCreation = 'location_created';
    case LocationUpdate = 'location_updated';
    case LocationDeletion = 'location_deleted';
    case ApiKeyCreation = 'api_key_created';
    case ApiKeyDeletion = 'api_key_deleted';
    case WebhookEndpointCreation = 'webhook_endpoint_created';
    case WebhookEndpointDeletion = 'webhook_endpoint_deleted';
    case MagicLinkCreated = 'magic_link_created';
    case PersonalProfileUpdate = 'user_profile_updated';
    case UpdateUserPassword = 'user_password_updated';
    case AutoDeleteUserUpdate = 'user_auto_delete_updated';
    case TwoFaQrCodeGeneration = '2fa_qr_generated';
    case TwoFaRemoval = '2fa_removed';

    public function translationKey(): string
    {
        return match ($this) {
            self::AccountCreation => 'Created an account',
            self::AccountUpdate => 'Updated the account called :name',
            self::AccountDeletion => 'Deleted the account called :name',
            self::MemberInvited => 'Invited :email to the account',
            self::InvitationAccepted => 'Joined the account called :name',
            self::MemberRemoved => 'Removed a member from the account',
            self::MemberRoleUpdated => 'Updated a member\'s role to :role',
            self::CollectionCreation => 'Created the collection called :name',
            self::CollectionUpdate => 'Updated the collection called :name',
            self::CollectionDeletion => 'Deleted the collection called :name',
            self::CollectionTypeCreation => 'Created the collection type called :name',
            self::CollectionTypeUpdate => 'Updated the collection type called :name',
            self::CollectionTypeDeletion => 'Deleted the collection type called :name',
            self::CustomFieldCreation => 'Created the custom field called :name',
            self::CustomFieldUpdate => 'Updated the custom field called :name',
            self::CustomFieldDeletion => 'Deleted the custom field called :name',
            self::LocationCreation => 'Created the location called :name',
            self::LocationUpdate => 'Updated the location called :name',
            self::LocationDeletion => 'Deleted the location called :name',
            self::ApiKeyCreation => 'Created an API key',
            self::ApiKeyDeletion => 'Deleted an API key',
            self::WebhookEndpointCreation => 'Created a webhook endpoint',
            self::WebhookEndpointDeletion => 'Deleted a webhook endpoint',
            self::MagicLinkCreated => 'Sent a magic link',
            self::PersonalProfileUpdate => 'Updated their personal profile',
            self::UpdateUserPassword => 'Updated their password',
            self::AutoDeleteUserUpdate => 'Updated auto delete account setting to :status',
            self::TwoFaQrCodeGeneration => 'Generated 2FA QR code for setup',
            self::TwoFaRemoval => 'Removed 2FA from account',
        };
    }
}

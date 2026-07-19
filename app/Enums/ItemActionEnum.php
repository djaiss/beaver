<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * The actions recorded on the activity tab of an item.
 *
 * These read from the point of view of the item, not the user, because the item
 * is already the context on that screen. The user feed says "Added the tag
 * Signed to the item called Amazing Spider-Man #1", and the same action reads
 * "added the tag Signed" here.
 */
enum ItemActionEnum: string
{
    case ItemCreation = 'item_created';
    case ItemUpdate = 'item_updated';
    case TagAttached = 'tag_attached';
    case TagDetached = 'tag_detached';
    case PhotoAdded = 'photo_added';
    case PhotoDeleted = 'photo_deleted';
    case PhotoMainSet = 'photo_main_set';
    case PhotoMoved = 'photo_moved';
    case CopyCreation = 'copy_created';
    case CopyUpdate = 'copy_updated';
    case CopyDeletion = 'copy_deleted';
    case TransactionCreation = 'transaction_created';
    case TransactionUpdate = 'transaction_updated';
    case TransactionDeletion = 'transaction_deleted';

    public function translationKey(): string
    {
        return match ($this) {
            self::ItemCreation => 'created this item',
            self::ItemUpdate => 'updated this item',
            self::TagAttached => 'added the tag',
            self::TagDetached => 'removed the tag',
            self::PhotoAdded => 'added a photo',
            self::PhotoDeleted => 'deleted a photo',
            self::PhotoMainSet => 'made a photo the main visual',
            self::PhotoMoved => 'reordered the photos',
            self::CopyCreation => 'added a copy',
            self::CopyUpdate => 'updated a copy',
            self::CopyDeletion => 'deleted a copy',
            self::TransactionCreation => 'recorded a transaction',
            self::TransactionUpdate => 'updated a transaction',
            self::TransactionDeletion => 'deleted a transaction',
        };
    }
}

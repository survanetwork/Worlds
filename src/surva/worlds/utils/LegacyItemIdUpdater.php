<?php

/**
 * Worlds | utility to update legacy item ID's to new string item names
 */

namespace surva\worlds\utils;

use pocketmine\data\bedrock\item\UnsupportedItemTypeException;
use pocketmine\data\SavedDataLoadingException;
use pocketmine\item\StringToItemParser;
use pocketmine\world\format\io\GlobalItemDataHandlers;

class LegacyItemIdUpdater
{
    /**
     * Check if an array contains legacy item/block ID's and needs to be updated
     *
     * @param  (string|int)[]  $items
     * @param  string  $flagName
     *
     * @return (string|int)[]
     */
    public static function tryToUpdateArray(array $items, string $flagName): array
    {
        if (!in_array($flagName, Flags::ITEM_CONTROL_LISTS)) {
            return $items;
        }

        $itemNames = [];

        foreach ($items as $legacyItem) {
            if (!is_numeric($legacyItem)) {
                $itemNames[] = $legacyItem;

                continue;
            }

            $newName = self::updateLegacyIdToItemName((int) $legacyItem);

            if ($newName === null) {
                continue;
            }

            $itemNames[] = $newName;
        }

        return $itemNames;
    }

    /**
     * Try to update a legacy int item/block ID to a string name
     *
     * @param  int  $legacyId
     *
     * @return string|null
     */
    private static function updateLegacyIdToItemName(int $legacyId): ?string
    {
        try {
            $itemData = GlobalItemDataHandlers::getUpgrader()->upgradeItemTypeDataInt($legacyId, 0, 1, null);
        } catch (SavedDataLoadingException $e) {
            return null;
        }
        try {
            $item = GlobalItemDataHandlers::getDeserializer()->deserializeStack($itemData);
        } catch (UnsupportedItemTypeException $e) {
            return null;
        }
        return StringToItemParser::getInstance()->lookupAliases($item)[0];
    }
}

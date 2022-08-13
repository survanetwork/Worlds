<?php

/**
 * Worlds | PM world functions
 */

namespace surva\worlds\logic;

use pocketmine\player\GameMode;
use surva\worlds\logic\exception\UnloadDefaultLevelException;
use surva\worlds\logic\exception\UnloadFailedException;
use surva\worlds\utils\Flags;
use surva\worlds\Worlds;

class WorldActions
{
    /**
     * Check if the directory of a world exists
     *
     * @param  \surva\worlds\Worlds  $worlds
     * @param  string  $worldName
     *
     * @return bool
     */
    public static function worldPathExists(Worlds $worlds, string $worldName): bool
    {
        return is_dir($worlds->getServer()->getDataPath() . "worlds/" . $worldName);
    }

    /**
     * Check if the given string is a valid flag name
     *
     * @param  string  $flagName
     *
     * @return bool
     */
    public static function isValidFlag(string $flagName): bool
    {
        return in_array($flagName, array_keys(Flags::AVAILABLE_WORLD_FLAGS));
    }

    /**
     * Get the type of flag name
     *
     * @param  string  $flagName
     *
     * @return int|null
     */
    public static function getFlagType(string $flagName): ?int
    {
        if (!isset(Flags::AVAILABLE_WORLD_FLAGS[$flagName])) {
            return null;
        }

        return Flags::AVAILABLE_WORLD_FLAGS[$flagName]["type"];
    }

    /**
     * Try to unload a world if it's loaded
     *
     * @param  \surva\worlds\Worlds  $worlds
     * @param  string  $worldName
     *
     * @return void
     * @throws \surva\worlds\logic\exception\UnloadDefaultLevelException
     * @throws \surva\worlds\logic\exception\UnloadFailedException
     */
    public static function unloadIfLoaded(Worlds $worlds, string $worldName): void
    {
        if (!$worlds->getServer()->getWorldManager()->isWorldLoaded($worldName)) {
            return;
        }

        if ($defLvl = $worlds->getServer()->getWorldManager()->getDefaultWorld()) {
            if ($defLvl->getFolderName() === $worldName) {
                throw new UnloadDefaultLevelException();
            }
        }

        if (
            !($worlds->getServer()->getWorldManager()->unloadWorld(
                $worlds->getServer()->getWorldManager()->getWorldByName($worldName)
            ))
        ) {
            throw new UnloadFailedException();
        }

        $worlds->unregisterWorld($worldName);
    }

    /**
     * Get the ID int value of a game mode
     *
     * @param  \pocketmine\player\GameMode  $gameMode
     *
     * @return int|null
     */
    public static function getGameModeId(GameMode $gameMode): ?int
    {
        return match ($gameMode->name()) {
            "survival" => 0,
            "creative" => 1,
            "adventure" => 2,
            "spectator" => 3,
            default => null
        };
    }
}

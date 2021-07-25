<?php
/**
 * Worlds | level functions
 */

namespace surva\worlds\logic;

use surva\worlds\Worlds;

class WorldActions
{

    public const SUCCESS = 0;

    public const UNLOAD_DEFAULT = 1;

    public const UNLOAD_FAILED = 2;

    /**
     * Check if the directory of a level exists
     *
     * @param  \surva\worlds\Worlds  $worlds
     * @param  string  $levelName
     *
     * @return bool
     */
    public static function worldPathExists(Worlds $worlds, string $levelName): bool
    {
        return is_dir($worlds->getServer()->getDataPath() . "worlds/" . $levelName);
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
        return in_array(
          $flagName,
          [
            "permission",
            "gamemode",
            "build",
            "pvp",
            "damage",
            "interact",
            "explode",
            "drop",
            "hunger",
            "fly",
            "daylightcycle",
          ]
        );
    }

    /**
     * Try to unload a level if it's loaded
     *
     * @param  \surva\worlds\Worlds  $worlds
     * @param  string  $levelName
     *
     * @return int
     */
    public static function unloadIfLoaded(Worlds $worlds, string $levelName): int
    {
        if (!$worlds->getServer()->isLevelLoaded($levelName)) {
            return self::SUCCESS;
        }

        if ($defLvl = $worlds->getServer()->getDefaultLevel()) {
            if ($defLvl->getName() === $levelName) {
                return self::UNLOAD_DEFAULT;
            }
        }

        if (!($worlds->getServer()->unloadLevel(
          $worlds->getServer()->getLevelByName($levelName)
        ))
        ) {
            return self::UNLOAD_FAILED;
        }

        $worlds->getWorlds()->remove($levelName);

        return self::SUCCESS;
    }

}

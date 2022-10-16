<?php

/**
 * Worlds | list worlds command
 */

namespace surva\worlds\commands;

use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

class ListCommand extends CustomCommand
{
    public function do(CommandSender $sender, array $args): bool
    {
        $worlds = [];

        foreach ($this->getWorlds()->getServer()->getWorldManager()->getWorlds() as $pmWorld) {
            $worlds[] = TextFormat::WHITE . $pmWorld->getFolderName();
        }

        $worldsStoragePath = $this->getWorlds()->getServer()->getDataPath() . "worlds";

        foreach (scandir($worldsStoragePath) as $unloadedWorld) {
            if (
                is_dir($worldsStoragePath . "/" . $unloadedWorld) and
                is_file($worldsStoragePath . "/" . $unloadedWorld . "/level.dat") and
                !in_array(TextFormat::WHITE . $unloadedWorld, $worlds)
            ) {
                $worlds[] = TextFormat::GRAY . $unloadedWorld;
            }
        }

        $this->getWorlds()->sendMessage($sender, "list.worlds", ["worlds" => implode(", ", $worlds)]);

        return true;
    }
}

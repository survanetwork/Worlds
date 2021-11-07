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
        $levels = [];

        foreach ($this->getWorlds()->getServer()->getWorldManager()->getWorlds() as $level) {
            $levels[] = TextFormat::WHITE . $level->getFolderName();
        }

        $worldsPath = $this->getWorlds()->getServer()->getDataPath() . "worlds";

        foreach (scandir($worldsPath) as $unloadedLevel) {
            if (
              is_dir($worldsPath . "/" . $unloadedLevel) and
              is_file($worldsPath . "/" . $unloadedLevel . "/level.dat") and
              !in_array(TextFormat::WHITE . $unloadedLevel, $levels)
            ) {
                $levels[] = TextFormat::GRAY . $unloadedLevel;
            }
        }

        $sender->sendMessage($this->getWorlds()->getMessage("list.worlds", ["worlds" => implode(", ", $levels)]));

        return true;
    }

}

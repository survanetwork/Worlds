<?php
/**
 * Worlds | copy world command
 */

namespace surva\worlds\commands;

use pocketmine\command\CommandSender;
use surva\worlds\logic\WorldActions;
use surva\worlds\utils\FileUtils;

class CopyCommand extends CustomCommand
{

    public function do(CommandSender $sender, array $args): bool
    {
        if (!(count($args) === 2)) {
            return false;
        }

        if (!WorldActions::worldPathExists($this->getWorlds(), $args[0])) {
            $sender->sendMessage($this->getWorlds()->getMessage("general.world.notexist", ["name" => $args[0]]));

            return true;
        }

        $fromFolderName = $args[0];
        $toFolderName   = $args[1];

        if ($fromFolderName === $toFolderName) {
            $sender->sendMessage($this->getWorlds()->getMessage("copy.same"));

            return true;
        }

        $fromPath = $this->getWorlds()->getServer()->getDataPath() . "worlds/" . $fromFolderName;
        $toPath   = $this->getWorlds()->getServer()->getDataPath() . "worlds/" . $toFolderName;

        $res = FileUtils::copyRecursive($fromPath, $toPath);

        if(!$res) {
            $sender->sendMessage($this->getWorlds()->getMessage("copy.error"));

            return true;
        }

        $sender->sendMessage(
          $this->getWorlds()->getMessage("copy.success", ["name" => $fromFolderName, "to" => $toFolderName])
        );

        return true;
    }

}

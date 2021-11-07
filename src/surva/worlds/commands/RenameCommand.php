<?php
/**
 * Worlds | rename world command
 */

namespace surva\worlds\commands;

use pocketmine\command\CommandSender;
use surva\worlds\logic\WorldActions;
use surva\worlds\utils\FileUtils;

class RenameCommand extends CustomCommand
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

        switch (WorldActions::unloadIfLoaded($this->getWorlds(), $args[0])) {
            case WorldActions::UNLOAD_DEFAULT:
                $sender->sendMessage($this->getWorlds()->getMessage("unload.default"));

                return true;
            case WorldActions::UNLOAD_FAILED:
                $sender->sendMessage($this->getWorlds()->getMessage("unload.failed"));

                return true;
        }

        $fromFolderName = $args[0];
        $toFolderName   = $args[1];

        if ($fromFolderName === $toFolderName) {
            $sender->sendMessage($this->getWorlds()->getMessage("rename.same"));

            return true;
        }

        $fromPath = $this->getWorlds()->getServer()->getDataPath() . "worlds/" . $fromFolderName;
        $toPath   = $this->getWorlds()->getServer()->getDataPath() . "worlds/" . $toFolderName;

        $copyRes = FileUtils::copyRecursive($fromPath, $toPath);

        if ($copyRes === true) {
            FileUtils::deleteRecursive($fromPath);
        }

        $sender->sendMessage(
          $this->getWorlds()->getMessage("rename.success", ["name" => $fromFolderName, "to" => $toFolderName])
        );

        return true;
    }

}

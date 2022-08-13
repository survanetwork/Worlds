<?php

/**
 * Worlds | rename world command
 */

namespace surva\worlds\commands;

use pocketmine\command\CommandSender;
use surva\worlds\logic\WorldActions;
use surva\worlds\utils\exception\SourceNotExistException;
use surva\worlds\utils\exception\TargetExistException;
use surva\worlds\utils\FileUtils;

class RenameCommand extends CustomCommand
{
    public function do(CommandSender $sender, array $args): bool
    {
        if (!(count($args) === 2)) {
            return false;
        }

        if (!WorldActions::worldPathExists($this->getWorlds(), $args[0])) {
            $sender->sendMessage($this->getWorlds()->getMessage("general.world.not_exist", ["name" => $args[0]]));

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
            $sender->sendMessage($this->getWorlds()->getMessage("rename.error_code.same_source_target"));

            return true;
        }

        $fromPath = $this->getWorlds()->getServer()->getDataPath() . "worlds/" . $fromFolderName;
        $toPath   = $this->getWorlds()->getServer()->getDataPath() . "worlds/" . $toFolderName;

        try {
            $copyRes = FileUtils::copyRecursive($fromPath, $toPath);
        } catch (SourceNotExistException $e) {
            $sender->sendMessage($this->getWorlds()->getMessage("copy.error_code.source_not_exist"));

            return true;
        } catch (TargetExistException $e) {
            $sender->sendMessage($this->getWorlds()->getMessage("copy.error_code.target_exist"));

            return true;
        }

        if (!$copyRes) {
            $sender->sendMessage($this->getWorlds()->getMessage("copy.error_code.copy_failed"));

            return true;
        }

        try {
            $deleteRes = FileUtils::deleteRecursive($fromPath);
        } catch (SourceNotExistException $e) {
            $sender->sendMessage($this->getWorlds()->getMessage("copy.error_code.source_not_exist"));

            return true;
        }

        if (!$deleteRes) {
            $sender->sendMessage($this->getWorlds()->getMessage("rename.error_code.delete_failed"));

            return true;
        }

        $sender->sendMessage(
            $this->getWorlds()->getMessage("rename.success", ["name" => $fromFolderName, "to" => $toFolderName])
        );

        return true;
    }
}

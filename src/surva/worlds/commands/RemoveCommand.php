<?php

/**
 * Worlds | remove world command
 */

namespace surva\worlds\commands;

use pocketmine\command\CommandSender;
use surva\worlds\logic\exception\UnloadDefaultLevelException;
use surva\worlds\logic\exception\UnloadFailedException;
use surva\worlds\logic\WorldActions;
use surva\worlds\utils\exception\SourceNotExistException;
use surva\worlds\utils\FileUtils;

class RemoveCommand extends CustomCommand
{
    public function do(CommandSender $sender, array $args): bool
    {
        if (!(count($args) === 1)) {
            return false;
        }

        try {
            WorldActions::unloadIfLoaded($this->getWorlds(), $args[0]);
        } catch (UnloadDefaultLevelException $e) {
            $sender->sendMessage($this->getWorlds()->getMessage("unload.default"));

            return true;
        } catch (UnloadFailedException $e) {
            $sender->sendMessage($this->getWorlds()->getMessage("unload.failed"));

            return true;
        }

        try {
            $res = FileUtils::deleteRecursive($this->getWorlds()->getServer()->getDataPath() . "worlds/" . $args[0]);
        } catch (SourceNotExistException $e) {
            $sender->sendMessage($this->getWorlds()->getMessage("copy.error_code.source_not_exist"));

            return true;
        }

        if (!$res) {
            $sender->sendMessage($this->getWorlds()->getMessage("remove.error"));

            return true;
        }

        $sender->sendMessage($this->getWorlds()->getMessage("remove.success", ["name" => $args[0]]));

        return true;
    }
}

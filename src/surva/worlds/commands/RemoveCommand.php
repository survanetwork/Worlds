<?php

/**
 * Worlds | remove world command
 */

namespace surva\worlds\commands;

use pocketmine\command\CommandSender;
use surva\worlds\utils\exception\SourceNotExistException;
use surva\worlds\utils\FileUtils;
use surva\worlds\utils\Messages;

class RemoveCommand extends CustomCommand
{
    /**
     * @inheritDoc
     */
    public function do(CommandSender $sender, array $args): bool
    {
        if (!(count($args) === 1)) {
            return false;
        }

        $messages = new Messages($this->getWorlds(), $sender);

        $res = UnloadCommand::tryToUnload($this->getWorlds(), $args[0], $sender, $messages);
        if (!$res) {
            return true;
        }

        try {
            $res = FileUtils::deleteRecursive($this->getWorlds()->getServer()->getDataPath() . "worlds/" . $args[0]);
        } catch (SourceNotExistException $e) {
            $sender->sendMessage($messages->getMessage("copy.error_code.source_not_exist"));

            return true;
        }

        if (!$res) {
            $sender->sendMessage($messages->getMessage("remove.error"));

            return true;
        }

        $sender->sendMessage($messages->getMessage("remove.success", ["name" => $args[0]]));

        return true;
    }
}

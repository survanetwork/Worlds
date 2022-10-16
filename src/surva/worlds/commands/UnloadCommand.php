<?php

/**
 * Worlds | unload world command
 */

namespace surva\worlds\commands;

use pocketmine\command\CommandSender;
use surva\worlds\utils\Messages;

class UnloadCommand extends CustomCommand
{
    public function do(CommandSender $sender, array $args): bool
    {
        if (!(count($args) === 1)) {
            return false;
        }

        $messages = new Messages($this->getWorlds(), $sender);

        if (!($this->getWorlds()->getServer()->getWorldManager()->isWorldLoaded($args[0]))) {
            $sender->sendMessage($messages->getMessage("general.world.not_loaded", ["name" => $args[0]]));

            return true;
        }

        if ($defLvl = $this->getWorlds()->getServer()->getWorldManager()->getDefaultWorld()) {
            if ($defLvl->getFolderName() === $args[0]) {
                $sender->sendMessage($messages->getMessage("unload.default"));

                return true;
            }
        }

        if (
            !($this->getWorlds()->getServer()->getWorldManager()->unloadWorld(
                $this->getWorlds()->getServer()->getWorldManager()->getWorldByName($args[0])
            ))
        ) {
            $sender->sendMessage($messages->getMessage("unload.failed"));

            return true;
        }

        $this->getWorlds()->unregisterWorld($args[0]);

        $sender->sendMessage($messages->getMessage("unload.success", ["world" => $args[0]]));

        return true;
    }
}

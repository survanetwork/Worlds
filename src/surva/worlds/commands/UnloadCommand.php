<?php
/**
 * Worlds | unload world command
 */

namespace surva\worlds\commands;

use pocketmine\command\CommandSender;

class UnloadCommand extends CustomCommand
{

    public function do(CommandSender $sender, array $args): bool
    {
        if (!(count($args) === 1)) {
            return false;
        }

        if (!($this->getWorlds()->getServer()->getWorldManager()->isWorldLoaded($args[0]))) {
            $sender->sendMessage($this->getWorlds()->getMessage("general.world.notloaded", ["name" => $args[0]]));

            return true;
        }

        if ($defLvl = $this->getWorlds()->getServer()->getWorldManager()->getDefaultWorld()) {
            if ($defLvl->getFolderName() === $args[0]) {
                $sender->sendMessage($this->getWorlds()->getMessage("unload.default"));

                return true;
            }
        }

        if (!($this->getWorlds()->getServer()->getWorldManager()->unloadWorld(
          $this->getWorlds()->getServer()->getWorldManager()->getWorldByName($args[0])
        ))
        ) {
            $sender->sendMessage($this->getWorlds()->getMessage("unload.failed"));

            return true;
        }

        $this->getWorlds()->unregisterWorld($args[0]);

        $sender->sendMessage($this->getWorlds()->getMessage("unload.success", ["world" => $args[0]]));

        return true;
    }

}

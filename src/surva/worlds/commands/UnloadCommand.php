<?php
/**
 * Worlds | unload world command
 */

namespace surva\worlds\commands;

use pocketmine\command\CommandSender;

class UnloadCommand extends CustomCommand {
    public function do(CommandSender $sender, array $args): bool {
        if(!(count($args) === 1)) {
            return false;
        }

        if(!($this->getWorlds()->getServer()->isLevelLoaded($args[0]))) {
            $sender->sendMessage($this->getWorlds()->getMessage("general.world.notloaded"));

            return true;
        }

        if($defLvl = $this->getWorlds()->getServer()->getDefaultLevel()) {
            if($defLvl->getName() === $args[0]) {
                $sender->sendMessage($this->getWorlds()->getMessage("unload.default"));

                return true;
            }
        }

        if(!($this->getWorlds()->getServer()->unloadLevel($this->getWorlds()->getServer()->getLevelByName($args[0])))) {
            $sender->sendMessage($this->getWorlds()->getMessage("unload.failed"));

            return true;
        }

        $this->getWorlds()->getWorlds()->remove($args[0]);

        $sender->sendMessage($this->getWorlds()->getMessage("unload.success", array("world" => $args[0])));

        return true;
    }
}

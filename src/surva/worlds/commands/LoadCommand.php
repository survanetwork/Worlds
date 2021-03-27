<?php
/**
 * Worlds | load world command
 */

namespace surva\worlds\commands;

use pocketmine\command\CommandSender;
use surva\worlds\logic\WorldActions;

class LoadCommand extends CustomCommand {
    public function do(CommandSender $sender, array $args): bool {
        if(!(count($args) === 1)) {
            return false;
        }

        if($this->getWorlds()->getServer()->isLevelLoaded($args[0])) {
            $sender->sendMessage($this->getWorlds()->getMessage("load.already", array("name" => $args[0])));

            return true;
        }

        if(!WorldActions::worldPathExists($this->getWorlds(), $args[0])) {
            $sender->sendMessage($this->getWorlds()->getMessage("general.world.notexist", array("name" => $args[0])));

            return true;
        }

        if(!($this->getWorlds()->getServer()->loadLevel($args[0]))) {
            $sender->sendMessage($this->getWorlds()->getMessage("load.failed"));

            return true;
        }

        $sender->sendMessage($this->getWorlds()->getMessage("load.success", array("world" => $args[0])));

        return true;
    }
}

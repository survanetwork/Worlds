<?php
/**
 * Worlds | remove world command
 */

namespace surva\worlds\commands;

use pocketmine\Player;
use surva\worlds\logic\WorldActions;

class RemoveCommand extends CustomCommand {
    public function do(Player $player, array $args) {
        if(!(count($args) === 1)) {
            return false;
        }

        switch(WorldActions::unloadIfLoaded($this->getWorlds(), $args[0])) {
            case WorldActions::UNLOAD_DEFAULT:
                $player->sendMessage($this->getWorlds()->getMessage("unload.default"));

                return true;
            case WorldActions::UNLOAD_FAILED:
                $player->sendMessage($this->getWorlds()->getMessage("unload.failed"));

                return true;
        }

        $this->getWorlds()->delete($this->getWorlds()->getServer()->getDataPath() . "worlds/" . $args[0]);

        $player->sendMessage($this->getWorlds()->getMessage("remove.success", array("name" => $args[0])));

        return true;
    }
}

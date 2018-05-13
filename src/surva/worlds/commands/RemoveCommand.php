<?php
/**
 * Created by PhpStorm.
 * User: jarne
 * Date: 01.10.17
 * Time: 11:45
 */

namespace surva\worlds\commands;

use pocketmine\Player;

class RemoveCommand extends CustomCommand {
    public function do(Player $player, array $args) {
        if(!(count($args) === 1)) {
            return false;
        }

        if($this->getWorlds()->getServer()->isLevelLoaded($args[0])) {
            if(!($this->getWorlds()->getServer()->unloadLevel(
                $this->getWorlds()->getServer()->getLevelByName($args[0])
            ))) {
                $player->sendMessage($this->getWorlds()->getMessage("unload.failed"));

                return true;
            }
        }

        $this->getWorlds()->delete($this->getWorlds()->getServer()->getFilePath() . "worlds/" . $args[0]);

        $player->sendMessage($this->getWorlds()->getMessage("remove.success", array("name" => $args[0])));

        return true;
    }
}

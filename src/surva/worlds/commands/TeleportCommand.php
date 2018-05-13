<?php
/**
 * Created by PhpStorm.
 * User: jarne
 * Date: 01.10.17
 * Time: 12:39
 */

namespace surva\worlds\commands;

use pocketmine\Player;

class TeleportCommand extends CustomCommand {
    public function do(Player $player, array $args) {
        if(!(count($args) === 1)) {
            return false;
        }

        if(!($this->getWorlds()->getServer()->isLevelLoaded($args[0]))) {
            $player->sendMessage($this->getWorlds()->getMessage("general.world.notloaded", array("name" => $args[0])));

            return true;
        }

        $targetWorld = $this->getWorlds()->getServer()->getLevelByName($args[0]);

        if(!$player->teleport($targetWorld->getSafeSpawn())) {
            $player->sendMessage($this->getWorlds()->getMessage("teleport.failed"));

            return true;
        }

        $player->sendMessage($this->getWorlds()->getMessage("teleport.success", array("world" => $args[0])));

        return true;
    }
}

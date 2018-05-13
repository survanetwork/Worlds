<?php
/**
 * Created by PhpStorm.
 * User: jarne
 * Date: 01.10.17
 * Time: 11:58
 */

namespace surva\worlds\commands;

use pocketmine\Player;

class LoadCommand extends CustomCommand {
    public function do(Player $player, array $args) {
        if(!(count($args) === 1)) {
            return false;
        }

        if($this->getWorlds()->getServer()->isLevelLoaded($args[0])) {
            $player->sendMessage($this->getWorlds()->getMessage("load.already", array("name" => $args[0])));

            return true;
        }

        if(!(is_dir($this->getWorlds()->getServer()->getDataPath() . "worlds/" . $args[0]))) {
            $player->sendMessage($this->getWorlds()->getMessage("general.world.notexist", array("name" => $args[0])));

            return true;
        }

        if(!($this->getWorlds()->getServer()->loadLevel($args[0]))) {
            $player->sendMessage($this->getWorlds()->getMessage("load.failed"));

            return true;
        }

        $player->sendMessage($this->getWorlds()->getMessage("load.success", array("world" => $args[0])));

        return true;
    }
}

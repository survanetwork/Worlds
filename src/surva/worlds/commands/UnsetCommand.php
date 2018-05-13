<?php
/**
 * Created by PhpStorm.
 * User: jarne
 * Date: 01.10.17
 * Time: 13:35
 */

namespace surva\worlds\commands;

use pocketmine\Player;

class UnsetCommand extends CustomCommand {
    public function do(Player $player, array $args) {
        if(!(count($args) === 1)) {
            return false;
        }

        if(!(in_array(
            $args[0],
            array("permission", "gamemode", "build", "pvp", "damage", "interact", "explode", "drop", "hunger", "fly")
        ))) {
            return false;
        }

        if(!($world = $this->getWorlds()->getWorldByName($player->getLevel()->getFolderName()))) {
            $player->sendMessage($this->getWorlds()->getMessage("general.world.notloaded"));

            return true;
        }

        $world->removeValue($args[0]);

        $player->sendMessage(
            $this->getWorlds()->getMessage(
                "unset.success",
                array("world" => $player->getLevel()->getFolderName(), "key" => $args[0])
            )
        );

        return true;
    }
}

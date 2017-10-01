<?php
/**
 * Created by PhpStorm.
 * User: jarne
 * Date: 01.10.17
 * Time: 11:36
 */

namespace surva\worlds\commands;

use pocketmine\Player;

class ListCommand extends CustomCommand {
    public function do(Player $player, array $args) {
        $levels = array();

        foreach($this->getWorlds()->getServer()->getLevels() as $level) {
            $levels[] = $level->getName();
        }

        $player->sendMessage($this->getWorlds()->getMessage("list.worlds", array("worlds" => implode(", ", $levels))));

        return true;
    }
}
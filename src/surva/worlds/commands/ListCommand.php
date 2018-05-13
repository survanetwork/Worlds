<?php
/**
 * Created by PhpStorm.
 * User: jarne
 * Date: 01.10.17
 * Time: 11:36
 */

namespace surva\worlds\commands;

use pocketmine\Player;
use pocketmine\utils\TextFormat;

class ListCommand extends CustomCommand {
    public function do(Player $player, array $args) {
        $levels = array();

        foreach($this->getWorlds()->getServer()->getLevels() as $level) {
            $levels[] = TextFormat::WHITE . $level->getName();
        }

        $worldsPath = $this->getWorlds()->getServer()->getDataPath() . "worlds";

        foreach(scandir($worldsPath) as $unloadedLevel) {
            if(
                is_dir($worldsPath . "/" . $unloadedLevel) AND
                is_file($worldsPath . "/" . $unloadedLevel . "/level.dat") AND
                !in_array(TextFormat::WHITE . $unloadedLevel, $levels)
            ) {
                $levels[] = TextFormat::GRAY . $unloadedLevel;
            }
        }

        $player->sendMessage($this->getWorlds()->getMessage("list.worlds", array("worlds" => implode(", ", $levels))));

        return true;
    }
}

<?php
/**
 * Created by PhpStorm.
 * User: jarne
 * Date: 01.10.17
 * Time: 12:39
 */

namespace surva\worlds\commands;

use pocketmine\Player;
use pocketmine\Server;

class SetCommand extends CustomCommand {
    public function do(Player $player, array $args) {
        if(!(count($args) === 2)) {
            return false;
        }

        if(!(in_array($args[0], array("permission", "gamemode", "build", "pvp", "damage", "interact", "explode", "drop", "hunger", "fly")))) {
            return false;
        }

        if($args[0] === "permission") {
            if(!($world = $this->getWorlds()->getWorldByName($player->getLevel()->getFolderName()))) {
                $player->sendMessage($this->getWorlds()->getMessage("general.world.notloaded"));

                return true;
            }

            if($this->getWorlds()->getServer()->getDefaultLevel()->getFolderName() === $player->getLevel()->getFolderName()) {
                $player->sendMessage($this->getWorlds()->getMessage("set.permission.notdefault"));

                return true;
            }

            $world->updateValue($args[0], $args[1]);

            $player->sendMessage($this->getWorlds()->getMessage("set.success", array("world" => $player->getLevel()->getFolderName(), "key" => $args[0], "value" => $args[1])));
        } elseif($args[0] === "gamemode") {
            if(($args[1] = Server::getGamemodeFromString($args[1])) === -1) {
                $player->sendMessage($this->getWorlds()->getMessage("set.gamemode.notexist"));

                return true;
            }

            if(!($world = $this->getWorlds()->getWorldByName($player->getLevel()->getFolderName()))) {
                $player->sendMessage($this->getWorlds()->getMessage("general.world.notloaded"));

                return true;
            }

            $world->updateValue($args[0], $args[1]);

            $player->sendMessage($this->getWorlds()->getMessage("set.success", array("world" => $player->getLevel()->getFolderName(), "key" => $args[0], "value" => $args[1])));
        } else {
            if(!(in_array($args[0], array("true", "false")))) {
                $player->sendMessage($this->getWorlds()->getMessage("set.gamemode.notbool", array("key" => $args[0])));

                return true;
            }

            if(!($world = $this->getWorlds()->getWorldByName($player->getLevel()->getFolderName()))) {
                $player->sendMessage($this->getWorlds()->getMessage("general.world.notloaded"));

                return true;
            }

            $world->updateValue($args[0], $args[1]);

            $player->sendMessage($this->getWorlds()->getMessage("set.success", array("world" => $player->getLevel()->getFolderName(), "key" => $args[0], "value" => $args[1])));
        }

        return true;
    }
}
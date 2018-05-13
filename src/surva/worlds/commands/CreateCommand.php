<?php
/**
 * Created by PhpStorm.
 * User: jarne
 * Date: 01.10.17
 * Time: 11:45
 */

namespace surva\worlds\commands;

use pocketmine\level\generator\Flat;
use pocketmine\level\generator\hell\Nether;
use pocketmine\level\generator\normal\Normal;
use pocketmine\Player;

class CreateCommand extends CustomCommand {
    public function do(Player $player, array $args) {
        switch(count($args)) {
            case 1:
                if(!$this->getWorlds()->getServer()->generateLevel($args[0])) {
                    $player->sendMessage($this->getWorlds()->getMessage("create.failed"));
                }

                $player->sendMessage($this->getWorlds()->getMessage("create.success", array("name" => $args[0])));

                return true;
            case 2:
                switch(strtolower($args[1])) {
                    case "normal":
                        $generator = Normal::class;
                        break;
                    case "flat":
                        $generator = Flat::class;
                        break;
                    case "nether":
                        $generator = Nether::class;
                        break;
                    default:
                        $player->sendMessage(
                            $this->getWorlds()->getMessage("create.generator.notexist", array("name" => $args[1]))
                        );

                        $generator = Normal::class;
                        break;
                }

                if(!$this->getWorlds()->getServer()->generateLevel($args[0], null, $generator)) {
                    $player->sendMessage($this->getWorlds()->getMessage("create.failed"));
                }

                $player->sendMessage($this->getWorlds()->getMessage("create.success", array("name" => $args[0])));

                return true;
            default:
                return false;
        }
    }
}

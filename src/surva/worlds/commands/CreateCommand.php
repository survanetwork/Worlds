<?php
/**
 * Worlds | create world command
 */

namespace surva\worlds\commands;

use pocketmine\command\CommandSender;
use pocketmine\level\generator\Flat;
use pocketmine\level\generator\hell\Nether;
use pocketmine\level\generator\normal\Normal;

class CreateCommand extends CustomCommand {
    public function do(CommandSender $sender, array $args): bool {
        switch(count($args)) {
            case 1:
                if(!$this->getWorlds()->getServer()->generateLevel($args[0])) {
                    $sender->sendMessage($this->getWorlds()->getMessage("create.failed"));
                }

                $sender->sendMessage($this->getWorlds()->getMessage("create.success", array("name" => $args[0])));

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
                        $sender->sendMessage(
                            $this->getWorlds()->getMessage("create.generator.notexist", array("name" => $args[1]))
                        );

                        $generator = Normal::class;
                        break;
                }

                if(!$this->getWorlds()->getServer()->generateLevel($args[0], null, $generator)) {
                    $sender->sendMessage($this->getWorlds()->getMessage("create.failed"));
                }

                $sender->sendMessage($this->getWorlds()->getMessage("create.success", array("name" => $args[0])));

                return true;
            default:
                return false;
        }
    }
}

<?php
/**
 * Worlds | create world command
 */

namespace surva\worlds\commands;

use pocketmine\command\CommandSender;
use pocketmine\level\generator\GeneratorManager;

class CreateCommand extends CustomCommand {
    public function do(CommandSender $sender, array $args): bool {
        switch(count($args)) {
            case 1:
                if(!$this->getWorlds()->getServer()->generateLevel($args[0])) {
                    $sender->sendMessage($this->getWorlds()->getMessage("create.failed"));

                    return true;
                }

                $sender->sendMessage($this->getWorlds()->getMessage("create.success", array("name" => $args[0])));

                return true;
            case 2:
                $givenGenName = strtolower($args[1]);

                if(!in_array($givenGenName, GeneratorManager::getGeneratorList())) {
                    $sender->sendMessage(
                        $this->getWorlds()->getMessage("create.generator.notexist", array("name" => $args[1]))
                    );
                }

                $generator = GeneratorManager::getGenerator($givenGenName);

                if(!$this->getWorlds()->getServer()->generateLevel($args[0], null, $generator)) {
                    $sender->sendMessage($this->getWorlds()->getMessage("create.failed"));

                    return true;
                }

                $sender->sendMessage($this->getWorlds()->getMessage("create.success", array("name" => $args[0])));

                return true;
            default:
                return false;
        }
    }
}

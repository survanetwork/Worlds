<?php

/**
 * Worlds | create world command
 */

namespace surva\worlds\commands;

use pocketmine\command\CommandSender;
use pocketmine\world\generator\GeneratorManager;
use pocketmine\world\WorldCreationOptions;

class CreateCommand extends CustomCommand
{
    public function do(CommandSender $sender, array $args): bool
    {
        switch (count($args)) {
            case 1:
                if (
                    !$this->getWorlds()->getServer()->getWorldManager()->generateWorld(
                        $args[0],
                        WorldCreationOptions::create()
                    )
                ) {
                    $sender->sendMessage($this->getWorlds()->getMessage("create.failed"));

                    return true;
                }

                $sender->sendMessage($this->getWorlds()->getMessage("create.success", ["name" => $args[0]]));

                return true;
            case 2:
                $givenGenName = strtolower($args[1]);
                $gm           = GeneratorManager::getInstance();
                $worldOpt     = WorldCreationOptions::create();

                $gmEntry = $gm->getGenerator($givenGenName);

                if ($gmEntry === null) {
                    $sender->sendMessage(
                        $this->getWorlds()->getMessage("create.generator.not_exist", ["name" => $args[1]])
                    );

                    return true;
                }

                $worldOpt->setGeneratorClass($gmEntry->getGeneratorClass());

                if (!$this->getWorlds()->getServer()->getWorldManager()->generateWorld($args[0], $worldOpt)) {
                    $sender->sendMessage($this->getWorlds()->getMessage("create.failed"));

                    return true;
                }

                $sender->sendMessage($this->getWorlds()->getMessage("create.success", ["name" => $args[0]]));

                return true;
            default:
                return false;
        }
    }
}

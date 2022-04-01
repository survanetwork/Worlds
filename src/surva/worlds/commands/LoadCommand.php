<?php

/**
 * Worlds | load world command
 */

namespace surva\worlds\commands;

use pocketmine\command\CommandSender;
use pocketmine\world\WorldException;
use surva\worlds\logic\WorldActions;

class LoadCommand extends CustomCommand
{
    public function do(CommandSender $sender, array $args): bool
    {
        if (!(count($args) === 1)) {
            return false;
        }

        if ($this->getWorlds()->getServer()->getWorldManager()->isWorldLoaded($args[0])) {
            $sender->sendMessage($this->getWorlds()->getMessage("load.already", ["name" => $args[0]]));

            return true;
        }

        if (!WorldActions::worldPathExists($this->getWorlds(), $args[0])) {
            $sender->sendMessage($this->getWorlds()->getMessage("general.world.notexist", ["name" => $args[0]]));

            return true;
        }

        $upgradeFormat = $this->getWorlds()->getConfig()->get("autoupgradeformat", true);

        try {
            if (!($this->getWorlds()->getServer()->getWorldManager()->loadWorld($args[0], $upgradeFormat))) {
                $sender->sendMessage($this->getWorlds()->getMessage("load.failed"));

                return true;
            }
        } catch (WorldException $ex) {
            $sender->sendMessage($this->getWorlds()->getMessage("load.error", ["message" => $ex->getMessage()]));

            return true;
        }

        $sender->sendMessage($this->getWorlds()->getMessage("load.success", ["world" => $args[0]]));

        return true;
    }
}

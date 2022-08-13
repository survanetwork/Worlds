<?php

/**
 * Worlds | teleport command
 */

namespace surva\worlds\commands;

use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;

class TeleportCommand extends CustomCommand
{
    public function do(CommandSender $sender, array $args): bool
    {
        if (!($sender instanceof Player)) {
            $sender->sendMessage($this->getWorlds()->getMessage("general.command.in_game"));

            return true;
        }

        $player = $sender;

        if (!(count($args) === 1)) {
            return false;
        }

        $isAllowed = $sender->hasPermission("worlds.admin.teleport") or
        (
          $sender->hasPermission("worlds.teleport.general") and
          $sender->hasPermission("worlds.teleport.world." . strtolower($args[0]))
        );

        if (!$isAllowed) {
            $sender->sendMessage(
                $this->getWorlds()->getServer()->getLanguage()->translateString(
                    TextFormat::RED . "%commands.generic.permission"
                )
            );

            return true;
        }

        if (!($this->getWorlds()->getServer()->getWorldManager()->isWorldLoaded($args[0]))) {
            $player->sendMessage($this->getWorlds()->getMessage("general.world.not_loaded", ["name" => $args[0]]));

            return true;
        }

        $targetWorld = $this->getWorlds()->getServer()->getWorldManager()->getWorldByName($args[0]);

        if (!$player->teleport($targetWorld->getSafeSpawn())) {
            $player->sendMessage($this->getWorlds()->getMessage("teleport.failed"));

            return true;
        }

        $player->sendMessage($this->getWorlds()->getMessage("teleport.success", ["world" => $args[0]]));

        return true;
    }
}

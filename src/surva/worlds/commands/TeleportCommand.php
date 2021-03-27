<?php
/**
 * Worlds | teleport command
 */

namespace surva\worlds\commands;

use pocketmine\command\CommandSender;
use pocketmine\Player;
use pocketmine\utils\TextFormat;

class TeleportCommand extends CustomCommand {
    public function do(CommandSender $sender, array $args): bool {
        if(!($sender instanceof Player)) {
            $sender->sendMessage($this->getWorlds()->getMessage("general.command.ingame"));

            return true;
        }

        $player = $sender;

        if(!(count($args) === 1)) {
            return false;
        }

        $isAllowed = $sender->hasPermission("worlds.admin.teleport") OR
        (
            $sender->hasPermission("worlds.teleport.general") AND
            $sender->hasPermission("worlds.teleport.world." . strtolower($args[0]))
        );

        if(!$isAllowed) {
            $sender->sendMessage(
                $this->getWorlds()->getServer()->getLanguage()->translateString(
                    TextFormat::RED . "%commands.generic.permission"
                )
            );

            return true;
        }

        if(!($this->getWorlds()->getServer()->isLevelLoaded($args[0]))) {
            $player->sendMessage($this->getWorlds()->getMessage("general.world.notloaded", array("name" => $args[0])));

            return true;
        }

        $targetWorld = $this->getWorlds()->getServer()->getLevelByName($args[0]);

        if(!$player->teleport($targetWorld->getSafeSpawn())) {
            $player->sendMessage($this->getWorlds()->getMessage("teleport.failed"));

            return true;
        }

        $player->sendMessage($this->getWorlds()->getMessage("teleport.success", array("world" => $args[0])));

        return true;
    }
}

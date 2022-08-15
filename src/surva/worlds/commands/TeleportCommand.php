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
        $otherPlayer = false;

        switch (count($args)) {
            case 1:
                if (!($sender instanceof Player)) {
                    $sender->sendMessage($this->getWorlds()->getMessage("general.command.in_game"));

                    return true;
                }

                $player = $sender;

                $isAllowed = $sender->hasPermission("worlds.admin.teleport.self") or
                (
                  $sender->hasPermission("worlds.teleport.general") and
                  $sender->hasPermission("worlds.teleport.world." . strtolower($args[0]))
                );
                break;
            case 2:
                $plName = array_shift($args);
                $player = $this->getWorlds()->getServer()->getPlayerExact($plName);

                if ($player === null) {
                    $sender->sendMessage($this->getWorlds()->getMessage("teleport.error_code.no_player"));

                    return true;
                }

                $otherPlayer = true;

                $isAllowed = $sender->hasPermission("worlds.admin.teleport.others");
                break;
            default:
                return false;
        }

        if (!$isAllowed) {
            $sender->sendMessage(
                $this->getWorlds()->getServer()->getLanguage()->translateString(
                    TextFormat::RED . "%commands.generic.permission"
                )
            );

            return true;
        }

        if (!($this->getWorlds()->getServer()->getWorldManager()->isWorldLoaded($args[0]))) {
            $sender->sendMessage($this->getWorlds()->getMessage("general.world.not_loaded", ["name" => $args[0]]));

            if ($otherPlayer) {
                $player->sendMessage($this->getWorlds()->getMessage("general.world.not_loaded", ["name" => $args[0]]));
            }

            return true;
        }

        $targetWorld = $this->getWorlds()->getServer()->getWorldManager()->getWorldByName($args[0]);

        if (!$player->teleport($targetWorld->getSafeSpawn())) {
            $sender->sendMessage($this->getWorlds()->getMessage("teleport.error_code.teleport_failed"));

            if ($otherPlayer) {
                $player->sendMessage($this->getWorlds()->getMessage("teleport.error_code.teleport_failed"));
            }

            return true;
        }

        if ($otherPlayer && isset($plName)) {
            $sender->sendMessage(
                $this->getWorlds()->getMessage("teleport.success_other", ["player" => $plName, "world" => $args[0]])
            );
            $player->sendMessage($this->getWorlds()->getMessage("teleport.success", ["world" => $args[0]]));
        } else {
            $sender->sendMessage($this->getWorlds()->getMessage("teleport.success", ["world" => $args[0]]));
        }

        return true;
    }
}

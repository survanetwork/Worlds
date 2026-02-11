<?php

/**
 * Worlds | teleport command, can be used to teleport the sender
 * or another player to a world
 */

namespace surva\worlds\commands;

use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use surva\worlds\utils\Messages;

class TeleportCommand extends CustomCommand
{
    /**
     * @inheritDoc
     */
    public function do(CommandSender $sender, array $args): bool
    {
        $otherPlayer = false;

        $messages = new Messages($this->getWorlds(), $sender);

        switch (count($args)) {
            case 1:
                if (!($sender instanceof Player)) {
                    $sender->sendMessage($messages->getMessage("general.command.in_game"));

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
                    $sender->sendMessage($messages->getMessage("teleport.error_code.no_player"));

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
            $sender->sendMessage($messages->getMessage("general.world.not_loaded", ["name" => $args[0]]));

            if ($otherPlayer) {
                $player->sendMessage($messages->getMessage("general.world.not_loaded", ["name" => $args[0]]));
            }

            return true;
        }

        $targetWorld = $this->getWorlds()->getServer()->getWorldManager()->getWorldByName($args[0]);
        if ($targetWorld === null) {
            $sender->sendMessage($messages->getMessage("general.world.not_loaded", ["name" => $args[0]]));

            if ($otherPlayer) {
                $player->sendMessage($messages->getMessage("general.world.not_loaded", ["name" => $args[0]]));
            }

            return true;
        }

        if (!$player->teleport($targetWorld->getSafeSpawn())) {
            $sender->sendMessage($messages->getMessage("teleport.error_code.teleport_failed"));

            if ($otherPlayer) {
                $player->sendMessage($messages->getMessage("teleport.error_code.teleport_failed"));
            }

            return true;
        }

        if ($otherPlayer && isset($plName)) {
            $sender->sendMessage(
                $messages->getMessage("teleport.success_other", ["player" => $plName, "world" => $args[0]])
            );
            $player->sendMessage($messages->getMessage("teleport.success", ["world" => $args[0]]));
        } else {
            $sender->sendMessage($messages->getMessage("teleport.success", ["world" => $args[0]]));
        }

        return true;
    }
}

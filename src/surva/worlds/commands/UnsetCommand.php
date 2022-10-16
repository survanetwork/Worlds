<?php

/**
 * Worlds | unset parameter command
 */

namespace surva\worlds\commands;

use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use surva\worlds\form\WorldSettingsForm;
use surva\worlds\logic\WorldActions;
use surva\worlds\types\exception\ConfigSaveException;
use surva\worlds\types\exception\ValueNotExistException;
use surva\worlds\utils\Messages;

class UnsetCommand extends CustomCommand
{
    public function do(CommandSender $sender, array $args): bool
    {
        $messages = new Messages($this->getWorlds(), $sender);

        if (!($sender instanceof Player)) {
            $sender->sendMessage($messages->getMessage("general.command.in_game"));

            return true;
        }

        $player = $sender;

        $folderName = $player->getWorld()->getFolderName();

        if (!($world = $this->getWorlds()->getWorldByName($folderName))) {
            $sender->sendMessage($messages->getMessage("general.world.not_loaded", ["name" => $folderName]));

            return true;
        }

        if (count($args) === 0) {
            $wsForm = new WorldSettingsForm(
                $this->getWorlds(),
                $folderName,
                $world,
                new Messages($this->getWorlds(), $sender)
            );

            $player->sendForm($wsForm);

            return true;
        }

        if (!(count($args) === 1)) {
            return false;
        }

        if (!WorldActions::isValidFlag($args[0])) {
            return false;
        }

        try {
            $world->removeValue($args[0]);
        } catch (ConfigSaveException | ValueNotExistException $e) {
            $player->sendMessage($messages->getMessage("general.config.save_error"));

            return true;
        }

        $player->sendMessage(
            $messages->getMessage(
                "unset.success",
                ["world" => $player->getWorld()->getFolderName(), "key" => $args[0]]
            )
        );

        return true;
    }
}

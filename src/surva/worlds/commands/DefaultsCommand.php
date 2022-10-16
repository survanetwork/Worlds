<?php

/**
 * Worlds | defaults set / unset command
 */

namespace surva\worlds\commands;

use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use surva\worlds\form\DefaultSettingsForm;
use surva\worlds\logic\WorldActions;
use surva\worlds\types\exception\ConfigSaveException;
use surva\worlds\types\exception\ValueNotExistException;
use surva\worlds\utils\Flags;
use surva\worlds\utils\Messages;

class DefaultsCommand extends SetCommand
{
    private Messages $messages;

    public function do(CommandSender $sender, array $args): bool
    {
        $this->messages = new Messages($this->getWorlds(), $sender);

        if (!($sender instanceof Player)) {
            $sender->sendMessage($this->messages->getMessage("general.command.in_game"));

            return true;
        }

        $player = $sender;

        $defaults = $this->getWorlds()->getDefaults();

        if (count($args) === 0) {
            $dfForm = new DefaultSettingsForm($this->getWorlds(), $defaults, new Messages($this->getWorlds(), $sender));

            $player->sendForm($dfForm);

            return true;
        }

        switch ($args[0]) {
            case "show":
                $msg = $this->messages->getMessage("defaults.list.info") . "\n\n";

                return $this->showFlagValues($player, $defaults, $msg, Flags::AVAILABLE_DEFAULT_FLAGS);
            case "set":
                if (count($args) < 3) {
                    return false;
                }

                if (!WorldActions::isValidFlag($args[1])) {
                    return false;
                }

                $flagType = WorldActions::getFlagType($args[1]);

                if ($flagType === Flags::TYPE_CONTROL_LIST && $args[2] === "list") {
                    if (count($args) < 4) {
                        return false;
                    }

                    array_shift($args);

                    if ($subCommand = $this->getControlListSubCommand($args[2], $defaults, $args[0])) {
                        return $subCommand->execute($sender, $this->getName(), $args);
                    }

                    return false;
                }

                if (!(count($args) === 3)) {
                    return false;
                }

                switch ($flagType) {
                    case Flags::TYPE_PERMISSION:
                        $player->sendMessage($this->messages->getMessage("set.permission.not_default"));

                        return true;
                    case Flags::TYPE_GAME_MODE:
                        return $this->setGameModeSub($player, $args[2], $defaults);
                    case Flags::TYPE_BOOL:
                        return $this->setBoolSub($player, $args[1], $args[2], $defaults);
                    case Flags::TYPE_CONTROL_LIST:
                        return $this->setControlListSub($player, $args[1], $args[2], $defaults);
                }

                return false;
            case "unset":
                if (!(count($args) === 2)) {
                    return false;
                }

                if (!WorldActions::isValidFlag($args[1])) {
                    return false;
                }

                try {
                    $defaults->removeValue($args[1]);
                } catch (ConfigSaveException | ValueNotExistException $e) {
                    $player->sendMessage($this->messages->getMessage("general.config.save_error"));

                    return true;
                }

                $player->sendMessage(
                    $this->messages->getMessage(
                        "defaults.unset.success",
                        ["key" => $args[1]]
                    )
                );

                return true;
            default:
                return false;
        }
    }

    protected function sendSuccessMessage(Player $player, string $key, string $val): bool
    {
        $player->sendMessage(
            $this->messages->getMessage(
                "defaults.set.success",
                ["key" => $key, "value" => $val]
            )
        );

        return true;
    }
}

<?php

/**
 * Worlds | defaults set / unset command
 */

namespace surva\worlds\commands;

use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use surva\worlds\form\DefaultSettingsForm;
use surva\worlds\logic\WorldActions;
use surva\worlds\utils\Flags;

class DefaultsCommand extends SetCommand
{
    public function do(CommandSender $sender, array $args): bool
    {
        if (!($sender instanceof Player)) {
            $sender->sendMessage($this->getWorlds()->getMessage("general.command.ingame"));

            return true;
        }

        $player = $sender;

        $defaults = $this->getWorlds()->getDefaults();

        if (count($args) === 0) {
            $dfForm = new DefaultSettingsForm($this->getWorlds(), $defaults);

            $player->sendForm($dfForm);

            return true;
        }

        switch ($args[0]) {
            case "legacy":
                $msg = $this->getWorlds()->getMessage("defaults.list.info") . "\n\n";

                foreach (Flags::AVAILABLE_DEFAULT_FLAGS as $flagName => $flagDetails) {
                    $flagStr = $this->getWorlds()->getMessage("forms.world.params." . $flagName);

                    $flagVal = match ($flagDetails["type"]) {
                        Flags::TYPE_BOOL => $this->formatBool($defaults->loadValue($flagName)),
                        Flags::TYPE_WHITEBLACKLIST => $this->formatWhiteBlack($defaults->loadValue($flagName)),
                        Flags::TYPE_GAMEMODE => $this->formatGameMode($defaults->loadValue($flagName)),
                        default => "null",
                    };

                    $msg .= "§e" . $flagStr . " (§7" . $flagName . "§e): " . $flagVal . "\n";
                }

                $player->sendMessage($msg);

                return true;
            case "set":
                if (!(count($args) === 3)) {
                    return false;
                }

                if (!WorldActions::isValidFlag($args[1])) {
                    return false;
                }

                $flagType = WorldActions::getFlagType($args[0]);

                switch ($flagType) {
                    case Flags::TYPE_PERMISSION:
                        $player->sendMessage($this->getWorlds()->getMessage("set.permission.notdefault"));

                        return true;
                    case Flags::TYPE_GAMEMODE:
                        return $this->setGameModeSub($player, $args[2], $defaults);
                    case Flags::TYPE_BOOL:
                        return $this->setBoolSub($player, $args[1], $args[2], $defaults);
                    case Flags::TYPE_WHITEBLACKLIST:
                        return $this->setWhiteBlackFlagSub($player, $args[1], $args[2], $defaults);
                }

                return false;
            case "unset":
                if (!(count($args) === 2)) {
                    return false;
                }

                if (!WorldActions::isValidFlag($args[1])) {
                    return false;
                }

                $defaults->removeValue($args[1]);

                $player->sendMessage(
                    $this->getWorlds()->getMessage(
                        "defaults.unset.success",
                        ["key" => $args[1]]
                    )
                );

                return true;
            default:
                return false;
        }
    }
}

<?php
/**
 * Worlds | defaults set / unset command
 */

namespace surva\worlds\commands;

use pocketmine\command\CommandSender;
use pocketmine\player\GameMode;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use surva\worlds\form\DefaultSettingsForm;
use surva\worlds\logic\WorldActions;
use surva\worlds\utils\Flags;

class DefaultsCommand extends CustomCommand
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
                    $flagVal = "null";

                    switch ($flagDetails["type"]) {
                        case Flags::TYPE_BOOL:
                            $flagVal = $this->formatBool($defaults->loadValue($flagName));
                            break;
                        case Flags::TYPE_GAMEMODE:
                            $flagVal = $this->formatGamemode($defaults->loadValue($flagName));
                            break;
                    }

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

                if ($args[1] === "permission") {
                    $player->sendMessage($this->getWorlds()->getMessage("set.permission.notdefault"));

                    return true;
                } elseif ($args[1] === "gamemode") {
                    $gm = GameMode::fromString($args[2]);

                    if ($gm === null) {
                        $player->sendMessage($this->getWorlds()->getMessage("set.gamemode.notexist"));

                        return true;
                    }

                    $defaults->updateValue("gamemode", $gm->id());

                    $player->sendMessage(
                      $this->getWorlds()->getMessage(
                        "defaults.set.success",
                        ["key" => $args[1], "value" => $args[2]]
                      )
                    );
                } else {
                    if (!(in_array($args[2], ["true", "false"]))) {
                        $player->sendMessage($this->getWorlds()->getMessage("set.notbool", ["key" => $args[1]]));

                        return true;
                    }

                    $defaults->updateValue($args[1], $args[2]);

                    $player->sendMessage(
                      $this->getWorlds()->getMessage(
                        "defaults.set.success",
                        ["key" => $args[1], "value" => $args[2]]
                      )
                    );
                }

                return true;
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

    /**
     * Format a text for showing its value
     *
     * @param  string|null  $value
     *
     * @return string
     */
    private function formatText(?string $value): string
    {
        if ($value === null) {
            return $this->getWorlds()->getMessage("set.list.notset");
        }

        return TextFormat::WHITE . $value;
    }

    /**
     * Format a gamemode for showing its value
     *
     * @param  int|null  $value
     *
     * @return string
     */
    private function formatGamemode(?int $value): string
    {
        if ($value === null) {
            return $this->getWorlds()->getMessage("set.list.notset");
        }

        return $this->getWorlds()->getServer()->getLanguage()->translateString(
          TextFormat::WHITE . GameMode::fromString($value)->getEnglishName()
        );
    }

    /**
     * Format a boolean for showing its value
     *
     * @param  bool|null  $value
     *
     * @return string
     */
    private function formatBool(?bool $value): string
    {
        if ($value === true) {
            return TextFormat::GREEN . "true";
        } elseif ($value === false) {
            return TextFormat::RED . "false";
        } else {
            return $this->getWorlds()->getMessage("set.list.notset");
        }
    }

}

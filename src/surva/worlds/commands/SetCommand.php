<?php
/**
 * Worlds | set parameter command
 */

namespace surva\worlds\commands;

use pocketmine\command\CommandSender;
use pocketmine\player\GameMode;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use surva\worlds\form\WorldSettingsForm;
use surva\worlds\logic\WorldActions;
use surva\worlds\utils\Flags;

class SetCommand extends CustomCommand
{

    public function do(CommandSender $sender, array $args): bool
    {
        if (!($sender instanceof Player)) {
            $sender->sendMessage($this->getWorlds()->getMessage("general.command.ingame"));

            return true;
        }

        $player = $sender;

        $folderName = $player->getWorld()->getFolderName();

        if (!($world = $this->getWorlds()->getWorldByName($folderName))) {
            $sender->sendMessage($this->getWorlds()->getMessage("general.world.notloaded", ["name" => $folderName]));

            return true;
        }

        if (count($args) === 0) {
            $wsForm = new WorldSettingsForm($this->getWorlds(), $folderName, $world);

            $player->sendForm($wsForm);

            return true;
        }

        if ($args[0] === "legacy") {
            $msg = $this->getWorlds()->getMessage("set.list.info", ["name" => $folderName]) . "\n\n";

            foreach (Flags::AVAILABLE_WORLD_FLAGS as $flagName => $flagDetails) {
                $flagStr = $this->getWorlds()->getMessage("forms.world.params." . $flagName);

                $flagVal = match ($flagDetails["type"]) {
                    Flags::TYPE_BOOL => $this->formatBool($world->loadValue($flagName)),
                    Flags::TYPE_PERMISSION => $this->formatText($world->loadValue($flagName)),
                    Flags::TYPE_GAMEMODE => $this->formatGameMode($world->loadValue($flagName)),
                    default => "null",
                };

                $msg .= "§e" . $flagStr . " (§7" . $flagName . "§e): " . $flagVal . "\n";
            }

            $player->sendMessage($msg);

            return true;
        }

        if (!(count($args) === 2)) {
            return false;
        }

        if (!WorldActions::isValidFlag($args[0])) {
            return false;
        }

        if ($args[0] === "permission") {
            if ($this->getWorlds()->getServer()->getWorldManager()->getDefaultWorld()->getFolderName()
                === $folderName
            ) {
                $player->sendMessage($this->getWorlds()->getMessage("set.permission.notdefault"));

                return true;
            }

            $world->updateValue($args[0], $args[1]);
        } elseif ($args[0] === "gamemode") {
            $gm = GameMode::fromString($args[1]);

            if ($gm === null) {
                $player->sendMessage($this->getWorlds()->getMessage("set.gamemode.notexist"));

                return true;
            }

            $world->updateValue("gamemode", WorldActions::getGameModeId($gm));
        } else {
            if (!(in_array($args[1], ["true", "false"]))) {
                $player->sendMessage($this->getWorlds()->getMessage("set.notbool", ["key" => $args[0]]));

                return true;
            }

            $world->updateValue($args[0], $args[1]);
        }

        $player->sendMessage(
          $this->getWorlds()->getMessage(
            "set.success",
            ["world" => $player->getWorld()->getFolderName(), "key" => $args[0], "value" => $args[1]]
          )
        );

        return true;
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
     * Format a game mode for showing its value
     *
     * @param  int|null  $value
     *
     * @return string
     */
    private function formatGameMode(?int $value): string
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

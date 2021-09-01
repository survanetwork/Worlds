<?php
/**
 * Worlds | set parameter command
 */

namespace surva\worlds\commands;

use pocketmine\command\CommandSender;
use pocketmine\Player;
use pocketmine\Server;
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

        $folderName = $player->getLevel()->getFolderName();

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
                $flagVal = "null";

                switch ($flagDetails["type"]) {
                    case Flags::TYPE_BOOL:
                        $flagVal = $this->formatBool($world->loadValue($flagName));
                        break;
                    case Flags::TYPE_PERMISSION:
                        $flagVal = $this->formatText($world->loadValue($flagName));
                        break;
                    case Flags::TYPE_GAMEMODE:
                        $flagVal = $this->formatGamemode($world->loadValue($flagName));
                        break;
                }

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
            if ($this->getWorlds()->getServer()->getDefaultLevel()->getFolderName() === $folderName) {
                $player->sendMessage($this->getWorlds()->getMessage("set.permission.notdefault"));

                return true;
            }

            $world->updateValue($args[0], $args[1]);

            $player->sendMessage(
              $this->getWorlds()->getMessage(
                "set.success",
                ["world" => $player->getLevel()->getFolderName(), "key" => $args[0], "value" => $args[1]]
              )
            );
        } elseif ($args[0] === "gamemode") {
            if (($args[1] = Server::getGamemodeFromString($args[1])) === -1) {
                $player->sendMessage($this->getWorlds()->getMessage("set.gamemode.notexist"));

                return true;
            }

            $world->updateValue($args[0], $args[1]);

            $player->sendMessage(
              $this->getWorlds()->getMessage(
                "set.success",
                ["world" => $player->getLevel()->getFolderName(), "key" => $args[0], "value" => $args[1]]
              )
            );
        } else {
            if (!(in_array($args[1], ["true", "false"]))) {
                $player->sendMessage($this->getWorlds()->getMessage("set.notbool", ["key" => $args[0]]));

                return true;
            }

            $world->updateValue($args[0], $args[1]);

            $player->sendMessage(
              $this->getWorlds()->getMessage(
                "set.success",
                ["world" => $player->getLevel()->getFolderName(), "key" => $args[0], "value" => $args[1]]
              )
            );
        }

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
          TextFormat::WHITE . Server::getGamemodeString($value)
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

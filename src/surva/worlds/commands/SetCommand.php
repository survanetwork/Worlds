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
use surva\worlds\types\World;
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
                    Flags::TYPE_CONTROL_LIST => $this->formatControlList($world->loadValue($flagName)),
                    Flags::TYPE_PERMISSION => $this->formatText($world->loadValue($flagName)),
                    Flags::TYPE_GAME_MODE => $this->formatGameMode($world->loadValue($flagName)),
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

        $flagType = WorldActions::getFlagType($args[0]);

        return match ($flagType) {
            Flags::TYPE_PERMISSION => $this->setPermissionSub($player, $args[0], $args[1], $world, $folderName),
            Flags::TYPE_GAME_MODE => $this->setGameModeSub($player, $args[1], $world),
            Flags::TYPE_BOOL => $this->setBoolSub($player, $args[0], $args[1], $world),
            Flags::TYPE_CONTROL_LIST => $this->setControlListSub($player, $args[0], $args[1], $world),
            default => false,
        };
    }

    /**
     * Sub command to set the permission flag
     *
     * @param  \pocketmine\player\Player  $player
     * @param  string  $key
     * @param  string  $val
     * @param  \surva\worlds\types\World  $world
     * @param  string  $folderName
     *
     * @return bool
     */
    protected function setPermissionSub(
        Player $player,
        string $key,
        string $val,
        World $world,
        string $folderName
    ): bool {
        if (
            $this->getWorlds()->getServer()->getWorldManager()->getDefaultWorld()->getFolderName()
            === $folderName
        ) {
            $player->sendMessage($this->getWorlds()->getMessage("set.permission.notdefault"));

            return true;
        }

        $world->updateValue($key, $val);

        return $this->sendSuccessMessage($player, $key, $val);
    }

    /**
     * Sub command to set game mode flag
     *
     * @param  \pocketmine\player\Player  $player
     * @param  string  $gmArg
     * @param  \surva\worlds\types\World  $world
     *
     * @return bool
     */
    protected function setGameModeSub(Player $player, string $gmArg, World $world): bool
    {
        $gm = GameMode::fromString($gmArg);

        if ($gm === null) {
            $player->sendMessage($this->getWorlds()->getMessage("set.gamemode.notexist"));

            return true;
        }

        $world->updateValue(Flags::FLAG_GAME_MODE, WorldActions::getGameModeId($gm));

        return $this->sendSuccessMessage($player, Flags::FLAG_GAME_MODE, $gmArg);
    }

    /**
     * Sub command to set bool flag
     *
     * @param  \pocketmine\player\Player  $player
     * @param  string  $key
     * @param  string  $val
     * @param  \surva\worlds\types\World  $world
     *
     * @return bool
     */
    protected function setBoolSub(Player $player, string $key, string $val, World $world): bool
    {
        if (!(in_array($val, Flags::VALID_BOOL_VALUES))) {
            $player->sendMessage($this->getWorlds()->getMessage("set.notbool", ["key" => $key]));

            return true;
        }

        $world->updateValue($key, $val);

        return $this->sendSuccessMessage($player, $key, $val);
    }

    /**
     * Sub command to set control list flag
     *
     * @param  \pocketmine\player\Player  $player
     * @param  string  $key
     * @param  string  $val
     * @param  \surva\worlds\types\World  $world
     *
     * @return bool
     */
    protected function setControlListSub(Player $player, string $key, string $val, World $world): bool
    {
        if (!(in_array($val, Flags::VALID_CONTROL_LIST_VALUES))) {
            $player->sendMessage($this->getWorlds()->getMessage("set.notcontrollist", ["key" => $key]));

            return true;
        }

        $world->updateValue($key, $val);

        return $this->sendSuccessMessage($player, $key, $val);
    }

    /**
     * Send set command success message
     *
     * @param  \pocketmine\player\Player  $player
     * @param  string  $key
     * @param  string  $val
     *
     * @return bool
     */
    protected function sendSuccessMessage(Player $player, string $key, string $val): bool
    {
        /*else {
                    $player->sendMessage(
                        $this->getWorlds()->getMessage(
                            "defaults.set.success",
                            ["key" => $args[1], "value" => $args[2]]
                        )
                    );
                }*/ // TODO: add case for defaults

        $player->sendMessage(
            $this->getWorlds()->getMessage(
                "set.success",
                ["world" => $player->getWorld()->getFolderName(), "key" => $key, "value" => $val]
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
    protected function formatText(?string $value): string
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
    protected function formatGameMode(?int $value): string
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
    protected function formatBool(?bool $value): string
    {
        if ($value === true) {
            return TextFormat::GREEN . $this->getWorlds()->getMessage("forms.world.options.true");
        } elseif ($value === false) {
            return TextFormat::RED . $this->getWorlds()->getMessage("forms.world.options.false");
        } else {
            return $this->getWorlds()->getMessage("set.list.notset");
        }
    }

    /**
     * Format a control list flag for showing its value
     *
     * @param  string|null  $value
     *
     * @return string
     */
    protected function formatControlList(?string $value): string
    {
        return match ($value) {
            Flags::VALUE_TRUE => TextFormat::GREEN . $this->getWorlds()->getMessage("forms.world.options.true"),
            Flags::VALUE_FALSE => TextFormat::RED . $this->getWorlds()->getMessage("forms.world.options.false"),
            Flags::VALUE_WHITELISTED => TextFormat::WHITE . $this->getWorlds()->getMessage("forms.world.options.white"),
            Flags::VALUE_BLACKLISTED => TextFormat::BLACK . $this->getWorlds()->getMessage("forms.world.options.black"),
            default => $this->getWorlds()->getMessage("set.list.notset")
        };
    }
}

<?php

/**
 * Worlds | command to set world config values, opens
 * a form GUI if no arguments are provided, or supports
 * setting/unsetting values using the command
 */

namespace surva\worlds\commands;

use pocketmine\command\CommandSender;
use pocketmine\player\GameMode;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use surva\worlds\commands\controllist\ControlListCommand;
use surva\worlds\commands\controllist\ListAddCommand;
use surva\worlds\commands\controllist\ListRemoveCommand;
use surva\worlds\commands\controllist\ListResetCommand;
use surva\worlds\commands\controllist\ListShowCommand;
use surva\worlds\form\WorldSettingsForm;
use surva\worlds\logic\WorldActions;
use surva\worlds\types\exception\ConfigSaveException;
use surva\worlds\types\World;
use surva\worlds\utils\Flags;
use surva\worlds\utils\Messages;

class SetCommand extends CustomCommand
{
    private Messages $messages;

    /**
     * @inheritDoc
     */
    public function do(CommandSender $sender, array $args): bool
    {
        $this->messages = new Messages($this->getWorlds(), $sender);

        if (!($sender instanceof Player)) {
            $sender->sendMessage($this->messages->getMessage("general.command.in_game"));

            return true;
        }

        $player = $sender;

        $folderName = $player->getWorld()->getFolderName();

        if (!($world = $this->getWorlds()->getWorldByName($folderName))) {
            $sender->sendMessage($this->messages->getMessage("general.world.not_loaded", ["name" => $folderName]));

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

        if ($args[0] === "show") {
            $msg = $this->messages->getMessage("set.list.info", ["name" => $folderName]) . "\n\n";

            return $this->showFlagValues($player, $world, $msg, Flags::AVAILABLE_WORLD_FLAGS);
        }

        if (count($args) < 2) {
            return false;
        }

        if (!WorldActions::isValidFlag($args[0])) {
            return false;
        }

        $flagType = WorldActions::getFlagType($args[0]);

        if ($flagType === Flags::TYPE_CONTROL_LIST && $args[1] === "list") {
            if (count($args) < 3) {
                return false;
            }

            if ($subCommand = $this->getControlListSubCommand($args[2], $world, $args[0])) {
                return $subCommand->execute($sender, $this->getName(), $args);
            }

            return false;
        }

        if (!(count($args) === 2)) {
            return false;
        }

        return match ($flagType) {
            Flags::TYPE_PERMISSION => $this->setPermissionSub($player, $args[0], $args[1], $world, $folderName),
            Flags::TYPE_GAME_MODE => $this->setGameModeSub($player, $args[1], $world),
            Flags::TYPE_BOOL => $this->setBoolSub($player, $args[0], $args[1], $world),
            Flags::TYPE_CONTROL_LIST => $this->setControlListSub($player, $args[0], $args[1], $world),
            default => false,
        };
    }

    /**
     * Show flag values of the world to player in chat
     *
     * @param Player $player
     * @param World $world
     * @param string $msg
     * @param array<string, array<string, mixed>> $availableFlags
     *
     * @return bool
     */
    protected function showFlagValues(Player $player, World $world, string $msg, array $availableFlags): bool
    {
        foreach ($availableFlags as $flagName => $flagDetails) {
            $flagStr = $this->messages->getMessage("forms.world.params." . $flagName);

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

    /**
     * Get sub command for control list features
     *
     * @param string $name
     * @param World $world
     * @param string $flagName
     *
     * @return ControlListCommand|null
     */
    protected function getControlListSubCommand(string $name, World $world, string $flagName): ?ControlListCommand
    {
        return match ($name) {
            "add" => new ListAddCommand($this->getWorlds(), $world, $flagName, "add", "worlds.admin.set"),
            "remove" => new ListRemoveCommand($this->getWorlds(), $world, $flagName, "remove", "worlds.admin.set"),
            "reset" => new ListResetCommand($this->getWorlds(), $world, $flagName, "reset", "worlds.admin.set"),
            "show" => new ListShowCommand($this->getWorlds(), $world, $flagName, "show", "worlds.admin.set"),
            default => null,
        };
    }

    /**
     * Sub command to set the permission flag
     *
     * @param Player $player
     * @param string $key
     * @param string $val
     * @param World $world
     * @param string $folderName
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
            $this->getWorlds()->getServer()->getWorldManager()->getDefaultWorld()?->getFolderName()
            === $folderName
        ) {
            $player->sendMessage($this->messages->getMessage("set.permission.not_default"));

            return true;
        }

        try {
            $world->updateValue($key, $val);
        } catch (ConfigSaveException $e) {
            $player->sendMessage($this->messages->getMessage("general.config.save_error"));

            return true;
        }

        return $this->sendSuccessMessage($player, $key, $val);
    }

    /**
     * Sub command to set game mode flag
     *
     * @param Player $player
     * @param string $gmArg
     * @param World $world
     *
     * @return bool
     */
    protected function setGameModeSub(Player $player, string $gmArg, World $world): bool
    {
        $gm = GameMode::fromString($gmArg);
        if ($gm === null) {
            $player->sendMessage($this->messages->getMessage("set.gamemode.not_exist"));

            return true;
        }

        $gmId = WorldActions::getGameModeId($gm);
        if ($gmId === null) {
            $player->sendMessage($this->messages->getMessage("set.gamemode.not_exist"));

            return true;
        }

        try {
            $world->updateValue(Flags::FLAG_GAME_MODE, $gmId);
        } catch (ConfigSaveException $e) {
            $player->sendMessage($this->messages->getMessage("general.config.save_error"));

            return true;
        }

        return $this->sendSuccessMessage($player, Flags::FLAG_GAME_MODE, $gmArg);
    }

    /**
     * Sub command to set bool flag
     *
     * @param Player $player
     * @param string $key
     * @param string $val
     * @param World $world
     *
     * @return bool
     */
    protected function setBoolSub(Player $player, string $key, string $val, World $world): bool
    {
        if (!(in_array($val, Flags::VALID_BOOL_VALUES))) {
            $player->sendMessage($this->messages->getMessage("set.not_bool", ["key" => $key]));

            return true;
        }

        try {
            $world->updateValue($key, $val);
        } catch (ConfigSaveException $e) {
            $player->sendMessage($this->messages->getMessage("general.config.save_error"));

            return true;
        }

        return $this->sendSuccessMessage($player, $key, $val);
    }

    /**
     * Sub command to set control list flag
     *
     * @param Player $player
     * @param string $key
     * @param string $val
     * @param World $world
     *
     * @return bool
     */
    protected function setControlListSub(Player $player, string $key, string $val, World $world): bool
    {
        if (!(in_array($val, Flags::VALID_CONTROL_LIST_VALUES))) {
            $player->sendMessage($this->messages->getMessage("set.not_controllist", ["key" => $key]));

            return true;
        }

        try {
            $world->updateValue($key, $val);
        } catch (ConfigSaveException $e) {
            $player->sendMessage($this->messages->getMessage("general.config.save_error"));

            return true;
        }

        return $this->sendSuccessMessage($player, $key, $val);
    }

    /**
     * Send set command success message
     *
     * @param Player $player
     * @param string $key
     * @param string $val
     *
     * @return bool
     */
    protected function sendSuccessMessage(Player $player, string $key, string $val): bool
    {
        $player->sendMessage(
            $this->messages->getMessage(
                "set.success",
                ["world" => $player->getWorld()->getFolderName(), "key" => $key, "value" => $val]
            )
        );

        return true;
    }

    /**
     * Format a text for showing its value
     *
     * @param string|null $value
     *
     * @return string
     */
    protected function formatText(?string $value): string
    {
        if ($value === null) {
            return $this->messages->getMessage("set.list.not_set");
        }

        return TextFormat::WHITE . $value;
    }

    /**
     * Format a game mode for showing its value
     *
     * @param int|null $value
     *
     * @return string
     */
    protected function formatGameMode(?int $value): string
    {
        if ($value === null) {
            return $this->messages->getMessage("set.list.not_set");
        }

        $gm = GameMode::fromString((string) $value);
        $gmName = $gm ? $gm->getEnglishName() : $value;

        return $this->getWorlds()->getServer()->getLanguage()->translateString(
            TextFormat::WHITE . $gmName
        );
    }

    /**
     * Format a boolean for showing its value
     *
     * @param bool|null $value
     *
     * @return string
     */
    protected function formatBool(?bool $value): string
    {
        if ($value === true) {
            return TextFormat::GREEN . $this->messages->getMessage("forms.world.options.true");
        } elseif ($value === false) {
            return TextFormat::RED . $this->messages->getMessage("forms.world.options.false");
        } else {
            return $this->messages->getMessage("set.list.not_set");
        }
    }

    /**
     * Format a control list flag for showing its value
     *
     * @param string|null $value
     *
     * @return string
     */
    protected function formatControlList(?string $value): string
    {
        return match ($value) {
            Flags::VALUE_TRUE => TextFormat::GREEN . $this->messages->getMessage("forms.world.options.true"),
            Flags::VALUE_FALSE => TextFormat::RED . $this->messages->getMessage("forms.world.options.false"),
            Flags::VALUE_WHITELISTED => TextFormat::WHITE . $this->messages->getMessage("forms.world.options.white"),
            Flags::VALUE_BLACKLISTED => TextFormat::BLACK . $this->messages->getMessage("forms.world.options.black"),
            default => $this->messages->getMessage("set.list.not_set")
        };
    }
}

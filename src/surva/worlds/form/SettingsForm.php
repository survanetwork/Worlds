<?php

/**
 * Worlds | settings form class
 */

namespace surva\worlds\form;

use pocketmine\form\Form;
use pocketmine\player\GameMode;
use pocketmine\player\Player;
use surva\worlds\logic\WorldActions;
use surva\worlds\types\World;
use surva\worlds\utils\Flags;
use surva\worlds\Worlds;

abstract class SettingsForm implements Form
{
    private Worlds $worlds;
    private World $storage;

    private string $type = "custom_form";
    protected string $title;
    protected array $content;

    public function __construct(Worlds $wsInstance, World $storage)
    {
        $this->worlds  = $wsInstance;
        $this->storage = $storage;
    }

    /**
     * Return JSON data of the form
     *
     * @return array
     */
    public function jsonSerialize(): array
    {
        return [
          "type"    => $this->type,
          "title"   => $this->title,
          "content" => $this->content,
        ];
    }

    /**
     * Getting a response from the client form
     *
     * @param  Player  $player
     * @param  mixed  $data
     */
    abstract public function handleResponse(Player $player, $data): void;

    /**
     * Convert a world config value to form value index
     *
     * @param  mixed  $val
     * @param  int  $type
     *
     * @return int
     */
    protected function confValueToForm(mixed $val, int $type): int
    {
        if ($val === null) {
            return 0;
        }

        return match ($type) {
            Flags::TYPE_BOOL => $val === Flags::VALUE_TRUE ? 2 : 1,
            Flags::TYPE_WHITEBLACKLIST => match ($val) {
                Flags::VALUE_FALSE => 1,
                Flags::VALUE_TRUE => 2,
                Flags::VALUE_WHITELISTED => 3,
                Flags::VALUE_BLACKLISTED => 4,
            },
            Flags::TYPE_GAMEMODE => $val + 1,
            default => 0,
        };
    }

    /**
     * Evaluate bool form response value
     *
     * @param  string  $name
     * @param  mixed  $data
     *
     * @return void
     */
    protected function procBool(string $name, $data): void
    {
        switch ($data) {
            case 1:
                $this->storage->updateValue($name, Flags::VALUE_FALSE);
                break;
            case 2:
                $this->storage->updateValue($name, Flags::VALUE_TRUE);
                break;
            default:
                $this->storage->removeValue($name);
                break;
        }
    }

    /**
     * Evaluate white-/blacklist form response value
     *
     * @param  string  $name
     * @param  mixed  $data
     *
     * @return void
     */
    protected function procWhiteBlack(string $name, $data): void
    {
        switch ($data) {
            case 1:
                $this->storage->updateValue($name, Flags::VALUE_FALSE);
                break;
            case 2:
                $this->storage->updateValue($name, Flags::VALUE_TRUE);
                break;
            case 3:
                $this->storage->updateValue($name, Flags::VALUE_WHITELISTED);
                break;
            case 4:
                $this->storage->updateValue($name, Flags::VALUE_BLACKLISTED);
                break;
            default:
                $this->storage->removeValue($name);
                break;
        }
    }

    /**
     * Evaluate game mode form response value
     *
     * @param  string  $name
     * @param  mixed  $data
     *
     * @return void
     */
    protected function procGameMode(string $name, $data): void
    {
        switch ($data) {
            case 1:
                $this->storage->updateValue($name, WorldActions::getGameModeId(GameMode::SURVIVAL()));
                break;
            case 2:
                $this->storage->updateValue($name, WorldActions::getGameModeId(GameMode::CREATIVE()));
                break;
            case 3:
                $this->storage->updateValue($name, WorldActions::getGameModeId(GameMode::ADVENTURE()));
                break;
            case 4:
                $this->storage->updateValue($name, WorldActions::getGameModeId(GameMode::SPECTATOR()));
                break;
            default:
                $this->storage->removeValue($name);
                break;
        }
    }

    /**
     * @return \surva\worlds\types\World
     */
    protected function getStorage(): World
    {
        return $this->storage;
    }

    /**
     * @return \surva\worlds\Worlds
     */
    protected function getWorlds(): Worlds
    {
        return $this->worlds;
    }
}

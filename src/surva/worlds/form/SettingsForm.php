<?php
/**
 * Worlds | settings form class
 */

namespace surva\worlds\form;

use pocketmine\form\Form;
use pocketmine\player\GameMode;
use pocketmine\player\Player;
use surva\worlds\types\World;
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
     * Convert stored bool to form option index
     *
     * @param  bool|null  $val
     *
     * @return int
     */
    protected function convBool(?bool $val): int
    {
        if ($val === null) {
            return 0;
        }

        return $val ? 2 : 1;
    }

    /**
     * Convert stored game mode to form option index
     *
     * @param  int|null  $gm
     *
     * @return int
     */
    protected function convGameMode(?int $gm): int
    {
        if ($gm === null) {
            return 0;
        }

        return $gm + 1;
    }

    /**
     * Evaluate bool form response value
     *
     * @param  string  $name
     * @param $data
     */
    protected function procBool(string $name, $data): void
    {
        switch ($data) {
            case 1:
                $this->storage->updateValue($name, "false");
                break;
            case 2:
                $this->storage->updateValue($name, "true");
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
     * @param $data
     */
    protected function procGameMode(string $name, $data): void
    {
        switch ($data) {
            case 1:
                $this->storage->updateValue($name, GameMode::SURVIVAL()->id());
                break;
            case 2:
                $this->storage->updateValue($name, GameMode::CREATIVE()->id());
                break;
            case 3:
                $this->storage->updateValue($name, GameMode::ADVENTURE()->id());
                break;
            case 4:
                $this->storage->updateValue($name, GameMode::SPECTATOR()->id());
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

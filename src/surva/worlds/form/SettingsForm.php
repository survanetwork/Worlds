<?php
/**
 * Worlds | settings form class
 */

namespace surva\worlds\form;

use pocketmine\form\Form;
use pocketmine\Player;
use surva\worlds\types\World;
use surva\worlds\Worlds;

abstract class SettingsForm implements Form
{

    /* @var Worlds */
    private $worlds;

    /* @var World */
    private $storage;

    /* @var string */
    private $type = "custom_form";

    /* @var string */
    protected $title;

    /* @var array */
    protected $content;

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
    public abstract function handleResponse(Player $player, $data): void;

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
     * Convert stored bool to form option index
     *
     * @param  int|null  $gm
     *
     * @return int
     */
    protected function convGamemode(?int $gm): int
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
     * Evaluate gamemode form response value
     *
     * @param  string  $name
     * @param $data
     */
    protected function procGamemode(string $name, $data): void
    {
        switch ($data) {
            case 1:
                $this->storage->updateValue($name, Player::SURVIVAL);
                break;
            case 2:
                $this->storage->updateValue($name, Player::CREATIVE);
                break;
            case 3:
                $this->storage->updateValue($name, Player::ADVENTURE);
                break;
            case 4:
                $this->storage->updateValue($name, Player::SPECTATOR);
                break;
            default:
                $this->storage->removeValue($name);
                break;
        }
    }

    /**
     * @return World
     */
    protected function getStorage(): World
    {
        return $this->storage;
    }

    /**
     * @return Worlds
     */
    protected function getWorlds(): Worlds
    {
        return $this->worlds;
    }

}

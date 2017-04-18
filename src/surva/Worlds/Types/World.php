<?php
/**
 * Created by PhpStorm.
 * User: Jarne
 * Date: 24.07.16
 * Time: 20:05
 */

namespace surva\Worlds\Types;

use surva\Worlds\Worlds;
use pocketmine\utils\Config;

class World {
    /* @var Worlds */
    private $worlds;
    /* @var Config */
    private $config;

    /* @var int */
    private $gamemode;
    /* @var bool */
    private $build;
    /* @var bool */
    private $pvp;
    /* @var bool */
    private $damage;
    /* @var bool */
    private $explode;
    /* @var bool */
    private $drop;
    /* @var bool */
    private $hunger;
    /* @var bool */
    private $fly;

    public function __construct(Worlds $worlds, Config $config) {
        $this->worlds = $worlds;
        $this->config = $config;

        $this->loadItems();
    }

    public function loadItems() {
        $this->loadValue("gamemode");
        $this->loadValue("build");
        $this->loadValue("pvp");
        $this->loadValue("damage");
        $this->loadValue("explode");
        $this->loadValue("drop");
        $this->loadValue("hunger");
        $this->loadValue("fly");
    }

    /**
     * Load value from config
     *
     * @param string $name
     */
    public function loadValue(string $name) {
        if($this->getConfig()->exists($name)) {
            switch($this->getConfig()->get($name)) {
                case "true":
                    $this->$name = true;
                    break;
                case "false":
                    $this->$name = false;
                    break;
                default:
                    $this->$name = $this->getConfig()->get($name);
                    break;
            }
        } else {
            if($name == "gamemode") {
                $this->$name = $this->getWorlds()->getServer()->getDefaultGamemode();
            } else {
                $this->$name = true;
            }
        }
    }

    /**
     * Update a config value
     *
     * @param string $name
     * @param string $value
     */
    public function updateValue(string $name, string $value) {
        if($value == "true") {
            $this->getConfig()->remove($name);
        } else {
            $this->getConfig()->set($name, $value);
        }

        $this->getConfig()->save();
        $this->loadItems();
    }

    /**
     * @return bool
     */
    public function getFly(): bool {
        return $this->fly;
    }

    /**
     * @return bool
     */
    public function getHunger(): bool {
        return $this->hunger;
    }

    /**
     * @return bool
     */
    public function getDrop(): bool {
        return $this->drop;
    }

    /**
     * @return bool
     */
    public function getExplode(): bool {
        return $this->explode;
    }

    /**
     * @return bool
     */
    public function getDamage(): bool {
        return $this->damage;
    }

    /**
     * @return bool
     */
    public function getPvp(): bool {
        return $this->pvp;
    }

    /**
     * @return bool
     */
    public function getBuild(): bool {
        return $this->build;
    }

    /**
     * @return int
     */
    public function getGamemode(): int {
        return $this->gamemode;
    }

    /**
     * @return Config
     */
    public function getConfig(): Config {
        return $this->config;
    }

    /**
     * @return Worlds
     */
    public function getWorlds(): Worlds {
        return $this->worlds;
    }
}
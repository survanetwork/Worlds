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

    /* @var int|null */
    private $gamemode;
    /* @var bool|null */
    private $build;
    /* @var bool|null */
    private $pvp;
    /* @var bool|null */
    private $damage;
    /* @var bool|null */
    private $explode;
    /* @var bool|null */
    private $drop;
    /* @var bool|null */
    private $hunger;
    /* @var bool|null */
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
                $this->$name = null;
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
     * @return bool|null
     */
    public function getFly() {
        return $this->fly;
    }

    /**
     * @return bool|null
     */
    public function getHunger() {
        return $this->hunger;
    }

    /**
     * @return bool|null
     */
    public function getDrop() {
        return $this->drop;
    }

    /**
     * @return bool|null
     */
    public function getExplode() {
        return $this->explode;
    }

    /**
     * @return bool|null
     */
    public function getDamage() {
        return $this->damage;
    }

    /**
     * @return bool|null
     */
    public function getPvp() {
        return $this->pvp;
    }

    /**
     * @return bool|null
     */
    public function getBuild() {
        return $this->build;
    }

    /**
     * @return int|null
     */
    public function getGamemode() {
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
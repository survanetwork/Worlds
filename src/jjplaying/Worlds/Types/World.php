<?php
/**
 * Created by PhpStorm.
 * User: Jarne
 * Date: 24.07.16
 * Time: 20:05
 */

namespace jjplaying\Worlds\Types;

use jjplaying\Worlds\Worlds;
use pocketmine\utils\Config;

class World {
    private $worlds;
    private $config;

    private $gamemode;
    private $build;
    private $pvp;
    private $damage;
    private $explode;
    private $drop;

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
    }

    /**
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
    public function getDrop() {
        return $this->drop;
    }

    /**
     * @return bool
     */
    public function getExplode() {
        return $this->explode;
    }

    /**
     * @return bool
     */
    public function getDamage() {
        return $this->damage;
    }

    /**
     * @return bool
     */
    public function getPvp() {
        return $this->pvp;
    }

    /**
     * @return bool
     */
    public function getBuild() {
        return $this->build;
    }

    /**
     * @return int
     */
    public function getGamemode() {
        return $this->gamemode;
    }

    /**
     * @return Config
     */
    public function getConfig() {
        return $this->config;
    }

    /**
     * @return Worlds
     */
    public function getWorlds() {
        return $this->worlds;
    }
}
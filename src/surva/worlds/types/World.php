<?php
/**
 * Worlds | world config class file
 */

namespace surva\worlds\types;

use pocketmine\utils\Config;
use surva\worlds\utils\Flags;
use surva\worlds\Worlds;

class World
{

    private Worlds $worlds;

    private Config $config;

    protected ?string $permission;

    protected ?int $gamemode;

    protected ?bool $build;

    protected ?bool $pvp;

    protected ?bool $damage;

    protected ?bool $interact;

    protected ?bool $explode;

    protected ?bool $drop;

    protected ?bool $hunger;

    protected ?bool $fly;

    protected ?bool $daylightcycle;

    protected ?bool $leavesdecay;

    protected ?bool $potion;

    /**
     * Load world options on class creation
     *
     * @param  \surva\worlds\Worlds  $worlds
     * @param  \pocketmine\utils\Config  $config
     */
    public function __construct(Worlds $worlds, Config $config)
    {
        $this->worlds = $worlds;
        $this->config = $config;

        $this->loadOptions();
    }

    /**
     * Load all possible config values
     */
    public function loadOptions(): void
    {
        foreach (array_keys(Flags::AVAILABLE_WORLD_FLAGS) as $flagName) {
            $this->loadValue($flagName);
        }
    }

    /**
     * Load value from config
     *
     * @param  string  $name
     *
     * @return mixed|null
     */
    public function loadValue(string $name)
    {
        if (!$this->config->exists($name)) {
            $defVal = $this->worlds->getDefaults()->getValue($name);

            $this->$name = $defVal;
            return $defVal;
        }

        $val = match ($this->config->get($name)) {
            "true" => true,
            "false" => false,
            default => $this->config->get($name),
        };

        $this->$name = $val;
        return $val;
    }

    /**
     * Update a config value
     *
     * @param  string  $name
     * @param  string  $value
     */
    public function updateValue(string $name, string $value): void
    {
        $this->config->set($name, $value);

        $this->config->save();
        $this->loadOptions();
    }

    /**
     * Remove a config value
     *
     * @param  string  $name
     */
    public function removeValue(string $name): void
    {
        if (!$this->config->exists($name)) {
            return;
        }

        $this->config->remove($name);

        $this->config->save();
        $this->loadOptions();
    }

    /**
     * @return bool|null
     */
    public function getPotion(): ?bool
    {
        return $this->potion;
    }

    /**
     * @return bool|null
     */
    public function getLeavesDecay(): ?bool
    {
        return $this->leavesdecay;
    }

    /**
     * @return bool|null
     */
    public function getDaylightCycle(): ?bool
    {
        return $this->daylightcycle;
    }

    /**
     * @return bool|null
     */
    public function getFly(): ?bool
    {
        return $this->fly;
    }

    /**
     * @return bool|null
     */
    public function getHunger(): ?bool
    {
        return $this->hunger;
    }

    /**
     * @return bool|null
     */
    public function getDrop(): ?bool
    {
        return $this->drop;
    }

    /**
     * @return bool|null
     */
    public function getExplode(): ?bool
    {
        return $this->explode;
    }

    /**
     * @return bool|null
     */
    public function getDamage(): ?bool
    {
        return $this->damage;
    }

    /**
     * @return bool|null
     */
    public function getInteract(): ?bool
    {
        return $this->interact;
    }

    /**
     * @return bool|null
     */
    public function getPvp(): ?bool
    {
        return $this->pvp;
    }

    /**
     * @return bool|null
     */
    public function getBuild(): ?bool
    {
        return $this->build;
    }

    /**
     * @return int|null
     */
    public function getGamemode(): ?int
    {
        return $this->gamemode;
    }

    /**
     * @return string|null
     */
    public function getPermission(): ?string
    {
        return $this->permission;
    }

    /**
     * @return \pocketmine\utils\Config
     */
    protected function getConfig(): Config
    {
        return $this->config;
    }

}

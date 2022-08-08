<?php

/**
 * Worlds | world config class file
 */

namespace surva\worlds\types;

use JsonException;
use pocketmine\utils\Config;
use surva\worlds\utils\Flags;
use surva\worlds\Worlds;

class World
{
    private Worlds $worlds;
    private Config $config;

    /**
     * @var array loaded flag values
     */
    protected array $flags;

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
        $this->flags = [];

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
    public function loadValue(string $name): mixed
    {
        if (!$this->config->exists($name)) {
            $defVal = $this->worlds->getDefaults()->getValue($name);

            $this->flags[$name] = $defVal;
            return $defVal;
        }

        $val = match ($this->config->get($name)) {
            "true" => true,
            "false" => false,
            default => $this->config->get($name),
        };

        $this->flags[$name] = $val;
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

        try {
            $this->config->save();
        } catch (JsonException $e) {
            return;
        }
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

        try {
            $this->config->save();
        } catch (JsonException $e) {
            return;
        }
        $this->loadOptions();
    }

    /**
     * Get the value of a bool flag
     *
     * @param  string  $flagName
     *
     * @return bool|null
     */
    public function getBoolFlag(string $flagName): ?bool
    {
        return $this->flags[$flagName];
    }

    /**
     * Get the value of an int flag
     *
     * @param  string  $flagName
     *
     * @return int|null
     */
    public function getIntFlag(string $flagName): ?int
    {
        return $this->flags[$flagName];
    }

    /**
     * Get the value of a string flag
     *
     * @param  string  $flagName
     *
     * @return string|null
     */
    public function getStringFlag(string $flagName): ?string
    {
        return $this->flags[$flagName];
    }

    /**
     * @return \pocketmine\utils\Config
     */
    protected function getConfig(): Config
    {
        return $this->config;
    }
}

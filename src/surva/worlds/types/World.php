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
        foreach (Flags::AVAILABLE_WORLD_FLAGS as $flagName => $flagOptions) {
            $this->loadValue($flagName, $flagOptions["type"]);
        }
    }

    /**
     * Load value from config
     *
     * @param  string  $name
     * @param  int|null  $type
     *
     * @return mixed
     */
    public function loadValue(string $name, ?int $type = null): mixed
    {
        if (!$this->config->exists($name)) {
            $defVal = $this->worlds->getDefaults()->getValue($name);

            $this->flags[$name] = $defVal;
            return $defVal;
        }

        $val = $this->config->get($name);
        $this->flags[$name] = $val;

        if ($type === Flags::TYPE_WHITEBLACKLIST) {
            if ($this->config->exists($name . "list")) {
                $this->flags[$name . "list"] = new WhiteBlackList($this->config->get($name . "list"));
            } else {
                $this->flags[$name . "list"] = new WhiteBlackList();
            }
        }

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
        return match ($this->flags[$flagName]) {
            Flags::VALUE_TRUE => true,
            Flags::VALUE_FALSE => false,
            default => null,
        };
    }

    /**
     * Get the value of a white-/blacklist flag
     *
     * @param  string  $flagName
     *
     * @return string|null
     */
    public function getWhiteBlackFlag(string $flagName): ?string
    {
        return $this->flags[$flagName];
    }

    /**
     * Get the list class of a white-/blacklist flag
     *
     * @param  string  $flagName
     *
     * @return \surva\worlds\types\WhiteBlackList|null
     */
    public function getWhiteBlackFlagList(string $flagName): ?WhiteBlackList
    {
        return $this->flags[$flagName . "list"];
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

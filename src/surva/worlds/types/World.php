<?php

/**
 * Worlds | world config class file
 */

namespace surva\worlds\types;

use InvalidArgumentException;
use JsonException;
use pocketmine\utils\Config;
use surva\worlds\types\exception\ConfigSaveException;
use surva\worlds\types\exception\ValueNotExistException;
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

        if ($type === Flags::TYPE_CONTROL_LIST) {
            if ($this->config->exists($name . "list")) {
                $this->flags[$name . "list"] = new ControlList($this->config->get($name . "list"));
            } else {
                $this->flags[$name . "list"] = new ControlList();
            }
        }

        return $val;
    }

    /**
     * Update a config value
     *
     * @param  string  $name
     * @param  mixed  $value
     *
     * @return void
     * @throws \surva\worlds\types\exception\ConfigSaveException
     */
    public function updateValue(string $name, mixed $value): void
    {
        $this->config->set($name, $value);

        try {
            $this->config->save();
        } catch (JsonException $e) {
            throw new ConfigSaveException();
        }
        $this->loadOptions();
    }

    /**
     * Save the content of a control list to config
     *
     * @param  string  $name
     *
     * @return void
     * @throws \surva\worlds\types\exception\ConfigSaveException
     */
    public function saveControlList(string $name): void
    {
        $list = $this->flags[$name . "list"];

        if (!($list instanceof ControlList)) {
            throw new InvalidArgumentException();
        }

        $this->config->set($name . "list", $list->getList());

        try {
            $this->config->save();
        } catch (JsonException $e) {
            throw new ConfigSaveException();
        }
        $this->loadOptions();
    }

    /**
     * Remove a config value
     *
     * @param  string  $name
     * @param  bool  $ifExisting
     *
     * @return void
     * @throws \surva\worlds\types\exception\ConfigSaveException
     * @throws \surva\worlds\types\exception\ValueNotExistException
     */
    public function removeValue(string $name, bool $ifExisting = false): void
    {
        if (!$this->config->exists($name)) {
            if (!$ifExisting) {
                throw new ValueNotExistException();
            }

            return;
        }

        $this->config->remove($name);

        try {
            $this->config->save();
        } catch (JsonException $e) {
            throw new ConfigSaveException();
        }
        $this->loadOptions();
    }

    /**
     * Check if using the item is allowed by the control list
     *
     * @param  string  $flagName
     * @param  mixed  $item
     *
     * @return bool|null
     */
    public function checkControlList(string $flagName, mixed $item): ?bool
    {
        $flagVal = $this->getControlListFlag($flagName);

        if ($flagVal === null) {
            return null;
        }

        if ($flagVal === Flags::VALUE_FALSE) {
            return false;
        }

        $controlList = $this->getControlListContent($flagName);

        if ($controlList === null) {
            return $flagVal === Flags::VALUE_TRUE;
        }

        if ($flagVal === Flags::VALUE_WHITELISTED && !$controlList->isListed($item)) {
            return false;
        }

        if ($flagVal === Flags::VALUE_BLACKLISTED && $controlList->isListed($item)) {
            return false;
        }

        return true;
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
     * Get the flag value of a control list flag
     *
     * @param  string  $flagName
     *
     * @return string|null
     */
    public function getControlListFlag(string $flagName): ?string
    {
        return $this->flags[$flagName];
    }

    /**
     * Get the list class (content) of a control list flag
     *
     * @param  string  $flagName
     *
     * @return \surva\worlds\types\ControlList|null
     */
    public function getControlListContent(string $flagName): ?ControlList
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

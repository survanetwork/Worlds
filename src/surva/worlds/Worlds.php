<?php
/**
 * Worlds | plugin main class
 */

namespace surva\worlds;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;
use surva\worlds\commands\CopyCommand;
use surva\worlds\commands\CreateCommand;
use surva\worlds\commands\CustomCommand;
use surva\worlds\commands\DefaultsCommand;
use surva\worlds\commands\ListCommand;
use surva\worlds\commands\LoadCommand;
use surva\worlds\commands\RemoveCommand;
use surva\worlds\commands\RenameCommand;
use surva\worlds\commands\SetCommand;
use surva\worlds\commands\TeleportCommand;
use surva\worlds\commands\UnloadCommand;
use surva\worlds\commands\UnsetCommand;
use surva\worlds\types\Defaults;
use surva\worlds\types\World;
use surva\worlds\utils\ArrayList;

class Worlds extends PluginBase
{

    /* @var Defaults */
    private $defaults;

    /* @var ArrayList */
    private $worlds;

    /**
     * @var Config
     */
    private $defaultMessages;

    /* @var Config */
    private $messages;

    public function onEnable()
    {
        $this->getServer()->getPluginManager()->registerEvents(new EventListener($this), $this);
        $this->saveDefaultConfig();

        $this->defaults = new Defaults($this, $this->getCustomConfig($this->getDataFolder() . "defaults.yml"));

        $this->worlds = new ArrayList();

        foreach ($this->getServer()->getLevels() as $level) {
            $this->loadWorld($level->getFolderName());
        }

        $this->defaultMessages = new Config($this->getFile() . "resources/languages/en.yml");
        $this->messages        = new Config(
          $this->getFile() . "resources/languages/" . $this->getConfig()->get("language", "en") . ".yml"
        );
    }

    public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool
    {
        $name = $command->getName();

        if (strtolower($name) === "worlds") {
            if (count($args) > 0) {
                if ($customCommand = $this->getCustomCommand($args[0])) {
                    if ($customCommand instanceof CustomCommand) {
                        return $customCommand->execute($sender, $name, $args);
                    }
                }
            }
        }

        return false;
    }

    /**
     * Get a custom command by its name
     *
     * @param  string  $name
     *
     * @return CustomCommand|null
     */
    public function getCustomCommand(string $name): ?CustomCommand
    {
        switch ($name) {
            case "list":
            case "ls":
                return new ListCommand($this, "list", "worlds.list");
            case "create":
            case "cr":
                return new CreateCommand($this, "create", "worlds.admin.create");
            case "remove":
            case "rm":
                return new RemoveCommand($this, "remove", "worlds.admin.remove");
            case "copy":
            case "cp":
                return new CopyCommand($this, "copy", "worlds.admin.copy");
            case "rename":
            case "rn":
                return new RenameCommand($this, "rename", "worlds.admin.rename");
            case "load":
            case "ld":
                return new LoadCommand($this, "load", "worlds.admin.load");
            case "unload":
            case "uld":
                return new UnloadCommand($this, "unload", "worlds.admin.unload");
            case "teleport":
            case "tp":
                return new TeleportCommand($this, "teleport", "worlds.teleport.general");
            case "set":
            case "st":
                return new SetCommand($this, "set", "worlds.admin.set");
            case "unset":
            case "ust":
                return new UnsetCommand($this, "unset", "worlds.admin.unset");
            case "defaults":
            case "df":
                return new DefaultsCommand($this, "defaults", "worlds.admin.defaults");
            default:
                return null;
        }
    }

    /**
     * Get a world by name
     *
     * @param  string  $name
     *
     * @return World|null
     */
    public function getWorldByName(string $name): ?World
    {
        if ($this->getWorlds()->containsKey($name)) {
            return $this->getWorlds()->get($name);
        }

        return null;
    }

    /**
     * Register a world load
     *
     * @param  string  $foldername
     */
    public function loadWorld(string $foldername): void
    {
        $file   = $this->getWorldFile($foldername);
        $config = $this->getCustomConfig($file);

        $this->getWorlds()->add(new World($this, $config), $foldername);
    }

    /**
     * Get the worlds.yml file of a world
     *
     * @param  string  $foldername
     *
     * @return string
     */
    public function getWorldFile(string $foldername): string
    {
        return $this->getServer()->getDataPath() . "worlds/" . $foldername . "/worlds.yml";
    }

    /**
     * Create a custom config file
     *
     * @param  string  $file
     *
     * @return Config
     */
    public function getCustomConfig(string $file): Config
    {
        $config = new Config($file);

        if (!file_exists($file)) {
            $config->save();
        }

        return $config;
    }

    /**
     * Copy a world
     *
     * @param  string  $from
     * @param  string  $to
     */
    public function copy(string $from, string $to): void
    {
        if (is_dir($from)) {
            $objects = scandir($from);

            mkdir($to);

            foreach ($objects as $object) {
                if ($object !== "." and $object !== "..") {
                    if (is_dir($from . "/" . $object)) {
                        $this->copy($from . "/" . $object, $to . "/" . $object);
                    } else {
                        copy($from . "/" . $object, $to . "/" . $object);
                    }
                }
            }
        }
    }

    /**
     * Delete a world
     *
     * @param  string  $directory
     */
    public function delete(string $directory): void
    {
        if (is_dir($directory)) {
            $objects = scandir($directory);

            foreach ($objects as $object) {
                if ($object !== "." and $object !== "..") {
                    if (is_dir($directory . "/" . $object)) {
                        $this->delete($directory . "/" . $object);
                    } else {
                        unlink($directory . "/" . $object);
                    }
                }
            }

            rmdir($directory);
        }
    }

    /**
     * Get a translated message
     *
     * @param  string  $key
     * @param  array  $replaces
     *
     * @return string
     */
    public function getMessage(string $key, array $replaces = []): string
    {
        if (($rawMessage = $this->messages->getNested($key)) === null) {
            $rawMessage = $this->defaultMessages->getNested($key);
        }

        if ($rawMessage === null) {
            return $key;
        }

        foreach ($replaces as $replace => $value) {
            $rawMessage = str_replace("{" . $replace . "}", $value, $rawMessage);
        }

        return $rawMessage;
    }

    /**
     * @return Config
     */
    public function getMessages(): Config
    {
        return $this->messages;
    }

    /**
     * @return ArrayList
     */
    public function getWorlds(): ArrayList
    {
        return $this->worlds;
    }

    /**
     * @return Defaults
     */
    public function getDefaults(): Defaults
    {
        return $this->defaults;
    }

}

<?php
/**
 * Created by PhpStorm.
 * User: Jarne
 * Date: 19.03.16
 * Time: 16:01
 */

namespace surva\Worlds;

use pocketmine\nbt\NBT;
use pocketmine\nbt\tag\StringTag;
use surva\Worlds\Types\World;
use surva\Worlds\Utils\StaticArrayList;
use pocketmine\level\generator\Flat;
use pocketmine\level\generator\hell\Nether;
use pocketmine\level\generator\normal\Normal;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\Server;
use pocketmine\utils\Config;

class Worlds extends PluginBase {
    /* @var StaticArrayList */
    private $worlds;
    /* @var Config */
    private $messages;

    public function onEnable() {
        $this->getServer()->getPluginManager()->registerEvents(new EventListener($this), $this);
        $this->saveDefaultConfig();

        $this->worlds = new StaticArrayList();

        foreach($this->getServer()->getLevels() as $level) {
            $this->loadWorld($level->getFolderName());
        }

        $messagesfile = $this->getDataFolder() . "messages.yml";

        if(!file_exists($messagesfile)) {
            file_put_contents($messagesfile, $this->getResource("messages.yml"));
        }

        $this->messages = new Config($messagesfile, Config::YAML, []);
    }

    public function onCommand(CommandSender $sender, Command $command, $label, array $args) {
        $name = $command->getName();

        switch(strtolower($name)) {
            case "worlds":
                if(count($args) >= 1) {
                    switch(strtolower($args[0])) {
                        case "info":
                            $sender->sendMessage("§7This server is using §l§9Worlds §r§fversion 1.0.9 §7(C) 2017 by §esurva network §7(https://github.com/survanetwork)");

                            return true;
                        case "list":
                        case "ls":
                            if($sender->hasPermission("worlds.list")) {
                                $levels = array();

                                foreach($this->getServer()->getLevels() as $level) {
                                    $levels[] = $level->getName();
                                }

                                $sender->sendMessage($this->getMessage("allworlds", array("worlds" => implode(", ", $levels))));
                            } else {
                                $sender->sendMessage($this->getMessage("permission"));
                            }

                            return true;
                        case "create":
                        case "cr":
                            if($sender->hasPermission("worlds.admin.create")) {
                                switch(count($args)) {
                                    case 2:
                                        $this->getServer()->generateLevel($args[1]);
                                        $sender->sendMessage($this->getMessage("created"));
                                        return true;
                                    case 3:
                                        switch($args[2]) {
                                            case "normal":
                                                $generator = Normal::class;
                                                break;
                                            case "flat":
                                                $generator = Flat::class;
                                                break;
                                            case "nether":
                                                $generator = Nether::class;
                                                break;
                                            default:
                                                $generator = Normal::class;
                                        }

                                        $this->getServer()->generateLevel($args[1], null, $generator);
                                        $sender->sendMessage($this->getMessage("created"));
                                        return true;
                                    default:
                                        return false;
                                }
                            } else {
                                $sender->sendMessage($this->getMessage("permission"));
                            }

                            return true;
                        case "remove":
                        case "rm":
                            if($sender->hasPermission("worlds.admin.remove")) {
                                if(count($args) == 2) {
                                    if($this->getServer()->isLevelLoaded($args[1])) {
                                        $this->getServer()->unloadLevel($this->getServer()->getLevelByName($args[1]));
                                    }

                                    $this->delete($this->getServer()->getFilePath() . "worlds/" . $args[1]);

                                    $sender->sendMessage($this->getMessage("removed"));
                                } else {
                                    return false;
                                }
                            } else {
                                $sender->sendMessage($this->getMessage("permission"));
                            }

                            return true;
                        case "copy":
                        case "cp":
                            if($sender->hasPermission("worlds.admin.copy")) {
                                if(count($args) == 3) {
                                    if($level = $this->getServer()->getLevelByName($args[1])) {
                                        $fromFolderName = $level->getFolderName();
                                        $toFolderName = $args[2];

                                        if($fromFolderName != $toFolderName) {
                                            $this->copy($this->getServer()->getDataPath() . "worlds/" . $fromFolderName, $this->getServer()->getDataPath() . "worlds/" . $toFolderName);

                                            $sender->sendMessage($this->getMessage("copied", array("to" => $toFolderName)));
                                        }
                                    } else {
                                        $sender->sendMessage($this->getMessage("notloaded"));
                                    }
                                } else {
                                    return false;
                                }
                            } else {
                                $sender->sendMessage($this->getMessage("permission"));
                            }

                            return true;
                        case "rename":
                        case "rn":
                            if($sender->hasPermission("worlds.admin.rename")) {
                                if(count($args) == 3) {
                                    if(is_dir($this->getServer()->getDataPath() . "worlds/" . $args[1])) {
                                        if($this->getServer()->isLevelLoaded($args[1])) {
                                            $this->getServer()->unloadLevel($this->getServer()->getLevelByName($args[1]));
                                        }

                                        $fromFolderName = $args[1];
                                        $toFolderName = $args[2];

                                        if($levelDatContent = file_get_contents($this->getServer()->getDataPath() . "worlds/" . $fromFolderName . "/level.dat")) {
                                            $nbt = new NBT(NBT::BIG_ENDIAN);
                                            $nbt->readCompressed($levelDatContent);

                                            $levelData = $nbt->getData();
                                            $levelData["Data"]["LevelName"] = new StringTag("LevelName", $toFolderName);
                                            $nbt->setData($levelData);

                                            $buffer = $nbt->writeCompressed();
                                            file_put_contents($this->getServer()->getDataPath() . "worlds/" . $fromFolderName . "/level.dat", $buffer);
                                        }

                                        if($fromFolderName != $toFolderName) {
                                            $this->copy($this->getServer()->getDataPath() . "worlds/" . $fromFolderName, $this->getServer()->getDataPath() . "worlds/" . $toFolderName);
                                            $this->delete($this->getServer()->getDataPath() . "worlds/" . $fromFolderName);

                                            $sender->sendMessage($this->getMessage("renamed", array("to" => $toFolderName)));
                                        }
                                    } else {
                                        $sender->sendMessage($this->getMessage("notloaded"));
                                    }
                                } else {
                                    return false;
                                }
                            } else {
                                $sender->sendMessage($this->getMessage("permission"));
                            }

                            return true;
                        case "load":
                        case "ld":
                            if($sender->hasPermission("worlds.admin.load")) {
                                if(count($args) == 2) {
                                    if(!$this->getServer()->isLevelLoaded($args[1])) {
                                        if($this->getServer()->loadLevel($args[1])) {
                                            if($level = $this->getServer()->getLevelByName($args[1])) {
                                                $sender->sendMessage($this->getMessage("loadworld", array("world" => $args[1])));
                                            }
                                        } else {
                                            $sender->sendMessage($this->getMessage("noworld"));
                                        }
                                    } else {
                                        $sender->sendMessage($this->getMessage("alreadyloaded"));
                                    }
                                } else {
                                    return false;
                                }
                            } else {
                                $sender->sendMessage($this->getMessage("permission"));
                            }

                            return true;
                        case "unload":
                        case "unld":
                            if($sender->hasPermission("worlds.admin.load")) {
                                if(count($args) == 2) {
                                    if($this->getServer()->isLevelLoaded($args[1])) {
                                        $this->getServer()->unloadLevel($this->getServer()->getLevelByName($args[1]));
                                        $sender->sendMessage($this->getMessage("unloadworld", array("world" => $args[1])));

                                        $this->getWorlds()->remove($args[1]);
                                    } else {
                                        $sender->sendMessage($this->getMessage("notloaded"));
                                    }
                                } else {
                                    return false;
                                }
                            } else {
                                $sender->sendMessage($this->getMessage("permission"));
                            }

                            return true;
                        case "teleport":
                        case "tp":
                            if($sender->hasPermission("worlds.admin.teleport")) {
                                if($sender instanceof Player) {
                                    if(count($args) == 2) {
                                        if(!$this->getServer()->isLevelLoaded($args[1])) {
                                            if($this->getServer()->loadLevel($args[1])) {
                                                if($level = $this->getServer()->getLevelByName($args[1])) {
                                                    $sender->sendMessage($this->getMessage("loadworld", array("world" => $args[1])));
                                                }
                                            } else {
                                                $sender->sendMessage($this->getMessage("noworld"));

                                                return true;
                                            }
                                        }

                                        $world = $this->getServer()->getLevelByName($args[1]);

                                        $sender->teleport($world->getSafeSpawn());
                                        $sender->sendMessage($this->getMessage("teleported", array("world" => $args[1])));
                                    } else {
                                        return false;
                                    }
                                } else {
                                    $sender->sendMessage($this->getMessage("ingame"));
                                }
                            } else {
                                $sender->sendMessage($this->getMessage("permission"));
                            }

                            return true;
                        case "set":
                            if($sender->hasPermission("worlds.admin.set")) {
                                if(count($args) == 3) {
                                    if(in_array($args[1], array("gamemode", "build", "pvp", "damage", "explode", "drop", "hunger", "fly"))) {
                                        if($args[1] == "gamemode") {
                                            if(($args[2] = Server::getGamemodeFromString($args[2])) != -1) {
                                                if($sender instanceof Player) {
                                                    if($world = $this->getWorldByName($sender->getLevel()->getFolderName())) {
                                                        $world->updateValue($args[1], $args[2]);

                                                        $sender->sendMessage($this->getMessage("set", array("world" => $sender->getLevel()->getFolderName(), "key" => $args[1], "value" => $args[2])));
                                                    } else {
                                                        $sender->sendMessage($this->getMessage("noworld"));
                                                    }
                                                } else {
                                                    $sender->sendMessage($this->getMessage("ingame"));
                                                }

                                                return true;
                                            }
                                        } else {
                                            if(in_array($args[2], array("true", "false"))) {
                                                if($sender instanceof Player) {
                                                    if($world = $this->getWorldByName($sender->getLevel()->getFolderName())) {
                                                        $world->updateValue($args[1], $args[2]);

                                                        $sender->sendMessage($this->getMessage("set", array("world" => $sender->getLevel()->getFolderName(), "key" => $args[1], "value" => $args[2])));
                                                    } else {
                                                        $sender->sendMessage($this->getMessage("noworld"));
                                                    }
                                                } else {
                                                    $sender->sendMessage($this->getMessage("ingame"));
                                                }

                                                return true;
                                            }
                                        }
                                    }
                                }
                            } else {
                                $sender->sendMessage($this->getMessage("permission"));

                                return true;
                            }

                            return false;
                    }
                }

                return false;
        }

        return false;
    }

    /**
     * Get a world by name
     *
     * @param string $name
     * @return World|bool
     */
    public function getWorldByName(string $name) {
        if($this->getWorlds()->containsKey($name)) {
            return $this->getWorlds()->get($name);
        }

        return false;
    }

    /**
     * Register a world load
     *
     * @param string $foldername
     */
    public function loadWorld(string $foldername) {
        $file = $this->getWorldFile($foldername);
        $config = $this->getCustomConfig($file);

        $this->getWorlds()->add(new World($this, $config), $foldername);
    }

    /**
     * Create a custom config file
     *
     * @param string $file
     * @return Config
     */
    public function getCustomConfig(string $file) {
        $config = new Config($file, Config::YAML, []);

        if(!file_exists($file)) {
            $config->save();
        }

        return $config;
    }

    /**
     * Get the worlds.yml file of a world
     *
     * @param string $foldername
     * @return string
     */
    public function getWorldFile(string $foldername) {
        return $this->getServer()->getDataPath() . "worlds/" . $foldername . "/worlds.yml";
    }

    /**
     * Copy a world
     *
     * @param string $from
     * @param string $to
     */
    public function copy(string $from, string $to) {
        if(is_dir($from)) {
            $objects = scandir($from);

            mkdir($to);

            foreach($objects as $object) {
                if($object != "." AND $object != "..") {
                    if(is_dir($from . "/" . $object)) {
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
     * @param string $directory
     */
    public function delete(string $directory) {
         if(is_dir($directory)) {
             $objects = scandir($directory);

             foreach($objects as $object) {
                 if($object != "." AND $object != "..") {
                     if(is_dir($directory . "/" . $object)) {
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
     * @param string $key
     * @param array|null $replaces
     * @return string
     */
    public function getMessage(string $key, array $replaces = null) {
        $messages = $this->getMessages();

        if($messages->exists($key)) {
            if(isset($replaces)) {
                $get = $messages->get($key);

                foreach($replaces as $replace => $value) {
                    $get = str_replace("{" . $replace . "}", $value, $get);
                }

                return $get;
            } else {
                return $messages->get($key);
            }
        }

        return $key;
    }

    /**
     * @return Config
     */
    public function getMessages(): Config {
        return $this->messages;
    }

    /**
     * @return StaticArrayList
     */
    public function getWorlds(): StaticArrayList {
        return $this->worlds;
    }
}
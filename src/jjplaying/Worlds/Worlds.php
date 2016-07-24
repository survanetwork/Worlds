<?php
/**
 * Created by PhpStorm.
 * User: Jarne
 * Date: 19.03.16
 * Time: 16:01
 */

namespace jjplaying\Worlds;

use jjplaying\Worlds\Types\World;
use jjplaying\Worlds\Utils\StaticArrayList;
use pocketmine\level\Level;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\utils\Config;

class Worlds extends PluginBase {
    private $worlds;
    private $messages;

    public function onEnable() {
        $this->getServer()->getPluginManager()->registerEvents(new EventListener($this), $this);
        $this->saveDefaultConfig();

        $this->worlds = new StaticArrayList();

        foreach($this->getServer()->getLevels() as $level) {
            $this->loadWorld($level->getFolderName());
        }

        $messagesfile = $this->getServer()->getPluginPath() . "Worlds/messages.yml";

        if(!file_exists($messagesfile)) {
            file_put_contents($messagesfile, $this->getResource("messages.yml"));
        }

        $this->messages = new Config($messagesfile, Config::YAML, []);
    }

    public function onCommand(CommandSender $sender, Command $command, $label, array $args) {
        switch(strtolower($command->getName())) {
            case "worlds":
                if(isset($args[0])) {
                    switch(strtolower($args[0])) {
                        case "info":
                            $sender->sendMessage("§7This server is using §l§9Worlds §r§fversion 1.0 §7(C) 2016 by §ejjplaying §7(https://github.com/jjplaying)");
                            return true;
                        case "list":
                            if($sender->hasPermission("worlds.list")) {
                                $levels = array();

                                foreach($this->getServer()->getLevels() as $level) {
                                    $levels[] = $level->getName();
                                }

                                $sender->sendMessage($this->getMessage("allworlds") . implode(", ", $levels));
                            } else {
                                $sender->sendMessage($this->getMessage("permission"));
                            }
                            return true;
                        case "create":
                            if($sender->hasPermission("worlds.admin.create")) {
                                // TODO
                            } else {
                                $sender->sendMessage($this->getMessage("permission"));
                            }
                            return true;
                        case "delete":
                            if($sender->hasPermission("worlds.admin.create")) {
                                if(isset($args[1])) {
                                    if($this->getServer()->isLevelLoaded($args[1])) {
                                        $this->getServer()->unloadLevel($this->getServer()->getLevelByName($args[1]));
                                    }

                                    // TODO: Delete folder
                                } else {
                                    return false;
                                }
                            } else {
                                $sender->sendMessage($this->getMessage("permission"));
                            }
                            return true;
                        case "load":
                            if($sender->hasPermission("worlds.admin.load")) {
                                if(isset($args[1])) {
                                    if(!$this->getServer()->isLevelLoaded($args[1])) {
                                        $level = $this->getServer()->loadLevel($args[1]);

                                        if($level instanceof Level) {
                                            $sender->sendMessage($this->getMessage("loadworld", array("world" => $args[1])));
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
                            if($sender->hasPermission("worlds.admin.load")) {
                                if(isset($args[1])) {
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
                        case "tp":
                            if($sender->hasPermission("worlds.admin.tp")) {
                                if($sender instanceof Player) {
                                    if(isset($args[1])) {
                                        if(!$this->getServer()->isLevelLoaded($args[1])) {
                                            $level = $this->getServer()->loadLevel($args[1]);

                                            if($level instanceof Level) {
                                                $sender->sendMessage($this->getMessage("loadworld", array("world" => $args[1])));
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
                                if(isset($args[1]) AND isset($args[2])) {
                                    if(in_array($args[1], array("gamemode", "build", "pvp", "damage", "explode", "drop"))) {
                                        if($args[1] == "gamemode") {
                                            if(in_array($args[2], array("0", "1", "2", "3"))) {
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
     * @param string $foldername
     */
    public function loadWorld(string $foldername) {
        $file = $this->getWorldFile($foldername);
        $config = $this->getCustomConfig($file);

        $this->getWorlds()->add(new World($this, $config), $foldername);
    }

    /**
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
     * @param string $foldername
     * @return string
     */
    public function getWorldFile(string $foldername) {
        return $this->getServer()->getDataPath() . "worlds/" . $foldername . "/worlds.yml";
    }

    /**
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
    public function getMessages() {
        return $this->messages;
    }

    /**
     * @return StaticArrayList
     */
    public function getWorlds() {
        return $this->worlds;
    }
}
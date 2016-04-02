<?php
/**
 * Created by PhpStorm.
 * User: Jarne
 * Date: 19.03.16
 * Time: 16:01
 */

namespace jjmc\Worlds;

use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\entity\EntityLevelChangeEvent;
use pocketmine\event\level\LevelLoadEvent;
use pocketmine\event\player\PlayerHungerChangeEvent;
use pocketmine\event\player\PlayerDropItemEvent;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\event\Listener;
use pocketmine\utils\Config;

class Worlds extends PluginBase implements Listener {
    public function onEnable() {
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        $this->saveDefaultConfig();

        $this->worlds = array();

        $messagesfile = $this->getServer()->getPluginPath() . "Worlds/messages.yml";

        if(!file_exists($messagesfile)) {
            file_put_contents($messagesfile, $this->getResource("messages.yml"));
            echo "erstellen";
        }
        $this->messages = new Config($messagesfile, Config::YAML, []);
    }

    public function onCommand(CommandSender $sender, Command $command, $label, array $args) {
        switch(strtolower($command->getName())) {
            case "worlds":
                if(isset($args[0])) {
                    switch(strtolower($args[0])) {
                        case "info":
                            $sender->sendMessage("§7This server is using §l§9Worlds §r§fversion 1.0 §7by §ejjplaying §7(https://github.com/jjplaying)");
                            return true;
                        case "list":
                            foreach($this->getServer()->getLevels() as $level) {
                                $levels[] = $level->getName();
                            }

                            $sender->sendMessage($this->getMessage("allworlds") . implode(", ", $levels));
                            return true;
                        case "tp":
                            if($sender instanceof Player) {
                                if(isset($args[1])) {
                                    if($this->getServer()->isLevelLoaded($args[1])) {
                                        $world = $this->getServer()->getLevelByName($args[1]);
                                        $sender->teleport($world->getSafeSpawn());
                                        $sender->sendMessage($this->getMessage("teleported", array("world" => $args[1])));
                                    } else {
                                        $sender->sendMessage($this->getMessage("noworld"));
                                    }
                                } else {
                                    return false;
                                }
                            } else {
                                $sender->sendMessage($this->getMessage("ingame"));
                            }
                            return true;;
                        case "set":
                            if(isset($args[1]) AND isset($args[2])) {
                                if(in_array($args[1], array("gamemode", "build", "pvp", "damage", "hunger", "drop")) AND in_array($args[2], array("true", "false"))) {
                                    if($sender instanceof Player) {
                                        $world = $sender->getLevel()->getName();
                                        $this->updateValue($world, $args[1], $args[2]);
                                        $sender->sendMessage($this->getMessage("set", array("world" => $world, "key" => $args[1], "value" => $args[2])));
                                    } else {
                                        $sender->sendMessage($this->getMessage("ingame"));
                                    }
                                    return true;
                                }
                            }
                            break;
                    }
                }
                break;
        }
    }

    public function onLevelLoad(LevelLoadEvent $event) {
        $foldername = $event->getLevel()->getFolderName();
        $this->loadWorld($foldername);
    }

    public function onEntityLevelChange(EntityLevelChangeEvent $event) {
        $player = $event->getEntity();
        $foldername = $event->getTarget()->getFolderName();

        if($player instanceof Player) {
            if(isset($this->worlds[$foldername]["gamemode"])) {
                $player->setGamemode($this->worlds[$foldername]["gamemode"]);
            }
        }
    }

    public function onBlockBreak(BlockBreakEvent $event) {
        $player = $event->getPlayer();

        if($player instanceof Player) {
            $foldername = $player->getLevel()->getFolderName();
            
            if(isset($this->worlds[$foldername]["build"])) {
                $event->setCancelled($this->worlds[$foldername]["build"]);
            }
        }
    }

    public function onBlockPlace(BlockPlaceEvent $event) {
        $player = $event->getPlayer();

        if($player instanceof Player) {
            $foldername = $player->getLevel()->getFolderName();

            if(isset($this->worlds[$foldername]["build"])) {
                $event->setCancelled($this->worlds[$foldername]["build"]);
            }
        }
    }

    public function onEntityDamage(EntityDamageEvent $event) {
        $player = $event->getEntity();
        $cause = $event->getCause();

        if($player instanceof Player) {
            $foldername = $player->getLevel()->getFolderName();

            switch($cause) {
                case EntityDamageEvent::CAUSE_ENTITY_ATTACK:
                    if(isset($this->worlds[$foldername]["pvp"])) {
                        $event->setCancelled($this->worlds[$foldername]["pvp"]);
                    }
                    break;
                default:
                    if(isset($this->worlds[$foldername]["damage"])) {
                        $event->setCancelled($this->worlds[$foldername]["damage"]);
                    }
                    break;
            }
        }
    }

    public function onPlayerHungerChange(PlayerHungerChangeEvent $event) {
        $player = $event->getPlayer();

        if($player instanceof Player) {
            $foldername = $player->getLevel()->getFolderName();

            if(isset($this->worlds[$foldername]["hunger"])) {
                if($this->worlds[$foldername]["hunger"]) {
                    $player->setFood(20);
                }
            }
        }
    }

    public function onPlayerDropItem(PlayerDropItemEvent $event) {
        $player = $event->getPlayer();

        if($player instanceof Player) {
            $foldername = $player->getLevel()->getFolderName();

            if(isset($this->worlds[$foldername]["drop"])) {
                $event->setCancelled($this->worlds[$foldername]["drop"]);
            }
        }
    }

    public function getWorldFile($foldername) {
        return $this->getServer()->getDataPath() . "worlds/" . $foldername . "/worlds.yml";
    }

    public function getMessage($key, $replaces = null) {
        $config = $this->messages;

        if($config instanceof Config) {
            if($config->exists($key)) {
                if(is_array($replaces)) {
                    $get = $config->get($key);

                    foreach($replaces as $replace => $value) {
                        $get = str_replace("{" . $replace . "}", $value, $get);
                    }

                    return $get;
                } else {
                    return $config->get($key);
                }
            }
        }
    }

    public function loadWorld($foldername) {
        $file = $this->getWorldFile($foldername);

        if(file_exists($file)) {
            $config = new Config($file, Config::YAML, []);

            $this->loadConfigItem($config, $foldername, "gamemode");
            $this->loadConfigItem($config, $foldername, "build");
            $this->loadConfigItem($config, $foldername, "pvp");
            $this->loadConfigItem($config, $foldername, "damage");
            $this->loadConfigItem($config, $foldername, "hunger");
            $this->loadConfigItem($config, $foldername, "drop");
        } else {
            $this->createDefaultConfig($foldername);
        }
    }

    public function loadConfigItem($config, $foldername, $key) {
        if($config instanceof Config) {
            if($config->exists($key)) {
                if(is_bool($config->get($key))) {
                    switch($config->get($key)) {
                        case true:
                            $value = false;
                            break;
                        case false:
                            $value = true;
                            break;
                    }
                } else {
                    $value = $config->get($key);
                }

                $this->worlds[$foldername][$key] = $value;
            }
        }
    }

    public function updateValue($foldername, $key, $value) {
        $file = $this->getWorldFile($foldername);

        if(!file_exists($file)) {
            $this->createDefaultConfig($foldername);
        }
        
        $config = new Config($file, Config::YAML, []);
        $config->set($key, $value);
        $config->save();

        $this->loadWorld($foldername);
    }

    public function createDefaultConfig($foldername) {
        $file = $this->getWorldFile($foldername);

        if(!file_exists($file)) {
            touch($file);
        }
    }
}
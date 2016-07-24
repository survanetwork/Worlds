<?php
/**
 * Created by PhpStorm.
 * User: Jarne
 * Date: 03.04.16
 * Time: 18:28
 */

namespace jjplaying\Worlds;

use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\entity\EntityLevelChangeEvent;
use pocketmine\event\entity\ExplosionPrimeEvent;
use pocketmine\event\level\LevelLoadEvent;
use pocketmine\event\player\PlayerDropItemEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\Listener;
use pocketmine\Player;

class EventListener implements Listener {
    private $worlds;

    public function __construct(Worlds $worlds) {
        $this->worlds = $worlds;
    }

    public function onLevelLoad(LevelLoadEvent $event) {
        $foldername = $event->getLevel()->getFolderName();

        $this->getWorlds()->loadWorld($foldername);
    }

    public function onPlayerJoin(PlayerJoinEvent $event) {
        $player = $event->getPlayer();
        $foldername = $player->getLevel()->getFolderName();

        if($world = $this->getWorlds()->getWorldByName($foldername)) {
            if($player instanceof Player) {
                if(!$player->hasPermission("worlds.admin.gamemode")) {
                    $player->setGamemode($world->getGamemode());
                }
            }
        }
    }

    public function onEntityLevelChange(EntityLevelChangeEvent $event) {
        $player = $event->getEntity();
        $foldername = $player->getLevel()->getFolderName();

        if($world = $this->getWorlds()->getWorldByName($foldername)) {
            if($player instanceof Player) {
                if(!$player->hasPermission("worlds.admin.gamemode")) {
                    $player->setGamemode($world->getGamemode());
                }
            }
        }
    }

    public function onBlockBreak(BlockBreakEvent $event) {
        $player = $event->getPlayer();
        $foldername = $player->getLevel()->getFolderName();

        if($world = $this->getWorlds()->getWorldByName($foldername)) {
            if($player instanceof Player) {
                if(!$player->hasPermission("worlds.admin.build")) {
                    $event->setCancelled(!$world->getBuild());
                }
            }
        }
    }

    public function onBlockPlace(BlockPlaceEvent $event) {
        $player = $event->getPlayer();
        $foldername = $player->getLevel()->getFolderName();

        if($world = $this->getWorlds()->getWorldByName($foldername)) {
            if($player instanceof Player) {
                if(!$player->hasPermission("worlds.admin.build")) {
                    $event->setCancelled(!$world->getBuild());
                }
            }
        }
    }

    public function onEntityDamage(EntityDamageEvent $event) {
        $player = $event->getEntity();
        $foldername = $player->getLevel()->getFolderName();

        if($world = $this->getWorlds()->getWorldByName($foldername)) {
            if($player instanceof Player) {
                if($event instanceof EntityDamageByEntityEvent) {
                    $event->setCancelled(!$world->getPvp());
                } else {
                    $event->setCancelled(!$world->getDamage());
                }
            }
        }
    }

    public function onExplosionPrime(ExplosionPrimeEvent $event) {
        $player = $event->getEntity();
        $foldername = $player->getLevel()->getFolderName();

        if($world = $this->getWorlds()->getWorldByName($foldername)) {
            if($player instanceof Player) {
                $event->setCancelled(!$world->getExplode());
            }
        }
    }

    public function onPlayerDropItem(PlayerDropItemEvent $event) {
        $player = $event->getPlayer();
        $foldername = $player->getLevel()->getFolderName();

        if($world = $this->getWorlds()->getWorldByName($foldername)) {
            if($player instanceof Player) {
                $event->setCancelled(!$world->getDrop());
            }
        }
    }

    /**
     * @return Worlds
     */
    public function getWorlds() {
        return $this->worlds;
    }
}
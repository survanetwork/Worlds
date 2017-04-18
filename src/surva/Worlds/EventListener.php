<?php
/**
 * Created by PhpStorm.
 * User: Jarne
 * Date: 03.04.16
 * Time: 18:28
 */

namespace surva\Worlds;

use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\entity\EntityLevelChangeEvent;
use pocketmine\event\entity\ExplosionPrimeEvent;
use pocketmine\event\level\LevelLoadEvent;
use pocketmine\event\player\PlayerDropItemEvent;
use pocketmine\event\player\PlayerExhaustEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\Listener;
use pocketmine\Player;

class EventListener implements Listener {
    /* @var Worlds */
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
            if(!$player->hasPermission("worlds.admin.gamemode")) {
                $player->setGamemode($world->getGamemode());
            }

            if($world->getFly() === true OR $player->hasPermission("worlds.admin.fly")) {
                $player->setAllowFlight(true);
            } else {
                $player->setAllowFlight(false);
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

                if($world->getFly() === true OR $player->hasPermission("worlds.admin.fly")) {
                    $player->setAllowFlight(true);
                } else {
                    $player->setAllowFlight(false);
                }
            }
        }
    }

    public function onBlockBreak(BlockBreakEvent $event) {
        $player = $event->getPlayer();
        $foldername = $player->getLevel()->getFolderName();

        if($world = $this->getWorlds()->getWorldByName($foldername)) {
            if(!$player->hasPermission("worlds.admin.build")) {
                if($world->getBuild() === false) {
                    $event->setCancelled(true);
                }
            }
        }
    }

    public function onBlockPlace(BlockPlaceEvent $event) {
        $player = $event->getPlayer();
        $foldername = $player->getLevel()->getFolderName();

        if($world = $this->getWorlds()->getWorldByName($foldername)) {
            if(!$player->hasPermission("worlds.admin.build")) {
                if($world->getBuild() === false) {
                    $event->setCancelled(true);
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
                    if($world->getPvp() === false) {
                        $event->setCancelled(true);
                    }
                } else {
                    if($event->getCause() != EntityDamageEvent::CAUSE_VOID) {
                        if($world->getDamage() === false) {
                            $event->setCancelled(true);
                        }
                    }
                }
            }
        }
    }

    public function onExplosionPrime(ExplosionPrimeEvent $event) {
        $player = $event->getEntity();
        $foldername = $player->getLevel()->getFolderName();

        if($world = $this->getWorlds()->getWorldByName($foldername)) {
            if($world->getExplode() === false) {
                $event->setCancelled(true);
            }
        }
    }

    public function onPlayerDropItem(PlayerDropItemEvent $event) {
        $player = $event->getPlayer();
        $foldername = $player->getLevel()->getFolderName();

        if($world = $this->getWorlds()->getWorldByName($foldername)) {
            if($world->getDrop() === false) {
                $event->setCancelled(true);
            }
        }
    }

    public function onPlayerExhaust(PlayerExhaustEvent $event) {
        $player = $event->getPlayer();
        $foldername = $player->getLevel()->getFolderName();

        if($world = $this->getWorlds()->getWorldByName($foldername)) {
            if($world->getHunger() === false) {
                $event->setCancelled(true);
            }
        }
    }

    /**
     * @return Worlds
     */
    public function getWorlds(): Worlds {
        return $this->worlds;
    }
}
<?php
/**
 * Created by PhpStorm.
 * User: Jarne
 * Date: 03.04.16
 * Time: 18:28
 */

namespace surva\worlds;

use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\entity\EntityLevelChangeEvent;
use pocketmine\event\entity\ExplosionPrimeEvent;
use pocketmine\event\level\LevelLoadEvent;
use pocketmine\event\player\PlayerDropItemEvent;
use pocketmine\event\player\PlayerExhaustEvent;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\Listener;
use pocketmine\Player;

class EventListener implements Listener {
    /* @var Worlds */
    private $worlds;

    public function __construct(Worlds $worlds) {
        $this->worlds = $worlds;
    }

    /**
     * @param LevelLoadEvent $event
     */
    public function onLevelLoad(LevelLoadEvent $event): void {
        $foldername = $event->getLevel()->getFolderName();

        $this->getWorlds()->loadWorld($foldername);
    }

    /**
     * @param PlayerJoinEvent $event
     */
    public function onPlayerJoin(PlayerJoinEvent $event): void {
        $player = $event->getPlayer();
        $foldername = $player->getLevel()->getFolderName();

        if($world = $this->getWorlds()->getWorldByName($foldername)) {
            if($world->getPermission() !== null) {
                if(!$player->hasPermission($world->getPermission())) {
                    $player->sendMessage($this->getWorlds()->getMessage("general.permission"));

                    $player->teleport($this->getWorlds()->getServer()->getDefaultLevel()->getSafeSpawn());
                }
            }

            if($world->getGamemode() !== null) {
                if(!$player->hasPermission("worlds.admin.gamemode")) {
                    $player->setGamemode($world->getGamemode());
                }
            }

            if($world->getFly() === true OR $player->hasPermission("worlds.admin.fly")) {
                $player->setAllowFlight(true);
            } elseif($world->getFly() === false) {
                $player->setAllowFlight(false);
            }
        }
    }

    /**
     * @param EntityLevelChangeEvent $event
     */
    public function onEntityLevelChange(EntityLevelChangeEvent $event): void {
        $player = $event->getEntity();
        $foldername = $event->getTarget()->getFolderName();

        if($world = $this->getWorlds()->getWorldByName($foldername)) {
            if($player instanceof Player) {
                if($world->getPermission() !== null) {
                    if(!$player->hasPermission($world->getPermission())) {
                        $player->sendMessage($this->getWorlds()->getMessage("general.permission"));

                        $event->setCancelled();

                        return;
                    }
                }

                if($world->getGamemode() !== null) {
                    if(!$player->hasPermission("worlds.admin.gamemode")) {
                        $player->setGamemode($world->getGamemode());
                    }
                }

                if($world->getFly() === true OR $player->hasPermission("worlds.admin.fly")) {
                    $player->setAllowFlight(true);
                } elseif($world->getFly() === false) {
                    $player->setAllowFlight(false);
                }
            }
        }
    }

    /**
     * @param BlockBreakEvent $event
     */
    public function onBlockBreak(BlockBreakEvent $event): void {
        $player = $event->getPlayer();
        $foldername = $player->getLevel()->getFolderName();

        if($world = $this->getWorlds()->getWorldByName($foldername)) {
            if(!$player->hasPermission("worlds.admin.build")) {
                if($world->getBuild() === false) {
                    $event->setCancelled();
                }
            }
        }
    }

    /**
     * @param BlockPlaceEvent $event
     */
    public function onBlockPlace(BlockPlaceEvent $event): void {
        $player = $event->getPlayer();
        $foldername = $player->getLevel()->getFolderName();

        if($world = $this->getWorlds()->getWorldByName($foldername)) {
            if(!$player->hasPermission("worlds.admin.build")) {
                if($world->getBuild() === false) {
                    $event->setCancelled();
                }
            }
        }
    }

    /**
     * @param EntityDamageEvent $event
     */
    public function onEntityDamage(EntityDamageEvent $event): void {
        $player = $event->getEntity();
        $level = $player->getLevel();

        if($level === null) {
            return;
        }

        $foldername = $level->getFolderName();

        if($world = $this->getWorlds()->getWorldByName($foldername)) {
            if($player instanceof Player) {
                if($event instanceof EntityDamageByEntityEvent) {
                    if($world->getPvp() === false) {
                        $event->setCancelled();
                    }
                } else {
                    if($event->getCause() !== EntityDamageEvent::CAUSE_VOID) {
                        if($world->getDamage() === false) {
                            $event->setCancelled();
                        }
                    }
                }
            }
        }
    }

    /**
     * @param ExplosionPrimeEvent $event
     */
    public function onExplosionPrime(ExplosionPrimeEvent $event): void {
        $player = $event->getEntity();
        $foldername = $player->getLevel()->getFolderName();

        if($world = $this->getWorlds()->getWorldByName($foldername)) {
            if($world->getExplode() === false) {
                $event->setCancelled();
            }
        }
    }

    /**
     * @param PlayerDropItemEvent $event
     */
    public function onPlayerDropItem(PlayerDropItemEvent $event): void {
        $player = $event->getPlayer();
        $foldername = $player->getLevel()->getFolderName();

        if($world = $this->getWorlds()->getWorldByName($foldername)) {
            if($world->getDrop() === false) {
                $event->setCancelled();
            }
        }
    }

    /**
     * @param PlayerExhaustEvent $event
     */
    public function onPlayerExhaust(PlayerExhaustEvent $event): void {
        $player = $event->getPlayer();
        $foldername = $player->getLevel()->getFolderName();

        if($world = $this->getWorlds()->getWorldByName($foldername)) {
            if($world->getHunger() === false) {
                $event->setCancelled();
            }
        }
    }

    /**
     * @param PlayerInteractEvent $event
     */
    public function onInteract(PlayerInteractEvent $event): void {
        $player = $event->getPlayer();
        $foldername = $player->getLevel()->getFolderName();

        if($world = $this->getWorlds()->getWorldByName($foldername)) {
            if(!$player->hasPermission("worlds.admin.interact")) {
                if($world->getInteract() === false) {
                    $event->setCancelled();
                }
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

<?php
/**
 * Worlds | EventListener
 */

namespace surva\worlds;

use pocketmine\block\Grass;
use pocketmine\block\ItemFrame;
use pocketmine\entity\object\Painting;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\block\LeavesDecayEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\entity\ExplosionPrimeEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerBucketEmptyEvent;
use pocketmine\event\player\PlayerDropItemEvent;
use pocketmine\event\player\PlayerExhaustEvent;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerItemConsumeEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\world\WorldLoadEvent;
use pocketmine\item\PaintingItem;
use pocketmine\item\Potion;
use pocketmine\item\TieredTool;
use pocketmine\player\GameMode;
use pocketmine\player\Player;

class EventListener implements Listener
{

    /* @var Worlds */
    private $worlds;

    public function __construct(Worlds $worlds)
    {
        $this->worlds = $worlds;
    }

    /**
     * @param  \pocketmine\event\world\WorldLoadEvent  $event
     */
    public function onWorldLoad(WorldLoadEvent $event): void
    {
        $folderName = $event->getWorld()->getFolderName();

        $this->getWorlds()->loadWorld($folderName);
    }

    /**
     * @param  PlayerJoinEvent  $event
     */
    public function onPlayerJoin(PlayerJoinEvent $event): void
    {
        $player     = $event->getPlayer();
        $targetLvl  = $player->getWorld();
        $folderName = $targetLvl->getFolderName();

        if ($world = $this->getWorlds()->getWorldByName($folderName)) {
            if ($world->getPermission() !== null) {
                if (!$player->hasPermission($world->getPermission())) {
                    $player->sendMessage($this->getWorlds()->getMessage("general.permission"));

                    $defaultWorld = $this->getWorlds()->getServer()->getWorldManager()->getDefaultWorld();

                    if ($defaultWorld === null) {
                        return;
                    }

                    $player->teleport($defaultWorld->getSafeSpawn());
                }
            }

            if ($world->getGamemode() !== null) {
                if (!$player->hasPermission("worlds.special.gamemode")) {
                    $player->setGamemode(GameMode::fromString($world->getGamemode()));
                }
            }

            if ($world->getFly() === true or $player->hasPermission("worlds.special.fly")) {
                $player->setAllowFlight(true);
            } elseif ($world->getFly() === false) {
                $player->setAllowFlight(false);
            }

            if ($world->getDaylightCycle() === true) {
                $targetLvl->startTime();
            } elseif ($world->getDaylightCycle() === false) {
                $targetLvl->stopTime();
            }
        }
    }

    /**
     * @param  EntityLevelChangeEvent  $event
     */
    // TODO: find new event name
    /*public function onEntityLevelChange(EntityLevelChangeEvent $event): void
    {
        $player     = $event->getEntity();
        $targetLvl  = $event->getTarget();
        $folderName = $targetLvl->getFolderName();

        if ($world = $this->getWorlds()->getWorldByName($folderName)) {
            if ($player instanceof Player) {
                if ($world->getPermission() !== null) {
                    if (!$player->hasPermission($world->getPermission())) {
                        $player->sendMessage($this->getWorlds()->getMessage("general.permission"));

                        $event->setCancelled();

                        return;
                    }
                }

                if ($world->getGamemode() !== null) {
                    if (!$player->hasPermission("worlds.special.gamemode")) {
                        $player->setGamemode(GameMode::fromString($world->getGamemode()));
                    }
                }

                if ($world->getFly() === true or $player->hasPermission("worlds.special.fly")) {
                    $player->setAllowFlight(true);
                } elseif ($world->getFly() === false) {
                    $player->setAllowFlight(false);
                }

                if ($world->getDaylightCycle() === true) {
                    $targetLvl->startTime();
                } elseif ($world->getDaylightCycle() === false) {
                    $targetLvl->stopTime();
                }
            }
        }
    }*/

    /**
     * @param  BlockBreakEvent  $event
     */
    public function onBlockBreak(BlockBreakEvent $event): void
    {
        $player     = $event->getPlayer();
        $folderName = $player->getWorld()->getFolderName();

        if ($world = $this->getWorlds()->getWorldByName($folderName)) {
            if (!$player->hasPermission("worlds.admin.build")) {
                if ($world->getBuild() === false) {
                    $event->cancel();
                }
            }
        }
    }

    /**
     * @param  BlockPlaceEvent  $event
     */
    public function onBlockPlace(BlockPlaceEvent $event): void
    {
        $player     = $event->getPlayer();
        $folderName = $player->getWorld()->getFolderName();

        if ($world = $this->getWorlds()->getWorldByName($folderName)) {
            if (!$player->hasPermission("worlds.admin.build")) {
                if ($world->getBuild() === false) {
                    $event->cancel();
                }
            }
        }
    }

    /**
     * @param  PlayerBucketEmptyEvent  $event
     */
    public function onPlayerBucketEmpty(PlayerBucketEmptyEvent $event)
    {
        $player     = $event->getPlayer();
        $folderName = $player->getWorld()->getFolderName();

        if ($world = $this->getWorlds()->getWorldByName($folderName)) {
            if (!$player->hasPermission("worlds.admin.build")) {
                if ($world->getBuild() === false) {
                    $event->cancel();
                }
            }
        }
    }

    /**
     * @param  EntityDamageEvent  $event
     */
    public function onEntityDamage(EntityDamageEvent $event): void
    {
        $entity = $event->getEntity();
        $level  = $entity->getWorld();

        if ($level === null) {
            return;
        }

        $folderName = $level->getFolderName();

        if ($world = $this->getWorlds()->getWorldByName($folderName)) {
            if ($entity instanceof Player) {
                if ($event instanceof EntityDamageByEntityEvent) {
                    if ($world->getPvp() === false) {
                        $event->cancel();
                    }
                } elseif ($event->getCause() !== EntityDamageEvent::CAUSE_VOID) {
                    if ($world->getDamage() === false) {
                        $event->cancel();
                    }
                }
            } elseif ($entity instanceof Painting) {
                if ($event instanceof EntityDamageByEntityEvent) {
                    $damager = $event->getDamager();

                    if ($damager instanceof Player) {
                        if (!$damager->hasPermission("worlds.admin.build")) {
                            if ($world->getBuild() === false) {
                                $event->cancel();
                            }
                        }
                    } elseif ($world->getBuild() === false) {
                        $event->cancel();
                    }
                } elseif ($world->getBuild() === false) {
                    $event->cancel();
                }
            }
        }
    }

    /**
     * @param  ExplosionPrimeEvent  $event
     */
    public function onExplosionPrime(ExplosionPrimeEvent $event): void
    {
        $player     = $event->getEntity();
        $folderName = $player->getWorld()->getFolderName();

        if ($world = $this->getWorlds()->getWorldByName($folderName)) {
            if ($world->getExplode() === false) {
                $event->cancel();
            }
        }
    }

    /**
     * @param  PlayerDropItemEvent  $event
     */
    public function onPlayerDropItem(PlayerDropItemEvent $event): void
    {
        $player     = $event->getPlayer();
        $folderName = $player->getWorld()->getFolderName();

        if ($world = $this->getWorlds()->getWorldByName($folderName)) {
            if ($world->getDrop() === false) {
                $event->cancel();
            }
        }
    }

    /**
     * @param  PlayerExhaustEvent  $event
     */
    public function onPlayerExhaust(PlayerExhaustEvent $event): void
    {
        $player     = $event->getPlayer();
        $folderName = $player->getWorld()->getFolderName();

        if ($world = $this->getWorlds()->getWorldByName($folderName)) {
            if ($world->getHunger() === false) {
                $event->cancel();
            }
        }
    }

    /**
     * @param  PlayerInteractEvent  $event
     */
    public function onPlayerInteract(PlayerInteractEvent $event): void
    {
        $player = $event->getPlayer();
        $item   = $event->getItem();
        $block  = $event->getBlock();

        $folderName = $player->getWorld()->getFolderName();

        if ($world = $this->getWorlds()->getWorldByName($folderName)) {
            if (!$player->hasPermission("worlds.admin.interact")) {
                if ($world->getInteract() === false) {
                    $event->cancel();
                }
            }

            if (
              $item instanceof PaintingItem or
              $block instanceof ItemFrame or
              ($item instanceof TieredTool and $block instanceof Grass)
            ) {
                if (!$player->hasPermission("worlds.admin.build")) {
                    if ($world->getBuild() === false) {
                        $event->cancel();
                    }
                }
            }
        }
    }

    /**
     * @param  \pocketmine\event\block\LeavesDecayEvent  $event
     */
    public function onLeavesDecay(LeavesDecayEvent $event): void
    {
        $folderName = $event->getBlock()->getPosition()->getWorld()->getFolderName();

        if ($world = $this->getWorlds()->getWorldByName($folderName)) {
            if ($world->getLeavesDecay() === false) {
                $event->cancel();
            }
        }
    }

    /**
     * @param  \pocketmine\event\player\PlayerItemConsumeEvent  $event
     */
    public function onPlayerItemConsume(PlayerItemConsumeEvent $event): void
    {
        $player     = $event->getPlayer();
        $item       = $event->getItem();
        $folderName = $player->getWorld()->getFolderName();

        if (!($item instanceof Potion)) {
            return;
        }

        if ($world = $this->getWorlds()->getWorldByName($folderName)) {
            if ($world->getPotion() === false) {
                $event->cancel();
            }
        }
    }

    /**
     * @return Worlds
     */
    public function getWorlds(): Worlds
    {
        return $this->worlds;
    }

}

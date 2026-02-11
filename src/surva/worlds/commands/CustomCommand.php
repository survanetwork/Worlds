<?php

/**
 * Worlds | parent class for main commands of the
 * Worlds plugin
 */

namespace surva\worlds\commands;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\plugin\Plugin;
use pocketmine\plugin\PluginOwned;
use pocketmine\utils\TextFormat;
use surva\worlds\Worlds;

class CustomCommand extends Command implements PluginOwned
{
    private Worlds $worlds;

    public function __construct(Worlds $worlds, string $name, string $permission)
    {
        $this->worlds = $worlds;
        $this->setPermission($permission);

        parent::__construct($name);
    }

    /**
     * Execute this sub command, check permissions before
     *
     * @param CommandSender $sender
     * @param string $commandLabel
     * @param string[] $args
     *
     * @return bool
     */
    public function execute(CommandSender $sender, string $commandLabel, array $args): bool
    {
        array_shift($args);

        $hasAllPermissions = true;
        foreach ($this->getPermissions() as $permission) {
            if (!$sender->hasPermission($permission)) {
                $hasAllPermissions = false;
            }
        }

        if (!$hasAllPermissions) {
            $sender->sendMessage(
                $this->getWorlds()->getServer()->getLanguage()->translateString(
                    TextFormat::RED . "%commands.generic.permission"
                )
            );

            return true;
        }

        return $this->do($sender, $args);
    }

    /**
     * Execution method of the sub command
     *
     * @param CommandSender $sender
     * @param string[] $args
     *
     * @return bool
     */
    public function do(CommandSender $sender, array $args): bool
    {
        return false;
    }

    public function getOwningPlugin(): Plugin
    {
        return $this->worlds;
    }

    /**
     * @return Worlds
     */
    protected function getWorlds(): Worlds
    {
        return $this->worlds;
    }
}

<?php
/**
 * Worlds | custom command class
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
     * @param  \pocketmine\command\CommandSender  $sender
     * @param  string  $commandLabel
     * @param  array  $args
     *
     * @return bool
     */
    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
        array_shift($args);

        if (!($sender->hasPermission($this->getPermission()))) {
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
     * @param  \pocketmine\command\CommandSender  $sender
     * @param  array  $args
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
     * @return \surva\worlds\Worlds
     */
    protected function getWorlds(): Worlds
    {
        return $this->worlds;
    }

}

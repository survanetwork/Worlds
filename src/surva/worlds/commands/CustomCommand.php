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

    /* @var Worlds */
    private $worlds;

    /* @var string */
    private $permission;

    public function __construct(Worlds $worlds, string $name, string $permission)
    {
        $this->worlds     = $worlds;
        $this->permission = $permission;

        parent::__construct($name);
    }

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

    public function do(CommandSender $sender, array $args): bool
    {
        return false;
    }

    public function getOwningPlugin(): Plugin
    {
        return $this->worlds;
    }

    /**
     * @return string
     */
    public function getPermission(): string
    {
        return $this->permission;
    }

    /**
     * @return Worlds
     */
    public function getWorlds(): Worlds
    {
        return $this->worlds;
    }

}

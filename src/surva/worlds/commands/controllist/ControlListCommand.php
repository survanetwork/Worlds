<?php

/**
 * Worlds | control list command
 */

namespace surva\worlds\commands\controllist;

use pocketmine\command\CommandSender;
use surva\worlds\commands\CustomCommand;
use surva\worlds\types\World;
use surva\worlds\Worlds;

class ControlListCommand extends CustomCommand
{
    private World $world;
    private string $flagName;

    /**
     * @param  \surva\worlds\Worlds  $worlds
     * @param  \surva\worlds\types\World  $world
     * @param  string  $flagName
     * @param  string  $name
     * @param  string  $permission
     */
    public function __construct(Worlds $worlds, World $world, string $flagName, string $name, string $permission)
    {
        $this->world = $world;
        $this->flagName = $flagName;

        parent::__construct($worlds, $name, $permission);
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
        $args = array_slice($args, 2);

        return parent::execute($sender, $commandLabel, $args);
    }

    /**
     * @return string
     */
    protected function getFlagName(): string
    {
        return $this->flagName;
    }

    /**
     * @return \surva\worlds\types\World
     */
    protected function getWorld(): World
    {
        return $this->world;
    }
}

<?php

/**
 * Worlds | parent class for commands used to manage control lists
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

    public function __construct(Worlds $worlds, World $world, string $flagName, string $name, string $permission)
    {
        $this->world = $world;
        $this->flagName = $flagName;

        parent::__construct($worlds, $name, $permission);
    }

    /**
     * @inheritDoc
     */
    public function execute(CommandSender $sender, string $commandLabel, array $args): bool
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
     * @return World
     */
    protected function getWorld(): World
    {
        return $this->world;
    }
}

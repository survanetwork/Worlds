<?php
/**
 * Created by PhpStorm.
 * User: jarne
 * Date: 01.10.17
 * Time: 11:29
 */

namespace surva\worlds\commands;

use pocketmine\command\CommandSender;
use pocketmine\command\PluginCommand;
use pocketmine\Player;
use pocketmine\utils\TextFormat;
use surva\worlds\Worlds;

class CustomCommand extends PluginCommand {
    /* @var Worlds */
    private $worlds;

    /* @var string */
    private $permission;

    public function __construct(Worlds $worlds, string $name, string $permission) {
        $this->worlds = $worlds;
        $this->permission = $permission;

        parent::__construct($name, $worlds);
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args) {
        array_shift($args);

        if(!($sender instanceof Player)) {
            $sender->sendMessage($this->getWorlds()->getMessage("general.command.ingame"));

            return true;
        }

        if(!($sender->hasPermission($this->getPermission()))) {
            $sender->sendMessage(
                $this->getWorlds()->getServer()->getLanguage()->translateString(
                    TextFormat::RED . "%commands.generic.permission"
                )
            );

            return true;
        }

        return $this->do($sender, $args);
    }

    public function do(Player $player, array $args) {
    }

    /**
     * @return string
     */
    public function getPermission(): string {
        return $this->permission;
    }

    /**
     * @return Worlds
     */
    public function getWorlds(): Worlds {
        return $this->worlds;
    }
}

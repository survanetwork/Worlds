<?php
/**
 * Worlds | unset parameter command
 */

namespace surva\worlds\commands;

use pocketmine\Player;
use surva\worlds\form\WorldSettingsForm;

class UnsetCommand extends CustomCommand {
    public function do(Player $player, array $args) {
        $folderName = $player->getLevel()->getFolderName();

        if(!($world = $this->getWorlds()->getWorldByName($folderName))) {
            $player->sendMessage($this->getWorlds()->getMessage("general.world.notloaded"));

            return true;
        }

        if(count($args) === 0) {
            $wsForm = new WorldSettingsForm($this->getWorlds(), $folderName, $world);

            $player->sendForm($wsForm);

            return true;
        }

        if(!(count($args) === 1)) {
            return false;
        }

        if(!(in_array(
            $args[0],
            array("permission", "gamemode", "build", "pvp", "damage", "interact", "explode", "drop", "hunger", "fly")
        ))) {
            return false;
        }

        $world->removeValue($args[0]);

        $player->sendMessage(
            $this->getWorlds()->getMessage(
                "unset.success",
                array("world" => $player->getLevel()->getFolderName(), "key" => $args[0])
            )
        );

        return true;
    }
}

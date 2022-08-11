<?php

/**
 * Worlds | control list add command
 */

namespace surva\worlds\commands\controllist;

use pocketmine\command\CommandSender;

class ListAddCommand extends ControlListCommand
{
    public function do(CommandSender $sender, array $args): bool
    {
        if (count($args) !== 1) {
            return false;
        }

        $item = $args[0];
        $flag = $this->getFlagName();

        $controlList = $this->getWorld()->getControlListContent($flag);

        if ($controlList === null) {
            return false;
        }

        $controlList->add($item);
        $this->getWorld()->saveControlList($flag);

        $sender->sendMessage(
            $this->getWorlds()->getMessage(
                "controllist.add.success",
                ["item" => $item, "key" => $flag]
            )
        );

        return true;
    }
}

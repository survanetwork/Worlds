<?php

/**
 * Worlds | control list remove command
 */

namespace surva\worlds\commands\controllist;

use pocketmine\command\CommandSender;

class ListRemoveCommand extends ControlListCommand
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

        $controlList->remove($item);
        $this->getWorld()->saveControlList($flag);

        $sender->sendMessage(
            $this->getWorlds()->getMessage(
                "controllist.remove.success",
                ["item" => $item, "key" => $flag]
            )
        );

        return true;
    }
}

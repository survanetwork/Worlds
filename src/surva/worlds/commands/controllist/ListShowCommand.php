<?php

/**
 * Worlds | control list show command
 */

namespace surva\worlds\commands\controllist;

use pocketmine\command\CommandSender;

class ListShowCommand extends ControlListCommand
{
    public function do(CommandSender $sender, array $args): bool
    {
        if (count($args) !== 0) {
            return false;
        }

        $flag = $this->getFlagName();

        $controlList = $this->getWorld()->getControlListContent($flag);

        if ($controlList === null) {
            return false;
        }

        $listArray = $controlList->getList();

        $this->getWorlds()->sendMessage(
            $sender,
            "controllist.list.description",
            ["key" => $flag, "content" => implode(", ", $listArray)]
        );

        return true;
    }
}

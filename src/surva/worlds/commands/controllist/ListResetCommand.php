<?php

/**
 * Worlds | control list reset command
 */

namespace surva\worlds\commands\controllist;

use pocketmine\command\CommandSender;

class ListResetCommand extends ControlListCommand
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

        $controlList->reset();
        $this->getWorld()->saveControlList($flag);

        $sender->sendMessage(
            $this->getWorlds()->getMessage(
                "controllist.reset.success",
                ["key" => $flag]
            )
        );

        return true;
    }
}

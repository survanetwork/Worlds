<?php

/**
 * Worlds | control list reset command
 */

namespace surva\worlds\commands\controllist;

use InvalidArgumentException;
use pocketmine\command\CommandSender;
use surva\worlds\types\exception\ConfigSaveException;

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
        try {
            $this->getWorld()->saveControlList($flag);
        } catch (ConfigSaveException | InvalidArgumentException $e) {
            $sender->sendMessage($this->getWorlds()->getMessage("general.config.save_error"));

            return true;
        }

        $sender->sendMessage(
            $this->getWorlds()->getMessage(
                "controllist.reset.success",
                ["key" => $flag]
            )
        );

        return true;
    }
}

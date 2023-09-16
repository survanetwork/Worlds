<?php

/**
 * Worlds | control list add command
 */

namespace surva\worlds\commands\controllist;

use InvalidArgumentException;
use pocketmine\command\CommandSender;
use pocketmine\item\StringToItemParser;
use surva\worlds\types\exception\ConfigSaveException;
use surva\worlds\utils\Flags;

class ListAddCommand extends ControlListCommand
{
    /**
     * @inheritDoc
     */
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

        if (
            in_array($flag, Flags::ITEM_CONTROL_LISTS)
            && StringToItemParser::getInstance()->parse($item) === null
        ) {
            $this->getWorlds()->sendMessage(
                $sender,
                "controllist.add.error_code.invalid_item",
                ["item" => $item]
            );

            return true;
        }

        $controlList->add($item);
        try {
            $this->getWorld()->saveControlList($flag);
        } catch (ConfigSaveException | InvalidArgumentException $e) {
            $this->getWorlds()->sendMessage($sender, "general.config.save_error");

            return true;
        }

        $this->getWorlds()->sendMessage(
            $sender,
            "controllist.add.success",
            ["item" => $item, "key" => $flag]
        );

        return true;
    }
}

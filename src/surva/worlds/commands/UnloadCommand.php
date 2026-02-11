<?php

/**
 * Worlds | unload world command
 */

namespace surva\worlds\commands;

use pocketmine\command\CommandSender;
use surva\worlds\logic\exception\UnloadDefaultLevelException;
use surva\worlds\logic\exception\UnloadFailedException;
use surva\worlds\logic\exception\WorldNotExistException;
use surva\worlds\logic\WorldActions;
use surva\worlds\utils\Messages;
use surva\worlds\Worlds;

class UnloadCommand extends CustomCommand
{
    /**
     * @inheritDoc
     */
    public function do(CommandSender $sender, array $args): bool
    {
        if (!(count($args) === 1)) {
            return false;
        }

        $messages = new Messages($this->getWorlds(), $sender);

        $res = self::tryToUnload($this->getWorlds(), $args[0], $sender, $messages);

        if ($res) {
            $sender->sendMessage($messages->getMessage("unload.success", ["world" => $args[0]]));
        }

        return true;
    }

    /**
     * Try to unload a world by name or send error message if it fails
     *
     * @param Worlds $worlds
     * @param string $worldName
     * @param CommandSender $sender
     * @param Messages $messages
     *
     * @return bool
     */
    public static function tryToUnload(
        Worlds $worlds,
        string $worldName,
        CommandSender $sender,
        Messages $messages
    ): bool {
        try {
            WorldActions::unloadIfLoaded($worlds, $worldName);
        } catch (UnloadDefaultLevelException $e) {
            $sender->sendMessage($messages->getMessage("unload.default"));

            return false;
        } catch (WorldNotExistException $e) {
            $sender->sendMessage($messages->getMessage("general.world.not_exist", ["name" => $worldName]));

            return false;
        } catch (UnloadFailedException $e) {
            $sender->sendMessage($messages->getMessage("unload.failed"));

            return false;
        }

        return true;
    }
}

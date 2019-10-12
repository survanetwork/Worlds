<?php
/**
 * Worlds | defaults set / unset command
 */

namespace surva\worlds\commands;

use pocketmine\Player;
use pocketmine\Server;
use pocketmine\utils\TextFormat;
use surva\worlds\form\DefaultSettingsForm;

class DefaultsCommand extends CustomCommand {
    public function do(Player $player, array $args) {
        $defaults = $this->getWorlds()->getDefaults();

        if(count($args) === 0) {
            $dfForm = new DefaultSettingsForm($this->getWorlds(), $defaults);

            $player->sendForm($dfForm);

            return true;
        }

        switch($args[0]) {
            case "legacy":
                $player->sendMessage(
                    $this->getWorlds()->getMessage(
                        "defaults.list.values",
                        array(
                            "permission" => $this->formatText($defaults->getPermission()),
                            "gamemode" => $this->formatGamemode($defaults->getGamemode()),
                            "build" => $this->formatBool($defaults->getBuild()),
                            "pvp" => $this->formatBool($defaults->getPvp()),
                            "damage" => $this->formatBool($defaults->getDamage()),
                            "interact" => $this->formatBool($defaults->getInteract()),
                            "explode" => $this->formatBool($defaults->getExplode()),
                            "drop" => $this->formatBool($defaults->getDrop()),
                            "hunger" => $this->formatBool($defaults->getHunger()),
                            "fly" => $this->formatBool($defaults->getFly()),
                        )
                    )
                );

                return true;
            case "set":
                if(!(count($args) === 3)) {
                    return false;
                }

                if(!(in_array(
                    $args[1],
                    array(
                        "permission",
                        "gamemode",
                        "build",
                        "pvp",
                        "damage",
                        "interact",
                        "explode",
                        "drop",
                        "hunger",
                        "fly",
                    )
                ))) {
                    return false;
                }

                if($args[1] === "permission") {
                    $player->sendMessage($this->getWorlds()->getMessage("set.permission.notdefault"));

                    return true;
                } elseif($args[1] === "gamemode") {
                    if(($args[2] = Server::getGamemodeFromString($args[1])) === -1) {
                        $player->sendMessage($this->getWorlds()->getMessage("set.gamemode.notexist"));

                        return true;
                    }

                    $defaults->updateValue($args[1], $args[2]);

                    $player->sendMessage(
                        $this->getWorlds()->getMessage(
                            "defaults.set.success",
                            array("key" => $args[1], "value" => $args[2])
                        )
                    );
                } else {
                    if(!(in_array($args[2], array("true", "false")))) {
                        $player->sendMessage($this->getWorlds()->getMessage("set.notbool", array("key" => $args[1])));

                        return true;
                    }

                    $defaults->updateValue($args[1], $args[2]);

                    $player->sendMessage(
                        $this->getWorlds()->getMessage(
                            "defaults.set.success",
                            array("key" => $args[1], "value" => $args[2])
                        )
                    );
                }

                return true;
            case "unset":
                if(!(count($args) === 2)) {
                    return false;
                }

                if(!(in_array(
                    $args[1],
                    array(
                        "permission",
                        "gamemode",
                        "build",
                        "pvp",
                        "damage",
                        "interact",
                        "explode",
                        "drop",
                        "hunger",
                        "fly",
                    )
                ))) {
                    return false;
                }

                $defaults->removeValue($args[1]);

                $player->sendMessage(
                    $this->getWorlds()->getMessage(
                        "defaults.unset.success",
                        array("key" => $args[1])
                    )
                );

                return true;
            default:
                return false;
        }
    }

    /**
     * Format a text for showing its value
     *
     * @param string|null $value
     * @return string
     */
    private function formatText(?string $value): string {
        if($value === null) {
            return $this->getWorlds()->getMessage("set.list.notset");
        }

        return TextFormat::WHITE . $value;
    }

    /**
     * Format a gamemode for showing its value
     *
     * @param int|null $value
     * @return string
     */
    private function formatGamemode(?int $value): string {
        if($value === null) {
            return $this->getWorlds()->getMessage("set.list.notset");
        }

        return $this->getWorlds()->getServer()->getLanguage()->translateString(
            TextFormat::WHITE . Server::getGamemodeString($value)
        );
    }

    /**
     * Format a boolean for showing its value
     *
     * @param bool|null $value
     * @return string
     */
    private function formatBool(?bool $value): string {
        if($value === true) {
            return TextFormat::GREEN . "true";
        } elseif($value === false) {
            return TextFormat::RED . "false";
        } else {
            return $this->getWorlds()->getMessage("set.list.notset");
        }
    }
}

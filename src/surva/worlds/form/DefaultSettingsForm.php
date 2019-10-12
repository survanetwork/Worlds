<?php
/**
 * Worlds | default settings form
 */

namespace surva\worlds\form;

use pocketmine\Player;
use pocketmine\Server;
use surva\worlds\types\Defaults;
use surva\worlds\Worlds;

class DefaultSettingsForm extends SettingsForm {
    public function __construct(Worlds $wsInstance, Defaults $defaults) {
        parent::__construct($wsInstance, $defaults);

        $this->title = $this->getWorlds()->getMessage("forms.default.title");
        $this->content = array(
            array(
                "type" => "dropdown",
                "text" => $this->getWorlds()->getMessage("forms.world.params.gamemode"),
                "options" => array(
                    $this->getWorlds()->getMessage("forms.world.options.notset"),
                    Server::getGamemodeString(Player::SURVIVAL),
                    Server::getGamemodeString(Player::CREATIVE),
                    Server::getGamemodeString(Player::ADVENTURE),
                    Server::getGamemodeString(Player::SPECTATOR)
                ),
                "default" => $this->convGamemode($defaults->getGamemode())
            ),
            array(
                "type" => "dropdown",
                "text" => $this->getWorlds()->getMessage("forms.world.params.build"),
                "options" => array(
                    $this->getWorlds()->getMessage("forms.world.options.notset"),
                    $this->getWorlds()->getMessage("forms.world.options.false"),
                    $this->getWorlds()->getMessage("forms.world.options.true")
                ),
                "default" => $this->convBool($defaults->getBuild())
            ),
            array(
                "type" => "dropdown",
                "text" => $this->getWorlds()->getMessage("forms.world.params.pvp"),
                "options" => array(
                    $this->getWorlds()->getMessage("forms.world.options.notset"),
                    $this->getWorlds()->getMessage("forms.world.options.false"),
                    $this->getWorlds()->getMessage("forms.world.options.true")
                ),
                "default" => $this->convBool($defaults->getPvp())
            ),
            array(
                "type" => "dropdown",
                "text" => $this->getWorlds()->getMessage("forms.world.params.damage"),
                "options" => array(
                    $this->getWorlds()->getMessage("forms.world.options.notset"),
                    $this->getWorlds()->getMessage("forms.world.options.false"),
                    $this->getWorlds()->getMessage("forms.world.options.true")
                ),
                "default" => $this->convBool($defaults->getDamage())
            ),
            array(
                "type" => "dropdown",
                "text" => $this->getWorlds()->getMessage("forms.world.params.interact"),
                "options" => array(
                    $this->getWorlds()->getMessage("forms.world.options.notset"),
                    $this->getWorlds()->getMessage("forms.world.options.false"),
                    $this->getWorlds()->getMessage("forms.world.options.true")
                ),
                "default" => $this->convBool($defaults->getInteract())
            ),
            array(
                "type" => "dropdown",
                "text" => $this->getWorlds()->getMessage("forms.world.params.explode"),
                "options" => array(
                    $this->getWorlds()->getMessage("forms.world.options.notset"),
                    $this->getWorlds()->getMessage("forms.world.options.false"),
                    $this->getWorlds()->getMessage("forms.world.options.true")
                ),
                "default" => $this->convBool($defaults->getExplode())
            ),
            array(
                "type" => "dropdown",
                "text" => $this->getWorlds()->getMessage("forms.world.params.drop"),
                "options" => array(
                    $this->getWorlds()->getMessage("forms.world.options.notset"),
                    $this->getWorlds()->getMessage("forms.world.options.false"),
                    $this->getWorlds()->getMessage("forms.world.options.true")
                ),
                "default" => $this->convBool($defaults->getDrop())
            ),
            array(
                "type" => "dropdown",
                "text" => $this->getWorlds()->getMessage("forms.world.params.hunger"),
                "options" => array(
                    $this->getWorlds()->getMessage("forms.world.options.notset"),
                    $this->getWorlds()->getMessage("forms.world.options.false"),
                    $this->getWorlds()->getMessage("forms.world.options.true")
                ),
                "default" => $this->convBool($defaults->getHunger())
            ),
            array(
                "type" => "dropdown",
                "text" => $this->getWorlds()->getMessage("forms.world.params.fly"),
                "options" => array(
                    $this->getWorlds()->getMessage("forms.world.options.notset"),
                    $this->getWorlds()->getMessage("forms.world.options.false"),
                    $this->getWorlds()->getMessage("forms.world.options.true")
                ),
                "default" => $this->convBool($defaults->getFly())
            )
        );
    }

    /**
     * Getting a response from the client form
     *
     * @param Player $player
     * @param mixed $data
     */
    public function handleResponse(Player $player, $data): void {
        if(!is_array($data)) {
            return;
        }

        if(count($data) !== 9) {
            return;
        }

        $this->procGamemode("gamemode", $data[0]);
        $this->procBool("build", $data[1]);
        $this->procBool("pvp", $data[2]);
        $this->procBool("damage", $data[3]);
        $this->procBool("interact", $data[4]);
        $this->procBool("explode", $data[5]);
        $this->procBool("drop", $data[6]);
        $this->procBool("hunger", $data[7]);
        $this->procBool("fly", $data[8]);

        $player->sendMessage($this->getWorlds()->getMessage("forms.saved"));
    }
}

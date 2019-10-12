<?php
/**
 * Worlds | world settings form
 */

namespace surva\worlds\form;

use pocketmine\Player;
use pocketmine\Server;
use surva\worlds\types\World;
use surva\worlds\Worlds;

class WorldSettingsForm extends SettingsForm {
    public function __construct(Worlds $wsInstance, string $worldName, World $world) {
        parent::__construct($wsInstance, $world);

        $this->title = $this->getWorlds()->getMessage("forms.world.title", array("name" => $worldName));
        $this->content = array(
            array(
                "type" => "input",
                "text" => $this->getWorlds()->getMessage("forms.world.params.permission"),
                "default" => $world->getPermission()
            ),
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
                "default" => $this->convGamemode($world->getGamemode())
            ),
            array(
                "type" => "dropdown",
                "text" => $this->getWorlds()->getMessage("forms.world.params.build"),
                "options" => array(
                    $this->getWorlds()->getMessage("forms.world.options.notset"),
                    $this->getWorlds()->getMessage("forms.world.options.false"),
                    $this->getWorlds()->getMessage("forms.world.options.true")
                ),
                "default" => $this->convBool($world->getBuild())
            ),
            array(
                "type" => "dropdown",
                "text" => $this->getWorlds()->getMessage("forms.world.params.pvp"),
                "options" => array(
                    $this->getWorlds()->getMessage("forms.world.options.notset"),
                    $this->getWorlds()->getMessage("forms.world.options.false"),
                    $this->getWorlds()->getMessage("forms.world.options.true")
                ),
                "default" => $this->convBool($world->getPvp())
            ),
            array(
                "type" => "dropdown",
                "text" => $this->getWorlds()->getMessage("forms.world.params.damage"),
                "options" => array(
                    $this->getWorlds()->getMessage("forms.world.options.notset"),
                    $this->getWorlds()->getMessage("forms.world.options.false"),
                    $this->getWorlds()->getMessage("forms.world.options.true")
                ),
                "default" => $this->convBool($world->getDamage())
            ),
            array(
                "type" => "dropdown",
                "text" => $this->getWorlds()->getMessage("forms.world.params.interact"),
                "options" => array(
                    $this->getWorlds()->getMessage("forms.world.options.notset"),
                    $this->getWorlds()->getMessage("forms.world.options.false"),
                    $this->getWorlds()->getMessage("forms.world.options.true")
                ),
                "default" => $this->convBool($world->getInteract())
            ),
            array(
                "type" => "dropdown",
                "text" => $this->getWorlds()->getMessage("forms.world.params.explode"),
                "options" => array(
                    $this->getWorlds()->getMessage("forms.world.options.notset"),
                    $this->getWorlds()->getMessage("forms.world.options.false"),
                    $this->getWorlds()->getMessage("forms.world.options.true")
                ),
                "default" => $this->convBool($world->getExplode())
            ),
            array(
                "type" => "dropdown",
                "text" => $this->getWorlds()->getMessage("forms.world.params.drop"),
                "options" => array(
                    $this->getWorlds()->getMessage("forms.world.options.notset"),
                    $this->getWorlds()->getMessage("forms.world.options.false"),
                    $this->getWorlds()->getMessage("forms.world.options.true")
                ),
                "default" => $this->convBool($world->getDrop())
            ),
            array(
                "type" => "dropdown",
                "text" => $this->getWorlds()->getMessage("forms.world.params.hunger"),
                "options" => array(
                    $this->getWorlds()->getMessage("forms.world.options.notset"),
                    $this->getWorlds()->getMessage("forms.world.options.false"),
                    $this->getWorlds()->getMessage("forms.world.options.true")
                ),
                "default" => $this->convBool($world->getHunger())
            ),
            array(
                "type" => "dropdown",
                "text" => $this->getWorlds()->getMessage("forms.world.params.fly"),
                "options" => array(
                    $this->getWorlds()->getMessage("forms.world.options.notset"),
                    $this->getWorlds()->getMessage("forms.world.options.false"),
                    $this->getWorlds()->getMessage("forms.world.options.true")
                ),
                "default" => $this->convBool($world->getFly())
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

        if(count($data) !== 10) {
            return;
        }

        $defFolderName = $this->getWorlds()->getServer()->getDefaultLevel()->getFolderName();
        $plFolderName = $player->getLevel()->getFolderName();

        $isDefLvl = $defFolderName === $plFolderName;

        $this->procPerm("permission", $data[0], $isDefLvl, $player);
        $this->procGamemode("gamemode", $data[1]);
        $this->procBool("build", $data[2]);
        $this->procBool("pvp", $data[3]);
        $this->procBool("damage", $data[4]);
        $this->procBool("interact", $data[5]);
        $this->procBool("explode", $data[6]);
        $this->procBool("drop", $data[7]);
        $this->procBool("hunger", $data[8]);
        $this->procBool("fly", $data[9]);

        $player->sendMessage($this->getWorlds()->getMessage("forms.saved"));
    }

    /**
     * Evaluate permission string form response value
     *
     * @param string $name
     * @param $data
     * @param bool $isDefLvl
     * @param Player $player
     */
    private function procPerm(string $name, $data, bool $isDefLvl, Player $player): void {
        if($data === "") {
            $this->getStorage()->removeValue($name);

            return;
        }

        if($isDefLvl) {
            $player->sendMessage($this->getWorlds()->getMessage("set.permission.notdefault"));

            return;
        }

        $this->getStorage()->updateValue($name, $data);
    }
}

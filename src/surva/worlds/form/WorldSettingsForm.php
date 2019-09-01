<?php
/**
 * Worlds | world settings form
 */

namespace surva\worlds\form;

use pocketmine\form\Form;
use pocketmine\Player;
use pocketmine\Server;
use surva\worlds\types\World;
use surva\worlds\Worlds;

class WorldSettingsForm implements Form {
    /* @var Worlds */
    private $worlds;

    /* @var World */
    private $actWorld;

    /* @var string */
    private $type = "custom_form";

    /* @var string */
    private $title;

    /* @var array */
    private $content;

    public function __construct(Worlds $wsInstance, string $worldName, World $world) {
        $this->worlds = $wsInstance;
        $this->actWorld = $world;

        $this->title = $this->worlds->getMessage("forms.world.title", array("name" => $worldName));
        $this->content = array(
            array(
                "type" => "input",
                "text" => $this->worlds->getMessage("forms.world.params.permission"),
                "default" => $world->getPermission()
            ),
            array(
                "type" => "dropdown",
                "text" => $this->worlds->getMessage("forms.world.params.gamemode"),
                "options" => array(
                    $this->worlds->getMessage("forms.world.options.notset"),
                    Server::getGamemodeString(Player::SURVIVAL),
                    Server::getGamemodeString(Player::CREATIVE),
                    Server::getGamemodeString(Player::ADVENTURE),
                    Server::getGamemodeString(Player::SPECTATOR)
                ),
                "default" => $this->convGamemode($world->getGamemode())
            ),
            array(
                "type" => "dropdown",
                "text" => $this->worlds->getMessage("forms.world.params.build"),
                "options" => array(
                    $this->worlds->getMessage("forms.world.options.notset"),
                    $this->worlds->getMessage("forms.world.options.false"),
                    $this->worlds->getMessage("forms.world.options.true")
                ),
                "default" => $this->convBool($world->getBuild())
            ),
            array(
                "type" => "dropdown",
                "text" => $this->worlds->getMessage("forms.world.params.pvp"),
                "options" => array(
                    $this->worlds->getMessage("forms.world.options.notset"),
                    $this->worlds->getMessage("forms.world.options.false"),
                    $this->worlds->getMessage("forms.world.options.true")
                ),
                "default" => $this->convBool($world->getPvp())
            ),
            array(
                "type" => "dropdown",
                "text" => $this->worlds->getMessage("forms.world.params.damage"),
                "options" => array(
                    $this->worlds->getMessage("forms.world.options.notset"),
                    $this->worlds->getMessage("forms.world.options.false"),
                    $this->worlds->getMessage("forms.world.options.true")
                ),
                "default" => $this->convBool($world->getDamage())
            ),
            array(
                "type" => "dropdown",
                "text" => $this->worlds->getMessage("forms.world.params.interact"),
                "options" => array(
                    $this->worlds->getMessage("forms.world.options.notset"),
                    $this->worlds->getMessage("forms.world.options.false"),
                    $this->worlds->getMessage("forms.world.options.true")
                ),
                "default" => $this->convBool($world->getInteract())
            ),
            array(
                "type" => "dropdown",
                "text" => $this->worlds->getMessage("forms.world.params.explode"),
                "options" => array(
                    $this->worlds->getMessage("forms.world.options.notset"),
                    $this->worlds->getMessage("forms.world.options.false"),
                    $this->worlds->getMessage("forms.world.options.true")
                ),
                "default" => $this->convBool($world->getExplode())
            ),
            array(
                "type" => "dropdown",
                "text" => $this->worlds->getMessage("forms.world.params.drop"),
                "options" => array(
                    $this->worlds->getMessage("forms.world.options.notset"),
                    $this->worlds->getMessage("forms.world.options.false"),
                    $this->worlds->getMessage("forms.world.options.true")
                ),
                "default" => $this->convBool($world->getDrop())
            ),
            array(
                "type" => "dropdown",
                "text" => $this->worlds->getMessage("forms.world.params.hunger"),
                "options" => array(
                    $this->worlds->getMessage("forms.world.options.notset"),
                    $this->worlds->getMessage("forms.world.options.false"),
                    $this->worlds->getMessage("forms.world.options.true")
                ),
                "default" => $this->convBool($world->getHunger())
            ),
            array(
                "type" => "dropdown",
                "text" => $this->worlds->getMessage("forms.world.params.fly"),
                "options" => array(
                    $this->worlds->getMessage("forms.world.options.notset"),
                    $this->worlds->getMessage("forms.world.options.false"),
                    $this->worlds->getMessage("forms.world.options.true")
                ),
                "default" => $this->convBool($world->getFly())
            )
        );
    }

    /**
     * Return JSON data of the form
     *
     * @return array
     */
    public function jsonSerialize(): array {
        return array(
            "type" => $this->type,
            "title" => $this->title,
            "content" => $this->content
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

        $defFolderName = $this->worlds->getServer()->getDefaultLevel()->getFolderName();
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
    }

    /**
     * Convert stored bool to form option index
     *
     * @param bool|null $val
     * @return int
     */
    private function convBool(?bool $val): int {
        if($val === null) {
            return 0;
        }

        return $val ? 2 : 1;
    }

    /**
     * Convert stored bool to form option index
     *
     * @param int|null $gm
     * @return int
     */
    private function convGamemode(?int $gm): int {
        if($gm === null) {
            return 0;
        }

        return $gm + 1;
    }

    /**
     * Evaluate bool form response value
     *
     * @param string $name
     * @param $data
     */
    private function procBool(string $name, $data): void {
        switch($data) {
            case 1:
                $this->actWorld->updateValue($name, "false");
                break;
            case 2:
                $this->actWorld->updateValue($name, "true");
                break;
            default:
                $this->actWorld->removeValue($name);
                break;
        }
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
            $this->actWorld->removeValue($name);

            return;
        }

        if($isDefLvl) {
            $player->sendMessage($this->worlds->getMessage("set.permission.notdefault"));

            return;
        }

        $this->actWorld->updateValue($name, $data);
    }

    /**
     * Evaluate gamemode form response value
     *
     * @param string $name
     * @param $data
     */
    private function procGamemode(string $name, $data): void {
        switch($data) {
            case 1:
                $this->actWorld->updateValue($name, Player::SURVIVAL);
                break;
            case 2:
                $this->actWorld->updateValue($name, Player::CREATIVE);
                break;
            case 3:
                $this->actWorld->updateValue($name, Player::ADVENTURE);
                break;
            case 4:
                $this->actWorld->updateValue($name, Player::SPECTATOR);
                break;
            default:
                $this->actWorld->removeValue($name);
                break;
        }
    }
}

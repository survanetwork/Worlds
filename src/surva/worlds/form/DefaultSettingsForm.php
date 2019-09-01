<?php
/**
 * Worlds | default settings form
 */

namespace surva\worlds\form;

use pocketmine\form\Form;
use pocketmine\Player;
use pocketmine\Server;
use surva\worlds\types\Defaults;
use surva\worlds\Worlds;

class DefaultSettingsForm implements Form {
    /* @var Worlds */
    private $worlds;

    /* @var Defaults */
    private $defaults;

    /* @var string */
    private $type = "custom_form";

    /* @var string */
    private $title;

    /* @var array */
    private $content;

    public function __construct(Worlds $wsInstance, Defaults $defaults) {
        $this->worlds = $wsInstance;
        $this->defaults = $defaults;

        $this->title = $this->worlds->getMessage("forms.default.title");
        $this->content = array(
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
                "default" => $this->convGamemode($defaults->getGamemode())
            ),
            array(
                "type" => "dropdown",
                "text" => $this->worlds->getMessage("forms.world.params.build"),
                "options" => array(
                    $this->worlds->getMessage("forms.world.options.notset"),
                    $this->worlds->getMessage("forms.world.options.false"),
                    $this->worlds->getMessage("forms.world.options.true")
                ),
                "default" => $this->convBool($defaults->getBuild())
            ),
            array(
                "type" => "dropdown",
                "text" => $this->worlds->getMessage("forms.world.params.pvp"),
                "options" => array(
                    $this->worlds->getMessage("forms.world.options.notset"),
                    $this->worlds->getMessage("forms.world.options.false"),
                    $this->worlds->getMessage("forms.world.options.true")
                ),
                "default" => $this->convBool($defaults->getPvp())
            ),
            array(
                "type" => "dropdown",
                "text" => $this->worlds->getMessage("forms.world.params.damage"),
                "options" => array(
                    $this->worlds->getMessage("forms.world.options.notset"),
                    $this->worlds->getMessage("forms.world.options.false"),
                    $this->worlds->getMessage("forms.world.options.true")
                ),
                "default" => $this->convBool($defaults->getDamage())
            ),
            array(
                "type" => "dropdown",
                "text" => $this->worlds->getMessage("forms.world.params.interact"),
                "options" => array(
                    $this->worlds->getMessage("forms.world.options.notset"),
                    $this->worlds->getMessage("forms.world.options.false"),
                    $this->worlds->getMessage("forms.world.options.true")
                ),
                "default" => $this->convBool($defaults->getInteract())
            ),
            array(
                "type" => "dropdown",
                "text" => $this->worlds->getMessage("forms.world.params.explode"),
                "options" => array(
                    $this->worlds->getMessage("forms.world.options.notset"),
                    $this->worlds->getMessage("forms.world.options.false"),
                    $this->worlds->getMessage("forms.world.options.true")
                ),
                "default" => $this->convBool($defaults->getExplode())
            ),
            array(
                "type" => "dropdown",
                "text" => $this->worlds->getMessage("forms.world.params.drop"),
                "options" => array(
                    $this->worlds->getMessage("forms.world.options.notset"),
                    $this->worlds->getMessage("forms.world.options.false"),
                    $this->worlds->getMessage("forms.world.options.true")
                ),
                "default" => $this->convBool($defaults->getDrop())
            ),
            array(
                "type" => "dropdown",
                "text" => $this->worlds->getMessage("forms.world.params.hunger"),
                "options" => array(
                    $this->worlds->getMessage("forms.world.options.notset"),
                    $this->worlds->getMessage("forms.world.options.false"),
                    $this->worlds->getMessage("forms.world.options.true")
                ),
                "default" => $this->convBool($defaults->getHunger())
            ),
            array(
                "type" => "dropdown",
                "text" => $this->worlds->getMessage("forms.world.params.fly"),
                "options" => array(
                    $this->worlds->getMessage("forms.world.options.notset"),
                    $this->worlds->getMessage("forms.world.options.false"),
                    $this->worlds->getMessage("forms.world.options.true")
                ),
                "default" => $this->convBool($defaults->getFly())
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
                $this->defaults->updateValue($name, "false");
                break;
            case 2:
                $this->defaults->updateValue($name, "true");
                break;
            default:
                $this->defaults->removeValue($name);
                break;
        }
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
                $this->defaults->updateValue($name, Player::SURVIVAL);
                break;
            case 2:
                $this->defaults->updateValue($name, Player::CREATIVE);
                break;
            case 3:
                $this->defaults->updateValue($name, Player::ADVENTURE);
                break;
            case 4:
                $this->defaults->updateValue($name, Player::SPECTATOR);
                break;
            default:
                $this->defaults->removeValue($name);
                break;
        }
    }
}

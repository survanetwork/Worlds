<?php
/**
 * Worlds | world settings form
 */

namespace surva\worlds\form;

use pocketmine\Player;
use pocketmine\Server;
use surva\worlds\types\World;
use surva\worlds\Worlds;

class WorldSettingsForm extends SettingsForm
{

    public function __construct(Worlds $wsInstance, string $worldName, World $world)
    {
        parent::__construct($wsInstance, $world);

        $this->title   = $this->getWorlds()->getMessage("forms.world.title", ["name" => $worldName]);
        $this->content = [
          [
            "type"    => "input",
            "text"    => $this->getWorlds()->getMessage("forms.world.params.permission"),
            "default" => $world->getPermission(),
          ],
          [
            "type"    => "dropdown",
            "text"    => $this->getWorlds()->getMessage("forms.world.params.gamemode"),
            "options" => [
              $this->getWorlds()->getMessage("forms.world.options.notset"),
              Server::getGamemodeString(Player::SURVIVAL),
              Server::getGamemodeString(Player::CREATIVE),
              Server::getGamemodeString(Player::ADVENTURE),
              Server::getGamemodeString(Player::SPECTATOR),
            ],
            "default" => $this->convGamemode($world->getGamemode()),
          ],
          [
            "type"    => "dropdown",
            "text"    => $this->getWorlds()->getMessage("forms.world.params.build"),
            "options" => [
              $this->getWorlds()->getMessage("forms.world.options.notset"),
              $this->getWorlds()->getMessage("forms.world.options.false"),
              $this->getWorlds()->getMessage("forms.world.options.true"),
            ],
            "default" => $this->convBool($world->getBuild()),
          ],
          [
            "type"    => "dropdown",
            "text"    => $this->getWorlds()->getMessage("forms.world.params.pvp"),
            "options" => [
              $this->getWorlds()->getMessage("forms.world.options.notset"),
              $this->getWorlds()->getMessage("forms.world.options.false"),
              $this->getWorlds()->getMessage("forms.world.options.true"),
            ],
            "default" => $this->convBool($world->getPvp()),
          ],
          [
            "type"    => "dropdown",
            "text"    => $this->getWorlds()->getMessage("forms.world.params.damage"),
            "options" => [
              $this->getWorlds()->getMessage("forms.world.options.notset"),
              $this->getWorlds()->getMessage("forms.world.options.false"),
              $this->getWorlds()->getMessage("forms.world.options.true"),
            ],
            "default" => $this->convBool($world->getDamage()),
          ],
          [
            "type"    => "dropdown",
            "text"    => $this->getWorlds()->getMessage("forms.world.params.interact"),
            "options" => [
              $this->getWorlds()->getMessage("forms.world.options.notset"),
              $this->getWorlds()->getMessage("forms.world.options.false"),
              $this->getWorlds()->getMessage("forms.world.options.true"),
            ],
            "default" => $this->convBool($world->getInteract()),
          ],
          [
            "type"    => "dropdown",
            "text"    => $this->getWorlds()->getMessage("forms.world.params.explode"),
            "options" => [
              $this->getWorlds()->getMessage("forms.world.options.notset"),
              $this->getWorlds()->getMessage("forms.world.options.false"),
              $this->getWorlds()->getMessage("forms.world.options.true"),
            ],
            "default" => $this->convBool($world->getExplode()),
          ],
          [
            "type"    => "dropdown",
            "text"    => $this->getWorlds()->getMessage("forms.world.params.drop"),
            "options" => [
              $this->getWorlds()->getMessage("forms.world.options.notset"),
              $this->getWorlds()->getMessage("forms.world.options.false"),
              $this->getWorlds()->getMessage("forms.world.options.true"),
            ],
            "default" => $this->convBool($world->getDrop()),
          ],
          [
            "type"    => "dropdown",
            "text"    => $this->getWorlds()->getMessage("forms.world.params.hunger"),
            "options" => [
              $this->getWorlds()->getMessage("forms.world.options.notset"),
              $this->getWorlds()->getMessage("forms.world.options.false"),
              $this->getWorlds()->getMessage("forms.world.options.true"),
            ],
            "default" => $this->convBool($world->getHunger()),
          ],
          [
            "type"    => "dropdown",
            "text"    => $this->getWorlds()->getMessage("forms.world.params.fly"),
            "options" => [
              $this->getWorlds()->getMessage("forms.world.options.notset"),
              $this->getWorlds()->getMessage("forms.world.options.false"),
              $this->getWorlds()->getMessage("forms.world.options.true"),
            ],
            "default" => $this->convBool($world->getFly()),
          ],
          [
            "type"    => "dropdown",
            "text"    => $this->getWorlds()->getMessage("forms.world.params.daylightcycle"),
            "options" => [
              $this->getWorlds()->getMessage("forms.world.options.notset"),
              $this->getWorlds()->getMessage("forms.world.options.false"),
              $this->getWorlds()->getMessage("forms.world.options.true"),
            ],
            "default" => $this->convBool($world->getDaylightCycle()),
          ],
        ];
    }

    /**
     * Getting a response from the client form
     *
     * @param  Player  $player
     * @param  mixed  $data
     */
    public function handleResponse(Player $player, $data): void
    {
        if (!is_array($data)) {
            return;
        }

        if (count($data) !== 11) {
            return;
        }

        $defFolderName = $this->getWorlds()->getServer()->getDefaultLevel()->getFolderName();
        $plFolderName  = $player->getLevel()->getFolderName();

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
        $this->procBool("daylightcycle", $data[10]);

        $player->sendMessage($this->getWorlds()->getMessage("forms.saved"));
    }

    /**
     * Evaluate permission string form response value
     *
     * @param  string  $name
     * @param $data
     * @param  bool  $isDefLvl
     * @param  Player  $player
     */
    private function procPerm(string $name, $data, bool $isDefLvl, Player $player): void
    {
        if ($data === "") {
            $this->getStorage()->removeValue($name);

            return;
        }

        if ($isDefLvl) {
            $player->sendMessage($this->getWorlds()->getMessage("set.permission.notdefault"));

            return;
        }

        $this->getStorage()->updateValue($name, $data);
    }

}

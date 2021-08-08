<?php
/**
 * Worlds | world settings form
 */

namespace surva\worlds\form;

use pocketmine\Player;
use pocketmine\Server;
use surva\worlds\types\World;
use surva\worlds\utils\Flags;
use surva\worlds\Worlds;

class WorldSettingsForm extends SettingsForm
{

    public function __construct(Worlds $wsInstance, string $worldName, World $world)
    {
        parent::__construct($wsInstance, $world);

        $this->title   = $this->getWorlds()->getMessage("forms.world.title", ["name" => $worldName]);
        $this->content = [];

        foreach (Flags::AVAILABLE_WORLD_FLAGS as $flagName => $flagDetails) {
            switch ($flagDetails["type"]) {
                case Flags::TYPE_BOOL:
                    $this->content[] = [
                      "type"    => "dropdown",
                      "text"    => $this->getWorlds()->getMessage("forms.world.params." . $flagName),
                      "options" => [
                        $this->getWorlds()->getMessage("forms.world.options.notset"),
                        $this->getWorlds()->getMessage("forms.world.options.false"),
                        $this->getWorlds()->getMessage("forms.world.options.true"),
                      ],
                      "default" => $this->convBool($world->loadValue($flagName)),
                    ];
                    break;
                case Flags::TYPE_PERMISSION:
                    $this->content[] = [
                      "type"    => "input",
                      "text"    => $this->getWorlds()->getMessage("forms.world.params." . $flagName),
                      "default" => $world->loadValue($flagName),
                    ];
                    break;
                case Flags::TYPE_GAMEMODE:
                    $this->content[] = [
                      "type"    => "dropdown",
                      "text"    => $this->getWorlds()->getMessage("forms.world.params." . $flagName),
                      "options" => [
                        $this->getWorlds()->getMessage("forms.world.options.notset"),
                        Server::getGamemodeString(Player::SURVIVAL),
                        Server::getGamemodeString(Player::CREATIVE),
                        Server::getGamemodeString(Player::ADVENTURE),
                        Server::getGamemodeString(Player::SPECTATOR),
                      ],
                      "default" => $this->convGamemode($world->loadValue($flagName)),
                    ];
                    break;
            }
        }
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

        if (count($data) !== count(Flags::AVAILABLE_WORLD_FLAGS)) {
            return;
        }

        $defFolderName = $this->getWorlds()->getServer()->getDefaultLevel()->getFolderName();
        $plFolderName  = $player->getLevel()->getFolderName();

        $isDefLvl = $defFolderName === $plFolderName;

        $i = 0;
        foreach (Flags::AVAILABLE_WORLD_FLAGS as $flagName => $flagDetails) {
            switch ($flagDetails["type"]) {
                case Flags::TYPE_BOOL:
                    $this->procBool($flagName, $data[$i]);
                    break;
                case Flags::TYPE_PERMISSION:
                    $this->procPerm($flagName, $data[$i], $isDefLvl, $player);
                    break;
                case Flags::TYPE_GAMEMODE:
                    $this->procGamemode($flagName, $data[$i]);
                    break;
            }

            $i++;
        }

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

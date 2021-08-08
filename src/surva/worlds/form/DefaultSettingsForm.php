<?php
/**
 * Worlds | default settings form
 */

namespace surva\worlds\form;

use pocketmine\Player;
use pocketmine\Server;
use surva\worlds\types\Defaults;
use surva\worlds\utils\Flags;
use surva\worlds\Worlds;

class DefaultSettingsForm extends SettingsForm
{

    public function __construct(Worlds $wsInstance, Defaults $defaults)
    {
        parent::__construct($wsInstance, $defaults);

        $this->title   = $this->getWorlds()->getMessage("forms.default.title");
        $this->content = [];

        foreach (Flags::AVAILABLE_DEFAULT_FLAGS as $flagName => $flagDetails) {
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
                      "default" => $this->convBool($defaults->loadValue($flagName)),
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
                      "default" => $this->convGamemode($defaults->loadValue($flagName)),
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

        if (count($data) !== count(Flags::AVAILABLE_DEFAULT_FLAGS)) {
            return;
        }

        $i = 0;
        foreach (Flags::AVAILABLE_DEFAULT_FLAGS as $flagName => $flagDetails) {
            switch ($flagDetails["type"]) {
                case Flags::TYPE_BOOL:
                    $this->procBool($flagName, $data[$i]);
                    break;
                case Flags::TYPE_GAMEMODE:
                    $this->procGamemode($flagName, $data[$i]);
                    break;
            }

            $i++;
        }

        $player->sendMessage($this->getWorlds()->getMessage("forms.saved"));
    }

}

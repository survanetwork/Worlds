<?php
/**
 * Worlds | default settings form
 */

namespace surva\worlds\form;

use pocketmine\player\GameMode;
use pocketmine\player\Player;
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
            $this->content[] = match ($flagDetails["type"]) {
                Flags::TYPE_BOOL => [
                  "type"    => "dropdown",
                  "text"    => $this->getWorlds()->getMessage("forms.world.params." . $flagName),
                  "options" => [
                    $this->getWorlds()->getMessage("forms.world.options.notset"),
                    $this->getWorlds()->getMessage("forms.world.options.false"),
                    $this->getWorlds()->getMessage("forms.world.options.true"),
                  ],
                  "default" => $this->convBool($defaults->loadValue($flagName)),
                ],
                Flags::TYPE_GAMEMODE => [
                  "type"    => "dropdown",
                  "text"    => $this->getWorlds()->getMessage("forms.world.params." . $flagName),
                  "options" => [
                    $this->getWorlds()->getMessage("forms.world.options.notset"),
                    GameMode::SURVIVAL()->getEnglishName(),
                    GameMode::CREATIVE()->getEnglishName(),
                    GameMode::ADVENTURE()->getEnglishName(),
                    GameMode::SPECTATOR()->getEnglishName(),
                  ],
                  "default" => $this->convGameMode($defaults->loadValue($flagName)),
                ],
            };
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
                    $this->procGameMode($flagName, $data[$i]);
                    break;
            }

            $i++;
        }

        $player->sendMessage($this->getWorlds()->getMessage("forms.saved"));
    }

}

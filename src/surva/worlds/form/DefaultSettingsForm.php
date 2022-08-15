<?php

/**
 * Worlds | default settings form
 */

namespace surva\worlds\form;

use pocketmine\player\GameMode;
use pocketmine\player\Player;
use surva\worlds\types\Defaults;
use surva\worlds\types\exception\ConfigSaveException;
use surva\worlds\types\exception\ValueNotExistException;
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
                    $this->getWorlds()->getMessage("forms.world.options.not_set"),
                    $this->getWorlds()->getMessage("forms.world.options.false"),
                    $this->getWorlds()->getMessage("forms.world.options.true"),
                  ],
                  "default" => $this->confValueToForm($defaults->loadValue($flagName), Flags::TYPE_BOOL),
                ],
                Flags::TYPE_CONTROL_LIST => [
                  "type"    => "dropdown",
                  "text"    => $this->getWorlds()->getMessage("forms.world.params." . $flagName),
                  "options" => [
                    $this->getWorlds()->getMessage("forms.world.options.not_set"),
                    $this->getWorlds()->getMessage("forms.world.options.false"),
                    $this->getWorlds()->getMessage("forms.world.options.true"),
                    $this->getWorlds()->getMessage("forms.world.options.white"),
                    $this->getWorlds()->getMessage("forms.world.options.black"),
                  ],
                  "default" => $this->confValueToForm($defaults->loadValue($flagName), Flags::TYPE_CONTROL_LIST),
                ],
                Flags::TYPE_GAME_MODE => [
                  "type"    => "dropdown",
                  "text"    => $this->getWorlds()->getMessage("forms.world.params." . $flagName),
                  "options" => [
                    $this->getWorlds()->getMessage("forms.world.options.not_set"),
                    GameMode::SURVIVAL()->getEnglishName(),
                    GameMode::CREATIVE()->getEnglishName(),
                    GameMode::ADVENTURE()->getEnglishName(),
                    GameMode::SPECTATOR()->getEnglishName(),
                  ],
                  "default" => $this->confValueToForm($defaults->loadValue($flagName), Flags::TYPE_GAME_MODE),
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
            $player->sendMessage($this->getWorlds()->getMessage("forms.error_code.invalid_data"));

            return;
        }

        if (count($data) !== count(Flags::AVAILABLE_DEFAULT_FLAGS)) {
            $player->sendMessage($this->getWorlds()->getMessage("forms.error_code.invalid_data"));

            return;
        }

        try {
            $i = 0;
            foreach (Flags::AVAILABLE_DEFAULT_FLAGS as $flagName => $flagDetails) {
                switch ($flagDetails["type"]) {
                    case Flags::TYPE_BOOL:
                        $this->procBool($flagName, $data[$i]);
                        break;
                    case Flags::TYPE_CONTROL_LIST:
                        $this->procControlList($flagName, $data[$i]);
                        break;
                    case Flags::TYPE_GAME_MODE:
                        $this->procGameMode($flagName, $data[$i]);
                        break;
                }

                $i++;
            }
        } catch (ConfigSaveException $e) {
            $player->sendMessage($this->getWorlds()->getMessage("general.config.save_error"));

            return;
        } catch (ValueNotExistException $e) {
            $player->sendMessage($this->getWorlds()->getMessage("forms.error_code.invalid_data"));

            return;
        }

        $player->sendMessage($this->getWorlds()->getMessage("forms.saved"));
    }
}

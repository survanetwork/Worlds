<?php

/**
 * Worlds | default settings form to configure the
 * default values of the config flags
 */

namespace surva\worlds\form;

use pocketmine\player\GameMode;
use pocketmine\player\Player;
use surva\worlds\types\Defaults;
use surva\worlds\types\exception\ConfigSaveException;
use surva\worlds\types\exception\ValueNotExistException;
use surva\worlds\utils\Flags;
use surva\worlds\utils\Messages;
use surva\worlds\Worlds;

class DefaultSettingsForm extends SettingsForm
{
    private Messages $messages;

    public function __construct(Worlds $wsInstance, Defaults $defaults, Messages $messages)
    {
        parent::__construct($wsInstance, $defaults);

        $this->messages = $messages;

        $this->title = $this->messages->getMessage("forms.default.title");
        $this->content = [];

        foreach (Flags::AVAILABLE_DEFAULT_FLAGS as $flagName => $flagDetails) {
            $this->content[] = match ($flagDetails["type"]) {
                Flags::TYPE_BOOL => [
                  "type" => "dropdown",
                  "text" => $this->messages->getMessage("forms.world.params." . $flagName),
                  "options" => [
                    $this->messages->getMessage("forms.world.options.not_set"),
                    $this->messages->getMessage("forms.world.options.false"),
                    $this->messages->getMessage("forms.world.options.true"),
                  ],
                  "default" => $this->confValueToForm($defaults->loadValue($flagName), Flags::TYPE_BOOL),
                ],
                Flags::TYPE_CONTROL_LIST => [
                  "type" => "dropdown",
                  "text" => $this->messages->getMessage("forms.world.params." . $flagName),
                  "options" => [
                    $this->messages->getMessage("forms.world.options.not_set"),
                    $this->messages->getMessage("forms.world.options.false"),
                    $this->messages->getMessage("forms.world.options.true"),
                    $this->messages->getMessage("forms.world.options.white"),
                    $this->messages->getMessage("forms.world.options.black"),
                  ],
                  "default" => $this->confValueToForm($defaults->loadValue($flagName), Flags::TYPE_CONTROL_LIST),
                ],
                Flags::TYPE_GAME_MODE => [
                  "type" => "dropdown",
                  "text" => $this->messages->getMessage("forms.world.params." . $flagName),
                  "options" => [
                    $this->messages->getMessage("forms.world.options.not_set"),
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
     * @inheritDoc
     */
    public function handleResponse(Player $player, $data): void
    {
        if (!is_array($data)) {
            $player->sendMessage($this->messages->getMessage("forms.error_code.invalid_data"));

            return;
        }

        if (count($data) !== count(Flags::AVAILABLE_DEFAULT_FLAGS)) {
            $player->sendMessage($this->messages->getMessage("forms.error_code.invalid_data"));

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
            $player->sendMessage($this->messages->getMessage("general.config.save_error"));

            return;
        } catch (ValueNotExistException $e) {
            $player->sendMessage($this->messages->getMessage("forms.error_code.invalid_data"));

            return;
        }

        $player->sendMessage($this->messages->getMessage("forms.saved"));
    }
}

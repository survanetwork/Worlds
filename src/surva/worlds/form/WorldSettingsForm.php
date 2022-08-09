<?php

/**
 * Worlds | world settings form
 */

namespace surva\worlds\form;

use pocketmine\player\GameMode;
use pocketmine\player\Player;
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
            $this->content[] = match ($flagDetails["type"]) {
                Flags::TYPE_BOOL => [
                  "type"    => "dropdown",
                  "text"    => $this->getWorlds()->getMessage("forms.world.params." . $flagName),
                  "options" => [
                    $this->getWorlds()->getMessage("forms.world.options.notset"),
                    $this->getWorlds()->getMessage("forms.world.options.false"),
                    $this->getWorlds()->getMessage("forms.world.options.true"),
                  ],
                  "default" => $this->confValueToForm($world->loadValue($flagName), Flags::TYPE_BOOL),
                ],
                Flags::TYPE_WHITEBLACKLIST => [
                  "type"    => "dropdown",
                  "text"    => $this->getWorlds()->getMessage("forms.world.params." . $flagName),
                  "options" => [
                    $this->getWorlds()->getMessage("forms.world.options.notset"),
                    $this->getWorlds()->getMessage("forms.world.options.false"),
                    $this->getWorlds()->getMessage("forms.world.options.true"),
                    $this->getWorlds()->getMessage("forms.world.options.white"),
                    $this->getWorlds()->getMessage("forms.world.options.black"),
                  ],
                  "default" => $this->confValueToForm($world->loadValue($flagName), Flags::TYPE_WHITEBLACKLIST),
                ],
                Flags::TYPE_PERMISSION => [
                  "type"    => "input",
                  "text"    => $this->getWorlds()->getMessage("forms.world.params." . $flagName),
                  "default" => $world->loadValue($flagName),
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
                  "default" => $this->confValueToForm($world->loadValue($flagName), Flags::TYPE_GAMEMODE),
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

        if (count($data) !== count(Flags::AVAILABLE_WORLD_FLAGS)) {
            return;
        }

        $defFolderName = $this->getWorlds()->getServer()->getWorldManager()->getDefaultWorld()->getFolderName();
        $plFolderName  = $player->getWorld()->getFolderName();

        $isDefLvl = $defFolderName === $plFolderName;

        $i = 0;
        foreach (Flags::AVAILABLE_WORLD_FLAGS as $flagName => $flagDetails) {
            switch ($flagDetails["type"]) {
                case Flags::TYPE_BOOL:
                    $this->procBool($flagName, $data[$i]);
                    break;
                case Flags::TYPE_WHITEBLACKLIST:
                    $this->procWhiteBlack($flagName, $data[$i]);
                    break;
                case Flags::TYPE_PERMISSION:
                    $this->procPerm($flagName, $data[$i], $isDefLvl, $player);
                    break;
                case Flags::TYPE_GAMEMODE:
                    $this->procGameMode($flagName, $data[$i]);
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
     * @param  mixed  $data
     * @param  bool  $isDefLvl
     * @param  \pocketmine\player\Player  $player
     *
     * @return void
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

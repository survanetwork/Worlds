<?php
/**
 * Created by PhpStorm.
 * User: jarne
 * Date: 01.10.17
 * Time: 11:52
 */

namespace surva\worlds\commands;

use pocketmine\nbt\BigEndianNBTStream;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\StringTag;
use pocketmine\Player;

class CopyCommand extends CustomCommand {
    public function do(Player $player, array $args) {
        if(!(count($args) === 2)) {
            return false;
        }

        if(!(is_dir($this->getWorlds()->getServer()->getDataPath() . "worlds/" . $args[0]))) {
            $player->sendMessage($this->getWorlds()->getMessage("general.world.notexist", array("name" => $args[0])));

            return true;
        }

        $fromFolderName = $args[0];
        $toFolderName = $args[1];

        if($fromFolderName === $toFolderName) {
            $player->sendMessage($this->getWorlds()->getMessage("copy.same"));

            return true;
        }

        $fromPath = $this->getWorlds()->getServer()->getDataPath() . "worlds/" . $fromFolderName;
        $toPath = $this->getWorlds()->getServer()->getDataPath() . "worlds/" . $toFolderName;

        $this->getWorlds()->copy($fromPath, $toPath);

        if(!($levelDatContent = file_get_contents($toPath . "/level.dat"))) {
            $player->sendMessage($this->getWorlds()->getMessage("copy.datfile.notexist"));

            return true;
        }

        $nbt = new BigEndianNBTStream();
        $levelData = $nbt->readCompressed($levelDatContent);

        if(!($levelData instanceof CompoundTag) OR !$levelData->hasTag("Data", CompoundTag::class)) {
            $player->sendMessage($this->getWorlds()->getMessage("copy.datfile.damaged"));

            return true;
        }

        $dataWorkingWith = $levelData->getCompoundTag("Data");

        if(!$dataWorkingWith->hasTag("LevelName", StringTag::class)) {
            $player->sendMessage($this->getWorlds()->getMessage("copy.datfile.damaged"));

            return true;
        }

        $dataWorkingWith->setString("LevelName", $toFolderName);

        if(!(file_put_contents(
            $toPath . "/level.dat",
            $nbt->writeCompressed(new CompoundTag("", array($dataWorkingWith)))
        ))) {
            $player->sendMessage($this->getWorlds()->getMessage("copy.datfile.notsave"));

            return true;
        }

        $player->sendMessage(
            $this->getWorlds()->getMessage("copy.success", array("name" => $fromFolderName, "to" => $toFolderName))
        );

        return true;
    }
}

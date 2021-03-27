<?php
/**
 * Worlds | rename world command
 */

namespace surva\worlds\commands;

use pocketmine\nbt\BigEndianNBTStream;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\StringTag;
use pocketmine\Player;
use surva\worlds\logic\WorldActions;

class RenameCommand extends CustomCommand {
    public function do(Player $player, array $args) {
        if(!(count($args) === 2)) {
            return false;
        }

        if(!WorldActions::worldPathExists($this->getWorlds(), $args[0])) {
            $player->sendMessage($this->getWorlds()->getMessage("general.world.notexist", array("name" => $args[0])));

            return true;
        }

        switch(WorldActions::unloadIfLoaded($this->getWorlds(), $args[0])) {
            case WorldActions::UNLOAD_DEFAULT:
                $player->sendMessage($this->getWorlds()->getMessage("unload.default"));

                return true;
            case WorldActions::UNLOAD_FAILED:
                $player->sendMessage($this->getWorlds()->getMessage("unload.failed"));

                return true;
        }

        $fromFolderName = $args[0];
        $toFolderName = $args[1];

        if($fromFolderName === $toFolderName) {
            $player->sendMessage($this->getWorlds()->getMessage("rename.same"));

            return true;
        }

        $fromPath = $this->getWorlds()->getServer()->getDataPath() . "worlds/" . $fromFolderName;
        $toPath = $this->getWorlds()->getServer()->getDataPath() . "worlds/" . $toFolderName;

        if(!($levelDatContent = file_get_contents($fromPath . "/level.dat"))) {
            $player->sendMessage($this->getWorlds()->getMessage("rename.datfile.notexist"));

            return true;
        }

        $nbt = new BigEndianNBTStream();
        $levelData = $nbt->readCompressed($levelDatContent);

        if(!($levelData instanceof CompoundTag) OR !$levelData->hasTag("Data", CompoundTag::class)) {
            $player->sendMessage($this->getWorlds()->getMessage("rename.datfile.damaged"));

            return true;
        }

        $dataWorkingWith = $levelData->getCompoundTag("Data");

        if(!$dataWorkingWith->hasTag("LevelName", StringTag::class)) {
            $player->sendMessage($this->getWorlds()->getMessage("rename.datfile.damaged"));

            return true;
        }

        $dataWorkingWith->setString("LevelName", $toFolderName);

        if(!(file_put_contents(
            $fromPath . "/level.dat",
            $nbt->writeCompressed(new CompoundTag("", array($dataWorkingWith)))
        ))) {
            $player->sendMessage($this->getWorlds()->getMessage("rename.datfile.notsave"));

            return true;
        }

        $this->getWorlds()->copy($fromPath, $toPath);
        $this->getWorlds()->delete($fromPath);

        $player->sendMessage(
            $this->getWorlds()->getMessage("rename.success", array("name" => $fromFolderName, "to" => $toFolderName))
        );

        return true;
    }
}

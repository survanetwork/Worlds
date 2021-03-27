<?php
/**
 * Worlds | copy world command
 */

namespace surva\worlds\commands;

use pocketmine\command\CommandSender;
use pocketmine\nbt\BigEndianNBTStream;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\StringTag;
use surva\worlds\logic\WorldActions;

class CopyCommand extends CustomCommand {
    public function do(CommandSender $sender, array $args): bool {
        if(!(count($args) === 2)) {
            return false;
        }

        if(!WorldActions::worldPathExists($this->getWorlds(), $args[0])) {
            $sender->sendMessage($this->getWorlds()->getMessage("general.world.notexist", array("name" => $args[0])));

            return true;
        }

        $fromFolderName = $args[0];
        $toFolderName = $args[1];

        if($fromFolderName === $toFolderName) {
            $sender->sendMessage($this->getWorlds()->getMessage("copy.same"));

            return true;
        }

        $fromPath = $this->getWorlds()->getServer()->getDataPath() . "worlds/" . $fromFolderName;
        $toPath = $this->getWorlds()->getServer()->getDataPath() . "worlds/" . $toFolderName;

        $this->getWorlds()->copy($fromPath, $toPath);

        if(!($levelDatContent = file_get_contents($toPath . "/level.dat"))) {
            $sender->sendMessage($this->getWorlds()->getMessage("copy.datfile.notexist"));

            return true;
        }

        $nbt = new BigEndianNBTStream();
        $levelData = $nbt->readCompressed($levelDatContent);

        if(!($levelData instanceof CompoundTag) OR !$levelData->hasTag("Data", CompoundTag::class)) {
            $sender->sendMessage($this->getWorlds()->getMessage("copy.datfile.damaged"));

            return true;
        }

        $dataWorkingWith = $levelData->getCompoundTag("Data");

        if(!$dataWorkingWith->hasTag("LevelName", StringTag::class)) {
            $sender->sendMessage($this->getWorlds()->getMessage("copy.datfile.damaged"));

            return true;
        }

        $dataWorkingWith->setString("LevelName", $toFolderName);

        if(!(file_put_contents(
            $toPath . "/level.dat",
            $nbt->writeCompressed(new CompoundTag("", array($dataWorkingWith)))
        ))) {
            $sender->sendMessage($this->getWorlds()->getMessage("copy.datfile.notsave"));

            return true;
        }

        $sender->sendMessage(
            $this->getWorlds()->getMessage("copy.success", array("name" => $fromFolderName, "to" => $toFolderName))
        );

        return true;
    }
}

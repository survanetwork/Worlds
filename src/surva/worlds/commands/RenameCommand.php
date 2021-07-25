<?php
/**
 * Worlds | rename world command
 */

namespace surva\worlds\commands;

use pocketmine\command\CommandSender;
use pocketmine\nbt\BigEndianNBTStream;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\StringTag;
use surva\worlds\logic\WorldActions;

class RenameCommand extends CustomCommand
{

    public function do(CommandSender $sender, array $args): bool
    {
        if (!(count($args) === 2)) {
            return false;
        }

        if (!WorldActions::worldPathExists($this->getWorlds(), $args[0])) {
            $sender->sendMessage($this->getWorlds()->getMessage("general.world.notexist", ["name" => $args[0]]));

            return true;
        }

        switch (WorldActions::unloadIfLoaded($this->getWorlds(), $args[0])) {
            case WorldActions::UNLOAD_DEFAULT:
                $sender->sendMessage($this->getWorlds()->getMessage("unload.default"));

                return true;
            case WorldActions::UNLOAD_FAILED:
                $sender->sendMessage($this->getWorlds()->getMessage("unload.failed"));

                return true;
        }

        $fromFolderName = $args[0];
        $toFolderName   = $args[1];

        if ($fromFolderName === $toFolderName) {
            $sender->sendMessage($this->getWorlds()->getMessage("rename.same"));

            return true;
        }

        $fromPath = $this->getWorlds()->getServer()->getDataPath() . "worlds/" . $fromFolderName;
        $toPath   = $this->getWorlds()->getServer()->getDataPath() . "worlds/" . $toFolderName;

        if (!($levelDatContent = file_get_contents($fromPath . "/level.dat"))) {
            $sender->sendMessage($this->getWorlds()->getMessage("rename.datfile.notexist"));

            return true;
        }

        $nbt       = new BigEndianNBTStream();
        $levelData = $nbt->readCompressed($levelDatContent);

        if (!($levelData instanceof CompoundTag) or !$levelData->hasTag("Data", CompoundTag::class)) {
            $sender->sendMessage($this->getWorlds()->getMessage("rename.datfile.damaged"));

            return true;
        }

        $dataWorkingWith = $levelData->getCompoundTag("Data");

        if (!$dataWorkingWith->hasTag("LevelName", StringTag::class)) {
            $sender->sendMessage($this->getWorlds()->getMessage("rename.datfile.damaged"));

            return true;
        }

        $dataWorkingWith->setString("LevelName", $toFolderName);

        if (!(file_put_contents(
          $fromPath . "/level.dat",
          $nbt->writeCompressed(new CompoundTag("", [$dataWorkingWith]))
        ))
        ) {
            $sender->sendMessage($this->getWorlds()->getMessage("rename.datfile.notsave"));

            return true;
        }

        $this->getWorlds()->copy($fromPath, $toPath);
        $this->getWorlds()->delete($fromPath);

        $sender->sendMessage(
          $this->getWorlds()->getMessage("rename.success", ["name" => $fromFolderName, "to" => $toFolderName])
        );

        return true;
    }

}

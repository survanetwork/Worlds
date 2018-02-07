# Worlds
Easy to use but feature rich world guard plugin

![](https://poggit.pmmp.io/ci.badge/survanetwork/Worlds/Worlds)

[Get the latest Worlds artifacts (PHAR file) here](https://poggit.pmmp.io/ci/survanetwork/Worlds/Worlds)

## Description
Because there is no plugin to manage all aspects of the game for each world like WorldGuard on Bukkit, I made Worlds.
With Worlds, you can control events like breaking blocks, dropping items, and many more.

If you want to help or you have ideas to make our plugin better, feel free to make a commit or open an issue.
Please report bugs on the issues page on GitHub.

## Features
- Create and remove worlds
- Copy and rename worlds
- Load and unload worlds
- Teleport to worlds
- Control nearly every event for each world (it's getting more and more).
    - World's gamemode
    - Permission to join a world
    - Block breaking and placing
    - PvP
    - Damage (everything like attacking, fall damage, ...)
    - Explosion
    - Item drop
    - Hunger
    - Fly
    - You have an idea what to add? Feel free to open an issue.
- Easy to use, fast and mostly bug-free.

## Usage
Worlds is really easy to use. Here is the command syntax:

```
/worlds list
/worlds create <worldname> [type]
/worlds <remove|load|unload|teleport> <worldname>
/worlds <copy|rename> <from> <to>
/worlds set
/worlds set permission <permissionstring>
/worlds set gamemode <survival|creative|adventure|spectator>
/worlds set <build|pvp|damage|drop|hunger|fly> <true|false>
/worlds unset <permission|gamemode|build|pvp|damage|drop|hunger|fly>
```

For a full list of commands, their usage and a description what they are for, take a look at the [wiki](https://github.com/survanetwork/Worlds/wiki/Commands).

## Contribution
Feel free to contribute if you have ideas or found an issue.

You can:
- [open an issue](https://github.com/survanetwork/Worlds/issues) (problems, bugs or feature requests)
- [create a pull request](https://github.com/survanetwork/Worlds/pulls) (code contributions like fixed bugs or added features)
- [help translating the plugin](https://github.com/survanetwork/Worlds/tree/master/resources/languages) (create a new language file or correct an existing one)

## License & Credits
[![Creative Commons License](https://i.creativecommons.org/l/by-nc-sa/4.0/88x31.png)](http://creativecommons.org/licenses/by-nc-sa/4.0/)

[Worlds](https://github.com/survanetwork/Worlds) by [surva network](https://github.com/survanetwork) is licensed under a [Creative Commons Attribution-NonCommercial-ShareAlike 4.0 International License](http://creativecommons.org/licenses/by-nc-sa/4.0/). Permissions beyond the scope of this license may be available on request.

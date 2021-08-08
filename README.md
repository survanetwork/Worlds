<p align="center">
    <img src=".github/.media/logo.png" width="144" height="144" alt="Worlds plugin Logo">
</p>

<h1 align="center">Worlds</h1>
<p align="center">Easy to use but feature rich World Guard plugin</p>

<br>

<p align="center">
    <a href="https://poggit.pmmp.io/p/Worlds">
        <img src="https://poggit.pmmp.io/shield.state/Worlds" alt="Plugin version">
    </a>
    <a href="https://github.com/pmmp/PocketMine-MP">
        <img src="https://poggit.pmmp.io/shield.api/Worlds" alt="API version">
    </a>
    <a href="https://poggit.pmmp.io/p/Worlds">
        <img src="https://poggit.pmmp.io/shield.dl/Worlds" alt="Downloads on Poggit">
    </a>
    <a href="https://github.com/survanetwork/Worlds/blob/master/LICENSE">
        <img src="https://img.shields.io/badge/license-CC--BY--NC--SA--4.0-orange.svg" alt="License">
    </a>
    <a href="https://discord.gg/t4Kg4j3829">
        <img src="https://img.shields.io/discord/685532530451283997?color=blueviolet" alt="Discord">
    </a>
    <a href="https://twitter.com/survanetwork">
        <img src="https://img.shields.io/twitter/url?label=SURVA%20network%20on%20Twitter&style=social&url=https%3A%2F%2Ftwitter.com%2Fsurvanetwork" alt="Twitter">
    </a>
</p>

##

<p align="center">
    <img src=".github/.media/feature-banner.png" width="650" height="366" alt="World plugin features">
</p>

[• Description](#-description)  
[• Features](#-features)  
[• Usage](#-usage)  
[• Contribution](#-contribution)  
[• License](#%EF%B8%8F-license)

## 📙 Description
Because there was no plugin to manage all aspects of Minecraft: Bedrock Edition servers for each world like WorldGuard on Bukkit, I made Worlds.
With Worlds, you can control events like breaking blocks, dropping items, and many more. And it's getting even more!

If you want to help or you have ideas to make our plugin better, feel free to make a commit or open an issue.
Please report bugs on the issues page on GitHub.

## 🎁 Features
- **CREATE / REMOVE** Create and remove worlds (even special ones with custom world generators)
- **COPY / RENAME** Copy and rename worlds (including all necessary files)
- **LOAD / UNLOAD** Load and unload worlds
- **TELEPORT** Teleport to worlds
- **CONTROL EVERYTHING** Control nearly every event for each world (it's getting more and more).
    - World's gamemode
    - Permission to join a world
    - Block breaking and placing
    - PvP
    - Damage (everything like attacking, fall damage, ...)
    - Explosion
    - Item drop
    - Hunger
    - Fly
    - Daylight cycle
    - Leaves decay
    - You have an idea what to add? Feel free to open an issue.
- **DEFAULT VALUES** Set default values for worlds without settings
- **SOPHISTICATED** Easy to use, fast and mostly bug-free.

**NEW** Edit the settings of a world (gamemode, building, damage, and so on) using a super-easy form GUI!

<img src=".github/.media/world-settings-form.png" width="540px" alt="Screenshot of world settings form">

## ⛏ Usage
Worlds is really easy to use. Here is the command syntax:

```
/worlds list
/worlds create <worldname> [type]
/worlds <remove|load|unload|teleport> <worldname>
/worlds <copy|rename> <from> <to>
/worlds set
/worlds set legacy
/worlds set permission <permissionstring>
/worlds set gamemode <survival|creative|adventure|spectator>
/worlds set <build|pvp|damage|interact|explode|drop|hunger|fly|daylightcycle|leavesdecay> <true|false>
/worlds unset
/worlds unset <permission|gamemode|build|pvp|damage|interact|explode|drop|hunger|fly|daylightcycle|leavesdecay>
/worlds defaults
/worlds defaults legacy
/worlds defaults set permission <permissionstring>
/worlds defaults set gamemode <survival|creative|adventure|spectator>
/worlds defaults set <build|pvp|damage|interact|explode|drop|hunger|fly|daylightcycle|leavesdecay> <true|false>
/worlds defaults unset <permission|gamemode|build|pvp|damage|interact|explode|drop|hunger|fly|daylightcycle|leavesdecay>
```

For a full list of commands, their usage and a description what they are for, take a look at the [wiki](https://plugins.surva.net/docs/Worlds#commands).

[Read the full documentation 📖](https://plugins.surva.net/docs/Worlds) • [Ask questions on Discord 💬](https://discord.gg/t4Kg4j3829)

## 🙋‍ Contribution
Feel free to contribute if you have ideas or found an issue.

You can:
- [open an issue](https://github.com/survanetwork/Worlds/issues) (problems, bugs or feature requests)
- [create a pull request](https://github.com/survanetwork/Worlds/pulls) (code contributions like fixed bugs or added features)
- [help translating the plugin](https://github.com/survanetwork/Worlds/tree/master/resources/languages) (create a new language file or correct an existing one)

Please read our **[Contribution Guidelines](CONTRIBUTING.md)** before creating an issue or submitting a pull request.

Many thanks for their support to all contributors!

## 👨‍⚖️ License
[![Creative Commons License](https://i.creativecommons.org/l/by-nc-sa/4.0/88x31.png)](http://creativecommons.org/licenses/by-nc-sa/4.0/)

[Worlds](https://github.com/survanetwork/Worlds) by [surva network](https://github.com/survanetwork) is licensed under a [Creative Commons Attribution-NonCommercial-ShareAlike 4.0 International License](http://creativecommons.org/licenses/by-nc-sa/4.0/). Permissions beyond the scope of this license may be available on request.

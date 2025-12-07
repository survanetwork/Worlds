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
        <img src="https://poggit.pmmp.io/shield.api/Worlds" alt="PocketMine-MP API version">
    </a>
    <a href="https://poggit.pmmp.io/p/Worlds">
        <img src="https://poggit.pmmp.io/shield.dl.total/Worlds" alt="Downloads on Poggit">
    </a>
    <a href="https://github.com/survanetwork/Worlds/blob/master/LICENSE">
        <img src="https://img.shields.io/github/license/survanetwork/Worlds.svg" alt="License">
    </a>
    <a href="https://discord.gg/t4Kg4j3829">
        <img src="https://img.shields.io/discord/685532530451283997?color=blueviolet" alt="Discord">
    </a>
    <a href="https://dev.surva.net/plugins/">
        <img src="https://img.shields.io/badge/website-visit-ee8031" alt="Website">
    </a>
</p>

##

<p align="center">
    <a href="https://dev.surva.net/plugins/#worlds">
        <img src="https://static.surva.net/osplugins/assets/dl-buttons/worlds.png" width="220" height="auto" alt="Download Worlds plugin release">
        <img src="https://static.surva.net/osplugins/assets/feature-banners/worlds.png" width="650" height="auto" alt="Worlds plugin features">
    </a>
</p>

[Description](#-description) | [Features](#-features) | [Usage](#-usage)
| [Contribution](#-contribution) | [License](#%EF%B8%8F-license)

## 📙 Description
Worlds is our feature-packed world management plugin. It provides general world management like creating, copying, loading and teleporting to worlds.
We've also added flags which can be used to control in game behaviour like game mode, block breaking/placing, damage, hunger, flying, daylight cycling and many more per-world and as default value.
Using control lists, you can precisely create whitelists and blacklists to add exceptions for specific blocks, items or commands.

## 🎁 Features
- **CREATE / REMOVE** Create and remove worlds (also custom world generators, e.g. with [FancyGenerators](https://dev.surva.net/plugins/#fancygenerators) plugin)
- **COPY / RENAME** Copy and rename worlds (including all necessary files)
- **LOAD / UNLOAD** Load and unload worlds
- **TELEPORT** Teleport to worlds
- **CONTROL EVERYTHING** Control nearly every event for each world (it's getting more and more).
    - World's game mode
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
    - Potion
    - Executing commands
    - You have an idea what to add? Feel free to open an issue.
- **CONTROL LISTS** Create whitelists and blacklists for many events like block placing, interacting with blocks, ...
- **DEFAULT VALUES** Set default values for worlds without settings
- **SOPHISTICATED** Easy to use, fast and mostly bug-free.

**NEW** Control lists can be used to create whitelists and blacklists instead of just allowing/disabling a world flag!  

**NEW** Add new level generators to use with Worlds using the [FancyGenerators](https://dev.surva.net/plugins/#fancygenerators) plugin!

**Forms GUI** Edit the settings of a world (game mode, building, damage, and so on) using a super-easy form GUI.

<img src="https://static.surva.net/osplugins/worlds/world-settings-form.png" width="540" height="auto" alt="Screenshot of world settings form">

## ⛏ Usage
Worlds is really easy to use. Here is the command syntax:

```
/worlds list
/worlds create <worldname> [type]
/worlds <remove|load|unload|teleport> <worldname>
/worlds teleport <player> <worldname>
/worlds <copy|rename> <from> <to>
/worlds set
/worlds set show
/worlds set permission <permissionstring>
/worlds set gamemode <survival|creative|adventure|spectator>
/worlds set <rulename> <true|false>
/worlds set <rulename> <true|false|white|black>
/worlds set <rulename> list <add|remove> <item>
/worlds set <rulename> list <show|reset>
/worlds unset
/worlds unset <rulename>
/worlds defaults
/worlds defaults show
/worlds defaults set permission <permissionstring>
/worlds defaults set gamemode <survival|creative|adventure|spectator>
/worlds defaults set <rulename> <true|false>
/worlds defaults set <rulename> <true|false|white|black>
/worlds defaults set <rulename> list <add|remove> <item>
/worlds defaults set <rulename> list <show|reset>
/worlds defaults unset <rulename>
```

Available world rules are:

| Flag name     | available as default rule | control lists | listed by    |
|---------------|---------------------------|---------------|--------------|
| permission    | ❌ no                      | ❌ no          |              |
| gamemode      | ✅ yes                     | ❌ no          |              |
| build         | ✅ yes                     | ✅ yes         | block ID     |
| pvp           | ✅ yes                     | ❌ no          |              |
| damage        | ✅ yes                     | ❌ no          |              |
| interact      | ✅ yes                     | ✅ yes         | block ID     |
| explode       | ✅ yes                     | ❌ no          |              |
| drop          | ✅ yes                     | ✅ yes         | item ID      |
| hunger        | ✅ yes                     | ❌ no          |              |
| fly           | ✅ yes                     | ❌ no          |              |
| daylightcycle | ✅ yes                     | ❌ no          |              |
| leavesdecay   | ✅ yes                     | ❌ no          |              |
| potion        | ✅ yes                     | ✅ yes         | item ID      |
| command       | ✅ yes                     | ✅ yes         | command name |

For a full list of commands, their usage and a description what they are for, take a look at the [wiki](https://plugin-docs.surva.net/category/commands).

[Read the full documentation 📖](https://plugin-docs.surva.net/worlds) • [Ask questions on Discord 💬](https://discord.gg/t4Kg4j3829)

## 🙋‍ Contribution
Feel free to contribute if you have ideas or found an issue.

You can:
- [open an issue](https://github.com/survanetwork/Worlds/issues) (problems, bugs or feature requests)
- [create a pull request](https://github.com/survanetwork/Worlds/pulls) (code contributions like fixed bugs or added features)
- [help translating the plugin](https://www.transifex.com/surva/worlds) (help us to translate this plugin into your language on Transifex platform)

Please read our **[Contribution Guidelines](CONTRIBUTING.md)** before creating an issue or submitting a pull request.

Many thanks for their support to all contributors!

## 👨‍⚖️ License
[MIT](https://github.com/survanetwork/Worlds/blob/master/LICENSE)

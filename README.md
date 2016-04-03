<h1>Worlds</h1>
<h3>A simple but feature rich world guard plugin for ImagicalMine</h3>

<h2>Description</h2>
Because there is no plugin to manage all aspects of the game for each world like WorldGuard on Bukkit, I made Worlds.
With Worlds, you can control events like breaking blocks, dropping items, and many more.

If you want to help or you have ideas to make my plugin better, feel free to make a commit or open an issue.
Please report bugs on the issues page.

<h2>Features</h2>
- Create and remove worlds
- Load and unload worlds
- Teleport to worlds
- Control nearly every event for each world (it's getting more and more).
    - World's gamemode
    - Block breaking and placing
    - PVP
    - Damage (everything like attacking, fall damage, ...)
    - Hunger
    - Item drop
    - You have an idea what to add? Feel free to open an issue.
- Easy to use, fast and mostly bug-free.

<h2>How to use</h2>
Worlds is really easy to use. Here is the command syntax:

`/worlds <info|list|tp|set> [gamemode|build|pvp|damage|hunger|drop] [true|false]`

And for beginners, here are some examples of commands:

List all worlds on the server: `/worlds list`

Teleport to world PVP: `/worlds tp PVP`

No hunger in survival world: Go to survival world an run `/worlds set hunger false`

Gamemode 1 in creative world: Go to creative world an run `/worlds set gamemode 1`


<h2>Development plan</h2>
Todo:

- [x] Main plugin
- [x] Events
- [x] World config

- [ ] Create and remove worlds
- [ ] Load and unload worlds
- [x] Teleport to worlds

- [x] Messages file
- [ ] Translate into diffrent languages

- [ ] First release

What to do next:

- Test everything
- Add more events
- Add language switch in config and translate into native language

<h2>License & Credits</h2>
[![Creative Commons License](https://i.creativecommons.org/l/by-sa/4.0/88x31.png)](http://creativecommons.org/licenses/by-sa/4.0/)

You are free to copy, redistribute, change or expand our work, but you must give credits share it under the same license.
Worlds by [jjplaying](https://github.com/jjplaying/Worlds) is licensed under a [Creative Commons Attribution-ShareAlike 4.0 International License](http://creativecommons.org/licenses/by-sa/4.0/).
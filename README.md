# FlyPE [![HitCount](http://hits.dwyl.com/AGTHARN/FlyPE/master.svg)](http://hits.dwyl.com/AGTHARN/FlyPE/master) [![GitHub license](https://img.shields.io/github/license/AGTHARN/FlyPE)](https://github.com/AGTHARN/FlyPE/blob/master/LICENSE) [![](https://poggit.pmmp.io/shield.state/FlyPE)](https://poggit.pmmp.io/p/FlyPE)
![flypeicon](https://user-images.githubusercontent.com/63234276/83245243-0b6bb180-a1d3-11ea-9a01-3eb2fcb60115.jpg)

![Latest version](https://img.shields.io/badge/Version-v3.5.1-orange?style=for-the-badge)

[FlyPE](https://poggit.pmmp.io/p/FlyPE/3.5.1) is a fly plugin for PocketMine that allows you to fly in survival mode when you run the fly command! There are many different features in the plugin that makes it special, and you should check them out!

This plugin would be useful for big or skyblock servers that would not like players to use fly in PVP zones or the hub. And if you would like players to pay for toggling flight, there's an option for that!

This plugin is in English and there are even customizable messages for those that want to use other languages. And there are also fly settings to prevent players from doing different actions while in flight mode.

Thanks to the Contributors for helping FlyPE!

Note: If you are updating from v2 to v3, please delete your old configuration or update it.

### Installation
If you are new and need help with installing the plugin, here's a step-by-step guide on how to do that!

1. Download the [latest phar](https://poggit.pmmp.io/p/FlyPE/3.5.1) by pressing on **Direct Download** on the top left corner of your screen. Make sure you have chosen the latest version before that.
2. Open your server's plugins folder and put the phar into your server's plugins folder. Make sure to also check if you're putting it into the right server. 
3. Ensure you have the permission to use the fly command, restart your server and type the command, /fly ingame to toggle flight!

That's all you have to do to install the plugin. If you wan't, you may also check out the config in your plugins data folder as there are many features for you to explore!

### Features
These are the list of features in FlyPE. If you have any suggestions for the plugin, feel free to let me know!

- [✔️] Blacklist selected worlds from flying
- [✔️] Whitelist selected worlds from flying
- [✔️] An option to disable fly when hurt by a player
- [✔️] An option to disable fly after joining if fly is on
- [✔️] Blocked players in creative from toggling fly
- [✔️] Able to toggle fly for other players
- [✔️] An option to allow a fly UI to toggle flight
- [✔️] An option to enable buying of fly
- [✔️] Ability to Change UI Texts
- [✔️] Many fly permissions
- [✔️] Customisable messages
- [✔️] Configurable Flight Speed
- [✔️] Flight Particles
- [✔️] Flight Effects
- [✔️] Toggle Sounds
- [❌] Temporal fly time (SOON)

### Requirements
The list below state the requirements of FlyPE!

+ A PocketMine-MP Server (REQUIRED)
+ [EconomyAPI](https://poggit.pmmp.io/p/EconomyAPI/5.7.2) (OPTIONAL)

### Support
If you have any issues with the plugin, or have any suggestions for the plugin, feel free to let me know [here](https://github.com/AGTHARN/FlyPE/issues) and I would look at it as soon as possible!

![GitHub issues](https://img.shields.io/github/issues/AGTHARN/FlyPE?style=for-the-badge) ![GitHub closed issues](https://img.shields.io/github/issues-closed/AGTHARN/FlyPE?style=for-the-badge)

### Permissions
Permissions required for players to use the command, /fly.

Permission                | Description                                                        | Default |
------------------------- | ------------------------------------------------------------------ | ------- |
flype.command             | Allows player to run the fly command                               | OP      |
flype.command.others      | Allows player to run the fly command on others                     | OP      |
flype.command.particles   | Allows player to have the flight particles                         | OP      |
flype.command.effects     | Allows player to have the flight effects                           | OP      |
flype.command.flightspeed | Allows player to have the flight speed modification                | OP      |
flype.command.bypass      | Allows player to bypass checks when switching to a different world | false   |

### Configuration
These are the config versions of FlyPE. To find your config version, open the **config.yml** file in FlyPE's plugin data folder and find the header, **"VERSION"**.

+ ![Config v1](https://img.shields.io/badge/Config-v1-orange?style=for-the-badge)
You can find the config file for v1.0.1 [here](https://pastebin.com/raw/RD19kW5s)!

+ ![Config v2](https://img.shields.io/badge/Config-v2-orange?style=for-the-badge)
You can find the config file for v2.0.0, v2.0.1, v2.0.2, v2.0.3 [here](https://pastebin.com/raw/qgu9u1eJ)!

+ ![Config v3](https://img.shields.io/badge/Config-v3-orange?style=for-the-badge)
You can find the config file for v3.0.0, v3.0.1, v3.1.1, v3.2.0, 3.3.1, 3.3.2, 3.3.3, 3.3.4, 3.4.0, 3.5.0, 3.5.1 [here](https://raw.githubusercontent.com/AGTHARN/FlyPE/master/resources/config.yml)!

### Plugin Info
+ Name: FlyPE
+ Author: AGTHARN
+ Version: 3.5.1
+ Api: 3.0.0

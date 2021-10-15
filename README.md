# FlyPE [![GitHub license](https://img.shields.io/github/license/AGTHARN/FlyPE)](https://github.com/AGTHARN/FlyPE/blob/master/LICENSE) [![](https://poggit.pmmp.io/shield.state/FlyPE)](https://poggit.pmmp.io/p/FlyPE)
![flypeicon](https://user-images.githubusercontent.com/63234276/83245243-0b6bb180-a1d3-11ea-9a01-3eb2fcb60115.jpg)

![Latest version](https://img.shields.io/badge/Version-v4.1.6-orange?style=for-the-badge)

***API 3 will stop receiving feature updates and will only receive version & bug updates.***

Time to abandon BlazinFly and move to a better alternative! [FlyPE](https://poggit.pmmp.io/p/FlyPE/4.1.6) is probably the best fly plugin for PocketMine-MP that is feature packed and with multiple commands that allows you to experiment with flying in survival mode! There are many different features in this plugin that makes it special, and you should check them out! 

We even have the most requested feature, Temporal Flight!

This plugin would be useful for big or skyblock servers that would not like players to use fly in PvP Zones or The Hub. And if you would like players to pay for toggling flight, there's an option for that! Tradeable items like Flight Coupons are also available and you can decide on allowing it!

This plugin is in English and there are even customizable preset messages for those that want to use other languages. And there are also fly settings to prevent players from doing different actions while in flight mode.

Thanks to the Contributors for helping FlyPE!

Note: If you are updating to any major version changes, for example, from v2 to v3, please delete your old configuration or update it.

## Installation
If you are new and need help with installing the plugin, here's a step-by-step guide on how to do that!

1. Download the [latest phar](https://poggit.pmmp.io/p/FlyPE/4.1.6) by pressing on **Direct Download** on the top left corner of your screen. Make sure you have chosen the latest version before that.
2. Open your server's plugins folder and put the phar into your server's plugins folder. Make sure to also check if you're putting it into the right server. 
3. Ensure you have the permission to use the fly command, restart your server and type the command, /fly ingame to toggle flight!

That's all you have to do to install the plugin. If you wan't, you may also check out the config in your plugins data folder as there are many features for you to explore!

## Features
These are the list of features in FlyPE. If you have any suggestions for the plugin, feel free to let me know!

- [✔️] Blacklist/Whitelist Worlds
- [✔️] Multiple Flight Rules/Permissions/Commands
- [✔️] No Creative Toggling
- [✔️] Toggling Flight For Others
- [✔️] Flight UI
- [✔️] Purchasable Flight
- [✔️] Temporal Flight Time
- [✔️] Ability to Change UI Texts
- [✔️] Customisable messages
- [✔️] Configurable Flight Speed
- [✔️] Flight Particles
- [✔️] Flight Effects
- [✔️] Flight Coupons
- [✔️] Flight States
- [✔️] Creative Mode Settings
- [✔️] Plugin Integrations
- [✔️] Toggle Sounds
- [✔️] Translations

## Requirements
The list below state the requirements of FlyPE!

+ A PocketMine-MP Server (REQUIRED)
+ [EconomyAPI](https://poggit.pmmp.io/p/EconomyAPI/5.7.2) (OPTIONAL)

## Support
If you have any issues with the plugin, or have any suggestions for the plugin, feel free to let me know [here](https://github.com/AGTHARN/FlyPE/issues) and I would look at it as soon as possible!

Alternatively, you can get support from our [Discord Server](https://discord.gg/bGKEJTKPZc)!

![GitHub issues](https://img.shields.io/github/issues/AGTHARN/FlyPE?style=for-the-badge) ![GitHub closed issues](https://img.shields.io/github/issues-closed/AGTHARN/FlyPE?style=for-the-badge)

## Permissions
Permissions required for players to use the command, /fly.

Permission                | Description                                                        | Default |
------------------------- | ------------------------------------------------------------------ | ------- |
flype.command             | Allows player to run the Flight Command                            | op      |
flype.command.help        | Allows player to run the Help Subcommand                           | true    |
flype.command.others      | Allows player to run the Toggle Subcommand on others               | op      |
flype.command.tempfly     | Allows player to run the Temp Flight Subcommand                    | op      |
flype.command.coupon      | Allows player to run the Coupon Subcommand                         | op      |
flype.command.reload      | Allows player to reload the Configuration!                         | false   |
flype.particles           | Allows player to have the Flight Particles                         | op      |
flype.effects             | Allows player to have the Flight Effects                           | op      |
flype.flightspeed         | Allows player to have the Flight Speed Modification                | op      |
flype.bypass              | Allows player to Bypass Checks when Switching Worlds               | false   |

## Configuration
These are the config versions of FlyPE. To find your config version, open the **config.yml** file in FlyPE's plugin data folder and find the header, **"VERSION"**.

+ ![Config v1](https://img.shields.io/badge/Config-v1-orange?style=for-the-badge)
You can find the config file for v1.0.0, v1.0.1 [here](https://pastebin.com/raw/RD19kW5s)!

+ ![Config v2](https://img.shields.io/badge/Config-v2-orange?style=for-the-badge)
You can find the config file for v2.0.0, v2.0.1, v2.0.2, v2.0.3 [here](https://pastebin.com/raw/qgu9u1eJ)!

+ ![Config v3](https://img.shields.io/badge/Config-v3-orange?style=for-the-badge)
You can find the config file for v3.3.2, v3.3.3, v3.3.4, v3.7.0, v3.8.3 [here](https://pastebin.com/raw/82znpD4P)!

+ ![Config v4](https://img.shields.io/badge/Config-v4-orange?style=for-the-badge)
You can find the config file for v4.0.2, v4.1.6 [here](https://raw.githubusercontent.com/AGTHARN/FlyPE/master/resources/config.yml)!

## Frequently-Asked-Questions
- [How do I report an issue or suggestion?](#how-do-i-report-an-issue-or-suggestion)
- [Does temporal flight count down while the player is online or offline?](#does-temporal-flight-count-down-while-the-player-is-online-or-offline)
- [Are any PMMP forks supported?](#are-any-pmmp-forks-supported)

### How do I report an issue or suggestion?
***A:*** Issues can be opened by clicking the **New issue** button located in [Issues](https://github.com/AGTHARN/FlyPE/issues). Feel free to use any format you'd like as long as you state the issue/suggestion you have! You may also get help by joining our [Discord Server](https://discord.gg/bGKEJTKPZc)!

### Does temporal flight count down while the player is online or offline?
***A:*** The temporal flight system counts down while the player is both online and offline. Changing timezones would be the only thing affecting it.

### Are any PMMP forks supported?
***A:*** No support will be given for PMMP forks.
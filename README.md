# FlyPE [![GitHub license](https://img.shields.io/github/license/AGTHARN/FlyPE)](https://github.com/AGTHARN/FlyPE/blob/master/LICENSE) [![](https://poggit.pmmp.io/shield.state/FlyPE)](https://poggit.pmmp.io/p/FlyPE)
![flypeicon](https://user-images.githubusercontent.com/63234276/83245243-0b6bb180-a1d3-11ea-9a01-3eb2fcb60115.jpg)

![Latest version](https://img.shields.io/badge/Version-v5.2.0-orange?style=for-the-badge)

***This is the PM4 branch of FlyPE. All the features from PM3 may be incomplete in this rewrite. Expect bugs and problems as well.***

Time to abandon BlazinFly and BetterFlight, and move to a better alternative! [FlyPE](https://poggit.pmmp.io/p/FlyPE/5.2.0) is the most extensive/advanced/customizable/best fly plugin in Minecraft history that is feature packed and with multiple commands that allows you to experiment with flying in survival mode! There are many different features in this plugin that makes it special, and you should check them out! 

This plugin would be useful for big or skyblock servers that would not like players to use fly in PvP Zones or The Hub. And if you would like players to pay for toggling flight, there's an option for that! Tradeable items like Flight Coupons are also available and you can decide on allowing it! We even have the most requested feature, Temporal Flight!

This plugin is in English and there are even customizable preset messages for those that want to use other languages. And there are also fly settings to prevent players from doing different actions while in flight mode.

Thanks to the Contributors for helping FlyPE!

Note: If you are updating to any major version changes, for example, from v2 to v3 or v4 to v5, please delete your old configuration or update it.

## Installation
If you are new and need help with installing the plugin, here's a step-by-step guide on how to do that!

1. Download the [latest phar](https://poggit.pmmp.io/p/FlyPE/5.2.0) by pressing on **Direct Download** on the top left corner of your screen. Make sure you have chosen the latest version before that.
2. Open your server's plugins folder and put the phar into your server's plugins folder. Make sure to also check if you're putting it into the right server. 
3. Ensure you have the permission to use the fly command, restart your server and type the command, /fly in-game to toggle flight!

That's all you have to do to install the plugin. If you wan't, you may also check out the config in your plugins data folder as there are many features for you to explore!

## Features
Here is a comparison of features in FlyPE with other plugins. If you have any suggestions for the plugin, feel free to let me know!

Comparison                            | FlyPE | BetterFlight | BlazinFly |
------------------------------------- | ----- | ------------ | --------- |
PM4 Support                           | ✔️   | ✔️           | ❌       |
Multilingual Translation              | ✔️   | ❌           | ❌       |
Auto Configuration Reset              | ✔️   | ❌           | ❌       |
Toggle Flight for Others              | ✔️   | ✔️           | ✔️       |
Toggle Flight for Everyone            | ❌   | ❌           | ❌       |
**Temporal Flight**                   | ✔️   | ❌           | ❌       |
Whitelist Worlds                      | ✔️   | ✔️           | ✔️       |
Blacklist Worlds                      | ✔️   | ✔️           | ❌       |
Plugin Integration                    | ✔️   | ❌           | ❌       |
Save Flight States                    | ✔️   | ❌           | ❌       |
Purchasable Flight                    | ✔️   | ❌           | ❌       |
Flight Combat Disable                 | ✔️   | ✔️           | ✔️       |
Flight Sounds                         | ✔️   | ❌           | ❌       |
Flight Particles                      | ✔️   | ❌           | ❌       |
Flight Effects                        | ✔️   | ❌           | ❌       |
Flight Capes                          | ✔️   | ❌           | ❌       |
Flight UI                             | ✔️   | ❌           | ❌       |
Completely Configurable               | ✔️   | ✔️           | ❌       |
SQlite Data Provider                  | ✔️   | ❌           | ❌       |
MySQL Data Provider                   | ✔️   | ❌           | ❌       |
YAML Data Provider                    | ✔️   | ❌           | ❌       |
JSON Data Provider                    | ✔️   | ❌           | ❌       |
Reload Configuration Files            | ✔️   | ❌           | ❌       |
Error Handling                        | ⚠️   | ⚠️           | ⚠️       |
Configurable Creative Mode            | ❌   | ❌           | ❌       |
Flight Rules                          | ❌   | ❌           | ❌       |
Flight Speed                          | ❌   | ❌           | ❌       |
Flight Coupons                        | ❌   | ❌           | ❌       |
Help SubCommand                       | ❌   | ❌           | ❌       |
Admin UI                              | ❌   | ❌           | ❌       |
Developer Documentation               | ⚠️   | ❌           | ❌       |
Flight Addons                         | ❌   | ❌           | ❌       |

## Requirements
The list below state the requirements of FlyPE!

+ A PocketMine-MP Server (REQUIRED)
+ PHP8 (REQUIRED)

## Commands
Commands and the permissions required to run commands.

Command                                                            | Description                                   | Permission             |
------------------------------------------------------------------ | --------------------------------------------- | ---------------------- |
/fly                                                               | Toggles your flight!                          | flype.command          |
/fly toggle [player:string] [toggleMode:string] [flightTime:string]| Toggles flight for others!                    | flype.command.others   |
/fly sound                                                         | Configure your flight toggle sound!           | flype.command.sound    |
/fly particle                                                      | Configure your flight particles while flying! | flype.command.particle |
/fly effect                                                        | Configure your flight effects while flying!   | flype.command.effect   |
/fly cape                                                          | Configure your flight cape while flying!      | flype.command.cape     |
/fly reload                                                        | Reload your configuration while server is on! | flype.command.reload   |

> **[player:string]** - The player's name. (doesn't have to be the full name)\
> **[toggleMode:string]** - The toggle mode. (on/off)\
> **[flightTime:string]** - The flight duration. (m, h, d, w, M, y)

## Permissions
Permissions required for players to use the command, /fly.

Permission                | Description                                       | Default |
------------------------- | ------------------------------------------------  | ------- |
flype.command             | Run the Flight Command!                           | op      |
flype.command.others      | Run the Toggle Subcommand on others!              | op      |
flype.command.sound       | Run the Sound Subcommand!                         | op      |
flype.command.particle    | Run the Particle Subcommand!                      | op      |
flype.command.effect      | Run the Effect Subcommand! (NOT RECOMMENDED)      | op      |
flype.command.cape        | Run the Cape Subcommand!                          | op      |
flype.command.reload      | Run the Reload Subcommand!                        | op      |
flype.allow.sound         | Hear the Flight Toggle Sound!                     | op      |
flype.allow.particle      | Have a Flight Particle!                           | op      |
flype.allow.effect        | Have a Flight Effect!                             | op      |
flype.allow.cape          | Have a Flight Cape!                               | op      |
flype.world.bypass        | Bypass Checks when Switching Worlds!              | false   |

## Configuration
These are the config versions of FlyPE. To find your config version, open the **config.yml** file in FlyPE's plugin data folder and find the header, **"VERSION"**.

+ ![Config v1](https://img.shields.io/badge/Config-v1-orange?style=for-the-badge)
You can find the config file for v1.0.0, v1.0.1 [here](https://pastebin.com/raw/RD19kW5s)!

+ ![Config v2](https://img.shields.io/badge/Config-v2-orange?style=for-the-badge)
You can find the config file for v2.0.0, v2.0.1, v2.0.2, v2.0.3 [here](https://pastebin.com/raw/qgu9u1eJ)!

+ ![Config v3](https://img.shields.io/badge/Config-v3-orange?style=for-the-badge)
You can find the config file for v3.3.2, v3.3.3, v3.3.4, v3.7.0, v3.8.3 [here](https://pastebin.com/raw/82znpD4P)!

+ ![Config v4](https://img.shields.io/badge/Config-v4-orange?style=for-the-badge)
You can find the config file for v4.0.2, v4.1.6 [here](https://pastebin.com/raw/9Yx4qapV)!

+ ![Config v4](https://img.shields.io/badge/Config-v5-orange?style=for-the-badge)
You can find the config file for v5.2.0 [here](https://github.com/AGTHARN/FlyPE/tree/pm4/resources)!

## Support
If you have any issues with the plugin, or have any suggestions for the plugin, feel free to let me know [here](https://github.com/AGTHARN/FlyPE/issues) and I would look at it as soon as possible!

Alternatively, you can get support from our [Discord Server](https://discord.gg/KD4Fp8dnZD)!

![GitHub issues](https://img.shields.io/github/issues/AGTHARN/FlyPE?style=for-the-badge) ![GitHub closed issues](https://img.shields.io/github/issues-closed/AGTHARN/FlyPE?style=for-the-badge)

## Developers
FlyPE contains a huge API you could use in your own plugin to manage players' flight! You could also use the API to integrate your plugins with FlyPE's! Below are the tools available to help you with integration:

> ![Dev Doc](https://media.discordapp.net/attachments/489366022172966922/922049514678931456/devdoc.png?width=161&height=34)\
> https://github.com/AGTHARN/FlyPE/wiki/Developer-Documentation

<details><summary>FAQ</summary>
<p>

## Frequently-Asked-Questions
- [How do I report an issue or suggestion?](#how-do-i-report-an-issue-or-suggestion)
- [Does temporal flight count down while the player is online or offline?](#does-temporal-flight-count-down-while-the-player-is-online-or-offline)
- [Are any PMMP forks supported?](#are-any-pmmp-forks-supported)

### How do I report an issue or suggestion?
***A:*** Issues can be opened by clicking the **New issue** button located in [Issues](https://github.com/AGTHARN/FlyPE/issues). Feel free to use any format you'd like as long as you state the issue/suggestion you have! You may also get help by joining our [Discord Server](https://discord.gg/KD4Fp8dnZD)!

### Does temporal flight count down while the player is online or offline?
***A:*** The temporal flight system counts down while the player is both online and offline.

### Are any PMMP forks supported?
***A:*** No support will be given for PMMP forks.

</p>
</details>
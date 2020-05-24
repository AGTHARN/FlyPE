# FlyPE [![HitCount](http://hits.dwyl.com/AGTHARN/FlyPE/master.svg)](http://hits.dwyl.com/AGTHARN/FlyPE/master) [![GitHub license](https://img.shields.io/github/license/AGTHARN/FlyPE)](https://github.com/AGTHARN/FlyPE/blob/master/LICENSE) [![](https://poggit.pmmp.io/shield.state/FlyPE)](https://poggit.pmmp.io/p/FlyPE)
![flypeicon](https://user-images.githubusercontent.com/63234276/82717419-895d2380-9cce-11ea-9d7e-0981d91c75fa.jpg)

FlyPE is a fly plugin for pocketmine that allows you to fly in survival mode when you run the fly command! You can choose to allow fly to be used or not allow fly to be used in selected worlds in the config! And there are more features listed below!

# How do I use it?
To use it, [download the latest phar](https://poggit.pmmp.io/ci/AGTHARN/FlyPE/FlyPE) and put it in your server's plugins folder. Restart your server and make sure that you have OP or the permission to use /fly ingame to toggle flight! After that, make sure to also check out the config in your plugins data folder and edit it if you feel like wanting to do so!

# Requirements
+ [EconomyAPI](https://poggit.pmmp.io/p/EconomyAPI/5.7.2)
(Only required if you have the payforfly config on)

# Features
- [x] Blacklist selected worlds from flying
- [x] Whitelist selected worlds from flying
- [x] An option to disable fly when hurt by a player
- [x] An option to disable fly after joining if fly is on
- [x] Blocked players in creative from toggling fly
- [x] Able to toggle fly for other players
- [x] An option to allow a fly UI to toggle flight
- [x] An option to enable buying of fly
- [x] Customisable messages
- [ ] Temporal fly time

# Issues
If you have found an issue while using the plugin, please report it [here](https://github.com/AGTHARN/FlyPE/issues). Thank you!

[![Average time to resolve an issue](http://isitmaintained.com/badge/resolution/AGTHARN/FlyPE.svg)](http://isitmaintained.com/project/AGTHARN/FlyPE "Average time to resolve an issue") [![Percentage of issues still open](http://isitmaintained.com/badge/open/AGTHARN/FlyPE.svg)](http://isitmaintained.com/project/AGTHARN/FlyPE "Percentage of issues still open")

# Permissions

Permission           | Description                                                        | Default |
-------------------- | ------------------------------------------------------------------ | ------- |
flype.command        | Allows player to run the fly command                               | OP      |
flype.command.others | Allows player to run the fly command on others                     | OP      |
flype.command.bypass | Allows player to bypass checks when switching to a different world | OP      |

# Config File
You can find the config file [here](https://github.com/AGTHARN/FlyPE/blob/master/resources/config.yml)!

# Plugin Info
+ Name: FlyPE
+ Author: AGTHARN
+ Version: 1.1.0
+ Api: 3.0.0

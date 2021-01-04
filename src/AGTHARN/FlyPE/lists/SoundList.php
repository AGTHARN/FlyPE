<?php

/* 
 *  ______ _  __     _______  ______ 
 * |  ____| | \ \   / /  __ \|  ____|
 * | |__  | |  \ \_/ /| |__) | |__   
 * |  __| | |   \   / |  ___/|  __|  
 * | |    | |____| |  | |    | |____ 
 * |_|    |______|_|  |_|    |______|
 *
 * FlyPE, is an advanced fly plugin for PMMP.
 * Copyright(C) 2020-2021 AGTHARN
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 *(at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace AGTHARN\FlyPE\lists;

use pocketmine\level\sound\AnvilBreakSound;
use pocketmine\level\sound\AnvilFallSound;
use pocketmine\level\sound\AnvilUseSound;
use pocketmine\level\sound\BlazeShootSound;
use pocketmine\level\sound\ClickSound;
use pocketmine\level\sound\DoorBumpSound;
use pocketmine\level\sound\DoorCrashSound;
use pocketmine\level\sound\DoorSound;
use pocketmine\level\sound\EndermanTeleportSound;
use pocketmine\level\sound\FizzSound;
use pocketmine\level\sound\GhastShootSound;
use pocketmine\level\sound\GhastSound;
use pocketmine\level\sound\LaunchSound;
use pocketmine\level\sound\PopSound;
use pocketmine\math\Vector3;

class SoundList {
            
    /**
     * getSound
     *
     * @param  string $soundName
     * @param  Vector3 $playerPos
     * @return void|object|mixed
     */
    public function getSound(string $soundName = "popsound", Vector3 $playerPos) {
        switch(str_replace(" ", "", strtolower($soundName))):
            case "anvilbreaksound":
            case "anvilbreak":
                return new AnvilBreakSound($playerPos);
            case "anvilfallsound":
            case "anvilfall":
                return new AnvilFallSound($playerPos);
            case "anvilusesound":
            case "anviluse":
                return new AnvilUseSound($playerPos);
            case "blazeshootsound":
            case "blazeshoot":
            case "blaze":
                return new BlazeShootSound($playerPos);
            case "clicksound":
            case "click":
                return new ClickSound($playerPos);
            case "doorbumpsound":
            case "doorbump":
                return new DoorBumpSound($playerPos);
            case "doorcrashsound":
            case "doorcrash":
                return new DoorCrashSound($playerPos);
            case "doorsound":
            case "door":
                return new DoorSound($playerPos);
            case "endermanteleportsound":
            case "endermanteleport":
            case "enderman":
            case "teleport":
                return new EndermanTeleportSound($playerPos);
            case "fizzsound":
            case "fizz":
                return new FizzSound($playerPos);
            case "ghastshootsound":
            case "ghastshoot":
                return new GhastShootSound($playerPos);
            case "ghastsound":
            case "ghast":
                return new GhastSound($playerPos);
            case "launchsound":
            case "launch":
                return new LaunchSound($playerPos);
            case "popsound":
            case "pop":
                return new PopSound($playerPos);
            default:
                return new PopSound($playerPos);
		endswitch;
    }
}

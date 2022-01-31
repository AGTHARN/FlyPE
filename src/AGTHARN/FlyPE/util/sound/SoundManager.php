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
 * Copyright (C) 2020-2021 AGTHARN
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

declare(strict_types=1);

namespace AGTHARN\FlyPE\util\sound;

use AGTHARN\FlyPE\Main;
use pocketmine\player\Player;
use pocketmine\world\sound\Sound;
use AGTHARN\FlyPE\util\sound\SoundList;

class SoundManager
{
    /** @var Main */
    public Main $plugin;

    /** @var SoundList */
    public SoundList $soundList;

    /**
     * __construct
     *
     * @param Main $plugin
     * @return void
     */
    public function __construct(Main $plugin)
    {
        $this->plugin = $plugin;

        $this->soundList = $plugin->soundList;
    }

    /**
     * Sets toggle flight sound for a player.
     *
     * @param Player $player
     * @param string $soundName
     * @return void
     */
    public function setFlightSound(Player $player, string $soundName): void
    {
        $this->plugin->sessionManager->getSessionByPlayer($player)->getProvider()->setData('flightSound', $soundName);
    }

    /**
     * Removes toggle flight sound for a player.
     *
     * @param Player $player
     * @return void
     */
    public function removeFlightSound(Player $player): void
    {
        $this->plugin->sessionManager->getSessionByPlayer($player)->getProvider()->setData('flightSound', '');
    }

    /**
     * Returns sound based on the sound name given. Returns null if doesn't exist.
     *
     * @param string $soundName
     * @return Sound|null
     */
    public function getSoundFromString(string $soundName): ?Sound
    {
        $soundName = str_replace(' ', '', strtolower($soundName));
        foreach ($this->soundList->allSounds as $soundReal) {
            if (strpos($soundName, strtolower($soundReal)) !== false) {
                $soundReal = "pocketmine\\world\\sound\\" . $soundReal;
                return new $soundReal();
            }
        }
        return null;
    }
}
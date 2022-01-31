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

use AGTHARN\FlyPE\util\trait\SimpleTrait;

class SoundList
{
    use SimpleTrait;

    /** @var string[] */
    public array $allSounds = [
        'AnvilBreakSound',
        'AnvilFallSound',
        'AnvilUseSound',
        'ArrowHitSound',
        'BarrelCloseSound',
        'BarrelOpenSound',
        'BellRingSound',
        'BlazeShootSound',
        //'BlockBreakSound',
        //'BlockPlaceSound',
        //'BlockPunchSound',
        'BowShootSound',
        'BucketEmptyLavaSound',
        'BucketEmptyWaterSound',
        'BucketFillLavaSound',
        'BucketFillWaterSound',
        'EnderChestCloseSound', // PRIORITY
        'EnderChestOpenSound',
        'ChestCloseSound',
        'ChestOpenSound',
        'ClickSound',
        'DoorBumpSound',
        'DoorCrashSound',
        'DoorSound',
        'EndermanTeleportSound',
        'EntityAttackNoDamageSound',
        'EntityAttackSound',
        //'EntityLandSound',
        //'EntityLongFallSound',
        //'EntityShortFallSound',
        'ExplodeSound',
        'FireExtinguishSound',
        'FizzSound',
        'FlintSteelSound',
        'GhastShootSound',
        'GhastSound',
        'IgniteSound',
        'ItemBreakSound',
        'LaunchSound',
        //'NoteInstrument',
        //'NoteSound',
        'PaintingPlaceSound',
        'PopSound',
        'PotionSplashSound',
        //'RecordSound',
        //'RecordStopSound',
        'RedstonePowerOffSound',
        'RedstonePowerOnSound',
        'ShulkerBoxCloseSound',
        'ShulkerBoxOpenSound',
        'ThrowSound',
        'TotemUseSound',
        'XpCollectSound'
        //'XpLevelUpSound'
    ];

    /**
     * Removes sounds that are excluded based on the cosmetic config.
     *
     * @return void
     */
    public function excludeSounds(): void
    {
        foreach ($this->allSounds as $soundReal) {
            foreach ($this->plugin->config->getConfig('cosmetic', 'excluded-sounds') as $excludedSound) {
                $excludedSound = str_replace(' ', '', strtolower($excludedSound));
                if (similar_text($excludedSound, $soundReal) >= 4) {
                    unset($this->allSounds[$soundReal]);
                }
            }
        }
    }
}

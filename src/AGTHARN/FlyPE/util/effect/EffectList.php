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

namespace AGTHARN\FlyPE\util\effect;

use AGTHARN\FlyPE\util\trait\SimpleTrait;

class EffectList
{
    use SimpleTrait;

    /** @var string[] */
    public array $allEffects = [
        'Absorption',
        'Blindness',
        'Conduit Power',
        'Fatal Poison',
        'Fire Resistance',
        'Haste',
        'Health Boost',
        'Hunger',
        'Instant Damage',
        'Instant Health',
        'Invisibility',
        'Jump Boost',
        'Levitation',
        'Mining Fatigue',
        'Nausea',
        'Night Vision',
        'Poison',
        'Regeneration',
        'Resistance',
        'Saturation',
        'Slowness',
        'Speed',
        'Strength',
        'Water Breathing',
        'Weakness',
        'Wither'
    ];

    /**
     * Removes effects that are excluded based on the cosmetic config.
     *
     * @return void
     */
    public function excludeEffects(): void
    {
        foreach ($this->allEffects as $effectReal) {
            foreach ($this->plugin->config->getConfig('cosmetic', 'excluded-effects') as $excludedEffect) {
                $excludedEffect = str_replace(' ', '', strtolower($excludedEffect));
                if (similar_text($excludedEffect, $effectReal) >= 4) {
                    unset($this->allEffects[$effectReal]);
                }
            }
        }
    }
}
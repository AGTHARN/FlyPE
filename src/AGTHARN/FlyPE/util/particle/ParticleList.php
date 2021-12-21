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

namespace AGTHARN\FlyPE\util\particle;

use AGTHARN\FlyPE\util\trait\SimpleTrait;

class ParticleList
{
    use SimpleTrait;

    /** @var string[] */
    public array $allParticles = [
        'AngryVillagerParticle',
        'BubbleParticle',
        'BlockForceFieldParticle',
        //'BlockPunchParticle',
        'CriticalParticle',
        //'DragonEggTeleportParticle',
        //'DustParticle',
        'EnchantmentTableParticle',
        //'EnchantParticle',
        'EndermanTeleportParticle',
        'EntityFlameParticle',
        'ExplodeParticle',
        'FlameParticle',
        'HappyVillagerParticle',
        'HeartParticle',
        'HugeExplodeParticle',
        'HugeExplodeSeedParticle',
        'InkParticle',
        //'InstantEnchantParticle',
        //'ItemBreakParticle',
        'LavaDripParticle',
        'LavaParticle',
        'MobSpawnParticle',
        'PortalParticle',
        //'PotionSplashParticle',
        'RainSplashParticle',
        'RedstoneParticle',
        'SmokeParticle',
        'SnowballPoofParticle',
        'SplashParticle',
        'SporeParticle',
        //'TerrainParticle',
        'WaterDripParticle',
        'WaterParticle'
    ];

    /**
     * Removes particles that are excluded based on the cosmetic config.
     *
     * @return void
     */
    public function excludeParticles(): void
    {
        foreach ($this->allParticles as $particleReal) {
            foreach ($this->plugin->config->getConfig('cosmetic', 'excluded-particles') as $excludedParticle) {
                $excludedParticle = str_replace(' ', '', strtolower($excludedParticle));
                if (similar_text($excludedParticle, $particleReal) >= 4) {
                    unset($this->allParticles[$particleReal]);
                }
            }
        }
    }
}
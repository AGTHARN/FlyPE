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
 * Copyright(C) 2020 AGTHARN
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

use pocketmine\level\particle\BubbleParticle;
use pocketmine\level\particle\CriticalParticle;
use pocketmine\level\particle\EnchantParticle;
use pocketmine\level\particle\InstantEnchantParticle;
use pocketmine\level\particle\ExplodeParticle;
use pocketmine\level\particle\HugeExplodeParticle;
use pocketmine\level\particle\EntityFlameParticle;
use pocketmine\level\particle\FlameParticle;
use pocketmine\level\particle\HeartParticle;
use pocketmine\level\particle\InkParticle;
use pocketmine\level\particle\LavaDripParticle;
use pocketmine\level\particle\LavaParticle;
use pocketmine\level\particle\PortalParticle;
use pocketmine\level\particle\RedstoneParticle;
use pocketmine\level\particle\SmokeParticle;
use pocketmine\level\particle\SplashParticle;
use pocketmine\level\particle\SporeParticle;
use pocketmine\level\particle\MobSpawnParticle;
use pocketmine\level\particle\WaterDripParticle;
use pocketmine\level\particle\WaterParticle;
use pocketmine\level\particle\EnchantmentTableParticle;
use pocketmine\level\particle\HappyVillagerParticle;
use pocketmine\level\particle\AngryVillagerParticle;
use pocketmine\level\particle\RainSplashParticle;
use pocketmine\level\particle\BlockForceFieldParticle;
use pocketmine\level\particle\DestroyBlockParticle;
use pocketmine\math\Vector3;

use AGTHARN\FlyPE\Main;

class ParticleList {
	
	/**
	 * __construct
	 *
	 * @param  Main $plugin
	 * @return void
	 */
	public function __construct(Main $plugin) {}
        
    /**
     * getParticle
     *
     * @param  string $particleName
     * @param  Vector3 $playerPos
     * @return void|object|mixed
     */
    public function getParticle(string $particleName, Vector3 $playerPos, Block $block) {
        switch(strtolower($particleName)):
            // a
            case "angryvillagerparticle":
            case "angryvillager":
            case "angry":
                return new AngryVillagerParticle($playerPos);
            // b
            case "bubbleparticle":
            case "bubble":
                return new BubbleParticle($playerPos);
            case "blockforcefield":
            case "blockforce":
            case "forcefield":
                return new BlockForceFieldParticle($playerPos, 0);
            // c
            case "criticalparticle":
            case "critical":
            case "crit":
                return new CriticalParticle($playerPos);
            // d
            case "destroyblockparticle":
            case "destroyblock":
            case "blockdestroy":
                return new DestroyBlockParticle($playerPos, $block);
            // e
            case "explodeparticle":
			case "explode":
                return new ExplodeParticle($playerPos);
            case "entityflameparticle":
            case "entityflame":
            case "flameentity":
                return new EntityFlameParticle($playerPos);
            case "enchantmenttableparticle":
            case "enchantmenttable":
            case "enchantment":
                return new EnchantmentTableParticle($playerPos);
            case "enchantparticle":
            case "enchant":
                return new EnchantParticle($playerPos);
            // f
            case "flameparticle":
            case "flame":
            case "fire":
                return new FlameParticle($playerPos);
            // g
            // h
            case "hugeexplodeparticle":
            case "hugeexplode":
                return new HugeExplodeParticle($playerPos);
            case "heartparticle":
            case "heart":
                return new HeartParticle($playerPos);
            case "happyvillagerparticle":
            case "happyvillager":
            case "happy":
                return new HappyVillagerParticle($playerPos);
            // i
            case "inkparticle":
            case "ink":
                return new InkParticle($playerPos);
            case "instantenchantparticle":
            case "instantenchant":
            case "instant":
                return new InstantEnchantParticle($playerPos);
            // j
            // k
            // l
            case "lavaparticle":
            case "lava":
				return new LavaParticle($playerPos);
            case "lavadripparticle":
            case "lavadrip":
            case "driplava":
                return new LavaDripParticle($playerPos);
            // m
            case "mobspawnparticle":
            case "mobspawn":
            case "spawnmob":
				return new MobSpawnParticle($playerPos);
            // n
            // o
            // p
            case "portalparticle":
            case "portal":
				return new PortalParticle($playerPos);
            // q
            // r
            case "redstoneparticle":
            case "redstone":
                return new RedstoneParticle($playerPos);
            case "rainsplashparticle":
            case "rainsplash":
            case "rain":
                return new RainSplashParticle($playerPos);
            // s
            case "smokeparticle":
            case "smoke":
                return new SmokeParticle($playerPos);
            case "splashparticle":
            case "splash":
                return new SplashParticle($playerPos);
            case "sporeparticle":
            case "spore":
                return new SporeParticle($playerPos);
            case "snowballpoofparticle":
            case "snowballpoof":
            case "snowball":
                return new SnowballPoofParticle($playerPos);
            // t
            // u
            // v
            // w
            case "waterparticle":
            case "water":
				return new WaterParticle($playerPos);
            case "waterdripparticle":
            case "waterdrip":
            case "dripwater":
                return new WaterDripParticle($playerPos);
            // x
            // y
            // z
            default:
                return new FlameParticle($playerPos);
		endswitch;
    }
}

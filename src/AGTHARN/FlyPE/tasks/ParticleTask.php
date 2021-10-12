<?php
declare(strict_types = 1);

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

namespace AGTHARN\FlyPE\tasks;

use AGTHARN\FlyPE\Main;
use pocketmine\block\Block;
use AGTHARN\FlyPE\util\Util;
use pocketmine\math\Vector3;
use pocketmine\scheduler\Task;

class ParticleTask extends Task
{
    /** @var Main */
    protected Main $plugin;
    /** @var Util */
    protected Util $util;
    
    /** @var mixed */
    private $vanishV2;
    /** @var mixed */
    private $simpleLay;
    
    /**
     * __construct
     *
     * @param  Main $plugin
     * @param  Util $util
     * @return void
     */
    public function __construct(Main $plugin, Util $util)
    {
        $this->plugin = $plugin;
        $this->util = $util;

        $this->vanishV2 = $this->plugin->getServer()->getPluginManager()->getPlugin('VanishV2') ?? null;
        $this->simpleLay = $this->plugin->getServer()->getPluginManager()->getPlugin('SimpleLay') ?? null;
    }
        
    /**
     * onRun
     *
     * @param  int $tick
     * @return void
     */
    public function onRun(int $tick): void
    {
        foreach ($this->plugin->getServer()->getOnlinePlayers() as $player) {
            if (!$this->plugin->getConfig()->get('creative-mode-particles') && $player->isCreative(true))
                return;
            
            if ($this->vanishV2 !== null && $this->plugin->getConfig()->get('vanishv2-support') && in_array($player->getName(), $this->vanishV2::$vanish))
                return;
            if ($this->simpleLay !== null && $this->plugin->getConfig()->get('simplelay-support') && ($this->simpleLay->isLaying($player) || $this->simpleLay->isSitting($player)))
                return;

            if ($player->getAllowFlight() && $player->getAllowFlight() && $player->hasPermission('flype.particles')) {
                $player->getLevel()->addParticle($this->util->getParticleList()->getParticle(new Vector3($player->x, $player->y, $player->z), Block::get((int)$this->plugin->getConfig()->get('particle-block-id'), (int)$this->plugin->getConfig()->get('fly-particle-type')) ?? Block::get(1)));
            }
        }
    }
}

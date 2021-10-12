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

use pocketmine\Player;
use AGTHARN\FlyPE\Main;
use pocketmine\entity\Effect;
use pocketmine\scheduler\Task;
use pocketmine\entity\EffectInstance;

class EffectTask extends Task
{
    /** @var Main */
    protected $plugin;

    /** @var mixed */
    private $vanishV2;
    /** @var mixed */
    private $simpleLay;
    
    /**
     * __construct
     *
     * @param  Main $plugin
     * @return void
     */
    public function __construct(Main $plugin)
    {
        $this->plugin = $plugin;

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
            if ($player->getAllowFlight() && $player->getAllowFlight() && $player->hasPermission('flype.effects')) {
                if (!$this->plugin->getConfig()->get('creative-mode-effects') && $player->getGamemode() === Player::CREATIVE) return;

                if ($this->vanishV2 !== null && $this->plugin->getConfig()->get('vanishv2-support') && in_array($player->getName(), $this->vanishV2::$vanish))
                    return;
                if ($this->simpleLay !== null && $this->plugin->getConfig()->get('simplelay-support') && ($this->simpleLay->isLaying($player) || $this->simpleLay->isSitting($player)))
                    return;
                
                $effect = new EffectInstance(Effect::getEffectByName($this->plugin->getConfig()->get('effect-type')) ?? Effect::getEffectByName('HASTE'));
                $effect->setDuration(40);
                $effect->setAmplifier(intval($this->plugin->getConfig()->get('effect-amplifier')));
                $effect->setVisible($this->plugin->getConfig()->get('effect-visible'));

                $player->addEffect($effect);
            }
        }
    }
}

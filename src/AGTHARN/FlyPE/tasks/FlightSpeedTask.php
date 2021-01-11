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

namespace AGTHARN\FlyPE\tasks;

use pocketmine\scheduler\Task;
use pocketmine\entity\Attribute;
use pocketmine\Player;

use AGTHARN\FlyPE\Main;

class FlightSpeedTask extends Task {

    /**
     * plugin
     * 
     * @var Main
     */
    private $plugin;
    
    /**
     * __construct
     *
     * @param  Main $plugin
     * @return void
     */
    public function __construct(Main $plugin) {
        $this->plugin = $plugin;
    }
        
    /**
     * onRun
     *
     * @param  int $tick
     * @return void
     */
    public function onRun(int $tick): void {
        foreach ($this->plugin->getServer()->getOnlinePlayers() as $player) {
            $attribute = $player->getAttributeMap()->getAttribute(Attribute::MOVEMENT_SPEED);
            
            if (!$this->plugin->getConfig()->get("fly-speed-creative") && $player->getGamemode() === Player::CREATIVE) return;
            if ($player->getAllowFlight() && $player->isFlying() && !$player->onGround && $player->hasPermission("flype.flightspeed")) {
                $attribute->setValue($attribute->getValue() * $this->plugin->getConfig()->get("fly-speed"));
            } elseif (!$player->isSprinting() && $player->onGround) {
                $attribute->resetToDefault();
            }
        }
    }
}

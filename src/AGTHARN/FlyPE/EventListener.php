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

namespace AGTHARN\FlyPE;

use AGTHARN\FlyPE\Main;
use AGTHARN\FlyPE\util\Flight;
use pocketmine\event\Listener;

class EventListener implements Listener
{
    /** @var Main */
    private Main $plugin;
    /** @var Flight */
    private Flight $flight;

    /**
     * __construct
     *
     * @param  Main $plugin
     * @param  Flight $util
     * @return void
     */
    public function __construct(Main $plugin, Flight $flight)
    {
        $this->plugin = $plugin;
        $this->flight = $flight;
    }
}
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

namespace AGTHARN\FlyPE\task;

use pocketmine\scheduler\Task;
use AGTHARN\FlyPE\session\PlayerSession;
use AGTHARN\FlyPE\util\trait\BasicTrait;

class TempFlightTask extends Task
{
    use BasicTrait;

    /**
     * onRun
     *
     * @return void
     */
    public function onRun(): void
    {
        /** @var PlayerSession $playerSession */
        foreach ($this->plugin->sessionManager->getSessions() as $playerSession) {
            $flightTime = $playerSession->getProvider()->extractData('flightTime');
            if ($flightTime !== 0) {
                if ($flightTime <= time()) {
                    $this->plugin->flight->toggleFlight($playerSession->getPlayer(), false, null, true);
                    $playerSession->getProvider()->setData('flightTime', 0);
                }
            }
        }
    }
}

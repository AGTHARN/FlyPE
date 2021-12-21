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

namespace AGTHARN\FlyPE\provider;

class SQProvider extends Provider
{
    /**
     * Saves the data.
     *
     * @return void
     */
    public function saveData(): void
    {
        $this->plugin->dataBase->libasynql->executeChange('flype.update', $this->getData());

        //$this->plugin->dataBase->libasynql->executeChange('flype.update', [
        //    'uuid' => $this->getUuid(),
        //    'username' => $this->extractData('username'),
        //    'flightState' => (bool) $this->getFlightState(),
        //    'flightSound' => $this->getFlightSound(),
        //    'flightParticle' => $this->getFlightParticle(),
        //    'flightEffect' => $this->getFlightEffect(),
        //    'flightCape' => $this->getFlightCape(),
        //    'flightTime' => $this->getFlightTime()
        //]); 
    }
}
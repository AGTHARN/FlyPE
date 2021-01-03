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

use AGTHARN\FlyPE\Main;
use AGTHARN\FlyPE\util\Util;
use AGTHARN\FlyPE\data\FlightData;

class FlightDataTask extends Task {

    /**
     * plugin
     * 
     * @var Main
     */
    private $plugin;

    /**
	 * util
	 * 
     * @var Util
     */
    private $util;
    
    /**
     * data
     *
     * @var array
     */
    private $data = [];
	
	/**
	 * __construct
	 *
	 * @param  Main $plugin
     * @param  Util $util
	 * @return void
	 */
	public function __construct(Main $plugin, Util $util) {
        $this->plugin = $plugin;
        $this->util = $util;
	}
        
    /**
     * onRun
     *
     * @param  int $tick
     * @return void
     */
    public function onRun(int $tick): void {
        foreach ($this->plugin->getServer()->getOnlinePlayers() as $player) {
            $this->data[$player->getId()] = new FlightData($this->plugin, $this->util, $player->getName());

            if(isset($this->data[$player->getId()]) && $player->getAllowFlight() === true){
                $data = $this->data[$player->getId()];
                $data->incrementTime();
                $data->saveData();

                if ($data->getDataTime() >= $this->plugin->getConfig()->get("fly-seconds")) {
                    $this->util->toggleFlight($player);
                    $data->resetDataTime();
                    $data->saveData();
                }
                
                // for debug (if this is not disabled please contact me immediately)
                // $this->plugin->getLogger()->info("FlightDataTask " . $player->getName() . " " . $data->getDataTime()); 
            }
        }
    }
}

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

namespace AGTHARN\FlyPE\data;

use AGTHARN\FlyPE\Main;
use AGTHARN\FlyPE\util\Util;

class FlightData {

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
     * playerName
     *
     * @var string
     */
    private $playerName;
    
    /**
     * time
     *
     * @var int
     */
    private $time = 0;
    
    /**
     * setTime
     *
     * @var int
     */
    private $setTime;
    
    /**
     * __construct
     *
     * @param  Main $plugin
     * @param  Util $util
     * @param  string $playerName
     * @return void
     */
    public function __construct(Main $plugin, Util $util, string $playerName, int $setTime) {
        $this->plugin = $plugin;
        $this->util = $util;
        $this->playerName = $playerName;

        if (!is_file($this->getDataPath())) return;

        $data = yaml_parse_file($this->getDataPath());
        $this->time = $data["time"];
        $this->setTime = $setTime;
    }
    
    /**
     * getDataPath
     *
     * @return string
     */
    public function getDataPath(): string {
        return $this->plugin->getDataFolder() . "data/". strtolower($this->playerName) . ".yml";
    }
    
    /**
     * getDataTime
     *
     * @return int
     */
    public function getDataTime(): int {
        return $this->time;
    }
    
    /**
     * setDataTime
     *
     * @return void
     */
    public function setDataTime(int $time): void {
        $this->time = $time;
    }
        
    /**
     * resetDataTime
     *
     * @return void
     */
    public function resetDataTime(): void {
        $this->time = $this->setTime;
    }
            
    /**
     * saveData
     *
     * @return void
     */
    public function saveData(): void {
        yaml_emit_file($this->getDataPath(), [
            "time" => $this->getDataTime()
        ]);
    }
        
    /**
     * decreaseTime
     *
     * @return void
     */
    public function decreaseTime(): void {
        $this->time--;
    }
}
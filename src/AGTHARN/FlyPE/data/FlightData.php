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

namespace AGTHARN\FlyPE\data;

use AGTHARN\FlyPE\util\Util;
use AGTHARN\FlyPE\Main;

class FlightData {

    /**
     * plugin
     * 
     * @var Main
     */
    protected $plugin;

    /**
     * util
     * 
     * @var Util
     */
    protected $util;
    
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
     * purchased
     *
     * @var bool
     */
    private $purchased;
    
    /**
     * flightState
     *
     * @var bool
     */
    private $flightState;
    
    /**
     * tempFlight
     *
     * @var bool
     */
    private $tempFlight;
    
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
        $this->checkKeys();

        $data = yaml_parse_file($this->getDataPath());

        $this->tempFlight = $data['temp-toggle'];
        $this->time = $data['time'];
        $this->setTime = $setTime;

        $this->purchased = $data['purchased'];
        $this->flightState = $data['flight-state'];
    }
    
    /**
     * checkKeys
     *
     * @return void
     */
    public function checkKeys(): void {
        $data = yaml_parse_file($this->getDataPath());

        if (empty($data['temp-toggle'])) {
            $data['temp-toggle'] = false;
        }

        if (empty($data['time'])) {
            $data['time'] = 0;
        }

        if (empty($data['purchased'])) {
            $data['purchased'] = false;
        }

        if (empty($data['flight-state'])) {
            $data['flight-state'] = false;
        }
        yaml_emit_file($this->getDataPath(), $data);
    }
    
    /**
     * getDataPath
     *
     * @return string
     */
    public function getDataPath(): string {
        return $this->plugin->getDataFolder() . 'data' . DIRECTORY_SEPARATOR . strtolower($this->playerName) . '.yml';
    }
    
    /**
     * getTempToggled
     *
     * @return mixed
     */
    public function getTempToggle() {
        return $this->tempFlight;
    }
    
    /**
     * getDataTime
     *
     * @return mixed
     */
    public function getDataTime() {
        return $this->time;
    }
    
    /**
     * getPurchased
     *
     * @return mixed
     */
    public function getPurchased() {
        return $this->purchased;
    }
    
    /**
     * getFlightState
     *
     * @return mixed
     */
    public function getFlightState() {
        return $this->flightState;
    }
            
    /**
     * setTempToggle
     *
     * @param  bool $toggle
     * @return void
     */
    public function setTempToggle(bool $toggle): void {
        $this->tempFlight = $toggle;
    }

    /**
     * setPurchased
     *
     * @param  bool $purchased
     * @return void
     */
    public function setPurchased(bool $purchased): void {
        $this->purchased = $purchased;
    }
    
    /**
     * setFlightState
     *
     * @param  bool $state
     * @return void
     */
    public function setFlightState(bool $state): void {
        $this->flightState = $state;
    }
        
    /**
     * resetDataTime
     *
     * @return void
     */
    public function resetDataTime(): void {
        $this->time = time() + $this->setTime;
    }
            
    /**
     * saveData
     *
     * @return void
     */
    public function saveData(): void {
        yaml_emit_file($this->getDataPath(), [
            'temp-toggle' => $this->getTempToggle(),
            'time' => $this->getDataTime(),
            'purchased' => $this->getPurchased(),
            'flight-state' => $this->getFlightState()
        ]);
    }
    
    /**
     * checkNew
     *
     * @return bool
     */
    public function checkNew(): bool {
        if ((!$this->getFlightState() || $this->getFlightState() === '~') && (!$this->getPurchased() || $this->getPurchased() === '~') && (!$this->getTempToggle() || $this->getTempToggle() === '')) {
            return true;
        }
        return false;
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
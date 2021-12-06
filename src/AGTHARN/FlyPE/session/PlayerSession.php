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

namespace AGTHARN\FlyPE\session;

use AGTHARN\FlyPE\Main;

class PlayerSession
{
    /** @var Main */
    private Main $plugin;

    /** @var string */
    private string $name;
    /** @var string */
    private string $uuid;
    /** @var bool */
    private bool $flightState = false;

    /**
     * __construct
     *
     * @param  Main $plugin
     * @param  string $uuid
     * @param  string $name
     * @param  bool $flightState
     * @return void
     */
    public function __construct(Main $plugin, string $uuid, string $name, bool $flightState)
    {
        $this->plugin = $plugin;

        $this->uuid = $uuid;
        $this->name = $name;
        $this->flightState = $flightState;
    }

    /**
     * returns the name of the player
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * returns the uuid of the player in form of a string
     *
     * @return string
     */
    public function getUuid(): string
    {
        return $this->uuid;
    }

    /**
     * sets the flight state of the player
     *
     * @param bool $flightState
     * @return void
     */
    public function setFlightState(bool $flightState): void
    {
        $this->flightState = $flightState;
        $this->save();
    }

    /**
     * returns the fly state of the player
     * true when player can fly
     * false when player cant fly
     * 
     * supersedes Player::getAllowFlight()
     *
     * @return bool
     */
    public function getFlightState(): bool
    {
        return $this->flightState;
    }

    /**
     * save
     *
     * @return void
     */
    public function save(): void
    {
        $this->plugin->dataBase->executeChange('flype.update', [
            'username' => $this->getName(),
            'uuid' => $this->getUuid(),
            'flightState' => $this->getFlightState()
        ]);
    }
}

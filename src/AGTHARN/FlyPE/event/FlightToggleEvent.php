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

namespace AGTHARN\FlyPE\event;

use pocketmine\player\Player;
use pocketmine\event\Cancellable;
use pocketmine\event\CancellableTrait;
use pocketmine\event\player\PlayerEvent;

class FlightToggleEvent extends PlayerEvent implements Cancellable
{
    use CancellableTrait;

    /** @var bool */
    protected bool $isFlying;
    /** @var bool */
    protected bool $worldAllowed;
    /** @var bool */
    protected bool $gamemodeAllowed;

    /**
     * __construct
     *
     * @param Player $player
     * @param bool $isFlying
     * @param bool $worldAllowed
     * @param bool $gamemodeAllowed
     * @return void
     */
    public function __construct(Player $player, bool $isFlying, bool $worldAllowed, bool $gamemodeAllowed)
    {
        $this->player = $player;
        $this->isFlying = $isFlying;
        $this->worldAllowed = $worldAllowed;
        $this->gamemodeAllowed = $gamemodeAllowed;
    }
    
    /**
     * isFlying
     *
     * @return bool
     */
    public function isFlying(): bool
    {
        return $this->isFlying;
    }
    
    /**
     * isWorldAllowed
     *
     * @return bool
     */
    public function isWorldAllowed(): bool
    {
        return $this->worldAllowed;
    }

    /**
     * isGamemodeAllowed
     *
     * @return bool
     */
    public function isGamemodeAllowed(): bool
    {
        return $this->gamemodeAllowed;
    }
}

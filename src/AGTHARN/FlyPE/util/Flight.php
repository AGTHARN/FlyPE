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

namespace AGTHARN\FlyPE\util;

use AGTHARN\FlyPE\Main;
use pocketmine\player\Player;
use AGTHARN\FlyPE\util\MessageTranslator;

class Flight
{
    /** @var MessageTranslator */
    private MessageTranslator $messageTranslator;

    /**
     * __construct
     *
     * @param  MessageTranslator $messageTranslator
     * @return void
     */
    public function __construct(MessageTranslator $messageTranslator)
    {
        $this->messageTranslator = $messageTranslator;
    }
    
    /**
     * toggleFlight
     *
     * @param  Player $player
     * @param  bool|null $toggleMode
     * @return bool
     */
    public function toggleFlight(Player $player, ?bool $toggleMode = null): bool
    {
        $toggleMode = $toggleMode ?? ($player->getAllowFlight() ? false : true);

        $player->setAllowFlight($toggleMode);
        $player->setFlying($toggleMode);
        
        $this->messageTranslator->sendTranslated($player, $toggleMode ? 'flype.flight.toggle.on' : 'flype.flight.toggle.off');
        return true;
    }
}
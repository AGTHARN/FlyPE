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

namespace AGTHARN\FlyPE\util;

use AGTHARN\FlyPE\Main;
use pocketmine\world\World;
use pocketmine\player\Player;
use AGTHARN\FlyPE\event\FlightToggleEvent;
use AGTHARN\FlyPE\session\SessionManager;

class Flight
{
    /** @var Main */
    private Main $plugin;

    /** @var SessionManager */
    private SessionManager $sessionManager;
    /** @var MessageTranslator */
    private MessageTranslator $messageTranslator;

    /**
     * __construct
     *
     * @param  Main $plugin
     * @return void
     */
    public function __construct(Main $plugin)
    {
        $this->plugin = $plugin;

        $this->sessionManager = $plugin->sessionManager;
        $this->messageTranslator = $plugin->messageTranslator;
    }

    /**
     * toggleFlight
     *
     * @param  Player $player
     * @param  bool|null $toggleMode
     * @param  bool $ignoreWorld
     * @return bool
     */
    public function toggleFlight(Player $player, ?bool $toggleMode = null, bool $ignoreWorld = false): bool
    {
        // NOTE TO SELF: THIS AND EVENT IS USED IN EVENTLISTENER
        $worldAllowed = true;
        if (!$ignoreWorld) {
            $worldAllowed = $this->isWorldAllowed($player->getWorld()) ?: $toggleMode = false;
        }
        $toggleMode = $toggleMode ?? !$player->getAllowFlight();
        $event = new FlightToggleEvent($player, $toggleMode, $worldAllowed);

        $event->call();
        if (!$event->isCancelled()) {
            $player->setAllowFlight($toggleMode);
            $player->setFlying($toggleMode);
            
            $this->sessionManager->getSessionByPlayer($player)->setFlightState($toggleMode);
            $this->messageTranslator->sendTranslated($player, $toggleMode ? 'flype.flight.toggle.on' : 'flype.flight.toggle.off');
            return true;
        }
        return false;
    }
        
    /**
     * isWorldAllowed
     *
     * @param  World $world
     * @return bool
     */
    public function isWorldAllowed(World $world): bool
    {
        // Don't ever change this lol
        $types = ['blacklist', 'whitelist'];
        $flightConfig = $this->plugin->configs['flight'];

        foreach ($types as $type) {
            if ($flightConfig->get('listed-mode') === $type) {
                if (in_array($world->getFolderName(), $flightConfig->get($type . 'ed-worlds'))) {
                    return false;
                }
            }
        }
        return true;
    }
}

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
use AGTHARN\FlyPE\session\SessionManager;
use AGTHARN\FlyPE\event\FlightToggleEvent;

class Flight
{
    /** @var Main */
    private Main $plugin;
    /** @var Config */
    private Config $config;

    /** @var SessionManager */
    private SessionManager $sessionManager;

    /**
     * __construct
     *
     * @param  Main $plugin
     * @return void
     */
    public function __construct(Main $plugin)
    {
        $this->plugin = $plugin;
        $this->config = $plugin->config;

        $this->sessionManager = $plugin->sessionManager;
    }

    /**
     * Toggles flight for a player and adjusts based on parameters given. Returns successful or not.
     *
     * @param Player $player
     * @param bool|null $toggleMode
     * @param int|null $flightTime
     * @param bool $ignoreWorld
     * @return bool
     */
    public function toggleFlight(Player $player, ?bool $toggleMode = null, ?int $flightTime = null, bool $ignoreWorld = false): bool
    {
        $gamemodeAllowed = $this->isGamemodeAllowed($player);
        $worldAllowed = true;
        if (!$ignoreWorld) {
            $worldAllowed = $this->isWorldAllowed($player->getWorld()) ?: $toggleMode = false;
        }
        $toggleMode = $toggleMode ?? !$this->isFlightToggled($player);
        $event = new FlightToggleEvent($player, $toggleMode, $worldAllowed, $gamemodeAllowed);

        $event->call();
        if (!$event->isCancelled()) {
            $playerSession = $this->sessionManager->getSessionByPlayer($player);
            if ($gamemodeAllowed) {
                if ($this->config->getConfig('flight', 'enable-flight-cost')) {
                    if (!$playerSession->reduceMoney($this->config->getConfig('flight', 'flight-cost-amount'))) {
                        return false;
                    }
                }
                if ($flightTime !== null) {
                    $playerSession->getProvider()->setData('flightTime', $flightTime);
                }
                $player->setAllowFlight($toggleMode);
                $player->setFlying($toggleMode);
            
                $this->sessionManager->getSessionByPlayer($player)->getProvider()->setData('flightState', $toggleMode);
                $playerSession->sendTranslated($toggleMode ? 'flype.flight.toggle.on' : 'flype.flight.toggle.off');
                return true;
            }
            $playerSession->sendTranslated('flype.gamemode.allowed.false');
        }
        return false;
    }

    /**
     * Returns whether flight is toggled for a player, including flight state check.
     *
     * @param Player $player
     * @param bool $strictCheck
     * @return bool
     */
    public function isFlightToggled(Player $player, bool $strictCheck = false): bool
    {
        if ($player->getAllowFlight()) {
            if ($strictCheck && !$this->sessionManager->getSessionByPlayer($player)->getProvider()->extractData('flightState')) {
                return false;
            }
            return true;
        }
        return false;
    }

    /**
     * Returns whether gamemode is allowed.
     *
     * @param Player $player
     * @return bool
     */
    public function isGamemodeAllowed(Player $player): bool
    {
        if (!in_array($player->getGamemode()->getEnglishName(), $this->config->getConfig('flight', 'gamemode-exclusions'))) {
            return true;
        }
        return false;
    }
        
    /**
     * Returns whether the world given is allowed.
     *
     * @param  World $world
     * @return bool
     */
    public function isWorldAllowed(World $world): bool
    {
        // Don't ever change this lol
        $types = ['blacklist', 'whitelist'];
        foreach ($types as $type) {
            if ($this->config->getConfig('world', 'listed-mode') === $type) {
                if (in_array($world->getFolderName(), $this->config->getConfig('world', $type . 'ed-worlds'))) {
                    return false;
                }
            }
        }
        return true;
    }
}

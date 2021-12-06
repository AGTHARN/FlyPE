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

namespace AGTHARN\FlyPE;

use AGTHARN\FlyPE\Main;
use pocketmine\player\Player;
use AGTHARN\FlyPE\util\Flight;
use pocketmine\event\Listener;
use AGTHARN\FlyPE\session\SessionManager;
use AGTHARN\FlyPE\util\MessageTranslator;
use AGTHARN\FlyPE\event\FlightToggleEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\entity\EntityTeleportEvent;

class EventListener implements Listener
{
    /** @var Main */
    private Main $plugin;

    /** @var SessionManager */
    private SessionManager $sessionManager;
    /** @var Flight */
    private Flight $flight;
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
        $this->flight = $plugin->flight;
        $this->messageTranslator = $plugin->messageTranslator;
    }
    
    /**
     * onPlayerJoin
     *
     * @param  PlayerJoinEvent $event
     * @return void
     */
    public function onPlayerJoin(PlayerJoinEvent $event): void
    {
        $player = $event->getPlayer();
        $this->sessionManager->registerSession($player);
        $this->plugin->dataBase->waitAll();

        $playerSession = $this->sessionManager->getSessionByPlayer($player);
        if ($this->plugin->configs['general']->get('save-flight-state') && $playerSession->getFlightState()) {
            $this->flight->toggleFlight($player, true);
        }
    }
    
    /**
     * onFlightToggle
     *
     * @param  FlightToggleEvent $event
     * @return void
     */
    public function onFlightToggle(FlightToggleEvent $event): void
    {
        $player = $event->getPlayer();
        $worldAllowed = $event->isWorldAllowed();

        // Will not implement in toggleFlight()
        $worldAllowedString = $worldAllowed ? 'true' : 'false';
        if ($this->plugin->configs['flight']->get('world-allowed-' . $worldAllowedString)) {
            $this->messageTranslator->sendTranslated($player, 'flype.world.allowed.' . $worldAllowedString);
        }
    }

    /**
     * onTeleport
     *
     * @param  EntityTeleportEvent $event
     * @return void
     */
    public function onTeleport(EntityTeleportEvent $event): void
    {
        $entity = $event->getEntity();
        if ($entity instanceof Player) {
            $fromWorldName = $event->getFrom()->getWorld()->getFolderName();
            $toWorldName = $event->getTo()->getWorld()->getFolderName();
            if ($entity->hasPermission('flype.world.bypass')) {
                if ($fromWorldName !== $toWorldName) {
                    // Will already run world checks here and make necessary changes
                    $this->flight->toggleFlight($entity);
                }
            }
        }
    }
}

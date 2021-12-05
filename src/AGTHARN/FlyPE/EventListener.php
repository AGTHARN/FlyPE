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

namespace AGTHARN\FlyPE;

use AGTHARN\FlyPE\Main;
use pocketmine\player\Player;
use AGTHARN\FlyPE\util\Flight;
use pocketmine\event\Listener;
use AGTHARN\FlyPE\util\MessageTranslator;
use pocketmine\event\entity\EntityTeleportEvent;

class EventListener implements Listener
{
    /** @var Main */
    private Main $plugin;
    /** @var Flight */
    private Flight $flight;
    /** @var MessageTranslator */
    private MessageTranslator $messageTranslator;

    /**
     * __construct
     *
     * @param  Main $plugin
     * @param  Flight $flight
     * @param  MessageTranslator $messageTranslator
     * @return void
     */
    public function __construct(Main $plugin, Flight $flight, MessageTranslator $messageTranslator)
    {
        $this->plugin = $plugin;
        $this->flight = $flight;
        $this->messageTranslator = $messageTranslator;
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
                    $disallowed = false;
                    switch ($this->plugin->flightConfig->get('listed-mode')) {
                        case 'blacklist':
                            if (in_array($toWorldName, $this->plugin->flightConfig->get('blacklisted-worlds'))) {
                                $disallowed = true;
                            }
                            break;
                        case 'whitelist':
                            if (in_array($toWorldName, $this->plugin->flightConfig->get('whitelisted-worlds'))) {
                                $disallowed = true;
                            }
                            break;
                        default:
                            return;
                    }
    
                    if ($disallowed) {
                        $this->flight->toggleFlight($entity, false);
                        if ($this->plugin->flightConfig->get('level-change-restricted')) {
                            $this->messageTranslator->sendTranslated($entity, 'world.flight.disallowed');
                        }
                        return;
                    }
                    if ($this->plugin->flightConfig->get('level-change-unrestricted')) {
                        $this->messageTranslator->sendTranslated($entity, 'world.flight.allowed');
                    }
                }
            }
        }
    }
}
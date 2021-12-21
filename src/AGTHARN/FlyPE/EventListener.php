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
use pocketmine\entity\Skin;
use pocketmine\player\Player;
use AGTHARN\FlyPE\util\Config;
use AGTHARN\FlyPE\util\Flight;
use pocketmine\event\Listener;
use pocketmine\world\sound\Sound;
use AGTHARN\FlyPE\session\SessionManager;
use AGTHARN\FlyPE\event\FlightToggleEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\event\entity\EntityTeleportEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;

class EventListener implements Listener
{
    /** @var Main */
    private Main $plugin;
    /** @var Config */
    private Config $config;

    /** @var SessionManager */
    private SessionManager $sessionManager;
    /** @var Flight */
    private Flight $flight;

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
        $this->flight = $plugin->flight;
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
        if (isset($this->plugin->dataBase->libasynql)) {
            $this->plugin->dataBase->libasynql->waitAll();
        }

        if ($this->config->getConfig('flight', 'save-flight-state') && (bool) $this->sessionManager->getSessionByPlayer($player)->getProvider()->extractData('flightState')) {
            $this->flight->toggleFlight($player, true);
        }
    }

    /**
     * onPlayerQuit
     *
     * @param PlayerQuitEvent $event
     * @return void
     */
    public function onPlayerQuit(PlayerQuitEvent $event): void
    {
        $player = $event->getPlayer();

        $this->plugin->particleManager->removeParticleSession($player);
        $this->plugin->effectManager->removeEffectSession($player);
        $this->plugin->capeManager->removePlayerCape($player);
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
        $playerSession = $this->plugin->sessionManager->getSessionByPlayer($player);
        $worldAllowed = $event->isWorldAllowed();

        // Will not implement in toggleFlight()
        $worldAllowedString = $worldAllowed ? 'true' : 'false';
        if ($this->config->getConfig('world', 'world-allowed-' . $worldAllowedString)) {
            $playerSession->sendTranslated('flype.world.allowed.' . $worldAllowedString);
        }
        if ($this->config->getConfig('cosmetic', 'enable-sound')) {
            $sound = $this->plugin->soundManager->getSoundFromString($playerSession->getProvider()->extractData('flightSound')) ?? null;
            if ($sound instanceof Sound && $player->hasPermission('flype.allow.sound')) {
                $player->getNetworkSession()->sendDataPacket($sound->encode($player->getPosition())[0]);
            }
        }
        if ($this->config->getConfig('cosmetic', 'enable-cape')) {
            $this->plugin->capeManager->setPlayerCape($player);
        }

        if ($event->isFlying()) {
            $this->plugin->particleManager->addParticleSession($player, $playerSession->getProvider()->extractData('flightParticle'));
            $this->plugin->effectManager->addEffectSession($player, $playerSession->getProvider()->extractData('flightEffect'));
            return;
        }
        $this->plugin->particleManager->removeParticleSession($player);
        $this->plugin->effectManager->removeEffectSession($player);
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

    /**
     * onEntityDamage
     *
     * @param EntityDamageByEntityEvent $event
     * @return void
     */
    public function onEntityDamage(EntityDamageByEntityEvent $event): void
    {
        $entity = $event->getEntity();
        $damager = $event->getDamager();
        if ($entity instanceof Player && $damager instanceof Player) {
            if ($this->config->getConfig('flight', 'combat-disable-fly')) {
                if ($this->flight->isFlightToggled($damager, true)) {
                    $this->flight->toggleFlight($damager, false, null, true);
                }
            }
        }
    }
}

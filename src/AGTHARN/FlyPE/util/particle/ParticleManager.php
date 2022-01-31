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

namespace AGTHARN\FlyPE\util\particle;

use AGTHARN\FlyPE\Main;
use pocketmine\player\Player;
use pocketmine\world\particle\Particle;
use AGTHARN\FlyPE\util\particle\ParticleList;

class ParticleManager
{
    /** @var Main */
    public Main $plugin;

    /** @var ParticleList */
    public ParticleList $particleList;

    /** @var array */
    private array $particleSessions = [];

    /**
     * __construct
     *
     * @param Main $plugin
     * @return void
     */
    public function __construct(Main $plugin)
    {
        $this->plugin = $plugin;

        $this->particleList = $plugin->particleList;
    }

    /**
     * Adds a new player particle session which would be handled in a task. Returns successful or not.
     *
     * @param Player $player
     * @param string $particleReal
     * @return bool
     */
    public function addParticleSession(Player $player, string $particleReal): bool
    {
        $particle = $this->getParticleFromString($particleReal) ?? null;
        if ($particle instanceof Particle && $player->hasPermission('flype.allow.particle')) {
            $this->particleSessions[$player->getName()] = [
                $player,
                $particle
            ];
            return true;
        }
        return false;
    }

    /**
     * Removes player particle session if it exists. Returns successful or not.
     *
     * @param Player $player
     * @return bool
     */
    public function removeParticleSession(Player $player): bool
    {
        if (isset($this->particleSessions[$player->getName()])) {
            unset($this->particleSessions[$player->getName()]);
            return true;
        }
        return false;
    }

    /**
     * Sets flight particle for a player.
     *
     * @param Player $player
     * @param string $particleName
     * @return void
     */
    public function setFlightParticle(Player $player, string $particleName): void
    {
        $this->plugin->sessionManager->getSessionByPlayer($player)->getProvider()->setData('flightParticle', $particleName);
        if ($this->plugin->flight->isFlightToggled($player)) {
            $this->addParticleSession($player, $particleName);
        }
    }

    /**
     * Removes flight particle for a player.
     *
     * @param Player $player
     * @return void
     */
    public function removeFlightParticle(Player $player): void
    {
        $this->plugin->sessionManager->getSessionByPlayer($player)->getProvider()->setData('flightParticle', '');
        $this->removeParticleSession($player);
    }

    /**
     * Returns particle based on the particle name given. Returns null if doesn't exist.
     *
     * @param string $particleName
     * @return Particle|null
     */
    public function getParticleFromString(string $particleName): ?Particle
    {
        $particleName = str_replace(' ', '', strtolower($particleName));
        foreach ($this->particleList->allParticles as $particleReal) {
            if (strpos($particleName, strtolower($particleReal)) !== false) {
                $particleReal = "pocketmine\\world\\particle\\" . $particleReal;
                return new $particleReal();
            }
        }
        return null;
    }

    /**
     * Returns all player particle sessions.
     *
     * @return array
     */
    public function getParticleSessions(): array
    {
        return $this->particleSessions;
    }
}

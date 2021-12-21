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

namespace AGTHARN\FlyPE\util\effect;

use AGTHARN\FlyPE\Main;
use pocketmine\player\Player;
use pocketmine\entity\effect\Effect;
use AGTHARN\FlyPE\util\effect\EffectList;
use pocketmine\entity\effect\VanillaEffects;

class EffectManager
{
    /** @var Main */
    public Main $plugin;

    /** @var EffectList */
    public EffectList $effectList;

    /** @var array */
    private array $effectSessions = [];

    /**
     * __construct
     *
     * @param Main $plugin
     * @return void
     */
    public function __construct(Main $plugin)
    {
        $this->plugin = $plugin;

        $this->effectList = $plugin->effectList;
    }

    /**
     * Adds a new player effect session which would be handled in a task. Returns successful or not.
     *
     * @param Player $player
     * @param string $effectReal
     * @return bool
     */
    public function addEffectSession(Player $player, string $effectReal): bool
    {
        $particle = $this->getEffectFromString($effectReal) ?? null;
        if ($particle instanceof Effect && $player->hasPermission('flype.allow.effect')) {
            $this->effectSessions[$player->getName()] = [
                $player,
                $particle
            ];
            return true;
        }
        return false;
    }

    /**
     * Removes player effect session if it exists. Returns successful or not.
     *
     * @param Player $player
     * @return bool
     */
    public function removeEffectSession(Player $player): bool
    {
        if (isset($this->effectSessions[$player->getName()])) {
            unset($this->effectSessions[$player->getName()]);
            return true;
        }
        return false;
    }

    /**
     * Sets flight effect for a player.
     *
     * @param Player $player
     * @param string $effectName
     * @return void
     */
    public function setFlightEffect(Player $player, string $effectName): void
    {
        $this->plugin->sessionManager->getSessionByPlayer($player)->getProvider()->setData('flightEffect', $effectName);
        if ($this->plugin->flight->isFlightToggled($player)) {
            $this->addEffectSession($player, $effectName);
        }
    }

    /**
     * Removes flight effect for a player.
     *
     * @param Player $player
     * @return void
     */
    public function removeFlightEffect(Player $player): void
    {
        $this->plugin->sessionManager->getSessionByPlayer($player)->getProvider()->setData('flightEffect', '');
        $this->removeEffectSession($player);
    }

    /**
     * Returns effect based on the effect name given. Returns null if doesn't exist.
     *
     * @param string $effectName
     * @return Effect|null
     */
    public function getEffectFromString(string $effectName): ?Effect
    {
        $effectName = str_replace(' ', '', strtolower($effectName));
        foreach ($this->effectList->allEffects as $effectReal) {
            if (strpos($effectName, strtolower($effectReal)) !== false) {
                $effectReal = strtoupper(str_replace(' ', '_', $effectReal));
                return VanillaEffects::$effectReal();
            }
        }
        return null;
    }

    /**
     * Returns all player effect sessions.
     *
     * @return array
     */
    public function getEffectSessions(): array
    {
        return $this->effectSessions;
    }
}

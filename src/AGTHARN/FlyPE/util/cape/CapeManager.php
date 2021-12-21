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

namespace AGTHARN\FlyPE\util\cape;

use AGTHARN\FlyPE\Main;
use pocketmine\entity\Skin;
use pocketmine\player\Player;
use AGTHARN\FlyPE\util\cape\CapeList;

class CapeManager
{
    /** @var Main */
    public Main $plugin;

    /** @var CapeList */
    public CapeList $capeList;

    /** @var Skin[] */
    public array $oldSkins;

    /**
     * __construct
     *
     * @param Main $plugin
     * @return void
     */
    public function __construct(Main $plugin)
    {
        $this->plugin = $plugin;

        $this->capeList = $plugin->capeList;
    }

    /**
     * setPlayerCape
     *
     * @param Player $player
     * @return bool
     */
    public function setPlayerCape(Player $player): bool
    {
        $playerSession = $this->plugin->sessionManager->getSessionByPlayer($player);
        $cape = $this->getCapeFromString($player->getSkin(), $playerSession->getProvider()->extractData('flightCape')) ?? null;
        if ($cape instanceof Skin && $player->hasPermission('flype.allow.cape')) {
            $this->oldSkins[$player->getUniqueId()->toString()] = $player->getSkin();
            $player->setSkin($cape);
            return true;
        }
        return false;
    }

    /**
     * removePlayerCape
     *
     * @param Player $player
     * @return bool
     */
    public function removePlayerCape(Player $player): bool
    {
        if (isset($this->oldSkins[$player->getUniqueId()->toString()])) {
            $player->setSkin($this->oldSkins[$player->getUniqueId()->toString()]);
            unset($this->oldSkins[$player->getUniqueId()->toString()]);
            return true;
        }
        return false;
    }

    /**
     * setFlightCape
     *
     * @param Player $player
     * @param string $capeName
     * @return void
     */
    public function setFlightCape(Player $player, string $capeName): void
    {
        $this->plugin->sessionManager->getSessionByPlayer($player)->getProvider()->setData('flightCape', $capeName);
        if ($this->plugin->flight->isFlightToggled($player)) {
            $this->setPlayerCape($player);
        }
    }

    /**
     * removeFlightCape
     *
     * @param Player $player
     * @return void
     */
    public function removeFlightCape(Player $player): void
    {
        $this->plugin->sessionManager->getSessionByPlayer($player)->getProvider()->setData('flightCape', '');
        $this->removePlayerCape($player);
    }

    /**
     * getCapeFromString
     *
     * @param Skin $oldSkin
     * @param string $capeName
     * @return Skin|null
     */
    public function getCapeFromString(Skin $oldSkin, string $capeName): ?Skin
    {
        $capeName = str_replace(' ', '', strtolower($capeName));
        foreach ($this->capeList->allCapes as $capeReal => $capeData) {
            if (strpos($capeName, strtolower($capeReal)) !== false) {
                return new Skin($oldSkin->getSkinId(), $oldSkin->getSkinData(), $capeData, $oldSkin->getGeometryName(), $oldSkin->getGeometryData());
            }
        }
        return null;
    }
}
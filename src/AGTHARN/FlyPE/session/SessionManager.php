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

namespace AGTHARN\FlyPE\session;

use AGTHARN\FlyPE\Main;
use pocketmine\player\Player;
use AGTHARN\FlyPE\session\PlayerSession;

class SessionManager
{
    /** @var Main */
    private Main $plugin;

    /** @var PlayerSession[] */
    private array $playerSessions = [];

    /**
     * __construct
     *
     * @param  Main $plugin
     * @return void
     */
    public function __construct(Main $plugin)
    {
        $this->plugin = $plugin;
    }

    /**
     * registerSession
     *
     * @param  Player $player
     * @return void
     */
    public function registerSession(Player $player): void
    {
        $this->plugin->dataBase->executeInsert('flype.create', [
            'uuid' => $player->getUniqueId()->toString(),
            'username' => $player->getName()
        ]);
        $this->plugin->dataBase->waitAll();
        $this->plugin->dataBase->executeSelect('flype.load', ['uuid' => $player->getUniqueId()->toString()], function (array $rows): void {
            foreach ($rows as $row) {
                $this->playerSessions[$row['uuid']] = new PlayerSession($this->plugin, $row['uuid'], $row['username'], (bool) $row['flightState']);
            }
		});
    }

    /**
     * getSessionByPlayer
     *
     * @param  Player $player
     * @return PlayerSession
     */
    public function getSessionByPlayer(Player $player): PlayerSession
    {
        return $this->getSessionByUUID($player->getUniqueId()->toString());
    }

    /**
     * getSessionByUUID
     *
     * @param  string $name
     * @return PlayerSession
     */
    public function getSessionByUUID(string $uuid): PlayerSession
    {
        return $this->playerSessions[$uuid];
    }
}

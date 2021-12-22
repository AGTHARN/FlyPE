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

use skymin\config\Data;
use pocketmine\player\Player;
use AGTHARN\FlyPE\util\Database;
use AGTHARN\FlyPE\provider\SQProvider;
use AGTHARN\FlyPE\provider\DataProvider;
use AGTHARN\FlyPE\session\PlayerSession;
use AGTHARN\FlyPE\util\trait\BasicTrait;

class SessionManager
{
    use BasicTrait;

    /** @var PlayerSession[] */
    private array $playerSessions = [];

    /**
     * Registers a new session for a player.
     *
     * @param Player $player
     * @return void
     */
    public function registerSession(Player $player): void
    {
        $type = $this->plugin->dataBase->getType();
        switch ($type) {
            case Database::SQLITE:
            case Database::MYSQL:
                $this->plugin->dataBase->libasynql->executeInsert('flype.create', [
                    'uuid' => $player->getUniqueId()->toString(),
                    'username' => $player->getName()
                ]);
                $this->plugin->dataBase->libasynql->waitAll();
                $this->plugin->dataBase->libasynql->executeSelect('flype.load', ['uuid' => $player->getUniqueId()->toString()], function (array $rows) use ($player): void {
                    foreach ($rows as $row) {
                        $this->playerSessions[$row['uuid']] = new PlayerSession($this->plugin, $player, new SQProvider($this->plugin, $row));
                    }
                });
                break;
            case Database::YAML:
            case Database::JSON:
                $fileLocation = $this->plugin->getDataFolder() . $type . '_data' . DIRECTORY_SEPARATOR . $player->getName() . '.' . str_replace('a', '', $type);
                $data = Data::call($fileLocation, $type === Database::YAML ? Data::YAML : Data::JSON, $this->plugin->dataBase->getDefaults($player));
                
                $this->playerSessions[$player->getUniqueId()->toString()] = new PlayerSession($this->plugin, $player, new DataProvider($this->plugin, $data));
                break;
        }
    }

    /**
     * Returns all player sessions.
     *
     * @return PlayerSession[]
     */
    public function getSessions(): array
    {
        return $this->playerSessions;
    }

    /**
     * Returns a player's session by player.
     *
     * @param  Player $player
     * @return PlayerSession
     */
    public function getSessionByPlayer(Player $player): PlayerSession
    {
        return $this->getSessionByUUID($player->getUniqueId()->toString());
    }

    /**
     * Returns a player's session by name.
     *
     * @param  string $playerName
     * @return PlayerSession
     */
    public function getSessionByName(string $playerName): PlayerSession
    {
        return $this->getSessionByUUID($this->plugin->getServer()->getPlayerByPrefix($playerName)->getUniqueId()->toString());
    }

    /**
     * Returns a player's session by UUID.
     *
     * @param  string $uuid
     * @return PlayerSession
     */
    public function getSessionByUUID(string $uuid): PlayerSession
    {
        return $this->playerSessions[$uuid];
    }
}

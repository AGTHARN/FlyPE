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

use pocketmine\player\Player;
use poggit\libasynql\libasynql;
use poggit\libasynql\DataConnector;
use AGTHARN\FlyPE\util\trait\BasicTrait;

class Database
{
    use BasicTrait;
    
    public const SQLITE = 'sqlite';
    public const MYSQL = 'mysql';
    public const YAML = 'yaml';
    public const JSON = 'json';

    /** @var DataConnector */
    public DataConnector $libasynql;

    /**
     * Initialize databases based on type.
     *
     * @return void
     */
    public function initDB(): void
    {
        switch ($this->getType()) {
            case self::SQLITE:
            case self::MYSQL:
                $libasynql = libasynql::create($this->plugin, $this->config->getConfig('general', 'database'), [
                    'sqlite' => 'database_stmts' . DIRECTORY_SEPARATOR . 'sqlite.sql',
                    'mysql' => 'database_stmts' . DIRECTORY_SEPARATOR . 'mysql.sql'
                ]);
                $libasynql->executeGeneric('flype.init');
                $libasynql->waitAll();
                $this->libasynql = $libasynql;
                break;
        }
    }

    /**
     * Disable databases based on type.
     *
     * @return void
     */
    public function runDisable(): void
    {
        switch ($this->getType()) {
            case self::SQLITE:
            case self::MYSQL:
                $this->libasynql->waitAll();
                $this->libasynql->close();
                break;
        }
    }

    /**
     * Returns the default keys and values of database.
     *
     * @param Player $player
     * @return array
     */
    public function getDefaults(Player $player): array
    {
        return [
            'uuid' => $player->getUniqueId()->toString(),
            'username' => $player->getName(),
            'flightState' => false,
            'flightSound' => 'XpCollectSound',
            'flightParticle' => 'FlameParticle',
            'flightEffect' => '',
            'flightCape' => '',
            'flightTime' => 0
        ];
    }

    /**
     * Returns the type of database.
     *
     * @return string
     */
    public function getType(): string
    {
        return $this->config->getConfig('general', 'database.type');
    }
}

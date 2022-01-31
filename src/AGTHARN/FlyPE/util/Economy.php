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

use pocketmine\plugin\Plugin;
use AGTHARN\FlyPE\util\trait\BasicTrait;

class Economy
{
    use BasicTrait;

    /**
     * Returns an array of present economy plugins. Returns null if none found.
     *
     * @return array|null
     */
    public function getEconomyPlugin(): ?array
    {
        $pluginManager = $this->plugin->getServer()->getPluginManager();
        $economyPlugins = [
            'EconomyAPI' => $pluginManager->getPlugin('EconomyAPI'),
            'BedrockEconomy' => $pluginManager->getPlugin('BedrockEconomy')
        ];
        
        foreach ($economyPlugins as $pluginName => $pluginInstance) {
            if ($pluginInstance instanceof Plugin) {
                return [$pluginName, $pluginInstance];
            }
        }
        return null;
    }
}

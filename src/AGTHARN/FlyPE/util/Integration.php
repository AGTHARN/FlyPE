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
use pocketmine\plugin\Plugin;
use AGTHARN\FlyPE\util\trait\BasicTrait;

class Integration
{
    use BasicTrait;

    /**
     * Returns whether another flight plugin is detected and will make a warning.
     *
     * @return bool
     */
    public function checkFlightPlugins(): bool
    {
        $flightPlugins = $this->getFlightPlugins();
        if (count($flightPlugins) > 0) {
            $this->plugin->getLogger()->alert('Another flight plugin has been detected! Please remove it to prevent conflicts! (' . implode(', ', $flightPlugins) . ')');
            return true;
        }
        return false;
    }

    /**
     * Returns the names of the flight plugins detected.
     *
     * @return array
     */
    public function getFlightPlugins(): array
    {
        $pluginIntegrations = [
            'BlazinFly' => $this->plugin->getServer()->getPluginManager()->getPlugin('BlazinFly'),
            'BetterFlight' => $this->plugin->getServer()->getPluginManager()->getPlugin('BetterFlight')
        ];

        $pluginsDetected = [];
        foreach ($pluginIntegrations as $pluginName => $pluginInstance) {
            if ($pluginInstance instanceof Plugin && $this->config->getConfig('integration', strtolower($pluginName) . '-integration')) {
                $pluginsDetected[] = $pluginName;
            }
        }
        return $pluginsDetected;
    }

    /**
     * Returns whether plugin integration is required.
     *
     * @param Player $player
     * @return bool
     */
    public function isAdjustmentRequired(Player $player): bool
    {
        $pluginIntegrations = $this->getIntegrationPlugins();
        $adjustment = false;
        foreach ($pluginIntegrations as $pluginData) {
            $pluginInstance = $pluginData[1];
            switch ($pluginData[0]) {
                case 'VanishV2':
                    $adjustment = $adjustment ?: in_array($player->getName(), $pluginInstance::$vanish);
                    break;
                case 'SimpleLay':
                    $adjustment = $adjustment ?: $pluginInstance->isLaying($player) || $pluginInstance->isSitting($player);
                    break;
            }
        }
        return $adjustment;
    }

    /**
     * Returns the [name, instance] of integration plugins detected.
     *
     * @return Plugin[]
     */
    public function getIntegrationPlugins(): array
    {
        $pluginIntegrations = [
            'VanishV2' => $this->plugin->getServer()->getPluginManager()->getPlugin('VanishV2'),
            'SimpleLay' => $this->plugin->getServer()->getPluginManager()->getPlugin('SimpleLay')
        ];

        $plugins = [];
        foreach ($pluginIntegrations as $pluginName => $pluginInstance) {
            if ($pluginInstance instanceof Plugin && $this->config->getConfig('integration', strtolower($pluginName) . '-integration')) {
                $plugins[] = [$pluginName, $pluginInstance];
            }
        }
        return $plugins;
    }
}

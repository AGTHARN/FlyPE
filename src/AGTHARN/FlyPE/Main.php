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

use pocketmine\utils\Config;
use AGTHARN\FlyPE\util\Flight;
use pocketmine\plugin\PluginBase;
use AGTHARN\FlyPE\util\Translator;
use AGTHARN\FlyPE\command\FlyCommand;
use pocketmine\utils\TextFormat as C;

class Main extends PluginBase
{
    /** @var Config */
    private Config $generalConfig;

    /** @var Translator */
    private Translator $translator;
    /** @var Flight */
    private Flight $flight;

    /** @var string */
    public const PREFIX = C::GRAY . "[" . C::GOLD . "FlyPE". C::GRAY . "] " . C::RESET;
    /** @var int */
    public const CONFIG_VERSION = 5;

    
    /**
     * onEnable
     *
     * @return void
     */
    public function onEnable(): void
    {
        $this->saveResource('config' . DIRECTORY_SEPARATOR . 'general.yml');
        $this->generalConfig = new Config($this->getDataFolder() . 'config' . DIRECTORY_SEPARATOR . 'general.yml', Config::YAML);
        
        $this->translator = new Translator($this);
        $this->flight = new Flight($this);

        $this->checkConfig();

        $this->translator->saveDefaultLanguages();

        $this->getServer()->getPluginManager()->registerEvents(new EventListener($this, $this->flight), $this);
        $this->getServer()->getCommandMap()->register('flype', new FlyCommand($this, $this->flight, $this->translator, 'fly', 'Toggles your flight!'));
    }
    
    /**
     * checkConfig
     *
     * @return void
     */
    private function checkConfig(): bool
    {
        if ($this->generalConfig->get('config-version') < self::CONFIG_VERSION) {
            $this->getLogger()->warning('Your config version is outdated. Running an automatic update!');
            $oldConfig = $this->generalConfig->getAll();

            unlink($this->generalConfig->getPath());
            $this->saveResource('config' . DIRECTORY_SEPARATOR . 'general.yml');
            
            $this->generalConfig->reload();
            foreach ($oldConfig as $config => $key) {
                if ($this->generalConfig->get($config) !== false) {
                    $this->generalConfig->set($config, $key);
                }
            }
            $this->generalConfig->reload();
            $this->getLogger()->warning('Automatic update completed! No reboot required.');
            return false;
        }
        if ($this->generalConfig->get('config-version') > self::CONFIG_VERSION) {
            $this->getLogger()->warning('Your config version is too new! Please use an older config!');
            $this->getServer()->getPluginManager()->disablePlugin($this);
            return false;
        }
        return true;
    }
}

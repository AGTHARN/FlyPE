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

use pocketmine\plugin\PluginBase;
use pocketmine\utils\TextFormat as C;

use AGTHARN\FlyPE\commands\FlyCommand;
use AGTHARN\FlyPE\util\Util;

use JackMD\ConfigUpdater\ConfigUpdater;

class Main extends PluginBase {
    
    /**
     * util
     * 
     * @var Util
     */
    private $util;

    public const PREFIX = C::GRAY . "[" . C::GOLD . "FlyPE". C::GRAY . "] " . C::RESET;
    
    public const CONFIG_VERSION = 4.0;
    
    /**
     * onEnable
     *
     * @return void
     */
    public function onEnable(): void {
        $this->util = new Util($this);

        $this->getServer()->getPluginManager()->registerEvents(new EventListener($this, $this->util), $this);

        $this->util->addDataDir();
        $this->util->checkConfiguration();
        $this->util->checkUpdates();
        $this->util->enableCoupon();
        $this->util->registerPacketHooker();
        
        if (!$this->util->checkDepend() || !$this->util->checkIncompatible() || !$this->util->checkFiles()) return;
        ConfigUpdater::checkUpdate($this, $this->getConfig(), 'config-version', (int)self::CONFIG_VERSION);

        $this->getServer()->getCommandMap()->register('flype', new FlyCommand($this, $this->util, 'fly', 'Toggles your flight!'));
    }
}

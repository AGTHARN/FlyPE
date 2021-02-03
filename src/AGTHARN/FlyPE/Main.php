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

namespace AGTHARN\FlyPE;

use pocketmine\plugin\PluginBase;
use pocketmine\utils\TextFormat as C;

use AGTHARN\FlyPE\commands\FlyCommand;
use AGTHARN\FlyPE\util\Util;

use kim\present\lib\translator\Translator;

class Main extends PluginBase {
    
    /**
     * util
     * 
     * @var Util
     */
    private $util;
    
    public const CONFIG_VERSION = 3.9;
    
    /**
     * onEnable
     *
     * @return void
     */
    public function onEnable(): void {
        $this->util = new Util($this, new Translator($this));

        $this->getServer()->getPluginManager()->registerEvents(new EventListener($this, $this->util), $this);
        $this->getServer()->getCommandMap()->register("FlyPE", new FlyCommand("fly", $this, $this->util));

        $this->util->addDataDir();
        $this->util->checkConfiguration();
        $this->util->checkUpdates();
        $this->util->enableCoupon();
        
        if (!$this->util->checkDepend() || !$this->util->checkIncompatible() || !$this->util->checkFiles()) return;

        if ($this->getConfig()->get("config-version") < self::CONFIG_VERSION) {
            $this->getLogger()->warning("Your config is outdated! Please delete your old config to get the latest features!");
            $this->getServer()->getPluginManager()->disablePlugin($this);
            return;
        }
    }
}

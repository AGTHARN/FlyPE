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

namespace AGTHARN\FlyPE\commands;

use pocketmine\command\CommandSender;
use pocketmine\command\PluginCommand;
use pocketmine\utils\TextFormat as C;
use pocketmine\Player;

use AGTHARN\FlyPE\Main;
use AGTHARN\FlyPE\util\Util;

class FlyCommand extends PluginCommand {

    /**
     * plugin
     * 
     * @var Main
     */
    private $plugin;
    
    /**
     * util
     * 
     * @var Util
     */
    private $util;
    
    /**
     * __construct
     *
     * @param  String $cmd
     * @param  Main $plugin
     * @param  Util $util
     * @return void
     */
    public function __construct(String $cmd, Main $plugin, Util $util) {
        parent::__construct($cmd, $plugin);

        $this->setUsage("/fly [string:player]");
        $this->setPermission("flype.command");
        $this->setDescription("Fly command to toggle flight");
        
        $this->plugin = $plugin;
        $this->util = $util;
    }
    
    /**
     * onCommand
     *
     * @param  CommandSender $sender
     * @param  string $commandLabel
     * @param  array $args
     * @return bool
     */
    public function execute(CommandSender $sender, string $commandLabel, array $args): bool {
        if (!$sender instanceof Player) {
            $sender->sendMessage("You can only use this command in-game!");
            return false;
        }
        if (!$sender->hasPermission("flype.command")) {
            $sender->sendMessage(C::RED . "You do not have the permission to use this command!");
            return false;
        }
        if ($this->plugin->getConfig()->get("enable-fly-ui") === true) {
            $this->util->openFlyUI($sender);
            return true;
        }
        if (empty($args)) {
            if ($this->util->doLevelChecks($sender) === true) {
                if ($this->plugin->getConfig()->get("coupon-command-toggle-item") === true && $sender->getAllowFlight() === false) {
                    $sender->getInventory()->addItem($this->util->getCouponItem());
                    return true;
                }
                $this->util->toggleFlight($sender);
                return true;
            }
        } else {
            $target = $this->plugin->getServer()->getPlayer($args[0]);

            if (!$target instanceof Player) {
                $sender->sendMessage(C::RED . str_replace("{name}", $args[0], $this->plugin->getConfig()->get("player-cant-be-found")));
                return false;
            }
            
            $targetName = $target->getName();

            if (!$sender->hasPermission("flype.command.others")) {
                $sender->sendMessage(C::RED . str_replace("{name}", $targetName, $this->plugin->getConfig()->get("cant-toggle-flight-others")));
                return false;
            }
                
            if ($this->util->doLevelChecks($target) === true) {
                if ($this->plugin->getConfig()->get("coupon-command-toggle-item") === true && $target->getAllowFlight() === false) {
                    $target->getInventory()->addItem($this->util->getCouponItem());
                    return true;
                }

                $this->util->toggleFlight($target);

                if ($target->getAllowFlight() === true) {
                    $sender->sendMessage(C::GREEN . str_replace("{name}", $targetName, $this->plugin->getConfig()->get("flight-for-other-on")));
                } else {
                    $sender->sendMessage(C::RED . str_replace("{name}", $targetName, $this->plugin->getConfig()->get("flight-for-other-off")));
                }
                return true;
            }
        }
        return false;
    }
}
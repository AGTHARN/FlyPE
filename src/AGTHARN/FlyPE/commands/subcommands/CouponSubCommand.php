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

namespace AGTHARN\FlyPE\commands\subcommands;

use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat as C;
use pocketmine\Player;

use AGTHARN\FlyPE\util\Util;
use AGTHARN\FlyPE\Main;

use CortexPE\Commando\args\RawStringArgument;
use CortexPE\Commando\BaseSubCommand;

class CouponSubCommand extends BaseSubCommand {

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

    public function __construct(Main $plugin, Util $util, string $name, string $description, $aliases = []) {
        $this->plugin = $plugin;
        $this->util = $util;
        
        parent::__construct($name, $description, $aliases);
    }

    public function prepare(): void {
        $this->setPermission("flype.command.coupon");
        $this->registerArgument(0, new RawStringArgument("player", true));
	}

    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void {
        if (isset($args["player"])) {
            $arg = $args["player"];

            if (!$this->plugin->getServer()->getPlayer($arg) instanceof Player || empty($arg)) {
                $sender->sendMessage(C::RED . str_replace("{name}", $arg, $this->util->messages->get("player-cant-be-found")));
                return;
            }

            $target = $this->plugin->getServer()->getPlayer($arg);
            $targetName = $target->getName();
                
            if ($this->util->doLevelChecks($target)) {
                if ($this->plugin->getConfig()->get("coupon-command-toggle-item") && $this->plugin->getConfig()->get("enable-coupon") && !$target->getAllowFlight()) {
                    $target->getInventory()->addItem($this->util->getCouponItem());
                }
            }
        } else {
            if (!$sender instanceof Player) {
                $sender->sendMessage("You can only use this command in-game!");
                return;
            }

            if ($this->util->doLevelChecks($sender)) {
                if ($this->plugin->getConfig()->get("coupon-command-toggle-item") && $this->plugin->getConfig()->get("enable-coupon") && !$sender->getAllowFlight()) {
                    $sender->getInventory()->addItem($this->util->getCouponItem());
                }
            }
        }
	}
}
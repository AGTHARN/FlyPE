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
 * Copyright (C) 2020 AGTHARN
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

class FlyCommand extends PluginCommand {

    /**
     * plugin
     * 
     * @var Main
     */
    private $plugin;
	
	/**
	 * __construct
	 *
	 * @param  String $cmd
	 * @param  Main $plugin
	 * @return void
	 */
	public function __construct(String $cmd, Main $plugin) {
        parent::__construct($cmd, $plugin);
		
        $this->setUsage("/fly [string:player]");
        $this->setPermission("flype.command");
        $this->setDescription("Fly command to toggle flight");

		$this->plugin = $plugin;
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
		if ($this->plugin->getConfig()->get("enableflyui") === true) {
			$this->plugin->openFlyUI($sender);
			return false;
		}
		if (empty($args)) {
			if ($this->plugin->doLevelChecks($sender) === true) {
				$this->plugin->toggleFlight($sender);
			}
		}
		if (isset($args[0])) {
			$target = $this->plugin->getServer()->getPlayer($args[0]);
			/** @phpstan-ignore-next-line */
			if ($target->getName() === null || !$target instanceof Player) {
				$sender->sendMessage(C::RED . $this->plugin->getConfig()->get("player-cant-be-found"));
				return false;
			}
			$targetName = $target->getName();

			if (!$sender->hasPermission("flype.command.others")) {
				$sender->sendMessage(C::RED . $this->plugin->getConfig()->get("cant-toggle-flight-others"));
				return false;
			}
				
			if ($this->plugin->doLevelChecks($target) === true) {
				$this->plugin->toggleFlight($target);

				if ($target->getAllowFlight() === true) {
					$sender->sendMessage(C::GREEN . str_replace("{name}", $targetName, $this->plugin->getConfig()->get("flight-for-other-on")));
				} else {
					$sender->sendMessage(C::RED . str_replace("{name}", $targetName, $this->plugin->getConfig()->get("flight-for-other-off")));
				}
			}
		}
		return false;
	}
}
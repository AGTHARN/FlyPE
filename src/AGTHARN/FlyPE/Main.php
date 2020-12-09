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

namespace AGTHARN\FlyPE;

use pocketmine\plugin\PluginBase;
use pocketmine\utils\TextFormat as C;
use pocketmine\Player;

use AGTHARN\FlyPE\commands\FlyCommand;

use jojoe77777\FormAPI\SimpleForm;
use JackMD\UpdateNotifier\UpdateNotifier;
use onebone\economyapi\EconomyAPI;

class Main extends PluginBase {
	
	/**
	 * instance
	 * 
     * @var Main
     */
    public static $instance;
	
	/**
	 * onEnable
	 *
	 * @return void
	 */
	public function onEnable(): void {
		self::$instance = $this;

		$this->getServer()->getPluginManager()->registerEvents(new EventListener($this), $this);
		$this->getServer()->getCommandMap()->register("fly", new FlyCommand("fly", $this));
	    
		if ($this->getConfig()->get("config-version") < "3") {
		    $this->getLogger()->warning("Your config is outdated! Please consider deleting your old config to get the latest features!");
		    //$this->getServer()->getPluginManager()->disablePlugin($this);
	    }
	    UpdateNotifier::checkUpdate($this->getDescription()->getName(), $this->getDescription()->getVersion());
	}
		
	/**
	 * openFlyUI
	 *
	 * @param  Player $player
	 * @return object
	 */
	public function openFlyUI(Player $player) {
		$form = new SimpleForm(function (Player $player, $data) {
            
        if ($data === null) {
            return;
        }
			
		switch ($data) {
            case 0:
			$cost = $this->getConfig()->get("buyflycost");
				
			if ($this->getConfig()->get("payforfly") === true) {
				if (EconomyAPI::getInstance()->myMoney($player) < $cost) {
					$player->sendMessage(C::RED . $this->getConfig()->get("not-enough-money"));
				}
				if ($player->getAllowFlight() === false) {
					$player->sendMessage(C::GREEN . $this->getConfig()->get("buy-fly-successful"));
					if ($this->doLevelChecks($player) === true) {
						$this->toggleFlight($player);
						EconomyAPI::getInstance()->reduceMoney($player, $cost);
					}
				} else {
					if ($this->doLevelChecks($player) === false) {
						$this->toggleFlight($player);
					}
				}
			} else {
				$this->toggleFlight($player);
			}
			break;
			case 1:
			// exit button
			break;
		}
		});
		
		/** @phpstan-ignore-next-line */
		if ($this->getConfig()->get("enableflyui") === true && $this->getConfig()->get("payforfly") === true && $this->getConfig()->get("customuitexts") === false) {
			$cost = $this->getConfig()->get("buyflycost");
					
			$form->setTitle("§l§7< §2FlyUI §7>");
			$form->addButton("§aToggle Fly §e(Costs $ {$cost})");
			$form->addButton("§cExit");
			$form->sendToPlayer($player);
			return $form;
		} elseif ($this->getConfig()->get("enableflyui") === true && $this->getConfig()->get("payforfly") === false && $this->getConfig()->get("customuitexts") === false) {
				$form->setTitle("§l§7< §6FlyUI §7>");
				$form->addButton("§aToggle Fly");
				$form->addButton("§cExit");
				$form->sendToPlayer($player);
				return $form;
		} elseif ($this->getConfig()->get("customuitexts") === true) {
				$cost = $this->getConfig()->get("buyflycost");

				$form->setTitle($this->getConfig()->get("flyuititle"));
				$form->addButton(str_replace("{cost}", $cost, $this->getConfig()->get("flyuitoggle")));
				$form->addButton($this->getConfig()->get("flyuiexit"));
				$form->sendToPlayer($player);
				return $form;
		}
	}
	
	/**
	 * doLevelChecks
	 *
	 * @param  Player $player
	 * @return bool
	 */
	private function doLevelChecks(Player $player): bool {
		if ($player->getGamemode() === Player::CREATIVE && $player->getAllowFlight() === true) {
			$player->sendMessage(C::RED . $this->getConfig()->get("disable-fly-creative"));
			return false;
		}

		if ($this->getConfig()->get("mode") === "blacklist" && !in_array($player->getLevel()->getName(), $this->getConfig()->get("blacklisted-worlds"))) {
			return true;
		}
		if ($this->getConfig()->get("mode") === "whitelist" && in_array($player->getLevel()->getName(), $this->getConfig()->get("whitelisted-worlds"))) {
			return true;
		}
		$player->sendMessage(C::RED . $this->getConfig()->get("flight-not-allowed"));
		return false;
	}
	
	/**
	 * toggleFlight
	 *
	 * @param  Player $player
	 * @return void
	 */
	public function toggleFlight(Player $player): void {
		if ($player->getAllowFlight() === true) {
			$player->setAllowFlight(false);
			$player->setFlying(false);
            $player->sendMessage(C::RED . $this->getConfig()->get("toggled-flight-off"));
		} else {
			$player->setAllowFlight(true);
			$player->setFlying(true);
            $player->sendMessage(C::GREEN . $this->getConfig()->get("toggled-flight-on"));
		}
	}

	/**
	 * getInstance
	 *
	 * @return Main
	 */
	public static function getInstance(): Main {
        return self::$instance;
    }
}

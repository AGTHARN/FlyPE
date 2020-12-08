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

use pocketmine\entity\Entity;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityLevelChangeEvent;
use pocketmine\event\player\PlayerItemConsumeEvent;
use pocketmine\event\inventory\InventoryPickupItemEvent;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerDropItemEvent;
use pocketmine\utils\Config;
use pocketmine\utils\TextFormat as C;
use pocketmine\event\Listener;
use pocketmine\Player;
use pocketmine\item\Item;
use pocketmine\plugin\PluginBase;

use jojoe77777\FormAPI\SimpleForm;
use JackMD\UpdateNotifier\UpdateNotifier;
use onebone\economyapi\EconomyAPI;

class Main extends PluginBase implements Listener {
	
	/**
	 * onEnable
	 *
	 * @return void
	 */
	public function onEnable() {
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
	    
		if ($this->getConfig()->get("config-version") < "2") {
		    $this->getLogger()->warning("Your config is outdated! Please delete your old config to get the latest features!");
		    $this->getServer()->getPluginManager()->disablePlugin($this);
	    }
	    UpdateNotifier::checkUpdate($this->getDescription()->getName(), $this->getDescription()->getVersion());
	}
		
	/**
	 * onCommand
	 *
	 * @param  CommandSender $sender
	 * @param  Command $cmd
	 * @param  mixed $label
	 * @param  array $args
	 * @return bool
	 */
	public function onCommand(CommandSender $sender, Command $cmd, $label, array $args): bool {
	    if ($cmd->getName() === "fly") {
		    if (!$sender instanceof Player) {
			    $sender->sendMessage("You can only use this command in-game!");
			    return false;
		    }
			if ($this->getConfig()->get("enableflyui") === true) {
				$this->openFlyUI($sender);
				return false;
			}
		    if (empty($args[0])) {
			    if ($this->doLevelChecks($sender) === true) {
					$this->toggleFlight($sender);
				}
		    }
		    if (isset($args[0])) {
				$target = $this->getServer()->getPlayer($args[0]);
				/** @phpstan-ignore-next-line */
				if ($target->getName() === null || !$target instanceof Player) {
					$sender->sendMessage(C::RED . $this->getConfig()->get("player-cant-be-found"));
				    return false;
				}
				$targetName = $target->getName();

			    if(!$sender->hasPermission("flype.command.others")){
					$sender->sendMessage(C::RED . $this->getConfig()->get("cant-toggle-flight-others"));
				    return false;
				}
				
				if ($this->doLevelChecks($target) === true) {
					$this->toggleFlight($target);

					if($target->getAllowFlight() === true) {
						$sender->sendMessage(C::GREEN . str_replace("{name}", $targetName, $this->getConfig()->get("flight-for-other-on")));
					} else {
						$sender->sendMessage(C::RED . str_replace("{name}", $targetName, $this->getConfig()->get("flight-for-other-off")));
					}
				}
		    }
		    return false;
	    }
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
					EconomyAPI::getInstance()->reduceMoney($player, $cost);
					$player->sendMessage(C::GREEN . $this->getConfig()->get("buy-fly-successful"));
					if ($this->doLevelChecks($player) === true) {
						$this->toggleFlight($player);
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
			
		if ($this->getConfig()->get("enableflyui") === true && $this->getConfig()->get("payforfly") === true) {
			$cost = $this->getConfig()->get("buyflycost");
					
			$form->setTitle("§l§7< §2FlyUI §7>");
			$form->addButton("§aToggle Fly §e(Costs $ {$cost})");
			$form->addButton("§cExit");
			$form->sendToPlayer($player);
			return $form;
		} else {
			if($this->getConfig()->get("enableflyui") === true && $this->getConfig()->get("payforfly") === false){
				$form->setTitle("§l§7< §6FlyUI §7>");
				$form->addButton("§aToggle Fly");
				$form->addButton("§cExit");
				$form->sendToPlayer($player);
				return $form;
			}
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
	public function toggleFlight(Player $player) {
		if ($player->getAllowFlight() === true) {
			$player->setAllowFlight(false);
			$player->setFlying(false);
            $player->sendMessage(C::GREEN . $this->getConfig()->get("toggled-flight-off"));
		} else {
			$player->setAllowFlight(true);
			$player->setFlying(true);
            $player->sendMessage(C::GREEN . $this->getConfig()->get("toggled-flight-on"));
		}
	}
	
	/**
	 * onPlayerJoin
	 *
	 * @param  PlayerJoinEvent $event
	 * @return void
	 */
	public function onPlayerJoin(PlayerJoinEvent $event): void {
		$player = $event->getPlayer();
		
		/** @phpstan-ignore-next-line */
		if(!$player->getGamemode() === Player::CREATIVE && $this->getConfig()->get("joindisablefly") === true && $player->getAllowFlight() === true) {
			$player->setFlying(false);
			$player->setAllowFlight(false);
			$player->sendMessage(C::RED . $this->getConfig()->get("onjoin-flight-disabled"));
			return;
		}
	}
	
	/**
	 * onLevelChange
	 *
	 * @param  EntityLevelChangeEvent $event
	 * @return void
	 */
	public function onLevelChange(EntityLevelChangeEvent $event): void {
		$entity = $event->getEntity();

		if (!$entity instanceof Player || $entity->hasPermission("flype.command.bypass") || $entity->getGamemode() === Player::CREATIVE) {
			return;
		}
		if ($this->doLevelChecks($entity) === false && $entity->getAllowFlight() === true) {
			$this->toggleFlight($entity);
		}
	}
		
	/**
	 * onInventoryPickupItem
	 *
	 * @param  InventoryPickupItemEvent $event
	 * @return void
	 */
	public function onInventoryPickupItem(InventoryPickupItemEvent $event): void {
		$inventory = $event->getInventory();
		$player = $event->getInventory()->getHolder();

		if (!$player instanceof Player || $player->getGamemode() === Player::CREATIVE) return;
		if ($this->getConfig()->get("picking-up-items") === false && $player->getAllowFlight() === true) {
			$event->setCancelled();
		}
	}
		
	/**
	 * onPlayerDropItem
	 *
	 * @param  PlayerDropItemEvent $event
	 * @return void
	 */
	public function onPlayerDropItem(PlayerDropItemEvent $event): void {
		$player = $event->getPlayer();
		
		if (!$player instanceof Player || $player->getGamemode() === Player::CREATIVE) return;
		if ($this->getConfig()->get("item-dropping") === false && $player->getAllowFlight() === true) {
			$event->setCancelled();
		 }
	}
		
	/**
	 * onBlockBreak
	 *
	 * @param  BlockBreakEvent $event
	 * @return void
	 */
	public function onBlockBreak(BlockBreakEvent $event): void {
		$player = $event->getPlayer();
		
		if (!$player instanceof Player || $player->getGamemode() === Player::CREATIVE) return;
		if ($this->getConfig()->get("block-breaking") === false && $player->getAllowFlight() === true) {
			$event->setCancelled();
		}
	}
		
	/**
	 * onBlockPlace
	 *
	 * @param  BlockPlaceEvent $event
	 * @return void
	 */
	public function onBlockPlace(BlockPlaceEvent $event): void {
		$player = $event->getPlayer();
		
		if (!$player instanceof Player || $player->getGamemode() === Player::CREATIVE) return;
		if ($this->getConfig()->get("block-placing") === false && $player->getAllowFlight() === true) {
			$event->setCancelled();
		}
	}
		
	/**
	 * onPlayerItemConsume
	 *
	 * @param  PlayerItemConsumeEvent $event
	 * @return void
	 */
	public function onPlayerItemConsume(PlayerItemConsumeEvent $event): void {
		$player = $event->getPlayer();
		
		if (!$player instanceof Player || $player->getGamemode() === Player::CREATIVE) return;
		if ($this->getConfig()->get("player-eating") === false && $player->getAllowFlight() === true) {
			$event->setCancelled();
		}
	}
    
    /**
     * onEntityDamageEntity
     *
     * @param  EntityDamageByEntityEvent $event
     * @return void
     */
    public function onEntityDamageEntity(EntityDamageByEntityEvent $event): void {
	    $entity = $event->getEntity();
	    $damager = $event->getDamager();

	    if ($this->getConfig()->get("combatdisablefly") === true && $event instanceof EntityDamageByEntityEvent && $entity instanceof Player && $damager instanceof Player) {
			if ($damager->getGamemode() === Player::CREATIVE || $entity->getGamemode() === Player::CREATIVE) return;
			if ($damager->getAllowFlight() === true) {
				$damager->setAllowFlight(false);
				$damager->setFlying(false);
				$damager->sendMessage(C::RED . $this->getConfig()->get("combat-fly-disable"));
			}
	    }
    }
}

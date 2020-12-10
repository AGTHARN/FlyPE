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

use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityLevelChangeEvent;
use pocketmine\event\inventory\InventoryPickupItemEvent;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\player\PlayerItemConsumeEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerDropItemEvent;
use pocketmine\event\Listener;
use pocketmine\utils\TextFormat as C;
use pocketmine\Player;

use AGTHARN\FlyPE\Main;

class EventListener implements Listener {
    
    /**
     * plugin
     * 
     * @var Main
     */
    private $plugin;
	
	/**
	 * __construct
	 *
	 * @param  Main $plugin
	 * @return void
	 */
	public function __construct(Main $plugin) {
		$this->plugin = $plugin;
	}

    /**
	 * onPlayerJoin
	 *
	 * @param  PlayerJoinEvent $event
	 * @return void
	 */
	public function onPlayerJoin(PlayerJoinEvent $event): void {
		$player = $event->getPlayer();
		$name = $player->getName();
		
		/** @phpstan-ignore-next-line */
		if (!$player->getGamemode() === Player::CREATIVE && $this->plugin->getConfig()->get("join-disable-fly") === true && $player->getAllowFlight() === true) {
			$player->setFlying(false);
			$player->setAllowFlight(false);
			$player->sendMessage(C::RED . str_replace("{name}", $name, $this->plugin->getConfig()->get("onjoin-flight-disabled")));
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
		// note: checks are done here instead of doLevelChecks() because getTarget() has to be used instead of getLevel()
		$entity = $event->getEntity();
		$targetLevel = $event->getTarget()->getName();

		if (!$entity instanceof Player || $entity->hasPermission("flype.command.bypass") || $entity->getGamemode() === Player::CREATIVE) return;
		if (($this->plugin->getConfig()->get("mode") === "blacklist" && in_array($targetLevel, $this->plugin->getConfig()->get("blacklisted-worlds")) || $this->plugin->getConfig()->get("mode") === "whitelist" && !in_array($targetLevel, $this->plugin->getConfig()->get("whitelisted-worlds"))) && $entity->getAllowFlight() === true) {
			$entity->sendMessage(C::RED . str_replace("{world}", $targetLevel, $this->plugin->getConfig()->get("flight-not-allowed")));
			$this->plugin->toggleFlight($entity);
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
		/** @phpstan-ignore-next-line */
		$player = $event->getInventory()->getHolder();

		if (!$player instanceof Player || $player->getGamemode() === Player::CREATIVE) return;
		if ($this->plugin->getConfig()->get("picking-up-items") === false && $player->getAllowFlight() === true) {
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
		if ($this->plugin->getConfig()->get("item-dropping") === false && $player->getAllowFlight() === true) {
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
		if ($this->plugin->getConfig()->get("block-breaking") === false && $player->getAllowFlight() === true) {
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
		if ($this->plugin->getConfig()->get("block-placing") === false && $player->getAllowFlight() === true) {
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
		if ($this->plugin->getConfig()->get("player-eating") === false && $player->getAllowFlight() === true) {
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
		$levelName = $event->getEntity()->getLevel()->getName();

	    if ($this->plugin->getConfig()->get("combat-disable-fly") === true && $event instanceof EntityDamageByEntityEvent && $entity instanceof Player && $damager instanceof Player) {
			if ($damager->getGamemode() === Player::CREATIVE || $entity->getGamemode() === Player::CREATIVE) return;
			if ($damager->getAllowFlight() === true) {
				$damager->setAllowFlight(false);
				$damager->setFlying(false);
				$damager->sendMessage(C::RED . str_replace("{world}", $levelName, $this->plugin->getConfig()->get("combat-fly-disable")));
			}
	    }
    }
}
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

use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityLevelChangeEvent;
use pocketmine\event\inventory\InventoryPickupItemEvent;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\player\PlayerItemConsumeEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\event\player\PlayerDropItemEvent;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\utils\TextFormat as C;
use pocketmine\event\Listener;
use pocketmine\Player;

use AGTHARN\FlyPE\Main;
use AGTHARN\FlyPE\util\Util;
use AGTHARN\FlyPE\tasks\FlightDataTask;

class EventListener implements Listener {
        
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
     * @param  Main $plugin
     * @param  Util $util
     * @return void
     */
    public function __construct(Main $plugin, Util $util) {
        $this->plugin = $plugin;
        $this->util = $util;
    }
    
    /**
     * onLevelChange
     *
     * @param  EntityLevelChangeEvent $event
     * @return void
     */
    public function onLevelChange(EntityLevelChangeEvent $event): void {
        $entity = $event->getEntity();
        $targetLevel = $event->getTarget()->getName();

        if (!$entity instanceof Player || $this->util->checkGamemodeCreative($entity) || $entity->hasPermission("flype.command.bypass") || !$entity->getAllowFlight()) return;
        if (!$this->util->doTargetLevelCheck($entity, $targetLevel)) {
            if ($this->plugin->getConfig()->get("level-change-restricted")) {
                $entity->sendMessage(C::RED . str_replace("{world}", $targetLevel, $this->util->getMessages()->get("flight-not-allowed")));
            }
            $this->util->toggleFlight($entity);
            return;
        }
        
        if ($this->plugin->getConfig()->get("level-change-unrestricted")) {
            $entity->sendMessage(C::GREEN . str_replace("{world}", $targetLevel, $this->util->getMessages()->get("flight-is-allowed")));
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
        $name = $player->getName();
        $playerData = $this->util->getFlightData($player, 0);
        
        if ((!$this->util->checkGamemodeCreative($player) || $this->util->checkGamemodeCreativeSetting($player)) && !$this->plugin->getConfig()->get("join-disable-fly") && $playerData->getFlightState() && $player instanceof Player) {
            $this->util->toggleFlight($player);
        }
    }
    
    /**
     * onPlayerQuit
     *
     * @param  PlayerQuitEvent $event
     * @return void
     */
    public function onPlayerQuit(PlayerQuitEvent $event){
        $player = $event->getPlayer();
        $playerData = $this->util->getFlightData($player, 0);
        $data[$player->getId()] = new FlightDataTask($this->plugin, $this->util);

        if (isset($data[$player->getId()])) {
            if ($this->plugin->getConfig()->get("save-flight-state")) {
                if ($player->getAllowFlight()) {
                    $playerData->setFlightState(true);
                }
                $playerData->setFlightState(false);
            }
            if ($playerData->checkNew()) {
                unlink($playerData->getDataPath());
            }
            $playerData->saveData();
            unset($data[$player->getId()]);
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

        if ((!$this->util->checkGamemodeCreative($player) || $this->util->checkGamemodeCreativeSetting($player)) && !$this->plugin->getConfig()->get("picking-up-items") && $player->getAllowFlight() && $player instanceof Player) {
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
        
        if ((!$this->util->checkGamemodeCreative($player) || $this->util->checkGamemodeCreativeSetting($player)) && !$this->plugin->getConfig()->get("item-dropping") && $player->getAllowFlight() && $player instanceof Player) {
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
        
        if ((!$this->util->checkGamemodeCreative($player) || $this->util->checkGamemodeCreativeSetting($player)) && !$this->plugin->getConfig()->get("block-breaking") && $player->getAllowFlight() && $player instanceof Player) {
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
        
        if ((!$this->util->checkGamemodeCreative($player) || $this->util->checkGamemodeCreativeSetting($player)) && !$this->plugin->getConfig()->get("block-placing") && $player->getAllowFlight() && $player instanceof Player) {
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
        
        if ((!$this->util->checkGamemodeCreative($player) || $this->util->checkGamemodeCreativeSetting($player)) && !$this->plugin->getConfig()->get("player-eating") && $player->getAllowFlight()) {
            $event->setCancelled();
        }
    }
    
    /**
     * onPlayerInteract
     *
     * @param  PlayerInteractEvent $event
     * @return void
     */
    public function onPlayerInteract(PlayerInteractEvent $event): void {
        $player = $event->getPlayer();
        $inventory = $player->getInventory();
        $item = $player->getInventory()->getItemInHand();

        if ($item->getNamedTag()->hasTag("coupon") && $this->plugin->getConfig()->get("enable-coupon") && $this->plugin->getConfig()->get("enable-cooldown") && $this->util->doLevelChecks($player)) {
            if ($player->getAllowFlight()) {
                $player->sendMessage(C::RED . str_replace("{name}", $player->getName(), $this->util->getMessages()->get("cant-use-coupon")));
                return;
            }

            if ($this->util->checkCooldown($player)) {
                $this->util->toggleFlight($player);
                $item->setCount($item->getCount() - 1);
                $inventory->setItem($inventory->getHeldItemIndex(), $item);
            }
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

        if ($entity instanceof Player && $damager instanceof Player) {
            if (((!$this->util->checkGamemodeCreative($damager) || $this->util->checkGamemodeCreativeSetting($damager)) || (!$this->util->checkGamemodeCreative($entity) || $this->util->checkGamemodeCreativeSetting($entity))) && $this->plugin->getConfig()->get("combat-disable-fly")) {
                
                if ($damager->getAllowFlight()) {
                    $this->util->toggleFlight($entity);
                    $damager->sendMessage(C::RED . str_replace("{world}", $levelName, $this->util->getMessages()->get("combat-fly-disable")));
                }
                
                if ($entity->getAllowFlight()) {
                    $this->util->toggleFlight($entity);
                    $entity->sendMessage(C::RED . str_replace("{world}", $levelName, $this->util->getMessages()->get("combat-fly-disable")));
                }
            }
        }
    }
}
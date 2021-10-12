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

use pocketmine\Player;
use AGTHARN\FlyPE\Main;
use AGTHARN\FlyPE\util\Util;
use pocketmine\event\Listener;
use pocketmine\utils\TextFormat as C;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\event\player\PlayerDropItemEvent;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\entity\EntityLevelChangeEvent;
use pocketmine\event\player\PlayerItemConsumeEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\inventory\InventoryPickupItemEvent;

class EventListener implements Listener
{
    /** @var Main */
    protected Main $plugin;
    /** @var Util */
    protected Util $util;

    /**
     * __construct
     *
     * @param  Main $plugin
     * @param  Util $util
     * @return void
     */
    public function __construct(Main $plugin, Util $util)
    {
        $this->plugin = $plugin;
        $this->util = $util;
    }
    
    /**
     * onLevelChange
     *
     * @param  EntityLevelChangeEvent $event
     * @return void
     */
    public function onLevelChange(EntityLevelChangeEvent $event): void
    {
        $entity = $event->getEntity();
        $targetLevel = $event->getTarget()->getName();

        if (!$entity instanceof Player || $entity->isCreative(true) || $entity->hasPermission('flype.bypass') || !$entity->getAllowFlight())
            return;
        if (!$this->util->doTargetLevelCheck($entity, $targetLevel)) {
            if ($this->plugin->getConfig()->get('level-change-restricted')) {
                $entity->sendMessage(C::RED . str_replace('{world}', $targetLevel, Main::PREFIX . C::colorize($this->util->messages->get('flight-not-allowed'))));
            }
            $this->util->toggleFlight($entity);
            return;
        }
        if ($this->plugin->getConfig()->get('level-change-unrestricted')) {
            $entity->sendMessage(C::GREEN . str_replace('{world}', $targetLevel, Main::PREFIX . C::colorize($this->util->messages->get('flight-is-allowed'))));
        }
    }

    /**
     * onPlayerJoin
     *
     * @param  PlayerJoinEvent $event
     * @return void
     */
    public function onPlayerJoin(PlayerJoinEvent $event): void
    {
        $player = $event->getPlayer();
        $playerData = $this->util->getFlightData($player, 0);
        
        if ((!$player->isCreative(true) || $this->plugin->getConfig()->get('apply-flight-settings-gmc')) && $playerData->getFlightState() && !$this->plugin->getConfig()->get('join-disable-fly')) {
            $this->util->toggleFlight($player);
        }
    }
    
    /**
     * onPlayerQuit
     *
     * @param  PlayerQuitEvent $event
     * @return void
     */
    public function onPlayerQuit(PlayerQuitEvent $event): void
    {
        $player = $event->getPlayer();
        $playerData = $this->util->getFlightData($player, 0);

        if ($this->plugin->getConfig()->get('save-flight-state')) {
            if ($player->getAllowFlight()) {
                $playerData->setFlightState(true);
            }
            $playerData->setFlightState(false);
        }
        $playerData->saveData();
        if ($playerData->checkNew()) {
            @unlink($playerData->getDataPath());
        }
    }
        
    /**
     * onInventoryPickupItem
     *
     * @param  InventoryPickupItemEvent $event
     * @return void
     */
    public function onInventoryPickupItem(InventoryPickupItemEvent $event): void
    {
        /** @phpstan-ignore-next-line */
        $player = $event->getInventory()->getHolder();

        if ((!$player->isCreative(true) || $this->plugin->getConfig()->get('apply-flight-settings-gmc')) && !$this->plugin->getConfig()->get('picking-up-items') && $player->getAllowFlight()) {
            $event->setCancelled();
        }
    }
        
    /**
     * onPlayerDropItem
     *
     * @param  PlayerDropItemEvent $event
     * @return void
     */
    public function onPlayerDropItem(PlayerDropItemEvent $event): void
    {
        $player = $event->getPlayer();
        
        if ((!$player->isCreative(true) || $this->plugin->getConfig()->get('apply-flight-settings-gmc')) && !$this->plugin->getConfig()->get('item-dropping') && $player->getAllowFlight()) {
            $event->setCancelled();
        }
    }
        
    /**
     * onBlockBreak
     *
     * @param  BlockBreakEvent $event
     * @return void
     */
    public function onBlockBreak(BlockBreakEvent $event): void
    {
        $player = $event->getPlayer();
        
        if ((!$player->isCreative(true) || $this->plugin->getConfig()->get('apply-flight-settings-gmc')) && !$this->plugin->getConfig()->get('block-breaking') && $player->getAllowFlight()) {
            $event->setCancelled();
        }
    }
        
    /**
     * onBlockPlace
     *
     * @param  BlockPlaceEvent $event
     * @return void
     */
    public function onBlockPlace(BlockPlaceEvent $event): void
    {
        $player = $event->getPlayer();
        
        if ((!$player->isCreative(true) || $this->plugin->getConfig()->get('apply-flight-settings-gmc')) && !$this->plugin->getConfig()->get('block-placing') && $player->getAllowFlight()) {
            $event->setCancelled();
        }
    }
        
    /**
     * onPlayerItemConsume
     *
     * @param  PlayerItemConsumeEvent $event
     * @return void
     */
    public function onPlayerItemConsume(PlayerItemConsumeEvent $event): void
    {
        $player = $event->getPlayer();
        
        if ((!$player->isCreative(true) || $this->plugin->getConfig()->get('apply-flight-settings-gmc')) && !$this->plugin->getConfig()->get('player-eating') && $player->getAllowFlight()) {
            $event->setCancelled();
        }
    }
    
    /**
     * onPlayerInteract
     *
     * @param  PlayerInteractEvent $event
     * @return void
     */
    public function onPlayerInteract(PlayerInteractEvent $event): void
    {
        $player = $event->getPlayer();
        $inventory = $player->getInventory();
        $item = $player->getInventory()->getItemInHand();

        if ($this->plugin->getConfig()->get('enable-coupon')) {
            if ($item->getNamedTagEntry('default') || $item->getNamedTagEntry('temporal')) {
                if (!$this->util->checkCooldown($player) && $this->util->doLevelChecks($player)) {
                    if ($player->getAllowFlight()) {
                        $player->sendMessage(C::RED . str_replace('{name}', $player->getName(), Main::PREFIX . C::colorize($this->util->messages->get('cant-use-coupon'))));
                        return;
                    }

                    if ($item->getNamedTagEntry('default')) {
                        if ($this->util->toggleFlight($player)) {
                            $item->setCount($item->getCount() - 1);
                            $inventory->setItem($inventory->getHeldItemIndex(), $item);
                        }
                    }
                    if ($item->getNamedTagEntry('temporal')) {
                        $time = $item->getNamedTag()->getString('temporal');
                        if ($this->util->toggleFlight($player, (int)$time, false, true)) {
                            $item->setCount($item->getCount() - 1);
                            $inventory->setItem($inventory->getHeldItemIndex(), $item);
                        }
                    }
                }
            }
            return;
        }
        $player->sendMessage(C::RED . str_replace('{name}', $player->getName(), Main::PREFIX . C::colorize($this->util->messages->get('coupon-config-disabled'))));
    }
    
    /**
     * onEntityDamageEntity
     *
     * @param  EntityDamageByEntityEvent $event
     * @return void
     */
    public function onEntityDamageEntity(EntityDamageByEntityEvent $event): void
    {
        $entity = $event->getEntity();
        $damager = $event->getDamager();
        $levelName = $event->getEntity()->getLevel()->getName();

        if (!$event->isCancelled() || !$this->plugin->getConfig()->get('combat-disable-fly'))
            return;
        if ($entity instanceof Player && $damager instanceof Player) {
            if ((!$entity->isCreative(true) || $this->plugin->getConfig()->get('apply-flight-settings-gmc'))) {
                if ($entity->getAllowFlight()) {
                    $this->util->toggleFlight($entity, 0, true);
                    $entity->sendMessage(C::RED . str_replace('{world}', $levelName, Main::PREFIX . C::colorize($this->util->messages->get('combat-fly-disable'))));
                }
            }
            if ((!$damager->isCreative(true) || $this->plugin->getConfig()->get('apply-flight-settings-gmc'))) {
                if ($damager->getAllowFlight()) {
                    $this->util->toggleFlight($damager, 0, true);
                    $damager->sendMessage(C::RED . str_replace('{world}', $levelName, Main::PREFIX . C::colorize($this->util->messages->get('combat-fly-disable'))));
                }
            }
        }
    }
}
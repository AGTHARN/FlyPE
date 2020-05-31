<?php

/* 
 *  ______ _  __     _______  ______ 
 * |  ____| | \ \   / /  __ \|  ____|
 * | |__  | |  \ \_/ /| |__) | |__   
 * |  __| | |   \   / |  ___/|  __|  
 * | |    | |____| |  | |    | |____ 
 * |_|    |______|_|  |_|    |______|
 *
 * FlyPE, is an advanced fly plugin for pocketmine.
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

    private $config;

    public function onEnable(){
        $this->getServer()->getPluginManager()->registerEvents($this, $this);

        $this->saveResource('config.yml');
        $this->config = new Config($this->getDataFolder() . 'config.yml', Config::YAML);
	    
        $configversion = $this->config->get("config-version");
	    
	    if($configversion < "2"){
		    $this->getLogger()->warning("Your config is outdated! Please delete your old config to get the latest features!");
		    $this->getServer()->getPluginManager()->disablePlugin($this);
		    //im sorry dylan
	    }
	    UpdateNotifier::checkUpdate($this->getDescription()->getName(), $this->getDescription()->getVersion());
    }

    public function onPlayerJoin(PlayerJoinEvent $event){
        $player = $event->getPlayer();
		
	    if($this->getConfig()->get("joindisablefly") === true){
		    if($player->getGamemode() === Player::CREATIVE) return;
			    if($player->getAllowFlight() === true){
				    $player->setFlying(false);
				    $player->setAllowFlight(false);
					$player->sendMessage(C::RED . $this->getConfig()->get("onjoin-flight-disabled"));
				    return;
			    }
	    }
    }
	
	public function BlacklistedWorldCheck($entity){
		if(!in_array($entity->getLevel()->getName(), $this->getConfig()->get("blacklisted-worlds"))){
			if($entity->getAllowFlight() === false){
				$entity->sendMessage(C::GREEN . $this->getConfig()->get("toggled-flight-on"));
				$entity->setFlying(true);
				$entity->setAllowFlight(true);
				return false;
				} else {
					$entity->setFlying(false);
					$entity->setAllowFlight(false);
					$entity->sendMessage(C::RED . $this->getConfig()->get("toggled-flight-off"));
					return false;
				}
		} else {
			$entity->sendMessage(C::RED . $this->getConfig()->get("flight-not-allowed"));
			return false;
		}
	}
	
	public function WhitelistedWorldCheck($entity){
		if(in_array($entity->getLevel()->getName(), $this->getConfig()->get("whitelisted-worlds"))){
			if($entity->getAllowFlight() === false){
				$entity->sendMessage(C::GREEN . $this->getConfig()->get("toggled-flight-on"));
				$entity->setFlying(true);
				$entity->setAllowFlight(true);
				return false;
			} else {
				$entity->setFlying(false);
				$entity->setAllowFlight(false);
				$entity->sendMessage(C::RED . $this->getConfig()->get("toggled-flight-off"));
				return false;
				}
		} else {
			$entity->sendMessage(C::RED . $this->getConfig()->get("flight-not-allowed"));
			return false;
		}
	}
	
	private function CheckLevel(Entity $entity) : bool{
		if($entity->getGamemode() === Player::CREATIVE){
			if($entity->getAllowFlight() === true){
				$entity->sendMessage(C::RED . $this->getConfig()->get("disable-fly-creative"));
				return false;
			}
		}
		if($this->getConfig()->get("mode") === "blacklist"){
			if($entity instanceof Player) $this->BlacklistedWorldCheck($entity);
			return false;
		}elseif($this->getConfig()->get("mode") === "whitelist"){
			if($entity instanceof Player) $this->WhitelistedWorldCheck($entity);
			return false;
		}elseif($this->getConfig()->get("mode") === "both"){
			if($entity instanceof Player) $this->BlacklistedWorldCheck($entity);
			if($entity instanceof Player) $this->WhitelistedWorldCheck($entity);
			return false;
		}
	}
	
	public function onLevelChange(EntityLevelChangeEvent $event) : void{
		$entity = $event->getEntity();
		if($entity->hasPermission("flype.command.bypass")){
			return;
		}
		if($entity->getGamemode() === Player::CREATIVE) return;
		if($this->getConfig()->get("mode") === "blacklist"){
			if(!in_array($entity->getLevel()->getName(), $this->getConfig()->get("blacklisted-worlds"))){
				if($entity->getAllowFlight() === true){
					$entity->setFlying(false);
					$entity->setAllowFlight(false);
					$entity->sendMessage(C::RED . $this->getConfig()->get("flight-not-allowed"));
					return;
				}
			}
		}
		if($this->getConfig()->get("mode") === "whitelist"){
			if(in_array($entity->getLevel()->getName(), $this->getConfig()->get("whitelisted-worlds"))){
				if($entity->getAllowFlight() === true){
					$entity->setFlying(false);
					$entity->setAllowFlight(false);
					$entity->sendMessage(C::RED . $this->getConfig()->get("flight-not-allowed"));
					return;
				}
			}
		}
	}
	
	public function openflyui($player){
		$form = new SimpleForm(function (Player $player, int $data = null){
			
			switch($data){
                case 0:
				$cost = $this->getConfig()->get("buyflycost");
				$playermoney = EconomyAPI::getInstance()->myMoney($player);
				
				if($this->getConfig()->get("payforfly") === true){
				if($playermoney < $cost){
					$player->sendMessage(C::RED . $this->getConfig()->get("not-enough-money"));
				} elseif($player->getAllowFlight() === false){
						EconomyAPI::getInstance()->reduceMoney($player, $cost);
						$player->sendMessage(C::GREEN . $this->getConfig()->get("buy-fly-successful"));
						if($player instanceof Player) $this->CheckLevel($player);
					} elseif($player instanceof Player) $this->CheckLevel($player);
				} elseif($this->getConfig()->get("payforfly") === false){
						if($player instanceof Player) $this->CheckLevel($player);
					}
				break;
			}
			});
			
			if($this->getConfig()->get("payforfly") === true){
				if($this->getConfig()->get("enableflyui") === true){
					$cost = $this->getConfig()->get("buyflycost");
					
					$form->setTitle("§l§7< §2FlyUI §7>");
					$form->addButton("§aToggle Fly §e(Costs $ {$cost})");
					$form->addButton("§cExit");
					$form->sendToPlayer($player);
					return $form;
			}
			} elseif($this->getConfig()->get("enableflyui") === true){
					if($this->getConfig()->get("payforfly") === false){
					$form->setTitle("§l§7< §6FlyUI §7>");
					$form->addButton("§aToggle Fly");
					$form->addButton("§cExit");
					$form->sendToPlayer($player);
					return $form;
					}
				}
	}
	
    public function onCommand(CommandSender $sender, Command $cmd, $label, array $args) : bool{
	    if($cmd->getName() === "fly"){
		    if(!$sender instanceof Player){
			    $sender->sendMessage("You can only use this command in-game!");
			    return false;
		    }
			if($this->getConfig()->get("enableflyui") === true){
				if($sender instanceof Player) $this->openflyui($sender);
				return false;
			}
		    if(empty($args[0])){
			    if($sender instanceof Player) $this->CheckLevel($sender);
		    }
		    if(isset($args[0])){
			    $target = $this->getServer()->getPlayer($args[0]);
			    $targetname = $target->getName();
			    $messageoff = str_replace("{name}", $targetname, $this->getConfig()->get("flight-for-other-off"));
			    $messageon = str_replace("{name}", $targetname, $this->getConfig()->get("flight-for-other-on"));
			    if(!$sender->hasPermission("flype.command.others")){
					$sender->sendMessage(C::RED . $this->getConfig()->get("cant-toggle-flight-others"));
				    return false;
			    }
			    if(!$target instanceof Player){
					$sender->sendMessage(C::RED . $this->getConfig()->get("player-cant-be-found"));
				    return false;
			    }
			    if($target instanceof Player) $this->CheckLevel($target);
				if($target->getAllowFlight() === false){
					$sender->sendMessage(C::RED . $messageoff);
				} elseif($target->getAllowFlight() === true){
						$sender->sendMessage(C::GREEN . $messageon);
					}
		    }
		    return false;
	    }
    }
	public function onInventoryPickupItem(InventoryPickupItemEvent $event){
		$inventory = $event->getInventory();
		$player = $inventory->getHolder();
		if(!$player instanceof Player) return;
		if($player->getGamemode() === Player::CREATIVE) return;
		if($this->getConfig()->get("picking-up-items") === false){
			if($player->getAllowFlight() === true){
				$event->setCancelled();
			}
		}
	}
	
	public function onPlayerDropItem(PlayerDropItemEvent $event){
		$player = $event->getPlayer();
		if(!$player instanceof Player) return;
		if($player->getGamemode() === Player::CREATIVE) return;
		if($this->getConfig()->get("item-dropping") === false){
			if($player->getAllowFlight() === true){
				$event->setCancelled();
			 }
		 }
	}
	
	public function onBlockBreak(BlockBreakEvent $event){
		$player = $event->getPlayer();
		if(!$player instanceof Player) return;
		if($player->getGamemode() === Player::CREATIVE) return;
		if($this->getConfig()->get("block-breaking") === false){
			if($player->getAllowFlight() === true){
				$event->setCancelled();
			}
		}
	}
	
	public function onBlockPlace(BlockPlaceEvent $event){
		$player = $event->getPlayer();
		if(!$player instanceof Player) return;
		if($player->getGamemode() === Player::CREATIVE) return;
		if($this->getConfig()->get("block-placing") === false){
			if($player->getAllowFlight() === true){
				$event->setCancelled();
			}
		}
	}
	
	public function onPlayerItemConsume(PlayerItemConsumeEvent $event){
		$player = $event->getPlayer();
		if(!$player instanceof Player) return;
		if($player->getGamemode() === Player::CREATIVE) return;
		if($this->getConfig()->get("player-eating") === false){
			if($player->getAllowFlight() === true){
				$event->setCancelled();
			}
		}
	}

    public function onEntityDamageEntity(EntityDamageByEntityEvent $event) : void {
	    $entity = $event->getEntity();
	    $damager = $event->getDamager();

	    if($this->getConfig()->get("combatdisablefly") === true){
		    if($event instanceof EntityDamageByEntityEvent){
			    if($entity instanceof Player){
				    if($damager instanceof Player){
					    if($damager->getGamemode() === Player::CREATIVE) return;
					    if($entity->getGamemode() === Player::CREATIVE) return;
					    if($damager->getAllowFlight() === true){
						    $damager->setAllowFlight(false);
						    $damager->setFlying(false);
						    $damager->sendMessage(C::RED . $this->getConfig()->get("combat-fly-disable"));
					    }
					    if($entity->getAllowFlight() === true){
						    $entity->setAllowFlight(false);
						    $entity->setFlying(false);
						    $entity->sendMessage(C::RED . $this->getConfig()->get("combat-fly-disable"));
					    }
				    }
			    }
		    }
	    }
    }
}

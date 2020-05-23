<?php

namespace AGTHARN\FlyPE;

use pocketmine\entity\Entity;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityLevelChangeEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\utils\Config;
use pocketmine\utils\TextFormat as C;
use pocketmine\event\Listener;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use AGTHARN\FlyPE\libs\jojoe77777\FormAPI\SimpleForm;
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
	    }
    }

    public function onPlayerJoin(PlayerJoinEvent $event){
        $player = $event->getPlayer();
		
	    if($this->getConfig()->get("joindisablefly") === true) {
		    if($player->getGamemode() === Player::CREATIVE){
			    return;
			    } else {
			    if($player->getAllowFlight() === true){
				    $player->setFlying(false);
				    $player->setAllowFlight(false);
				    $player->sendMessage(C::RED . "Your flight has been disabled");
			    }
		    }
	    }
    }
	
	private function levelcheck(Entity $entity) : bool{
		if($entity->getGamemode() === Player::CREATIVE){
			if($entity->getAllowFlight() === false){
				$entity->sendMessage(C::RED . "You can't toggle fly in creative!");
				return false;
			}
			if($entity->getAllowFlight() === true){
				$entity->sendMessage(C::RED . "You can't disable fly in creative!");
				return false;
			}
		}
		if($this->getConfig()->get("mode") === "blacklist"){
			if(!in_array($entity->getLevel()->getName(), $this->getConfig()->get("blacklisted-worlds"))){
				if($entity->getAllowFlight() === false){
					$entity->sendMessage(C::GREEN . "Toggled your flight on!");
					$entity->setFlying(true);
					$entity->setAllowFlight(true);
					return false;
					} else {
						$entity->setFlying(false);
						$entity->setAllowFlight(false);
						$entity->sendMessage(C::RED . "Toggled your flight off!");
						return false;
					}
				}
		} else {
			if($this->getConfig()->get("mode") === "whitelist"){
				if(in_array($entity->getLevel()->getName(), $this->getConfig()->get("whitelisted-worlds"))){
					if($entity->getAllowFlight() === false){
						$entity->sendMessage(C::GREEN . "Toggled your flight on!");
						$entity->setFlying(true);
						$entity->setAllowFlight(true);
						return false;
					} else {
							$entity->setFlying(false);
							$entity->setAllowFlight(false);
							$entity->sendMessage(C::RED . "Toggled your flight off!");
							return false;
						}
					}
					$entity->sendMessage(C::RED . "This world does not allow flight!");
					return false;
				}
			}
		$entity->sendMessage(C::RED . "This world does not allow flight!");
		return false;
	}
	
	public function onLevelChange(EntityLevelChangeEvent $event) : void{
		$entity = $event->getEntity();
		if($entity->hasPermission("flype.command.bypass")){
			return;
		}
		if($entity->getGamemode() === Player::CREATIVE){
			return;
		}
		if($this->getConfig()->get("mode") === "blacklist"){
			if(!in_array($entity->getLevel()->getName(), $this->getConfig()->get("blacklisted-worlds"))){
				if($entity->getAllowFlight() === true){
					$entity->setFlying(false);
					$entity->setAllowFlight(false);
					$entity->sendMessage(C::RED . "This world does not allow flight!");
					return;
				}
			}
		}
		if($this->getConfig()->get("mode") === "whitelist"){
			if(in_array($entity->getLevel()->getName(), $this->getConfig()->get("whitelisted-worlds"))){
				if($entity->getAllowFlight() === true){
					$entity->setFlying(false);
					$entity->setAllowFlight(false);
					$entity->sendMessage(C::RED . "This world does not allow flight!");
					return;
				}
			}
		}
	}
	
	public function openflyui($player){
		$this->economy = $this->getServer()->getPluginManager()->getPlugin("EconomyAPI");
		
		$form = new SimpleForm(function (Player $player, int $data = null){
			
			switch($data){
                case 0:
				$cost = $this->getConfig()->get("buyflycost");
				$playermoney = EconomyAPI::getInstance()->myMoney($player);
				
				if($this->getConfig()->get("payforfly") === true){
				if($playermoney < $cost){
					$player->sendMessage(C::RED . "You do not have enough money!");
				} else {
					EconomyAPI::getInstance()->reduceMoney($player, $cost);
					$player->sendMessage(C::GREEN . "Successful purchase of fly!");
					
				if($player instanceof Player) $this->levelcheck($player);
				}
				} else {
					if($this->getConfig()->get("payforfly") === false){
						if($player instanceof Player) $this->levelcheck($player);
					}
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
			} else {
				if($this->getConfig()->get("enableflyui") === true){
					if($this->getConfig()->get("payforfly") === false){
					$form->setTitle("§l§7< §6FlyUI §7>");
					$form->addButton("§aToggle Fly");
					$form->addButton("§cExit");
					$form->sendToPlayer($player);
					return $form;
					}
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
			    if($sender instanceof Player) $this->levelcheck($sender);
		    }
		    if(isset($args[0])){
			    $target = $this->getServer()->getPlayer($args[0]);
			    if(!$sender->hasPermission("flype.command.others")){
				    $sender->sendMessage(C::RED . "You do not have permission to toggle flight for others!");
				    return false;
			    }
			    if(!$target instanceof Player){
				    $sender->sendMessage(C::RED . "Player could not be found!");
				    return false;
			    }
			    if($target instanceof Player) $this->levelcheck($target);
				if($target->getAllowFlight() === false){
					$sender->sendMessage(C::RED . "Flight for " . $target->getName() . " has been toggled off!");
				} else {
					if($target->getAllowFlight() === true){
						$sender->sendMessage(C::GREEN . "Flight for " . $target->getName() . " has been toggled on!");
					}
				}
		    }
		    return false;
	    }
    }

    public function onEntityDamageEntity(EntityDamageByEntityEvent $event) : void {
	    $entity = $event->getEntity();
	    $damager = $event->getDamager();

	    if($this->getConfig()->get("combatdisablefly") === true){
		    if($event instanceof EntityDamageByEntityEvent){
			    if($entity instanceof Player){
				    if(!$damager instanceof Player) return;
				    if($damager->getGamemode() === Player::CREATIVE) return;
				    if($damager->getAllowFlight() === true){
					    $damager->setAllowFlight(false);
					    $damager->setFlying(false);
					    $damager->sendMessage(C::RED . "You can't fly during combat!");
					}
				}
			}
		}
	}
}

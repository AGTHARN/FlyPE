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

class Main extends PluginBase implements Listener {

    private $config;

    public function onEnable()
    {
        $this->getServer()->getPluginManager()->registerEvents($this, $this);

        $this->saveResource('config.yml');
        $this->config = new Config($this->getDataFolder() . 'config.yml', Config::YAML);
	    
        $configversion = $this->config->get("config-version");
	    
	    if($configversion < "0.9.9"){
		    $this->getLogger()->warning("Your config is outdated! Please delete your old config to get the latest features!");
		    $this->getServer()->getPluginManager()->disablePlugin($this);
	    }
    }

    public function onPlayerJoin(PlayerJoinEvent $event){
        $player = $event->getPlayer();
		
		if($this->getConfig()->get("joindisablefly") === true) {
			if($player->getGamemode() === Player::CREATIVE){
				return;
				}else{
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
				}else{
					if($entity->getAllowFlight() === true){
						$entity->setFlying(false);
						$entity->setAllowFlight(false);
						$entity->sendMessage(C::RED . "Toggled your flight off!");
						return false;
					}
				}
				}
				}else{
					if($this->getConfig()->get("mode") === "whitelist"){
						if(in_array($entity->getLevel()->getName(), $this->getConfig()->get("whitelisted-worlds"))){
			if($entity->getAllowFlight() === false){
				$entity->sendMessage(C::GREEN . "Toggled your flight on!");
				$entity->setFlying(true);
				$entity->setAllowFlight(true);
				return false;
				}else{
					if($entity->getAllowFlight() === true){
						$entity->setFlying(false);
						$entity->setAllowFlight(false);
						$entity->sendMessage(C::RED . "Toggled your flight off!");
						return false;
					}
				}
						return false;
						}
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

    public function onCommand(CommandSender $sender, Command $cmd, $label, array $args) : bool{
        if($cmd->getName() === "fly"){
            if(!$sender instanceof Player){
                $sender->sendMessage("You can only use this command in-game!");
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
					if($target->getGamemode() === Player::CREATIVE){
						$sender->sendMessage(C::RED . "Unable to toggle because player is in creative!");
						return false;
					}
					if($target->getAllowFlight() === true){
							$target->setFlying(false);
							$target->setAllowFlight(false);
							$target->sendMessage(C::RED . "Your flight was toggled off!");
							$sender->sendMessage(C::RED . "Flight for " . $target->getName() . " has been toggled off!");
							}else{
								$target->setAllowFlight(true);
								$target->setFlying(true);
								$target->sendMessage(C::GREEN . "Your flight was toggled on!");
								$sender->sendMessage(C::GREEN . "Flight for " . $target->getName() . " has been toggled on!");
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
					if($damager->isCreative()) return;
					if($damager->getAllowFlight() === true){
						$damager->setAllowFlight(false);
						$damager->setFlying(false);
						//$entity->sendMessage(C::RED . "You can't fly during combat!");
					}
				}
			}
		}
	}
}

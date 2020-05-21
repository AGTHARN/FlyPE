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
    }

    public function onPlayerJoin(PlayerJoinEvent $event)
    {
        $player = $event->getPlayer();
		
		if ($this->getConfig()->get("joindisablefly") === true) {
			if($player->getGamemode() === Player::CREATIVE){
				return;
			}elseif($player->getAllowFlight() === true){
					$player->setFlying(false);
					$player->setAllowFlight(false);
					$player->sendMessage(C::RED . "Your flight has been disabled");
			}
        }
    }
	
	private function levelcheck(Entity $sender) : bool{
		if($sender->getGamemode() === Player::CREATIVE){
			if($sender->getAllowFlight() === false){
				$sender->sendMessage(C::RED . "You can't toggle fly in creative!");
				return false;
				}
				if($sender->getAllowFlight() === true){
				$sender->sendMessage(C::RED . "You can't disable fly in creative!");
				return false;
				}
				}
		if(!in_array($sender->getLevel()->getName(), $this->getConfig()->get("disabled-worlds"))){
			$sender->sendMessage(C::GREEN . "Toggled your flight on!");
			$sender->setFlying(true);
			$sender->setAllowFlight(true);
			return false;
		}
		if($sender->getAllowFlight() === true){
			$sender->setFlying(false);
			$sender->setAllowFlight(false);
			$sender->sendMessage(C::RED . "Toggled your flight off!");
			return false;
		}
		$sender->sendMessage(C::RED . "This world does not allow flight!");
		return false;
	}
	
	public function onLevelChange(EntityLevelChangeEvent $event) : void{
		$sender = $event->getEntity();
		if($sender->getGamemode() === Player::CREATIVE){
			$sender->sendMessage(C::RED . "You can't toggle fly in creative!");
			return;
		}
		if(!in_array($sender->getLevel()->getName(), $this->getConfig()->get("disabled-worlds"))){
			if($sender->getAllowFlight() === true){
			$sender->setFlying(false);
			$sender->setAllowFlight(false);
			$sender->sendMessage(C::RED . "This world does not allow flight!");
			return;
			}
		}
	}

    public function onCommand(CommandSender $sender, Command $cmd, $label, array $args) : bool{
        if($cmd->getName() === "fly"){
            if(!$sender instanceof Player){
                $sender->sendMessage("You can only use this command in-game!");
                return false;
            }
            if(isset($args[0])){
                if(!$sender->hasPermission("flype.command.others")){
                    $sender->sendMessage(C::RED . "You do not have permission to toggle flight for others!");
                    return false;
                }
                $target = $sender->getServer()->getPlayer($args[0]);
                if(!$target instanceof Player){
                    $sender->sendMessage(C::RED . "Player could not be found!");
                    return false;
                }
				if(!$sender->hasPermission("flype.command.others")){
					if($target->getGamemode() === Player::CREATIVE){
						$target->sendMessage(C::RED . "Target is in creative mode!");
						return false;
					}
					if($target->getAllowFlight() === true){
						$target->setFlying(false);
						$target->setAllowFlight(false);
						$target->sendMessage(C::RED . "Your flight was toggled off!");
						$sender->sendMessage(C::RED . "Toggled " . $target->getName() . "'s flight off");
						return false;
					}elseif($target->getAllowFlight() === false){
						$target->setFlying(true);
						$target->setAllowFlight(true);
						$target->sendMessage(C::GREEN . "Your flight was toggled on!");
						$sender->sendMessage(C::GREEN . "Toggled " . $target->getName() . "'s flight on");
						return false;
				}
			}
			}
			if($sender->getGamemode() === Player::CREATIVE){
			if($sender->getAllowFlight() === false){
				$sender->sendMessage(C::RED . "You can't toggle fly in creative!");
				return false;
				}
				if($sender->getAllowFlight() === true){
				$sender->sendMessage(C::RED . "You can't disable fly in creative!");
				return false;
				}
				}
            if($sender->getAllowFlight() === true){
                $sender->setFlying(false);
                $sender->setAllowFlight(false);
                $sender->sendMessage(C::RED . "Toggled your flight off!");
				return false;
            }elseif($sender->getAllowFlight() === false){
				if($sender instanceof Player) $this->levelcheck($sender);
			}
        }
        return false;
    }

    public function onEntityDamageEntity(EntityDamageByEntityEvent $event) : void
    {
        $entity = $event->getEntity();
        $damager = $event->getDamager();

		if ($this->getConfig()->get("combatdisablefly") === true){
			if(!$damager instanceof Player) return;
			if($damager->isCreative()) return;
			if($damager->getAllowFlight() === true){
				$entity->setFlying(false);
				$entity->setAllowFlight(false);
				$entity->sendMessage(C::RED . "You can't fly while in combat!");
				return;
			}
        }
    }
}

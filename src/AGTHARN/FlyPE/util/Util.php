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

namespace AGTHARN\FlyPE\util;

use pocketmine\nbt\tag\StringTag;
use pocketmine\utils\TextFormat as C;
use pocketmine\utils\Config;
use pocketmine\entity\Entity;
use pocketmine\math\Vector3;
use pocketmine\item\Item;
use pocketmine\Player;

use AGTHARN\FlyPE\tasks\ParticleTask;
use AGTHARN\FlyPE\tasks\FlightSpeedTask;
use AGTHARN\FlyPE\tasks\FlightDataTask;
use AGTHARN\FlyPE\tasks\EffectTask;
use AGTHARN\FlyPE\lists\ParticleList;
use AGTHARN\FlyPE\lists\SoundList;
use AGTHARN\FlyPE\data\FlightData;
use AGTHARN\FlyPE\Main;

use JackMD\UpdateNotifier\UpdateNotifier;
use jojoe77777\FormAPI\SimpleForm;
use onebone\economyapi\EconomyAPI;
use CortexPE\Commando\PacketHooker;

class Util /** aka api */ { 
        
    /** @var Main */
    protected $plugin;

    /** @var array */
    private $cooldownArray = [];
    
    /** @var mixed */
    public $messages;
    
    /**
     * __construct
     *
     * @param  Main $plugin
     * @return void
     */
    public function __construct(Main $plugin) {
        $this->plugin = $plugin;

        $this->plugin->saveResource( 'lang/' . $this->plugin->getConfig()->get('lang') . '.yml');
        $this->messages = new Config($this->plugin->getDataFolder() . 'lang/' . $this->plugin->getConfig()->get('lang') . '.yml', Config::YAML) ?? new Config($this->plugin->getDataFolder() . 'lang' . DIRECTORY_SEPARATOR . 'en_US.yml', Config::YAML);
    }
    
    /**
     * openFlyUI
     *
     * @param  Player $player
     * @return mixed
     */
    public function openFlyUI(Player $player): mixed {
        $form = new SimpleForm(function (Player $player, $data) {
            
        if ($data === null) return;
            
        switch($data) {
            case 0:
                $cost = $this->plugin->getConfig()->get('buy-fly-cost');
                $name = $player->getName();
                $playerData = $this->getFlightData($player, 0);
                
                if ($this->plugin->getConfig()->get('pay-for-fly')) {
                    if (EconomyAPI::getInstance()->myMoney($player) < $cost) {
                        $player->sendMessage(C::RED . str_replace('{cost}', $cost, str_replace('{name}', $name, Main::PREFIX . $this->messages->get('not-enough-money'))));
                        return;
                    }
                    if (!$player->isFlying()) {
                        if ($this->doLevelChecks($player)) {
                            $this->toggleFlight($player);
                            
                            if ($this->plugin->getConfig()->get('save-purchased-data')) {
                                if (!$playerData->getPurchased()) {
                                    EconomyAPI::getInstance()->reduceMoney($player, $cost);
                                    $player->sendMessage(C::GREEN . str_replace('{cost}', $cost, str_replace('{name}', $name, Main::PREFIX . $this->messages->get('buy-fly-successful'))));
                                    $playerData->setPurchased(true);
                                    $playerData->saveData();
                                }
                            }
                        }
                        return;
                    }

                    if ($this->doLevelChecks($player) && $player->isFlying()) {
                        $this->toggleFlight($player);
                    }
                    return;
                }
                $this->toggleFlight($player);
                break;
            case 1:
                // exit button
                break;
        }
        });
        
        if ($this->plugin->getConfig()->get('enable-fly-ui') && $this->plugin->getConfig()->get('pay-for-fly') && !$this->plugin->getConfig()->get('custom-ui-texts')) {
            $cost = $this->plugin->getConfig()->get('buy-fly-cost');
                    
            $form->setTitle('§l§7< §2FlyUI §7>');
            $form->addButton('§aToggle Fly §e(Costs $ {$cost})');
            $form->addButton('§cExit');
            $form->sendToPlayer($player);
            return $form;
        }

        if ($this->plugin->getConfig()->get('enable-fly-ui') && !$this->plugin->getConfig()->get('pay-for-fly') && !$this->plugin->getConfig()->get('custom-ui-texts')) {
            $form->setTitle('§l§7< §6FlyUI §7>');
            $form->addButton('§aToggle Fly');
            $form->addButton('§cExit');
            $form->sendToPlayer($player);
            return $form;
        }
        
        if ($this->plugin->getConfig()->get('custom-ui-texts')) {
            $cost = $this->plugin->getConfig()->get('buy-fly-cost');

            $form->setTitle($this->plugin->getConfig()->get('fly-ui-title'));
            $form->addButton(str_replace('{cost}', $cost, $this->plugin->getConfig()->get('fly-ui-toggle')));
            $form->addButton($this->plugin->getConfig()->get('fly-ui-exit'));
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
    public function doLevelChecks(Player $player): bool {
        $levelName = $player->getLevel()->getName();
        $name = $player->getName();

        if ($this->checkGamemodeCreative($player) && $player->isFlying() && !$this->plugin->getConfig()->get('allow-toggle-flight-gmc')) {
            $player->sendMessage(C::RED . str_replace('{name}', $name, Main::PREFIX . $this->messages->get('disable-fly-creative')));
            return false;
        }

        if ($this->plugin->getConfig()->get('mode') === 'blacklist' && !in_array($player->getLevel()->getName(), $this->plugin->getConfig()->get('blacklisted-worlds'))) {
            return true;
        }
        if ($this->plugin->getConfig()->get('mode') === 'whitelist' && in_array($player->getLevel()->getName(), $this->plugin->getConfig()->get('whitelisted-worlds'))) {
            return true;
        }
        $player->sendMessage(C::RED . str_replace('{world}', $levelName, Main::PREFIX . $this->messages->get('flight-not-allowed')));
        return false;
    }
    
    /**
     * doTargetLevelCheck
     *
     * @param  Player $entity
     * @param  string $targetLevel
     * @return bool
     */
    public function doTargetLevelCheck(Player $entity, string $targetLevel): bool {
        // returns false if not allowed
        if (($this->plugin->getConfig()->get('mode') === 'blacklist' && in_array($targetLevel, $this->plugin->getConfig()->get('blacklisted-worlds')) || $this->plugin->getConfig()->get('mode') === 'whitelist' && !in_array($targetLevel, $this->plugin->getConfig()->get('whitelisted-worlds'))) && $entity->isFlying()) {
            return false;
        }
        return true;
    }
    
    /**
     * toggleFlight
     *
     * @param  Player $player
     * @param  int $time
     * @param  bool $overwrite
     * @param  bool $temp
     * @return bool
     */
    public function toggleFlight(Player $player, int $time = 0, bool $overwrite = false, bool $temp = false): bool {
        $name = $player->getName();
        $playerData = $this->getFlightData($player, $time);
        
        if ($this->checkCooldown($player, $overwrite)) {
            if ($this->plugin->getConfig()->get('send-cooldown-message')) {
                $player->sendMessage(C::RED . str_replace('{seconds}', $this->cooldownArray[$name] - time(), str_replace('{name}', $player->getName(), Main::PREFIX . $this->messages->get('currently-on-cooldown'))));
            }
            return false;
        }
        unset($this->cooldownArray[$name]);

        if ($player->isFlying()) {
            $player->setAllowFlight(false);
            $player->setFlying(false);
            if (is_file($playerData->getDataPath())) {
                if ($this->plugin->getConfig()->get('save-flight-state')) {
                    $playerData->setFlightState(false);
                }
                $playerData->setTempToggle(false);
            }
            $player->sendMessage(C::RED . str_replace('{name}', $name, Main::PREFIX . $this->messages->get('toggled-flight-off')));
            
            if ($this->plugin->getConfig()->get('enable-fly-sound')) {
                $player->getLevel()->addSound($this->getSoundList()->getSound($this->plugin->getConfig()->get('fly-disabled-sound'), new Vector3($player->x, $player->y, $player->z)));
            }
        } else {
            $player->setAllowFlight(true);
            $player->setFlying(true);
            if ($this->plugin->getConfig()->get('save-flight-state')) {
                if (is_file($playerData->getDataPath())) {
                    $playerData->setFlightState(true);
                }
            }
            $player->sendMessage(C::GREEN . str_replace('{name}', $name, Main::PREFIX . $this->messages->get('toggled-flight-on')));
    
            if ($this->plugin->getConfig()->get('enable-fly-sound')) {
                $player->getLevel()->addSound($this->getSoundList()->getSound($this->plugin->getConfig()->get('fly-enabled-sound'), new Vector3($player->x, $player->y, $player->z)));
            }
            if ($this->plugin->getConfig()->get('time-fly') && $temp) {
                if (!$this->plugin->getConfig()->get('time-fly')) {
                    $player->sendMessage(C::RED . str_replace('{name}', $player, Main::PREFIX . $this->messages->get('temp-fly-config-disabled')));
                    return false;
                }

                if (is_file($playerData->getDataPath())) {
                    $playerData->resetDataTime();
                    $playerData->setTempToggle(true);
                }
            }
        }
        $this->cooldownArray[$name] = time() + $this->plugin->getConfig()->get('cooldown-seconds');
        $playerData->saveData();
        return true;
    }
        
    /**
     * checkCooldown
     *
     * @param  Player $player
     * @param  bool $overwrite
     * @return bool
     */
    public function checkCooldown(Player $player, bool $overwrite): bool {
        if (isset($this->cooldownArray[$player->getName()]) && time() < $this->cooldownArray[$player->getName()] && !$overwrite) {
            return true;
        }
        return false;
    }
    
    /**
     * getCouponItem
     *
     * @param  string $type
     * @param  int $count
     * @param  Player $player
     * @param  int $cost
     * @param  int $time
     * @return Item
     */
    public function getCouponItem(string $type = 'norm', int $count = 1, Player $player = null, int $cost = null, int $time = 0): Item {
        if (!$this->plugin->getConfig()->get('enable-coupon')) {
            $player->sendMessage(C::RED . str_replace('{name}', $player->getName(), Main::PREFIX . $this->messages->get('coupon-config-disabled')));
            return Item::get(0);
        }
        if ($player === null) {
            return Item::get(0);
        }
        $item = Item::get($this->plugin->getConfig()->get('coupon-item-id'));

        switch($type) {
            case 'norm':
                $item->setCustomName(str_replace('&', '§', $this->plugin->getConfig()->get('coupon-name')));
                $item->setNamedTagEntry(new StringTag('default', ''));
                $item->setCount($count);
                return $item;
            case 'temp':
                $item->setCustomName(str_replace('&', '§', $this->plugin->getConfig()->get('coupon-name')));
                $item->setNamedTagEntry(new StringTag('temporal', (string)$time)); 
                $item->setCount($count);
                return $item;
        }
        return Item::get(0);
    }
    
    /**
     * enableCoupon
     *
     * @return void
     */
    public function enableCoupon(): void {
        if ($this->plugin->getConfig()->get('enable-coupon')) {
            if ($this->plugin->getConfig()->get('coupon-creative-item')) {
                Item::addCreativeItem($this->getCouponItem());
            }
        }
    }
        
    /**
     * checkConfiguration
     *
     * @return void
     */
    public function checkConfiguration(): void {
        if ($this->plugin->getConfig()->get('enable-fly-particles')) {
            $this->plugin->getScheduler()->scheduleRepeatingTask(new ParticleTask($this->plugin, $this), $this->plugin->getConfig()->get('fly-particle-rate'));
        }

        if ($this->plugin->getConfig()->get('enable-fly-effects')) {
            $this->plugin->getScheduler()->scheduleRepeatingTask(new EffectTask($this->plugin), $this->plugin->getConfig()->get('fly-effect-check-rate'));
        }

        if ($this->plugin->getConfig()->get('time-fly')) {
            $this->plugin->getScheduler()->scheduleRepeatingTask(new FlightDataTask($this->plugin, $this), 20);
        }
        
        if ($this->plugin->getConfig()->get('fly-speed-mod')) {
            if ($this->plugin->getConfig()->get('fly-speed') > 3) {
                $this->plugin->getLogger()->warning('The fly speed limit is 3! The fly speed modification will be turned off.');
                return;
            }
            $this->plugin->getScheduler()->scheduleRepeatingTask(new FlightSpeedTask($this->plugin), $this->plugin->getConfig()->get('fly-speed-check-rate'));
        }
    }
    
    /**
     * checkDepend
     *
     * @return bool
     */
    public function checkDepend(): bool {
        if ($this->plugin->getServer()->getPluginManager()->getPlugin('EconomyAPI') === null && $this->plugin->getConfig()->get('pay-for-fly') && $this->plugin->getConfig()->get('enable-fly-ui')) {
            $this->plugin->getLogger()->warning('EconomyAPI not found while pay-for-fly is turned on!');
            $this->plugin->getServer()->getPluginManager()->disablePlugin($this->plugin);
            return false;
        }
        return true;
    }
    
    /**
     * checkIncompatible
     *
     * @return bool
     */
    public function checkIncompatible(): bool {
        if ($this->plugin->getServer()->getPluginManager()->getPlugin('BlazinFly') !== null) {
            $this->plugin->getLogger()->warning('FlyPE is not compatible with others fly plugins! (BlazinFly)');
            $this->plugin->getServer()->getPluginManager()->disablePlugin($this->plugin);
            return false;
        }
        return true;
    }
        
    /**
     * checkFiles
     *
     * @return bool
     */
    public function checkFiles(): bool {
        if (!is_dir($this->plugin->getDataFolder() . 'data/') || !is_dir($this->plugin->getDataFolder() . 'lang/') || !is_file($this->plugin->getDataFolder() . 'lang/' . $this->plugin->getConfig()->get('lang') . '.yml') || !is_file($this->plugin->getDataFolder() . 'config.yml') || !is_dir($this->plugin->getDataFolder())) {
            $this->plugin->getLogger()->warning('Detected a missing directory/file!');
            $this->plugin->getServer()->getPluginManager()->disablePlugin($this->plugin);
            return false;
        }
        return true;
    }
    
    /**
     * checkUpdates
     *
     * @return void
     */
    public function checkUpdates(): void {
        UpdateNotifier::checkUpdate($this->plugin->getDescription()->getName(), $this->plugin->getDescription()->getVersion());
    }
    
    /**
     * addDataDir
     *
     * @return void
     */
    public function addDataDir(): void {
        if (!is_dir($this->plugin->getDataFolder() . 'data/')) {
            mkdir($this->plugin->getDataFolder() . 'data');
        }
    }
    
    /**
     * registerPacketHooker
     *
     * @return void
     */
    public function registerPacketHooker(): void {
        if(!PacketHooker::isRegistered()) {
            PacketHooker::register($this->plugin);
        }
    }
    
    /**
     * checkLanguageFiles
     *
     * @return void
     */
    public function checkLanguageFiles(): void {
        if ($this->messages->get('lang-version') < 1) {
            $this->plugin->saveResource('lang/' . $this->plugin->getConfig()->get('lang') . '.yml');
            $this->messages = new Config($this->plugin->getDataFolder() . 'lang/' . $this->plugin->getConfig()->get('lang') . '.yml', Config::YAML);
        }
    }
    
    /**
     * checkGamemodeCreative
     *
     * @param  Entity $entity
     * @return bool
     */
    public function checkGamemodeCreative(Entity $entity): bool {
        // reason for using a function is cuz this will check both gamemode creative and player validity
        // (may need other checks in the future so why not)
        if ($entity instanceof Player && $entity->getGamemode() === Player::CREATIVE) {
            return true;
        }
        return false;
    }
    
    /**
     * checkGamemodeCreativeSetting
     *
     * @param  Entity $entity
     * @return bool
     */
    public function checkGamemodeCreativeSetting(Entity $entity): bool {
        if ($this->checkGamemodeCreative($entity) && $this->plugin->getConfig()->get('apply-flight-settings-gmc')) {
            return true;
        }
        return false;
    }
    
    /**
     * getFlightData
     *
     * @param  Player $player
     * @return mixed
     */
    public function getFlightData(Player $player, int $time) {
        return new FlightData($this->plugin, $this, $player->getName(), $time);
    }

    /**
     * getParticles
     *
     * @return mixed
     */
    public function getParticleList() {
        return new ParticleList();
    }
        
    /**
     * getSoundList
     *
     * @return mixed
     */
    public function getSoundList() {
        return new SoundList();
    }
}

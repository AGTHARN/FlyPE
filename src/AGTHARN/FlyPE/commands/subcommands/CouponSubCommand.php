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

namespace AGTHARN\FlyPE\commands\subcommands;

use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat as C;
use pocketmine\Player;

use AGTHARN\FlyPE\util\Util;
use AGTHARN\FlyPE\Main;

use CortexPE\Commando\args\RawStringArgument;
use CortexPE\Commando\args\IntegerArgument;
use CortexPE\Commando\BaseSubCommand;

class CouponSubCommand extends BaseSubCommand {

    /**
     * plugin
     *
     * @var Main
     */
    protected $plugin;

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
     * @param  string $name
     * @param  string $description
     * @param  array $aliases
     * @return void
     */
    public function __construct(Main $plugin, Util $util, string $name, string $description, $aliases = []) {
        $this->plugin = $plugin;
        $this->util = $util;
        
        parent::__construct($plugin, $name, $description, $aliases);
    }
    
    /**
     * prepare
     *
     * @return void
     */
    public function prepare(): void {
        $this->setPermission('flype.command.coupon');
        $this->registerArgument(0, new RawStringArgument('type', true));
        $this->registerArgument(1, new RawStringArgument('player', true));
        $this->registerArgument(2, new IntegerArgument('amount', true));
        $this->registerArgument(3, new IntegerArgument('time', true));
    }
    
    /**
     * onRun
     *
     * @param  CommandSender $sender
     * @param  string $aliasUsed
     * @param  array $args
     * @return void
     */
    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void {
        if (isset($args['type'])) {
            $type = substr(strtolower($args['type']), 0, 4);

            if ($type === 'norm' || $type === 'temp') {
                // nothing yet
            } else {
                $sender->sendMessage(C::RED . str_replace('{name}', $sender->getName(), Main::PREFIX . $this->util->messages->get('invalid-coupon-type')));
                return;
            }

            if ($type === 'temp' && empty($args['time'])) {
                $sender->sendMessage(C::RED . str_replace('{name}', $sender->getName(), Main::PREFIX . $this->util->messages->get('invalid-temp-argument')));
                return;
            }

            if (isset($args['player'])) {
                $arg = $args['player'];
                $count = $args['amount'] ?? 1;
    
                if (!$this->plugin->getServer()->getPlayer($arg) instanceof Player || empty($arg)) {
                    $sender->sendMessage(C::RED . str_replace('{name}', $arg, Main::PREFIX . $this->util->messages->get('player-cant-be-found')));
                    return;
                }
    
                $target = $this->plugin->getServer()->getPlayer($arg);
                $targetName = $target->getName();
    
                if (!$sender->hasPermission('flype.command.coupon')) {
                    $sender->sendMessage(C::RED . str_replace('{name}', $targetName, Main::PREFIX . $this->util->messages->get('no-permission')));
                    return;
                }
                    
                if ($this->plugin->getConfig()->get('coupon-command-toggle-item')) {
                    $time = $args['time'];

                    $target->getInventory()->addItem($this->util->getCouponItem($type, $count, $target, null, $time));
                }
            } else {
                if (!$sender instanceof Player) {
                    $sender->sendMessage('You can only use this command in-game!');
                    return;
                }
    
                if (!$sender->hasPermission('flype.command.coupon')) {
                    $sender->sendMessage(C::RED . str_replace('{name}', $sender, Main::PREFIX . $this->util->messages->get('no-permission')));
                    return;
                }
    
                if ($this->plugin->getConfig()->get('coupon-command-toggle-item')) {
                    $sender->getInventory()->addItem($this->util->getCouponItem($type, 1, $sender, null));
                }
            }
        } else {
            $sender->sendMessage(C::RED . str_replace('{name}', $sender->getName(), Main::PREFIX . $this->util->messages->get('invalid-coupon-type')));
        }
    }
}
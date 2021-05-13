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

namespace AGTHARN\FlyPE\commands;

use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat as C;
use pocketmine\Player;

use AGTHARN\FlyPE\commands\subcommands\ToggleSubCommand;
use AGTHARN\FlyPE\commands\subcommands\HelpSubCommand;
use AGTHARN\FlyPE\commands\subcommands\CouponSubCommand;
use AGTHARN\FlyPE\commands\subcommands\TempFlightSubCommand;
use AGTHARN\FlyPE\util\Util;
use AGTHARN\FlyPE\Main;

use CortexPE\Commando\BaseCommand;

class FlyCommand extends BaseCommand {

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
        $this->registerSubCommand(new ToggleSubCommand($this->plugin, $this->util, 'toggle', 'Toggles flight for others!'));
        $this->registerSubCommand(new HelpSubCommand($this->plugin, $this->util, 'help', 'Displays basic information about the plugin!'));
        $this->registerSubCommand(new CouponSubCommand($this->plugin, $this->util, 'coupon', 'Gives a flight coupon!'));
        $this->registerSubCommand(new TempFlightSubCommand($this->plugin, $this->util, 'tempflight', 'Toggles temporal flight!'));

        $this->setPermission('flype.command');
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
        if (!$sender instanceof Player) {
            $sender->sendMessage('You can only use this command in-game!');
            return;
        }

        if (!$sender->hasPermission('flype.command')) {
            $sender->sendMessage(C::RED . str_replace('{name}', $sender->getName(), Main::PREFIX . C::colorize($this->util->messages->get('no-permission'))));
            return;
        }

        if ($this->plugin->getConfig()->get('enable-fly-ui')) {
            $this->util->openFlyUI($sender);
            return;
        }
    
        if ($this->util->doLevelChecks($sender)) {
            $this->util->toggleFlight($sender);
        }
    }
}
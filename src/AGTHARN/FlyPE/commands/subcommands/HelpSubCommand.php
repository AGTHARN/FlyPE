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

use AGTHARN\FlyPE\util\Util;
use AGTHARN\FlyPE\Main;

use CortexPE\Commando\BaseSubCommand;

class HelpSubCommand extends BaseSubCommand {

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
        $this->setPermission('flype.command.help');
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
        if (!$sender->hasPermission('flype.command.help')) {
            $sender->sendMessage(C::RED . 'You do not have the permission to use this command!');
            return;
        }

        $sender->sendMessage(C::GRAY . '-=========[ ' . C::GREEN . 'FlyPE' . C::GRAY . ' ]=========-' . C::EOL . C::GOLD . 'Version: ' . $this->plugin->getDescription()->getVersion() . 
                        C::EOL . C::EOL . C::AQUA . '/fly - Toggles your flight!' . C::EOL . C::AQUA . '/fly help - Displays basic information about the plugin!'  . C::EOL . C::AQUA . 
                        '/fly toggle - Toggles flight for others!' . C::EOL . C::AQUA . '/fly coupon - Gives a flight coupon!' . C::EOL . C::AQUA . '/fly tempflight - Toggles temporal flight!');
    }
}
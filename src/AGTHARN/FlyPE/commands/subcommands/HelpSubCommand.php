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

namespace AGTHARN\FlyPE\commands\subcommands;

use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat as C;
use pocketmine\Player;

use AGTHARN\FlyPE\commands\FlyCommand;
use AGTHARN\FlyPE\util\Util;
use AGTHARN\FlyPE\Main;

use CortexPE\Commando\BaseSubCommand;

class HelpSubCommand extends BaseSubCommand {

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

    public function __construct(Main $plugin, Util $util, string $name, string $description, $aliases = []) {
        $this->plugin = $plugin;
        $this->util = $util;
        
        parent::__construct($name, $description, $aliases);
    }

    public function prepare(): void {
        $this->setPermission("flype.command.help");
	}

    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void {
        $sender->sendMessage("WIP");
	}
}
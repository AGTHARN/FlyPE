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

use AGTHARN\FlyPE\Main;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat as C;
use pocketmine\console\ConsoleCommandSender;

class MessageTranslator
{
    /** @var Main */
    private Main $plugin;

    /**
     * __construct
     *
     * @param  Main $plugin
     * @return void
     */
    public function __construct(Main $plugin)
    {
        $this->plugin = $plugin;
    }
    
    /**
     * sendTranslated
     *
     * @param  Player|ConsoleCommandSender $player
     * @param  string $message
     * @return void
     */
    public function sendTranslated(Player|ConsoleCommandSender $player, string $message): void
    {
        $message = C::colorize($this->plugin->translateTo($message, [], $player));
        $message = str_replace('{name}', $player->getName(), Main::PREFIX . $message);
        $player->sendMessage(C::RED . $message);
    }
}
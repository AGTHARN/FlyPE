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

namespace AGTHARN\FlyPE\command\subcommand;

use AGTHARN\FlyPE\Main;
use pocketmine\player\Player;
use AGTHARN\FlyPE\util\Flight;
use CortexPE\Commando\BaseSubCommand;
use pocketmine\command\CommandSender;
use AGTHARN\FlyPE\util\MessageTranslator;
use CortexPE\Commando\args\BooleanArgument;
use CortexPE\Commando\args\RawStringArgument;

class ToggleSubCommand extends BaseSubCommand
{
    /** @var Flight */
    private Flight $flight;
    /** @var MessageTranslator */
    private MessageTranslator $messageTranslator;
    
    /**
     * __construct
     *
     * @param  Main $plugin
     * @param  Flight $flight
     * @param  MessageTranslator $messageTranslator
     * @param  string $name
     * @param  string $description
     * @param  array $aliases
     * @return void
     */
    public function __construct(Main $plugin, Flight $flight, MessageTranslator $messageTranslator, string $name, string $description, $aliases = [])
    {
        $this->flight = $flight;
        $this->messageTranslator = $messageTranslator;
        
        parent::__construct($plugin, $name, $description, $aliases);
    }
    
    /**
     * prepare
     *
     * @return void
     */
    public function prepare(): void
    {
        $this->setPermission('flype.command.others');
        $this->registerArgument(0, new RawStringArgument('player', false));
        $this->registerArgument(1, new BooleanArgument('toggleMode', true));
    }
    
    /**
     * onRun
     *
     * @param  CommandSender $sender
     * @param  string $aliasUsed
     * @param  array $args
     * @return void
     */
    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void
    {
        if (isset($args['player'])) {
            $arg = $args['player'];
            $toggleMode = $args['toggleMode'] ?? null;
            if (!$this->plugin->getServer()->getPlayerByPrefix($arg) instanceof Player || empty($arg)) {
                $this->messageTranslator->sendTranslated($sender, 'command.invalid.player');
                return;
            }
            if (!$sender->hasPermission('flype.command.others')) {
                $this->messageTranslator->sendTranslated($sender, 'command.no.permission');
                return;
            }
                
            $target = $this->plugin->getServer()->getPlayerByPrefix($arg);
            if ($this->flight->toggleFlight($target, $toggleMode)) {
                if ($target->getAllowFlight()) {
                    $this->messageTranslator->sendTranslated($sender, 'flight.other.on');
                    return;
                }
                $this->messageTranslator->sendTranslated($sender, 'flight.other.off');
            }
            return;
        }
        $this->sendUsage();
    }
}
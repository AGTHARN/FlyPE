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

namespace AGTHARN\FlyPE\command;

use AGTHARN\FlyPE\Main;
use pocketmine\player\Player;
use AGTHARN\FlyPE\util\Flight;
use CortexPE\Commando\BaseCommand;
use pocketmine\command\CommandSender;
use AGTHARN\FlyPE\util\MessageTranslator;
use CortexPE\Commando\args\BooleanArgument;
use AGTHARN\FlyPE\command\subcommand\ToggleSubCommand;

class FlyCommand extends BaseCommand
{
    /** @var Flight */
    private Flight $flight;
    /** @var MessageTranslator */
    private MessageTranslator $translator;
    
    /**
     * __construct
     *
     * @param  Main $plugin
     * @param  Flight $flight
     * @param  MessageTranslator $translator
     * @param  string $name
     * @param  string $description
     * @param  array $aliases
     * @return void
     */
    public function __construct(Main $plugin, Flight $flight, MessageTranslator $translator, string $name, string $description, array $aliases = [])
    {
        $this->flight = $flight;
        $this->translator = $translator;
        
        parent::__construct($plugin, $name, $description, $aliases);
    }
    
    /**
     * prepare
     *
     * @return void
     */
    public function prepare(): void
    {
        $this->setPermission('flype.command');
        $this->registerArgument(0, new BooleanArgument('toggleMode', true));

        $this->registerSubCommand(new ToggleSubCommand($this->plugin, $this->flight, $this->translator, 'toggle', 'Toggles flight for others!'));
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
        $toggleMode = $args['toggleMode'] ?? null;
        if (!$sender instanceof Player) {
            $this->translator->sendTranslated($sender, 'command.not.player');
            return;
        }
        if (!$sender->hasPermission('flype.command')) {
            $this->translator->sendTranslated($sender, 'command.no.permission');
            return;
        }
        $this->flight->toggleFlight($sender, $toggleMode);
    }
}
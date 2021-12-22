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

declare(strict_types=1);

namespace AGTHARN\FlyPE\command;

use AGTHARN\FlyPE\Main;
use pocketmine\player\Player;
use AGTHARN\FlyPE\util\Flight;
use CortexPE\Commando\BaseCommand;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat as C;
use AGTHARN\FlyPE\session\SessionManager;
use CortexPE\Commando\args\BooleanArgument;
use AGTHARN\FlyPE\command\subcommand\CapeSubCommand;
use AGTHARN\FlyPE\command\subcommand\SoundSubCommand;
use AGTHARN\FlyPE\command\subcommand\EffectSubCommand;
use AGTHARN\FlyPE\command\subcommand\ReloadSubCommand;
use AGTHARN\FlyPE\command\subcommand\ToggleSubCommand;
use AGTHARN\FlyPE\command\subcommand\ParticleSubCommand;

/**
 * @property Main $plugin
 */
class FlyCommand extends BaseCommand
{
    /** @var SessionManager */
    private SessionManager $sessionManager;
    /** @var Flight */
    private Flight $flight;

    /**
     * __construct
     *
     * @param  Main $plugin
     * @param  string $name
     * @param  string $description
     * @param  array $aliases
     * @return void
     */
    public function __construct(Main $plugin, string $name, string $description, array $aliases = [])
    {
        $this->sessionManager = $plugin->sessionManager;
        $this->flight = $plugin->flight;

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

        $this->registerSubCommand(new ToggleSubCommand($this->plugin, 'toggle', 'Toggles flight for others!'));
        $this->registerSubCommand(new SoundSubCommand($this->plugin, 'sound', 'Configure your flight toggle sound!'));
        $this->registerSubCommand(new ParticleSubCommand($this->plugin, 'particle', 'Configure your flight particles while flying!'));
        $this->registerSubCommand(new EffectSubCommand($this->plugin, 'effect', 'Configure your flight effects while flying!'));
        $this->registerSubCommand(new CapeSubCommand($this->plugin, 'cape', 'Configure your flight cape while flying!'));
        $this->registerSubCommand(new ReloadSubCommand($this->plugin, 'reload', 'Reload your configuration while server is on!'));
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
            $sender->sendMessage(C::colorize(Main::PREFIX . $this->plugin->translateTo('flype.command.not.player', [], $sender)));
            return;
        }

        $senderSession = $this->sessionManager->getSessionByPlayer($sender);
        if (!$this->testPermissionSilent($sender)) {
            $senderSession->sendTranslated('flype.command.no.permission');
            return;
        }
        if ($this->plugin->config->getConfig('flight', 'enable-flight-ui')) {
            $this->plugin->flightForm->openFlightForm($sender);
            return;
        }
        $this->flight->toggleFlight($sender, $toggleMode);
    }
}

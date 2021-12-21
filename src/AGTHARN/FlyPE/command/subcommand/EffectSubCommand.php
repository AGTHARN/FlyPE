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

namespace AGTHARN\FlyPE\command\subcommand;

use AGTHARN\FlyPE\Main;
use pocketmine\player\Player;
use CortexPE\Commando\BaseSubCommand;
use pocketmine\command\CommandSender;
use AGTHARN\FlyPE\util\form\EffectForm;
use AGTHARN\FlyPE\session\SessionManager;

class EffectSubCommand extends BaseSubCommand
{
    /** @var SessionManager */
    private SessionManager $sessionManager;
    /** @var EffectForm */
    private EffectForm $effectForm;

    /**
     * __construct
     *
     * @param  Main $plugin
     * @param  string $name
     * @param  string $description
     * @param  array $aliases
     * @return void
     */
    public function __construct(Main $plugin, string $name, string $description, $aliases = [])
    {
        $this->sessionManager = $plugin->sessionManager;
        $this->effectForm = $plugin->effectForm;
        
        parent::__construct($plugin, $name, $description, $aliases);
    }

    /**
     * prepare
     *
     * @return void
     */
    public function prepare(): void
    {
        $this->setPermission('flype.command.effect');
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
        if (!$sender instanceof Player) {
            $this->plugin->util->sendTranslated($sender, 'flype.command.not.player');
            return;
        }

        $senderSession = $this->sessionManager->getSessionByPlayer($sender);
        if (!$this->testPermissionSilent($sender)) {
            $senderSession->sendTranslated('flype.command.no.permission');
            return;
        }
        if (!$this->plugin->config->getConfig('cosmetic', 'enable-effect')) {
            $senderSession->sendTranslated('flype.effects.allowed.false');
            return;
        }
        $this->effectForm->openEffectForm($sender);
    }
}

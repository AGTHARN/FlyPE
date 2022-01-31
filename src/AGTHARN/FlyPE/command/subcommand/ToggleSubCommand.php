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
use AGTHARN\FlyPE\util\Flight;
use CortexPE\Commando\BaseSubCommand;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat as C;
use AGTHARN\FlyPE\session\SessionManager;
use CortexPE\Commando\args\RawStringArgument;

/**
 * @property Main $plugin
 */
class ToggleSubCommand extends BaseSubCommand
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
    public function __construct(Main $plugin, string $name, string $description, $aliases = [])
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
        $this->setPermission('flype.command.others');
        $this->registerArgument(0, new RawStringArgument('playerName', false));
        $this->registerArgument(1, new RawStringArgument('toggleMode', false));
        $this->registerArgument(2, new RawStringArgument('flightTime', true));
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
        $playerName = $args['playerName'];
        $toggleMode = preg_match("/(on|yes|true|enable|yea|okay|affirmative|confirm|sure|granted|yep)/i", $args['toggleMode']);
        $flightTime = isset($args['flightTime']) ? $this->parseDuration($args['flightTime']) : null;
        if (!$this->plugin->getServer()->getPlayerByPrefix($playerName) instanceof Player) {
            $sender->sendMessage(C::colorize(Main::PREFIX . $this->plugin->translateTo('flype.command.invalid.player', [], $sender)));
            return;
        }

        $senderSession = $this->sessionManager->getSessionByPlayer($sender);
        if (!$this->testPermissionSilent($sender)) {
            $senderSession->sendTranslated('flype.command.no.permission');
            return;
        }

        $target = $this->plugin->getServer()->getPlayerByPrefix($playerName);
        if ($this->flight->toggleFlight($target, $toggleMode, $flightTime)) {
            if ($target->getAllowFlight()) {
                $senderSession->sendTranslated('flype.flight.other.on');
                return;
            }
            $senderSession->sendTranslated('flype.flight.other.off');
        }
        return;
    }

    /**
     * Code extracted from adeynes/parsecmd
     * (https://github.com/adeynes/parsecmd)
     * 
     * @param string $duration Must be of the form [ay][bM][cw][dd][eh][fm] with a, b, c, d, e, f integers
     * @return int UNIX timestamp corresponding to the duration (1y will return the timestamp one year from now)
     * @throws \InvalidArgumentException If the duration is invalid
     */
    public function parseDuration(string $duration): int
    {
        $time_units = ['y' => 'year', 'M' => 'month', 'w' => 'week', 'd' => 'day', 'h' => 'hour', 'm' => 'minute'];
        $regex = '/^([0-9]+y)?([0-9]+M)?([0-9]+w)?([0-9]+d)?([0-9]+h)?([0-9]+m)?$/';
        $matches = [];
        $is_matching = preg_match($regex, $duration, $matches);
        if (!$is_matching) {
            throw new \InvalidArgumentException("Invalid duration passed to CommandParser::parseDuration(). Must be of the form [ay][bM][cw][dd][eh][fm] with a, b, c, d, e, f integers");
        }

        $time = '';
        foreach ($matches as $index => $match) {
            if ($index === 0 || strlen($match) === 0) continue; // index 0 is the full match
            $n = substr($match, 0, -1);
            $unit = $time_units[substr($match, -1)];
            $time .= "$n $unit ";
        }
        $time = trim($time);
        return $time === '' ? time() : strtotime($time);
    }
}

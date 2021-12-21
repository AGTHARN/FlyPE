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

namespace AGTHARN\FlyPE\util\form;

use AGTHARN\FlyPE\Main;
use pocketmine\player\Player;
use AGTHARN\FlyPE\util\Flight;
use jojoe77777\FormAPI\CustomForm;
use jojoe77777\FormAPI\SimpleForm;
use AGTHARN\FlyPE\session\SessionManager;

class FlightForm
{
    /** @var Main */
    private Main $plugin;

    /** @var SessionManager */
    private SessionManager $sessionManager;
    /** @var Flight */
    private Flight $flight;

    /** @var array */
    private array $flightButtons = [];

    /**
     * __construct
     *
     * @param  Main $plugin
     * @return void
     */
    public function __construct(Main $plugin)
    {
        $this->plugin = $plugin;

        $this->sessionManager = $plugin->sessionManager;
        $this->flight = $plugin->flight;
    }

    /**
     * openFlightForm
     *
     * @param Player $player
     * @return void
     */
    public function openFlightForm(Player $player): void
    {
        $playerSession = $this->sessionManager->getSessionByPlayer($player);
        $form = new SimpleForm(function (Player $player, ?int $data) {
            if ($data === null) return;
            $result = $this->flightButtons[$player->getName()][$data];

            switch ($result) {
                case 'flype.command':
                    $this->flight->toggleFlight($player);
                    break;
                case 'flype.command.others':
                    $this->openFlightOthersForm($player);
                    break;
                case 'flype.command.sound':
                    $this->plugin->soundForm->openSoundForm($player);
                    break;
                case 'flype.command.particle':
                    $this->plugin->particleForm->openParticleForm($player);
                    break;
                case 'flype.command.effect':
                    $this->plugin->effectForm->openEffectForm($player);
                    break;
                case 'flype.command.cape':
                    $this->plugin->capeForm->openCapeForm($player);
                    break;
            }
        });

        $form->setTitle($playerSession->getTranslated('flight.ui.title', false));
        $form->setContent($playerSession->getTranslated('flight.ui.content', false));

        $i = 0;
        foreach ($this->getMapping($player) as $text => $permission) {
            if ($player->hasPermission($permission)) {
                $this->flightButtons[$player->getName()][$i] = $permission;
                $form->addButton($text);
                $i++;
            }
        }
        $form->addButton($playerSession->getTranslated('ui.button.exit', false));

        $player->sendForm($form);
    }

    /**
     * openFlightOthersForm
     *
     * @param Player $player
     * @return void
     */
    public function openFlightOthersForm(Player $player): void
    {
        // NOTE: PERMISSION CHECK ALREADY IN openFlightForm()
        $playerSession = $this->sessionManager->getSessionByPlayer($player);
        $form = new CustomForm(function (Player $player, array $data) use ($playerSession) {
            if ($data[1] === null || $data[2] === null) return;
            $target = $this->plugin->getServer()->getPlayerByPrefix($data[1]) ?? null;

            if (!$target instanceof Player) {
                $playerSession->sendTranslated('flype.command.invalid.player', false);
                return;
            }
            $this->flight->toggleFlight($target, $data[2]);
        });

        $form->setTitle($playerSession->getTranslated('flight.ui.title', false));
        $form->addLabel($playerSession->getTranslated('flight.ui.other.content', false));
        $form->addInput($playerSession->getTranslated('flight.ui.other.input', false));
        $form->addToggle($playerSession->getTranslated('flight.ui.other.toggle', false), true);

        $player->sendForm($form);
    }

    /**
     * getMapping
     *
     * @param Player $player
     * @return array
     */
    public function getMapping(Player $player): array
    {
        $playerSession = $this->sessionManager->getSessionByPlayer($player);
        return [
            $playerSession->getTranslated('flight.ui.button.1', false) => 'flype.command',
            $playerSession->getTranslated('flight.ui.button.2', false) => 'flype.command.others',
            $playerSession->getTranslated('flight.ui.button.3', false) => 'flype.command.sound',
            $playerSession->getTranslated('flight.ui.button.4', false) => 'flype.command.particle',
            $playerSession->getTranslated('flight.ui.button.5', false) => 'flype.command.effect',
            $playerSession->getTranslated('flight.ui.button.6', false) => 'flype.command.cape'
        ];
    }
}

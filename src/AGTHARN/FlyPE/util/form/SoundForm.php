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
use jojoe77777\FormAPI\SimpleForm;
use AGTHARN\FlyPE\session\SessionManager;
use AGTHARN\FlyPE\util\sound\SoundManager;

class SoundForm
{
    /** @var Main */
    private Main $plugin;

    /** @var SessionManager */
    private SessionManager $sessionManager;
    /** @var SoundManager */
    private SoundManager $soundManager;
    
    /** @var array */
    private array $soundButtons = [];

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
        $this->soundManager = $plugin->soundManager;
    }

    /**
     * openSoundForm
     *
     * @param Player $player
     * @return void
     */
    public function openSoundForm(Player $player): void
    {
        $playerSession = $this->sessionManager->getSessionByPlayer($player);
        $form = new SimpleForm(function (Player $player, ?int $data) {
            if ($data === null) return;
            
            switch ($data) {
                case 0:
                    $this->openSelectSoundForm($player);
                    break;
                case 1:
                    $this->soundManager->removeFlightSound($player);
                    break;
            }
        });

        $form->setTitle($playerSession->getTranslated('sound.ui.title', false));
        $form->setContent($playerSession->getTranslated('sound.ui.content', false));
        $form->addButton($playerSession->getTranslated('sound.ui.button.1', false));
        $form->addButton($playerSession->getTranslated('sound.ui.button.2', false));
        $form->addButton($playerSession->getTranslated('ui.button.exit', false));

        $player->sendForm($form);
    }

    /**
     * openSelectSoundForm
     *
     * @param Player $player
     * @return void
     */
    public function openSelectSoundForm(Player $player): void
    {
        $playerSession = $this->sessionManager->getSessionByPlayer($player);
        $form = new SimpleForm(function (Player $player, ?int $data) {
            if ($data === null) return;
            $soundReal = $this->soundButtons[$player->getName()][$data] ?? null;

            if (is_string($soundReal)) {
                $this->plugin->soundManager->setFlightSound($player, $soundReal);
                unset($this->soundButtons[$player->getName()]);
            }
        });

        $form->setTitle($playerSession->getTranslated('sound.ui.title', false));
        $form->setContent($playerSession->getTranslated('sound.ui.select.content', false));
        
        $i = 0;
        foreach ($this->plugin->soundList->allSounds as $soundReal) {
            $this->soundButtons[$player->getName()][$i] = $soundReal;
            $form->addButton($soundReal);
            $i++;
        }
        $form->addButton($playerSession->getTranslated('ui.button.exit', false));

        $player->sendForm($form);
    }
}

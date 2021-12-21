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
use AGTHARN\FlyPE\util\effect\EffectManager;

class EffectForm
{
    /** @var Main */
    private Main $plugin;

    /** @var SessionManager */
    private SessionManager $sessionManager;
    /** @var EffectManager */
    private EffectManager $effectManager;
    
    /** @var array */
    private array $effectButtons = [];

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
        $this->effectManager = $plugin->effectManager;
    }

    /**
     * openEffectForm
     *
     * @param Player $player
     * @return void
     */
    public function openEffectForm(Player $player): void
    {
        $playerSession = $this->sessionManager->getSessionByPlayer($player);
        $form = new SimpleForm(function (Player $player, ?int $data) {
            if ($data === null) return;
            
            switch ($data) {
                case 0:
                    $this->openSelectEffectForm($player);
                    break;
                case 1:
                    $this->effectManager->removeFlightEffect($player);
                    break;
            }
        });

        $form->setTitle($playerSession->getTranslated('effect.ui.title', false));
        $form->setContent($playerSession->getTranslated('effect.ui.content', false));
        $form->addButton($playerSession->getTranslated('effect.ui.button.1', false));
        $form->addButton($playerSession->getTranslated('effect.ui.button.2', false));
        $form->addButton($playerSession->getTranslated('ui.button.exit', false));

        $player->sendForm($form);
    }

    /**
     * openSelectEffectForm
     *
     * @param Player $player
     * @return void
     */
    public function openSelectEffectForm(Player $player): void
    {
        $playerSession = $this->sessionManager->getSessionByPlayer($player);
        $form = new SimpleForm(function (Player $player, ?int $data) {
            if ($data === null) return;
            $effectReal = $this->effectButtons[$player->getName()][$data] ?? null;

            if (is_string($effectReal)) {
                $this->plugin->effectManager->setFlightEffect($player, $effectReal);
                unset($this->effectButtons[$player->getName()]);
            }
        });

        $form->setTitle($playerSession->getTranslated('effect.ui.title', false));
        $form->setContent($playerSession->getTranslated('effect.ui.select.content', false));
        
        $i = 0;
        foreach ($this->plugin->effectList->allEffects as $effectReal) {
            $this->effectButtons[$player->getName()][$i] = $effectReal;
            $form->addButton($effectReal);
            $i++;
        }
        $form->addButton('Exit');

        $player->sendForm($form);
    }
}

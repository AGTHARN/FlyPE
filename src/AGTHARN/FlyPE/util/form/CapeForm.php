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
use AGTHARN\FlyPE\util\cape\CapeManager;
use AGTHARN\FlyPE\session\SessionManager;

class CapeForm
{
    /** @var Main */
    private Main $plugin;

    /** @var SessionManager */
    private SessionManager $sessionManager;
    /** @var CapeManager */
    private CapeManager $capeManager;

    /** @var array */
    private array $capeButtons = [];

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
        $this->capeManager = $plugin->capeManager;
    }

    /**
     * openCapeForm
     *
     * @param Player $player
     * @return void
     */
    public function openCapeForm(Player $player): void
    {
        $playerSession = $this->sessionManager->getSessionByPlayer($player);
        $form = new SimpleForm(function (Player $player, ?int $data) {
            if ($data === null) return;

            switch ($data) {
                case 0:
                    $this->openSelectCapeForm($player);
                    break;
                case 1:
                    $this->capeManager->removeFlightCape($player);
                    break;
            }
        });

        $form->setTitle($playerSession->getTranslated('cape.ui.title', false));
        $form->setContent($playerSession->getTranslated('cape.ui.content', false));
        $form->addButton($playerSession->getTranslated('cape.ui.button.1', false));
        $form->addButton($playerSession->getTranslated('cape.ui.button.2', false));
        $form->addButton($playerSession->getTranslated('ui.button.exit', false));

        $player->sendForm($form);
    }

    /**
     * openSelectCapeForm
     *
     * @param Player $player
     * @return void
     */
    public function openSelectCapeForm(Player $player): void
    {
        $playerSession = $this->sessionManager->getSessionByPlayer($player);
        $form = new SimpleForm(function (Player $player, ?int $data) {
            if ($data === null) return;
            $capeReal = $this->capeButtons[$player->getName()][$data] ?? null;

            if (is_string($capeReal)) {
                $this->plugin->capeManager->setFlightCape($player, $capeReal);
                unset($this->capeButtons[$player->getName()]);
            }
        });

        $form->setTitle($playerSession->getTranslated('cape.ui.title', false));
        $form->setContent($playerSession->getTranslated('cape.ui.select.content', false));

        $i = 0;
        foreach ($this->plugin->capeList->allCapes as $capeReal => $cape) {
            $this->capeButtons[$player->getName()][$i] = $capeReal;
            $form->addButton($capeReal);
            $i++;
        }
        $form->addButton($playerSession->getTranslated('ui.button.exit', false));

        $player->sendForm($form);
    }
}

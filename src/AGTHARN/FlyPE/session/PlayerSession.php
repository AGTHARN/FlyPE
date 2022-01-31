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

namespace AGTHARN\FlyPE\session;

use AGTHARN\FlyPE\Main;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use AGTHARN\FlyPE\provider\Provider;

class PlayerSession
{
    /** @var Main */
    private Main $plugin;

    /** @var Player */
    private Player $player;
    /** @var Provider */
    private Provider $provider;

    /**
     * __construct
     *
     * @param Main $plugin
     * @param Player $player
     * @param Provider $provider
     * @return void
     */
    public function __construct(Main $plugin, Player $player, Provider $provider)
    {
        $this->plugin = $plugin;

        $this->player = $player;
        $this->provider = $provider;
    }

    /**
     * Sends the player a translated message based on the id given.
     *
     * @param string $id
     * @param bool $includePrefix
     * @return void
     */
    public function sendTranslated(string $id, bool $includePrefix = true): void
    {
        $this->getPlayer()->sendMessage($this->getTranslated($id, $includePrefix));
    }

    /**
     * Returns a translated message based on the id given.
     *
     * @param string $id
     * @param bool $includePrefix
     * @return string
     */
    public function getTranslated(string $id, bool $includePrefix = true): string
    {
        $message = TextFormat::colorize($this->plugin->translateTo($id, [], $this->getPlayer()));
        $message = $includePrefix ? Main::PREFIX . $message : $message;
        $message = str_replace('{name}', $this->getPlayer()->getName(), $message);
        return $message;
    }

    /**
     * Reduces money the player has for an economy plugin. Returns successful or not.
     *
     * @param float $amount
     * @return bool
     */
    public function reduceMoney(float $amount): bool
    {
        $economyPlugin = $this->plugin->economy->getEconomyPlugin() ?? null;
        if ($economyPlugin === null) {
            return false;
        }

        switch ($economyPlugin[0]) {
            case 'EconomyAPI':
                $economyPlugin[1]->reduceMoney($this->getPlayer(), $amount);
                return true;
            case 'BedrockEconomy':
                $economyPlugin[1]->getSessionManager()->getSession($this->getPlayer()->getName(), 0)->getCache()->subtractFromBalance(floor($amount));
                return true;
        }
        return false;
    }

    /**
     * Returns the player.
     *
     * @return Player
     */
    public function getPlayer(): Player
    {
        return $this->player;
    }

    /**
     * Returns the database provider for the player.
     *
     * @return Provider
     */
    public function getProvider(): Provider
    {
        return $this->provider;
    }
}

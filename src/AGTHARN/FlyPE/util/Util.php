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

namespace AGTHARN\FlyPE\util;

use AGTHARN\FlyPE\Main;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat as C;
use AGTHARN\FlyPE\util\trait\SimpleTrait;

class Util
{
    use SimpleTrait;

    /**
     * Schedules repeating tasks.
     *
     * @return void
     */
    public function prepareTasks(): void
    {
        $configs = [
            'tempFlight' => 'flight',
            'particle' => 'cosmetic',
            'effect' => 'cosmetic'
        ];
        foreach ($configs as $config => $file) {
            if ($this->plugin->config->getConfig($file, 'enable-' . $config)) {
                $taskName = "AGTHARN\\FlyPE\\task\\" . ucfirst($config . 'Task');
                $this->plugin->getScheduler()->scheduleRepeatingTask(new $taskName($this->plugin), $this->plugin->config->getConfig('technical', $config . '-task-tick'));
            }
        }
    }

    /**
     * Prepares cosmetics by excluding cosmetics based on the cosmetic config.
     *
     * @return void
     */
    public function prepareCosmetics(): void
    {
        $this->plugin->soundList->excludeSounds();
        $this->plugin->particleList->excludeParticles();
        $this->plugin->effectList->excludeEffects();
        $this->plugin->capeList->prepareCapes();
    }

    /**
     * Sends a translated message to a player.
     *
     * @param CommandSender $sender
     * @param string $id
     * @deprecated
     * @return void
     */
    public function sendTranslated(CommandSender $sender, string $id): void
    {
        $sender->sendMessage(C::RED . C::colorize(Main::PREFIX . $this->plugin->translateTo($id, [], $sender)));
    }
}

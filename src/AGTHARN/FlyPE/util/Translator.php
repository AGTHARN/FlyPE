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

namespace AGTHARN\FlyPE\util;

use RuntimeException;
use pocketmine\Player;
use AGTHARN\FlyPE\Main;
use pocketmine\utils\TextFormat as C;
use kim\present\lib\translator\Language;
use kim\present\lib\translator\traits\TranslatablePluginTrait;

class Translator
{
    use TranslatablePluginTrait;
    
    /** @var Main */
    private Main $plugin;

    /**
     * __construct
     *
     * @param  Main $plugin
     * @return void
     */
    public function __construct(Main $plugin)
    {
        $this->plugin = $plugin;
    }
    
    /**
     * sendTranslated
     *
     * @param  mixed $player
     * @param  mixed $str
     * @return void
     */
    public function sendTranslated(Player $player, string $str): void
    {
        $player->sendMessage(C::RED . str_replace('{name}', $player->getName(), Main::PREFIX . C::colorize($this->translateTo($str, [], $player))));
    }
    
    /**
     * loadLanguages
     *
     * @return array
     */
    public function loadLanguages(): array
    {  
        $languages = [];
        $path = $this->plugin->getDataFolder() . "locale/";  
        if (!is_dir($path)) {
            throw new RuntimeException("Language directory {$path} does not exist or is not a directory");  
        }

        foreach (scandir($path, SCANDIR_SORT_NONE) as $_ => $filename) {  
            if (!preg_match("/^([a-zA-Z]{3})\.ini$/", $filename, $matches) || !isset($matches[1])) {
                continue;
            }
            $languages[$matches[1]] = Language::fromFile($path . $filename, $matches[1]);  
        }  
        return $languages;  
    }  
    
    /**
     * loadDefaultLanguage
     *
     * @return Language|null
     */
    public function loadDefaultLanguage(): ?Language
    {  
        $resource = $this->plugin->getResource("locale/{$this->plugin->getServer()->getLanguage()->getLang()}.ini"); 
        if ($resource === null) {  
            foreach ($this->plugin->getResources() as $filePath => $info) {  
                if (!preg_match("/^locale\/([a-zA-Z]{3})\.ini$/", $filePath, $matches) || !isset($matches[1])) {
                    continue;
                }

                $locale = $matches[1];  
                $resource = $this->plugin->getResource($filePath);  
                if ($resource !== null) {
                    break;
                }
            }  
        }  
        if ($resource !== null) {  
            $contents = stream_get_contents($resource);  
            fclose($resource);  
            return Language::fromContents($contents, strtolower($locale));  
        }
        return null;  
    }  
    
    /**
     * saveDefaultLanguages
     *
     * @return void
     */
    public function saveDefaultLanguages(): void
    {
        foreach ($this->plugin->getResources() as $filePath => $info) {  
            if (preg_match("/^locale\/[a-zA-Z]{3}\.ini$/", $filePath)) {  
                $this->plugin->saveResource($filePath);  
            }  
        }  
    }  
}
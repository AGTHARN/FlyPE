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

namespace AGTHARN\FlyPE;

use RuntimeException;
use pocketmine\utils\Config;
use AGTHARN\FlyPE\util\Flight;
use pocketmine\plugin\PluginBase;
use AGTHARN\FlyPE\command\FlyCommand;
use pocketmine\utils\TextFormat as C;
use kim\present\lib\translator\Language;
use AGTHARN\FlyPE\util\MessageTranslator;
use kim\present\lib\translator\traits\TranslatablePluginTrait;

class Main extends PluginBase
{
    use TranslatablePluginTrait;
    
    /** @var Config */
    private Config $generalConfig;

    /** @var MessageTranslator */
    private MessageTranslator $messageTranslator;
    /** @var Flight */
    private Flight $flight;

    /** @var string */
    public const PREFIX = C::GRAY . "[" . C::GOLD . "FlyPE". C::GRAY . "] " . C::RESET;
    /** @var int */
    public const CONFIG_VERSION = 5;

    
    /**
     * onEnable
     *
     * @return void
     */
    public function onEnable(): void
    {
        $this->saveResource('config' . DIRECTORY_SEPARATOR . 'general.yml');
        $this->generalConfig = new Config($this->getDataFolder() . 'config' . DIRECTORY_SEPARATOR . 'general.yml', Config::YAML);
        
        $this->messageTranslator = new MessageTranslator($this);
        $this->flight = new Flight($this, $this->messageTranslator);

        $this->checkConfig();
        $this->saveDefaultLanguages();

        $this->getServer()->getPluginManager()->registerEvents(new EventListener($this, $this->flight), $this);
        $this->getServer()->getCommandMap()->register('flype', new FlyCommand($this, $this->flight, $this->messageTranslator, 'fly', 'Toggles your flight!'));
    }
    
    /**
     * checkConfig
     *
     * @return bool
     */
    private function checkConfig(): bool
    {
        if ($this->generalConfig->get('config-version') < self::CONFIG_VERSION) {
            $this->getLogger()->warning('Your config version is outdated. Running an automatic update!');
            $oldConfig = $this->generalConfig->getAll();

            unlink($this->generalConfig->getPath());
            $this->saveResource('config' . DIRECTORY_SEPARATOR . 'general.yml');
            
            $this->generalConfig->reload();
            foreach ($oldConfig as $config => $key) {
                if ($this->generalConfig->get($config) !== false) {
                    $this->generalConfig->set($config, $key);
                }
            }
            $this->generalConfig->reload();
            $this->getLogger()->warning('Automatic update completed! No reboot required.');
            return false;
        }
        if ($this->generalConfig->get('config-version') > self::CONFIG_VERSION) {
            $this->getLogger()->warning('Your config version is too new! Please use an older config!');
            $this->getServer()->getPluginManager()->disablePlugin($this);
            return false;
        }
        return true;
    }

    /**
     * loadLanguages
     *
     * @return array
     */
    public function loadLanguages(): array
    {  
        $languages = [];
        $path = $this->getDataFolder() . "locale/";  
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
        $resource = $this->getResource("locale/{$this->getServer()->getLanguage()->getLang()}.ini"); 
        $locale = 'eng';
        if ($resource === null) {  
            foreach ($this->getResources() as $filePath => $info) {  
                if (!preg_match("/^locale\/([a-zA-Z]{3})\.ini$/", $filePath, $matches) || !isset($matches[1])) {
                    continue;
                }

                $locale = $matches[1];  
                $resource = $this->getResource($filePath);  
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
        foreach ($this->getResources() as $filePath => $info) {  
            if (preg_match("/^locale\/[a-zA-Z]{3}\.ini$/", $filePath)) {  
                $this->saveResource($filePath);  
            }  
        }  
    }  
}

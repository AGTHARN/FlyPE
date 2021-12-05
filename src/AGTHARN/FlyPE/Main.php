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
    private Config $flightConfig;
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
        $this->saveAllResources();
        $this->prepareConfigs();
        
        $this->messageTranslator = new MessageTranslator($this);
        $this->flight = new Flight($this, $this->messageTranslator);

        $this->checkConfigs($this->generalConfig, $this->flightConfig);
        $this->saveDefaultLanguages();

        $this->getServer()->getPluginManager()->registerEvents(new EventListener($this, $this->flight, $this->messageTranslator), $this);
        $this->getServer()->getCommandMap()->register('flype', new FlyCommand($this, $this->flight, $this->messageTranslator, 'fly', 'Toggles your flight!'));
    }
    
    /**
     * saveAllResources
     *
     * @return void
     */
    public function saveAllResources(): void
    {
        $dir = $this->getFile() . 'resources' . DIRECTORY_SEPARATOR;
        foreach (new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($dir)) as $path => $file) {
            if (!$file->isFile()) {
                continue;
            }
            $this->saveResource(str_replace($dir, '', $path));
        }
    }
    
    /**
     * prepareConfigs
     *
     * @return void
     */
    public function prepareConfigs(): void
    {
        $this->generalConfig = new Config($this->getDataFolder() . 'config' . DIRECTORY_SEPARATOR . 'general.yml', Config::YAML);
        $this->flightConfig = new Config($this->getDataFolder() . 'config' . DIRECTORY_SEPARATOR . 'flight.yml', Config::YAML);
    }
    
    /**
     * checkConfigs
     *
     * @param  Config[] $configTypes
     * @return bool
     */
    private function checkConfigs(Config ...$configTypes): bool
    {
        $oldConfigVersion = $this->generalConfig->get('config-version');
        if ($oldConfigVersion < self::CONFIG_VERSION) {
            $this->getLogger()->warning('Your config version is outdated. Running an automatic update! v' . $oldConfigVersion);
            foreach ($configTypes as $configType) {
                $this->updateConfig($configType);
            }
            $this->getLogger()->warning('Automatic update completed! No reboot required.');
            return false;
        }
        if ($oldConfigVersion> self::CONFIG_VERSION) {
            $this->getLogger()->warning('Your config version is too new! Please delete your current config! v' . $oldConfigVersion);
            $this->getServer()->getPluginManager()->disablePlugin($this);
            return false;
        }
        return true;
    }
    
    /**
     * updateConfig
     *
     * @param  Config $configType
     * @return void
     */
    public function updateConfig(Config $configType): void
    {
        $originalConfig = $configType->getAll();

        $originalConfigPath = $configType->getPath();
        $oldConfigPath = basename($originalConfigPath) . '_OLD.yml';
        
        rename($originalConfigPath, $this->getDataFolder()  . 'config' . DIRECTORY_SEPARATOR . $oldConfigPath);
        $this->saveResource(str_replace($this->getDataFolder(), '', $originalConfigPath));
            
        $configType->reload();
        foreach ($originalConfig as $config => $key) {
            // General Config
            if ($config === 'config-version') {
                $configType->set('config-version', self::CONFIG_VERSION);
                continue;
            }
                
            if ($configType->get($config, null) !== null) {
                $configType->set($config, $key);
            }
        }
        $configType->reload();
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
        $locale = $this->generalConfig->get('lang', 'en_US');
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
    
    /**
     * getFile
     *
     * @return string
     */
    public function getFile(): string
    {
        return parent::getFile();
    }
}

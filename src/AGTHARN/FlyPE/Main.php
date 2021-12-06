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

namespace AGTHARN\FlyPE;

use pocketmine\utils\Config;
use AGTHARN\FlyPE\util\Flight;
use poggit\libasynql\libasynql;
use pocketmine\plugin\PluginBase;
use poggit\libasynql\DataConnector;
use AGTHARN\FlyPE\command\FlyCommand;
use pocketmine\utils\TextFormat as C;
use kim\present\lib\translator\Language;
use AGTHARN\FlyPE\session\SessionManager;
use AGTHARN\FlyPE\util\MessageTranslator;
use kim\present\lib\translator\traits\TranslatablePluginTrait;

class Main extends PluginBase
{
    use TranslatablePluginTrait;

    /** 
     * @var Config[] 
     * @see $this->generateConfigs()
     */
    public array $configs;

    /** @var DataConnector */
    public DataConnector $dataBase;

    /** @var SessionManager */
    public SessionManager $sessionManager;
    /** @var MessageTranslator */
    public MessageTranslator $messageTranslator;
    /** @var Flight */
    public Flight $flight;

    /** @var string */
    public const PREFIX = C::GRAY . "[" . C::GOLD . "FlyPE" . C::GRAY . "] " . C::RESET;
    /** @var float */
    public const CONFIG_VERSION = 5.10;

    /**
     * onEnable
     *
     * @return void
     */
    public function onEnable(): void
    {
        $this->saveAllResources();
        $this->generateConfigs();
        $this->initDataBase();

        $this->sessionManager = new SessionManager($this);
        $this->messageTranslator = new MessageTranslator($this);
        $this->flight = new Flight($this);

        $this->checkConfigs();
        $this->saveDefaultLanguages();

        $this->getServer()->getPluginManager()->registerEvents(new EventListener($this), $this);
        $this->getServer()->getCommandMap()->register('flype', new FlyCommand($this, 'fly', 'Toggles your flight!'));
    }

    /**
     * onDisable
     *
     * @return void
     */
    public function onDisable(): void
    {
        $this->dataBase->waitAll();
        $this->dataBase->close();
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
     * generateConfigs
     *
     * @return void
     */
    public function generateConfigs(): void
    {
        $configsPath = [];
        $dir = $this->getFile() . 'resources' . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR;
        foreach (new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($dir)) as $path => $file) {
            if (!$file->isFile()) {
                continue;
            }
            $configsPath[] = str_replace($this->getFile() . 'resources' . DIRECTORY_SEPARATOR, '', $path);
        }
        foreach ($configsPath as $configPath) {
            $this->configs[basename($configPath, '.yml')] = new Config($this->getDataFolder() . $configPath, Config::YAML);
        }
    }

    /**
     * checkConfigs
     *
     * @return bool
     */
    private function checkConfigs(): bool
    {
        $oldConfigVersion = $this->configs['general']->get('config-version', 1.00);
        if ($oldConfigVersion < self::CONFIG_VERSION) {
            $this->getLogger()->warning('Your config version is outdated. Running an automatic update! v' . $oldConfigVersion);
            $this->updateConfigs($this->configs);
            $this->updateLanguageFiles();
            $this->getLogger()->warning('Automatic update completed! No reboot required.');
            return false;
        }
        if ($oldConfigVersion > self::CONFIG_VERSION) {
            $this->getLogger()->warning('Your config version is too new! Please delete your current config! v' . $oldConfigVersion);
            $this->getServer()->getPluginManager()->disablePlugin($this);
            return false;
        }
        return true;
    }

    /**
     * updateConfigs
     *
     * @param  Config[] $configTypes
     * @return void
     */
    public function updateConfigs(array $configTypes): void
    {
        foreach ($configTypes as $configType) {
            $configType->save();
            
            $originalConfig = $configType->getAll();

            $originalConfigPath = $configType->getPath();
            $oldConfigPath = basename($originalConfigPath, '.yml') . '_OLD.yml';

            rename($originalConfigPath, $this->getDataFolder()  . 'config' . DIRECTORY_SEPARATOR . $oldConfigPath);
            $this->saveResource(str_replace($this->getDataFolder(), '', $originalConfigPath));

            $configType->reload();
            foreach ($originalConfig as $key => $value) {
                if ($key === 'config-version') {
                    $configType->set('config-version', self::CONFIG_VERSION);
                    continue;
                }
                if ($configType->get($key, null) !== null) {
                    $configType->set($key, $value);
                }
            }
            $configType->save();
            $configType->reload();
        }
    }
    
    /**
     * updateLanguageFiles
     *
     * @return void
     */
    public function updateLanguageFiles(): void
    {
        $dir = $this->getFile() . 'resources' . DIRECTORY_SEPARATOR . 'locale' . DIRECTORY_SEPARATOR;
        foreach (new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($dir)) as $path => $file) {
            if (!$file->isFile()) {
                continue;
            }
            rename($path, $dir . basename($path, '.ini') . '_OLD.ini');
            $this->saveResource(str_replace($dir, '', $path));
        }
    }

    /**
     * Initiates the Databases
     *
     * @return void
     */
    public function initDataBase(): void
    {
        $dataBase = libasynql::create($this, $this->configs['general']->get('database'), [
            'sqlite' => 'database_stmts' . DIRECTORY_SEPARATOR . 'sqlite.sql',
            'mysql' => 'database_stmts' . DIRECTORY_SEPARATOR . 'mysql.sql'
        ]);
        $dataBase->executeGeneric('flype.init');
        $dataBase->waitAll();
        $this->dataBase = $dataBase;
    }

    /**
     * Load default language from plugin resources
     *
     * @return Language|null
     */
    public function loadDefaultLanguage(): ?Language
    {
        $locale = $this->configs['general']->get('lang', 'en_US');
        $resource = $this->getResource("locale/{$locale}.ini");
        if ($resource === null) {
            foreach ($this->getResources() as $filePath => $info) {
                if (!preg_match("/^locale\/([a-zA-Z]{3})\.ini$/", $filePath, $matches) || !isset($matches[1]))
                    continue;

                $locale = $matches[1];
                $resource = $this->getResource($filePath);
                if ($resource !== null)
                    break;
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
     * getFile
     *
     * @return string
     */
    public function getFile(): string
    {
        return parent::getFile();
    }
}

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
use pocketmine\utils\Config as ConfigPM;
use AGTHARN\FlyPE\util\trait\SimpleTrait;

class Config
{
    use SimpleTrait;

    /** @var ConfigPM[] */
    public array $configs;

    /**
     * Returns a config value in a file.
     * Look at generateConfigs() for file name.
     *
     * @param string $fileName
     * @param string $configName
     * @return mixed
     */
    public function getConfig(string $fileName, string $configName): mixed
    {
        if ($this->configs[$fileName] instanceof ConfigPM) {
            $config = $this->configs[$fileName]->getNested($configName);
            if ($config === false) {
                return $this->getConfigDefault($fileName, $configName);
            }
            return $config;
        }
    }

    /**
     * Returns the default config value in the plugin.
     * Used as a backup value for config values.
     *
     * @param string $fileName
     * @param string $configName
     * @return mixed
     */
    public function getConfigDefault(string $fileName, string $configName): mixed
    {
        $filePath = $this->plugin->getFile() . 'resources' . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . $fileName . '.yml';
        $tempConfig = new ConfigPM($filePath, ConfigPM::YAML);
        return $tempConfig->getNested($configName);
    }

    /**
     * Generates all the configs in plugin data automatically.
     *
     * @return void
     */
    public function generateConfigs(): void
    {
        $configsPath = [];
        $dir = $this->plugin->getFile() . 'resources' . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR;
        foreach (new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($dir)) as $path => $file) {
            if (!$file->isFile()) {
                continue;
            }
            $configsPath[] = str_replace($this->plugin->getFile() . 'resources' . DIRECTORY_SEPARATOR, '', $path);
        }
        foreach ($configsPath as $configPath) {
            $this->configs[basename($configPath, '.yml')] = new ConfigPM($this->plugin->getDataFolder() . $configPath, ConfigPM::YAML);
        }
    }

    /**
     * Checks the version of the config files and takes action.
     *
     * @return bool
     */
    public function checkConfigs(): bool
    {
        // will not use $this->getConfig btw
        $oldConfigVersion = $this->configs['technical']->get('config-version', 1.00);
        if ($oldConfigVersion < Main::CONFIG_VERSION) {
            $this->plugin->getLogger()->warning('Your config version is outdated. Running an automatic update! v' . $oldConfigVersion);
            $this->updateConfigs($this->configs);
            $this->updateLanguageFiles();
            $this->plugin->getLogger()->warning('Automatic update completed! No reboot required.');
            return false;
        }
        if ($oldConfigVersion > Main::CONFIG_VERSION) {
            $this->plugin->getLogger()->warning('Your config version is too new! Please delete your current config! v' . $oldConfigVersion);
            $this->plugin->getServer()->getPluginManager()->disablePlugin($this->plugin);
            return false;
        }
        return true;
    }

    /**
     * Update the configs to the configs in the plugin.
     *
     * @param  ConfigPM[] $configTypes
     * @return void
     */
    public function updateConfigs(array $configTypes): void
    {
        foreach ($configTypes as $configType) {
            $configType->save();

            $originalConfig = $configType->getAll();

            $originalConfigPath = $configType->getPath();
            $oldConfigPath = basename($originalConfigPath, '.yml') . '_OLD.yml';

            rename($originalConfigPath, $this->plugin->getDataFolder()  . 'config' . DIRECTORY_SEPARATOR . $oldConfigPath);
            $this->plugin->saveResource(str_replace($this->plugin->getDataFolder(), '', $originalConfigPath));

            $configType->reload();
            foreach ($originalConfig as $key => $value) {
                if ($key === 'config-version') {
                    $configType->set('config-version', Main::CONFIG_VERSION);
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
     * Updates the languages to the languages in the plugin.
     *
     * @return void
     */
    public function updateLanguageFiles(): void
    {
        $dir = $this->plugin->getFile() . 'resources' . DIRECTORY_SEPARATOR . 'locale' . DIRECTORY_SEPARATOR;
        foreach (new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($dir)) as $path => $file) {
            if (!$file->isFile()) {
                continue;
            }
            rename($path, $dir . basename($path, '.ini') . '_OLD.ini');
            $this->plugin->saveResource(str_replace($dir, '', $path));
        }
    }

    /**
     * Saves all resources/files in the plugin data folder.
     *
     * @return void
     */
    public function saveAllResources(): void
    {
        $dir = $this->plugin->getFile() . 'resources' . DIRECTORY_SEPARATOR;
        foreach (new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($dir)) as $path => $file) {
            if (!$file->isFile()) {
                continue;
            }
            $this->plugin->saveResource(str_replace($dir, '', $path));
        }
    }

    /**
     * Reloads all the configs.
     *
     * @return void
     */
    public function reloadConfigs(): void
    {
        foreach ($this->configs as $config) {
            $config->reload();
        }
        $this->plugin->util->prepareCosmetics();
    }
}

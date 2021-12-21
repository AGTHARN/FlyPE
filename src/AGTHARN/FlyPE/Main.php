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

use AGTHARN\FlyPE\util\Util;
use AGTHARN\FlyPE\util\Config;
use AGTHARN\FlyPE\util\Flight;
use AGTHARN\FlyPE\util\Economy;
use AGTHARN\FlyPE\EventListener;
use AGTHARN\FlyPE\util\Database;
use pocketmine\plugin\PluginBase;
use AGTHARN\FlyPE\util\Integration;
use AGTHARN\FlyPE\command\FlyCommand;
use AGTHARN\FlyPE\util\cape\CapeList;
use AGTHARN\FlyPE\util\form\CapeForm;
use pocketmine\utils\TextFormat as C;
use AGTHARN\FlyPE\task\TempFlightTask;
use AGTHARN\FlyPE\util\form\SoundForm;
use AGTHARN\FlyPE\util\form\EffectForm;
use AGTHARN\FlyPE\util\form\FlightForm;
use AGTHARN\FlyPE\util\sound\SoundList;
use AGTHARN\FlyPE\util\cape\CapeManager;
use kim\present\lib\translator\Language;
use AGTHARN\FlyPE\session\SessionManager;
use AGTHARN\FlyPE\util\effect\EffectList;
use AGTHARN\FlyPE\util\form\ParticleForm;
use AGTHARN\FlyPE\util\sound\SoundManager;
use AGTHARN\FlyPE\util\effect\EffectManager;
use AGTHARN\FlyPE\util\particle\ParticleList;
use AGTHARN\FlyPE\util\particle\ParticleManager;
use kim\present\lib\translator\traits\TranslatablePluginTrait;

class Main extends PluginBase
{
    use TranslatablePluginTrait;

    /** @var Util */
    public Util $util;
    /** @var Config */
    public Config $config;
    /** @var Database */
    public Database $dataBase;

    /** @var Economy */
    public Economy $economy;
    /** @var SessionManager */
    public SessionManager $sessionManager;
    /** @var Integration */
    public Integration $integration;
    /** @var Flight */
    public Flight $flight;

    /** @var SoundList */
    public SoundList $soundList;
    /** @var SoundManager */
    public SoundManager $soundManager;
    /** @var ParticleList */
    public ParticleList $particleList;
    /** @var ParticleManager */
    public ParticleManager $particleManager;
    /** @var EffectList */
    public EffectList $effectList;
    /** @var EffectManager */
    public EffectManager $effectManager;
    /** @var CapeList */
    public CapeList $capeList;
    /** @var CapeManager */
    public CapeManager $capeManager;

    /** @var FlightForm */
    public FlightForm $flightForm;
    /** @var SoundForm */
    public SoundForm $soundForm;
    /** @var ParticleForm */
    public ParticleForm $particleForm;
    /** @var EffectForm */
    public EffectForm $effectForm;
    /** @var CapeForm */
    public CapeForm $capeForm;

    /** @var string */
    public const PREFIX = C::GRAY . "[" . C::GOLD . "FlyPE" . C::GRAY . "] " . C::RESET;
    /** @var float */
    public const CONFIG_VERSION = 5.20;

    /**
     * onEnable
     *
     * @return void
     */
    public function onEnable(): void
    {
        $this->util = new Util($this);
        $this->config = new Config($this);
        $this->config->saveAllResources();
        $this->config->generateConfigs();
        $this->dataBase = new Database($this);
        $this->dataBase->initDB();

        $this->economy = new Economy($this);
        $this->sessionManager = new SessionManager($this);
        $this->integration = new Integration($this);
        $this->flight = new Flight($this);

        $this->soundList = new SoundList($this);
        $this->soundManager = new SoundManager($this);
        $this->particleList = new ParticleList($this);
        $this->particleManager = new ParticleManager($this);
        $this->effectList = new EffectList($this);
        $this->effectManager = new EffectManager($this);
        $this->capeList = new CapeList($this);
        $this->capeManager = new CapeManager($this);

        $this->flightForm = new FlightForm($this);
        $this->soundForm = new SoundForm($this);
        $this->particleForm = new ParticleForm($this);
        $this->effectForm = new EffectForm($this);
        $this->capeForm = new CapeForm($this);

        $this->config->checkConfigs();
        $this->saveDefaultLanguages();

        $this->getServer()->getPluginManager()->registerEvents(new EventListener($this), $this);
        $this->getServer()->getCommandMap()->register('flype', new FlyCommand($this, 'fly', 'Toggles your flight!'));

        $this->integration->checkFlightPlugins();
        $this->util->prepareTasks();
        $this->util->prepareCosmetics();
    }

    /**
     * onDisable
     *
     * @return void
     */
    public function onDisable(): void
    {
        $this->dataBase->runDisable();
    }

    /**
     * Load default language from plugin resources
     *
     * @return Language|null
     */
    public function loadDefaultLanguage(): ?Language
    {
        $locale = $this->config->getConfig('general', 'lang');
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

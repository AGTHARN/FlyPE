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

namespace AGTHARN\FlyPE\util\cape;

use AGTHARN\FlyPE\util\trait\SimpleTrait;

class CapeList
{
    use SimpleTrait;

    /** @var array */
    public array $allCapes = [];

    /**
     * prepareCapes
     *
     * @return void
     */
    public function prepareCapes(): void
    {
        $dir = $this->plugin->getDataFolder() . 'cape' . DIRECTORY_SEPARATOR;
        @mkdir($dir);
        /** @var \DirectoryIterator $file */
        foreach (new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($dir)) as $path => $file) {
            if (!$file->isFile()) {
                continue;
            }
            $fileName = $file->getBasename();
            if ($file->getExtension() !== 'png') {
                $notSuitable[] = $fileName;
                continue;
            }
            $this->allCapes[$file->getBasename('.png')] = $this->convertPngToCape($path);
        }
        if (isset($notSuitable)) {
            $this->plugin->getLogger()->alert('Unsupported image format for cape: ' . implode(', ', $notSuitable));
        }
    }

    /**
     * Code extracted from *robske110/PngToPlayer.php*
     * (https://gist.github.com/robske110/5f93a00b2dee86b83497c437edfe4451)
     *
     * @param string $path
     * @return string
     */
    public function convertPngToCape(string $path): string
    {
        $img = @imagecreatefrompng($path);
        $l = (int) @getimagesize($path)[1];
        $bytes = '';
        for ($y = 0; $y < $l; $y++) {
            for ($x = 0; $x < 64; $x++) {
                $rgba = @imagecolorat($img, $x, $y);
                $a = ((~((int)($rgba >> 24))) << 1) & 0xff;
                $r = ($rgba >> 16) & 0xff;
                $g = ($rgba >> 8) & 0xff;
                $b = $rgba & 0xff;
                $bytes .= chr($r) . chr($g) . chr($b) . chr($a);
            }
        }
        @imagedestroy($img);
        return $bytes;
    }
}

<?php

/*
 * Copyright (C) 2015 André Gregor-Herrmann
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

/**
 * Description of lvextmedia_oxpicturehandler
 *
 * @author Gate4Games
 * @author André Gregor-Herrmann
 */
class lvextmedia_oxpicturehandler extends lvextmedia_oxpicturehandler_parent {
    
    /**
     * Returns requested product picture url. If image is not available - returns url to nopic.jpg
     *
     * @param string $sPath  path from pictures/master/
     * @param string $sFile  picture file name
     * @param string $sSize  picture sizes (x, y)
     * @param string $sIndex picture index [optional]
     * @param bool   $bSsl   Whether to force SSL
     *
     * @return string | bool
     */
    public function getProductPicUrl($sPath, $sFile, $sSize, $sIndex = null, $bSsl = null)
    {
        if ( strpos($sFile, "http") !== false ) {
            $sUrl = $sFile;
        }
        else {
            $sUrl = parent::getProductPicUrl( $sPath, $sFile, $sSize, $sIndex, $bSsl );
        }
        
        return $sUrl;
    }
    
}

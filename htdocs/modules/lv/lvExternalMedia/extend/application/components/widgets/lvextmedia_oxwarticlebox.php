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
 * Description of lvextmedia_oxwarticlebox
 *
 * @author Gate4Games
 * @author André Gregor-Herrmann
 */
class lvextmedia_oxwarticlebox extends lvextmedia_oxwarticlebox_parent {
    /**
     * Returns the list image max height
     * 
     * @param void
     * @return string
     */
    public function lvGetListImageMaxHeight() {
        $oConfig = $this->getConfig();
        
        $sSize = $oConfig->getConfigParam( 'sThumbnailsize' );
        $aSize = explode( '*', $sSize );
        
        if ( is_array( $aSize ) && is_numeric( $aSize[0] ) && is_numeric( $aSize[1] ) ) {
            $sHeight  = $aSize[1];
        }
        else {
            // dummy standard default
            $sHeight  = '150';
        }
        
        return $sHeight;
    }


    /**
     * Returns the list image max width
     * 
     * @param void
     * @return string
     */
    public function lvGetListImageMaxWidth() {
        $oConfig = $this->getConfig();
        
        $sSize = $oConfig->getConfigParam( 'sThumbnailsize' );
        $aSize = explode( '*', $sSize );
        
        if ( is_array( $aSize ) && is_numeric( $aSize[0] ) && is_numeric( $aSize[1] ) ) {
            $sWidth   = $aSize[0];
        }
        else {
            // dummy standard default
            $sWidth   = '185';
        }
        
        return $sWidth;
    }
}

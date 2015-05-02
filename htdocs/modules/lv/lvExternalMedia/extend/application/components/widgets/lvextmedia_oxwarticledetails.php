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
 * Description of lvextmedia_oxwarticledetails
 *
 * @author Gate4Games
 * @author André Gregor-Herrmann
 */
class lvextmedia_oxwarticledetails extends lvextmedia_oxwarticledetails_parent {

    /**
     * Template variable getter. Returns youtube media file if there is one
     *
     * @param void
     * @return mixed
     */
    public function lvGetYouTubeMediaEmbed() {
        $sYouTubeEmbed = false;
        if ($this->_aMediaFiles === null) {
            $aMediaFiles = $this->getProduct()->getMediaUrls();
            // $this->_aMediaFiles = count($aMediaFiles) ? $aMediaFiles : false;
            foreach ( $aMediaFiles as $oMediaUrl ) {
                $sUrl = $this->oxmediaurls__oxurl->value;
                //youtube link
                if ( strpos( $sUrl, 'youtube.com' ) || strpos( $sUrl, 'youtu.be' ) ) {
                    $sYouTubeEmbed = $oMediaUrl->getHtml();
                    break;
                }
            }
        }

        return $sYouTubeEmbed;
    }
    
}

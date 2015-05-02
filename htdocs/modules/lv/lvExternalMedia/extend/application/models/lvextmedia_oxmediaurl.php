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
 * Description of lvextmedia_oxmediaurl
 *
 * @author Gate4Games
 * @author André Gregor-Herrmann
 */
class lvextmedia_oxmediaurl extends lvextmedia_oxmediaurl_parent {
    /**
     * Transforms the link to YouTube object, and returns it.
     *
     * @return string
     */
    protected function _getYoutubeHtml()
    {
        parent::_getYoutubeHtml();
               
        $sUrl = $this->oxmediaurls__oxurl->value;
        $oPictureHandler = oxNew( 'oxPictureHandler' );
        
        $oConfig = $this->getConfig();
        $sSize = $oConfig->getConfigParam( 'aDetailImageSizes' );
        $aSize = $this->getImageSize( $sSize, 1 );
        
        if ( is_array( $aSize ) && is_numeric( $aSize[0] ) && is_numeric( $aSize[1] ) ) {
            $sIFrameWidth   = $aSize[0];
            $sIFrameHeight  = $aSize[1];
        }
        else {
            $sIFrameWidth   = '425';
            $sIFrameHeight  = '344';
        }
                
        if (strpos($sUrl, 'youtube.com')) {
            $sYoutubeUrl = str_replace("www.youtube.com/watch?v=", "www.youtube.com/embed/", $sUrl);
            $sYoutubeUrl = preg_replace('/&amp;/', '?', $sYoutubeUrl, 1);
        }
        if (strpos($sUrl, 'youtu.be')) {
            $sYoutubeUrl = str_replace("youtu.be/", "www.youtube.com/embed/", $sUrl);
        }

        $sYoutubeTemplate = '%s<br><iframe width="'.$sIFrameWidth.'" height="'.$sIFrameHeight.'" src="%s" frameborder="0" allowfullscreen></iframe>';
        $sYoutubeHtml = sprintf($sYoutubeTemplate, $sDesc, $sYoutubeUrl, $sYoutubeUrl);

        return $sYoutubeHtml;
    }
}

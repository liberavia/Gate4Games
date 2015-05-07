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
     * ID to set into youtube iframe html
     * @var string
     */
    protected $_sLvIframeId = null;
    
    
    /**
     * Public setter that sets the id of an iframe
     * 
     * @param type $sIFrameId
     * @return void
     */
    public function lvSetIFrameId( $sIFrameId ) {
        $this->_sLvIframeId = $sIFrameId;
    }


    /**
     * Returns the youtube thumbnail of an video. Returns empty string, if media is no youtube video
     * 
     * @param void
     * @return string
     */
    public function lvGetYouTubeThumbnailUrl() {
        $sUrl = $this->oxmediaurls__oxurl->value;
        
        $sThumbnailUrl = "";
        if ( strpos( $sUrl, 'youtube.com' ) || strpos( $sUrl, 'youtu.be' ) ) {
            if ( strpos($sUrl, 'https') === false ) {
                $sProtocol = "http://";
            }
            else {
                $sProtocol = "https://";
            }
            $sVideoId = str_replace( $sProtocol."www.youtube.com/watch?v=", "", $sUrl );
            
            $sThumbnailUrl = $sProtocol."img.youtube.com/vi/".$sVideoId."/0.jpg";
        }
        
        return $sThumbnailUrl;
    }
    
    /**
     * Transforms the link to YouTube object, and returns it.
     *
     * @return string
     */
    protected function _getYoutubeHtml()
    {
        parent::_getYoutubeHtml();
               
        $sUrl = $this->oxmediaurls__oxurl->value;
        
        $oConfig = $this->getConfig();
        $aSizes = $oConfig->getConfigParam( 'aDetailImageSizes' );
        $aSize = explode('*', $aSizes['oxpic1']);
        
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

        $sFrameIdCode = '';
        if ( $this->_sLvIframeId !== null ) {
            $sFrameIdCode = 'id="'.$this->_sLvIframeId.'" style="display:none;"';
        }
        $sYoutubeTemplate = '%s<iframe '.$sFrameIdCode.' width="'.$sIFrameWidth.'" height="'.$sIFrameHeight.'" src="%s" frameborder="0" allowfullscreen></iframe>';
        $sYoutubeHtml = sprintf($sYoutubeTemplate, $sDesc, $sYoutubeUrl, $sYoutubeUrl);
        if ( $this->_sLvIframeId !== null ) {
            $this->_sLvIframeId = null;
        }

        return $sYoutubeHtml;
    }
}

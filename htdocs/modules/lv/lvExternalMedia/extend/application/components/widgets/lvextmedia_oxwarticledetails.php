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
     * All media elements of that article
     * @var array
     */
    protected $_aLvAllMedia = null;
    
    /**
     * Array of media objects
     * @var array
     */
    protected $_aLvMediaFiles = null;

    /**
     * Template variable getter. Returns youtube media file of given index if there is one
     *
     * @param void
     * @return mixed
     */
    public function lvGetYouTubeMediaEmbed( $iIndex=0 ) {
        $sYouTubeEmbed = false;

        if ($this->_aMediaFiles === null) {
            $this->_aLvMediaFiles = $this->getProduct()->getMediaUrls();
        }

        $iIteration = 0;
        foreach ( $this->_aLvMediaFiles as $oMediaUrl ) {
            $sUrl = $oMediaUrl->oxmediaurls__oxurl->value;
            //youtube link
            if ( strpos( $sUrl, 'youtube.com' ) || strpos( $sUrl, 'youtu.be' ) ) {
                $sYouTubeEmbed = $oMediaUrl->getHtml();
                if ( $iIteration == $iIndex ) {
                    break;
                }
                $iIteration++;
            }
        }

        return $sYouTubeEmbed;
    }
    
    
    /**
     * Template getter returns an array of all media (youtube video and images)
     * 
     * @param void
     * @return array
     */
    public function lvGetAllMedia() {
        $this->_aLvAllMedia = array();
        
        // first get all the youtube videos
        if ($this->_aMediaFiles === null) {
            $this->_aLvMediaFiles = $this->getProduct()->getMediaUrls();
        }

        foreach ( $this->_aLvMediaFiles as $oMediaUrl ) {
            if ( strpos( $sUrl, 'youtube.com' ) || strpos( $sUrl, 'youtu.be' ) ) {
                $aVideoMedia = array(
                    'mediatype' => 'youtube',
                    'embedurl'  => $oMediaUrl->getHtml(),
                );
                $this->_aLvAllMedia[] = $aVideoMedia;
            }
        }
        
        // next geet all the picture links
        $aExtPictureLinks = $this->_lvGetExtPictureLinks();
        
        
        return $this->_aLvAllMedia;
    }
    
    
    /**
     * Template getter returns if there is more media available then the first chosen
     * 
     * @param void
     * @return bool
     */
    public function lvHasMoreMedia() {
        if ( $this->_aLvAllMedia === null ) {
            $aAllMedia = $this->lvGetAllMedia();
        }
        else {
            $aAllMedia = $this->_aLvAllMedia;
        }
        
        $blReturn = false;
        if ( count( $aAllMedia ) > 1 ) {
            $blReturn = true;
        }
        
        return $blReturn;
    }
    
    
    
    /**
     * Returns an array of all external picture links
     * 
     * @param void
     * @return array
     */
    protected function _lvGetExtPictureLinks() {
        $aExtPicLinks = array();
        
        for ( $iIndex=1; $iIndex<=12; $iIndex++ ) {
            $sCurrentPicField = "oxarticles__oxpic".(string)$iIndex;
            $sCurrentPictureUrl = $this->$sCurrentPicField->value;
            
            // check if this is an external link picture
            if ( strpos( $sCurrentPictureUrl, 'http' ) !== false ) {
                $aExtPicLinks[] = $sCurrentPictureUrl;
            }
        }
        
        return $aExtPicLinks;
    }
}

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
 * Description of lvextmedia_oxarticle
 *
 * @author Gate4Games
 * @author André Gregor-Herrmann
 */
class lvextmedia_oxarticle extends lvextmedia_oxarticle_parent {
    
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
     * Public getter returns an array of all media (youtube video and images)
     * 
     * @param void
     * @return array
     */
    public function lvGetAllMedia( $blIncludePictures=true ) {
        $this->_aLvAllMedia = array();
        
        // first get all the youtube videos
        if ( $this->_aLvMediaFiles === null ) {
            $this->_aLvMediaFiles = $this->getMediaUrls();
        }
        
        // get sizes of icon
        $sIconSize = $this->getConfig()->getConfigParam( 'sIconsize' );
        if ( strpos( $sIconSize, "*" ) !== false ) {
            $aIconSize = explode( "*", $sIconSize );
            $sIconWidth     = trim( $aIconSize[0] );
            $sIconHeight    = trim( $aIconSize[0] );
        }
        else {
            // use dummy standard
            $sIconWidth     = '87';
            $sIconHeight    = '87';
        }

        $iVideoIndex = 1;
        foreach ( $this->_aLvMediaFiles as $oMediaUrl ) {
            $oMediaUrl->lvSetIFrameId( 'detailsvideoiframe_'.$iVideoIndex );
            $oMediaUrl->lvSetIFrameVisible(false);
            if ( $iVideoIndex == 1 ) {
                $oMediaUrl->lvSetIFrameVisible(true);
            }
            $sUrl = $oMediaUrl->getHtml();
            if ( strpos( $sUrl, 'youtube.com' ) || strpos( $sUrl, 'youtu.be' ) ) {
                $aVideoMedia = array(
                    'mediatype'     => 'youtube',
                    'index'         => $iVideoIndex,
                    'embedurl'      => $sUrl,
                    'url'           => $oMediaUrl->getLink(),
                    'iconurl'       => $oMediaUrl->lvGetYouTubeThumbnailUrl(),
                    'iconwidth'     => $sIconWidth,
                    'iconheight'    => $sIconHeight,
                );
                $this->_aLvAllMedia[] = $aVideoMedia;
                $iVideoIndex++;
            }
        }

        if ( $blIncludePictures ) {
            // next geet all the picture links
            $aExtPictureLinks = $this->_lvGetExtPictureLinks();

            foreach ( $aExtPictureLinks as $iIndex=>$sExtPictureLink ) {
                $aPicMedia = array(
                    'mediatype'     => 'extpic',
                    'index'         => $iIndex+1,
                    'detailsurl'    => $sExtPictureLink,
                    'iconurl'       => $sExtPictureLink,
                    'iconwidth'     => $sIconWidth,
                    'iconheight'    => $sIconHeight,
                );

                $this->_aLvAllMedia[] = $aPicMedia;
            }
        }
        
        return $this->_aLvAllMedia;
    }
    
    
    /**
     * Public getter returns first image entry of all media
     * 
     * @param void
     * @return string
     */
    public function lvGetFirstPictureUrl() {
        if ( $this->_aLvAllMedia === null ) {
            $aAllMedia = $this->lvGetAllMedia();
        }
        else {
            $aAllMedia = $this->_aLvAllMedia;
        }
        
        $sPicUrl = '';
        foreach ( $aAllMedia as $aCurrentMediaEntry ) {
            if ( $aCurrentMediaEntry['mediatype'] == 'extpic' ) {
                $sPicUrl = $aCurrentMediaEntry['detailsurl'];
                break;
            }
        }
        
        return $sPicUrl;
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

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
     * Returns the details image max height
     * 
     * @param void
     * @return string
     */
    public function lvGetDetailsImageMaxHeight() {
        $oConfig = $this->getConfig();
        $aSizes = $oConfig->getConfigParam( 'aDetailImageSizes' );
        $aSize = explode( '*', $aSizes['oxpic1'] );
        
        if ( is_array( $aSize ) && is_numeric( $aSize[0] ) && is_numeric( $aSize[1] ) ) {
            $sHeight  = $aSize[1];
        }
        else {
            // dummy standard default
            $sHeight  = '380';
        }
        
        return $sHeight;
    }


    /**
     * Returns the details image max width
     * 
     * @param void
     * @return string
     */
    public function lvGetDetailsImageMaxWidth() {
        $oConfig = $this->getConfig();
        $aSizes = $oConfig->getConfigParam( 'aDetailImageSizes' );
        $aSize = explode( '*', $aSizes['oxpic1'] );
        
        if ( is_array( $aSize ) && is_numeric( $aSize[0] ) && is_numeric( $aSize[1] ) ) {
            $sWidth   = $aSize[0];
        }
        else {
            // dummy standard default
            $sWidth   = '340';
        }
        
        return $sWidth;
    }

    
    /**
     * Template variable getter. Returns youtube media file of given index if there is one
     *
     * @param void
     * @return mixed
     */
    public function lvGetYouTubeMediaEmbed( $iIndex=0 ) {
        $sYouTubeEmbed = false;

        if ( $this->_aMediaFiles === null ) {
            $oProduct = $this->getProduct()->lvGetProduct();
            $this->_aLvMediaFiles = $oProduct->getMediaUrls();
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
    public function lvGetAllMedia( $blIncludePictures=true ) {
        $this->_aLvAllMedia = $this->getProduct()->lvGetAllMedia();
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
     * Template getter returns first image entry of all media
     * 
     * @param void
     * @return string
     */
    public function lvGetFirstPictureUrl() {
        return $this->getProduct()->lvGetFirstPictureUrl();
    }
    
    
    /**
     * Template getter returns coverpic of details article
     * 
     * @param void
     * @return string
     */
    public function lvGetCoverPictureUrl() {
        return $this->getProduct()->lvGetCoverPictureUrl();
    }
}

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
     * Wrapper method for getting the product object
     * 
     * @param void
     * @return object
     */
    public function lvGetProduct() {
        return $this;
    }
    

    
    /**
     * Public getter returns an array of all media (youtube video and images)
     * 
     * @param void
     * @return array
     */
    public function lvGetAllMedia( $blIncludePictures=true ) {
        $this->_aLvAllMedia = array();
        $oProduct = $this->lvGetProduct();
        
        
        // first get all the youtube videos
        if ( $this->_aLvMediaFiles === null ) {
            $this->_aLvMediaFiles = $oProduct->getMediaUrls();
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
            if ( $oMediaUrl->oxmediaurls__lvmediatype->value != 'productvideo' ) continue;
            
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
        
        // if this is still empty set link to nopic
        if ( $sPicUrl == '' ) {
            $sSize = $this->getConfig()->getConfigParam( 'aDetailImageSizes' );
            $sPicUrl = oxRegistry::get("oxPictureHandler")->getProductPicUrl( "product/1/", 'nopic.jpg', $sSize, 'oxpic1' );
        }
        
        return $sPicUrl;
    }
    
    
    /**
     * Public getter returns defined cover pic if exists and first image if not
     * For fair traffic reasons we will always try to fetch packshot from 
     * best offer which will be presented
     * 
     * @param void
     * @return string
     */
    public function lvGetCoverPictureUrl() {
        // target field 
        if ( method_exists( $this, 'lvGetBestAffiliateDetails' ) ) {
            $aBestArticleDetails = $this->lvGetBestAffiliateDetails();
            $oProduct = $aBestArticleDetails['product'];
        }
        else {
            $oProduct = $this->lvGetProduct();
        }

        $sCoverPicFieldName = $oProduct->oxarticles__lvcoverpic->value;
        
        if ( $sCoverPicFieldName != '' ) {
            $sCoverPicFieldName = "oxarticles__".$sCoverPicFieldName;
            $sPicUrl = $oProduct->$sCoverPicFieldName->value;
            if ( $sPicUrl == '' ) {
                $sPicUrl = $this->_lvSaveAndSetAlternativeCoverFromOtherVendor();
                if ( $sPicUrl == '' ) {
                    $sPicUrl = $this->lvGetFirstPictureUrl();
                }        
            }
            else if ( strpos( $sPicUrl, 'http' ) === false ) {
                // seems to be a native picture, but existing
                $sSize = $this->getConfig()->getConfigParam( 'aDetailImageSizes' );
                $sPicUrl = oxRegistry::get("oxPictureHandler")->getProductPicUrl( "product/1/", $sPicUrl, $sSize, 'oxpic1' );
            }
        }
        else {
            // falbackurl
            $sPicUrl = $this->lvGetFirstPictureUrl();
        }
        
        return $sPicUrl;
    }
    
    
    /**
     * Fallback method which looks if other vendors have got a valid picture url which
     * can be used to download cover and put it into own picture folder
     * 
     * @param void
     * @return string
     */
    protected function _lvSaveAndSetAlternativeCoverFromOtherVendor() {
        $oDb = oxDb::getDb( oxDb::FETCH_MODE_ASSOC );
        $sAlternativeImageUrl = '';
        
        // determine if we are a parent
        if ( $this->oxarticles__oxparentid->value == '' ) {
            $sParentId = $this->oxarticles__oxid->value;
        }
        else {
            $sParentId = $this->oxarticles__oxparentid->value;
        }
        
        
        $sQuery = "SELECT OXID FROM oxarticles WHERE OXPARENTID='".$sParentId."'";
        $oRs    = $oDb->Execute( $sQuery );
        
        if ( $oRs != false && $oRs->recordCount() > 0 ) {
            $blFound = false;
            while ( !$oRs->EOF && $blFound == false ) {
                $sOxid  = $oRs->fields['OXID'];
                
                if ( $sOxid ) {
                    $oArticle = oxNew( 'oxarticle' );
                    $oArticle->load( $sOxid );
                    
                    $sTitleTrimmed = str_replace( " ", "", $oArticle->oxarticles__oxtitle->value );
                    $sArtnum       = $oArticle->oxarticles__oxartnum->value;
                    
                    $sCoverPicFieldName = $oArticle->oxarticles__lvcoverpic->value;
                    $sCoverPicFieldName = "oxarticles__".$sCoverPicFieldName;
                    $sPicUrl            = $oArticle->$sCoverPicFieldName->value;
                    
                    if ( $sPicUrl != '' ) {
                        $sPictureName = $sTitleTrimmed."_".$sArtnum.".jpg";
                        $sTarget = getShopBasePath()."out/pictures/master/product/1/".$sPictureName;
                        // copy foreign file to master folder
                        file_put_contents( $sTarget, file_get_contents( $sPicUrl ) );
                        $blFound = true;
                    }

                    
                }
                
                unset( $oArticle );
                $oRs->moveNext();
            }
            
            if ( isset($sTarget) && file_exists( $sTarget ) && $sPictureName != '' ) {
                $this->oxarticles__oxpic1 = new oxField( $sPictureName );
                $this->save();
                $sAlternativeImageUrl = $this->getPictureUrl();
            }
        }
        
        return $sAlternativeImageUrl;
    }
    
    /**
     * Returns an array of all external picture links
     * 
     * @param void
     * @return array
     */
    protected function _lvGetExtPictureLinks() {
        $aExtPicLinks   = array();
        $oProduct       = $this->lvGetProduct();
        $sLvCoverPic    = $oProduct->oxarticles__lvcoverpic->value;
                
        for ( $iIndex=1; $iIndex<=12; $iIndex++ ) {
            $sCurrentPicField = "oxarticles__oxpic".(string)$iIndex;
            $sCoverPicCompare = "oxpic".(string)$iIndex;
            
            if ( $sLvCoverPic == $sCoverPicCompare ) {
                // skip coverpicture due it will be shown right next to other pics
                continue;
            }
            
            $sCurrentPictureUrl = $oProduct->$sCurrentPicField->value;
            
            // check if this is an external link picture
            if ( strpos( $sCurrentPictureUrl, 'http' ) !== false ) {
                $aExtPicLinks[] = $sCurrentPictureUrl;
            }
        }
        
        return $aExtPicLinks;
    }    
    
}

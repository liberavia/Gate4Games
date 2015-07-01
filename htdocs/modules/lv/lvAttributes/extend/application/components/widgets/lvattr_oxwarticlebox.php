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
 * Description of lvattr_oxwarticlebox
 *
 * @author Gate4Games
 * @author André Gregor-Herrmann
 */
class lvattr_oxwarticlebox extends lvattr_oxwarticlebox_parent {

    /**
     * Template getter returns an array with compatibility icons
     * 
     * @param void
     * @return array
     */
    public function lvGetCompatibilityInformation() {
        $oArticle = $this->getProduct();
        return $oArticle->lvGetCompatibilityInformation();
    }
    
    /**
     * Sums up all compatibility information available of all variants
     * Has option to grey out compatibilty icons that are generally available in offers but not 
     * in current best offer
     * 
     * @param $blGreyOutBestNotAvailable
     * @return array
     */
    public function lvGetSumCompatibilityInformation( $blGreyOutBestNotAvailable = false ) {
        $aReturn = array();
        $oArticle = $this->getProduct();
        $aSumCompatibilityInformation = $oArticle->lvGetSumCompatibilityInformation();
        
        if ( $blGreyOutBestNotAvailable === false ) {
            // add dummy greyout
            foreach ( $aSumCompatibilityInformation as $sAttrId=>$aCompatibility ) {
                $aSumCompatibilityInformation[$sAttrId]['greyout'] = '';
            }
            
            $aReturn = $aSumCompatibilityInformation;
        }
        else {
            // compare best offers compatibilty against sum compatibility
            $oLang                      = oxRegistry::getLang();
            $aBestOfferDetails          = $this->lvGetBestAffiliateDetails();
            $oBestOfferProduct          = $aBestOfferDetails['product'];
            $aBestOfferCompatibility    = $oBestOfferProduct->lvGetCompatibilityInformation();
            
            foreach ( $aSumCompatibilityInformation as $sAttrId=>$aCompatibility ) {
                if ( isset( $aBestOfferCompatibility[$sAttrId] )  ) {
                    // compatibility given in best offer
                    $aSumCompatibilityInformation[$sAttrId]['greyout'] = '';
                }
                else {
                    // compatibility not given, so grey out compatibility
                    $aSumCompatibilityInformation[$sAttrId]['greyout']  = 'opacity:0.3;';
                    // add different title
                    $sCombatibilityTitle                                = $oLang->translateString( 'LV_ATTR_COMPATIBILITY_AVAILABLE' );
                    $aSumCompatibilityInformation[$sAttrId]['title']    = $sCombatibilityTitle;                   
                }
            }
            
            $aReturn = $aSumCompatibilityInformation;
        }
        
        return $aReturn;
    }

    /**
     * Template getter delivers information for best affiliate offer
     * Needs lvAffiliate module to be activated to deliver results
     * 
     * @param void
     * @return array
     */
    public function lvGetBestAffiliateDetails() {
        $oProduct   = $this->getProduct();
        $aReturn    = false;
        
        if ( method_exists( $oProduct, 'lvGetBestAffiliateDetails' ) ) {
            $aReturn = $oProduct->lvGetBestAffiliateDetails();
        }
        
        return $aReturn;
    }
}

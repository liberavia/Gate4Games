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
 * Description of lvagecheck_oxwarticledetails
 *
 * @author Gate4Games
 * @author André Gregor-Herrmann
 */
class lvagecheck_oxwarticledetails extends lvagecheck_oxwarticledetails_parent {
    
    /**
     * Session variable name for age check
     * @var string
     */
    protected $_sLvAgeSessionName = 'sCustomerBirthdate'; 
    
    /**
     * Cookie variable name for age check
     * @var string
     */
    protected $_sLvAgeCookieName = 'lvG4GCustomerBirthdate'; 
    
    
    
    /**
     * Overloading render to first check age
     * @return string
     */
    public function render() {
        
        $aRecommendedAges = $this->_lvGetRecommendedAges();
        
        $sCheckAgeType = false;
        if ( count( $aRecommendedAges ) > 0 ) {
            $sCheckAgeType = $this->_lvNeedToCheckAge( $aRecommendedAges );
        }
        
        if ( $sCheckAgeType == 'none' || $sCheckAgeType == 'denied' ) {
            $this->_lvRedirectToCheckPage( $sCheckAgeType );
        }
        
        return parent::render();
    }
    
    
    /**
     * Redirects to age check page. If user is not allowed top see contend by age add relating
     * parameter to redirect url
     * 
     * @param string $sCheckAgeType
     */
    protected function _lvRedirectToCheckPage( $sCheckAgeType ) {
        $oUtils         = oxRegistry::getUtils();
        $oConfig        = $this->getConfig();
        $oProduct       = $this->getProduct();

        // redirect to age check page
        $sShopUrl               = $oConfig->getShopUrl();
        $sArticleId             = $oProduct->getId();
        
        $sAddToUrl  = "index.php?cl=lvagecheck&anid=".$sArticleId;
        
        if ( $sCheckAgeType == 'denied' ) {
            $sAddToUrl .= "&forbidden=1";
        }
        
        $sUrl       = $sShopUrl.$sAddToUrl;
        
        $oUtils->redirect( $sUrl, false );
    }


    
    /**
     * Method returns recommended ages of usk and pegi in article attributes
     * 
     * @param void
     * @return array
     */
    protected function _lvGetRecommendedAges() {
        $aAttributes        = $this->getAttributes();
        $aRecommendedAges   = array();
        
        foreach ( $aAttributes as $sTitleHash=>$oAttribute ) {
            $blRecommendedUskExists     = $oAttribute->id == 'RecommendedAgeUsk';
            $blRecommendedPegiExists    = $oAttribute->id == 'RecommendedAgePegi';

            if ( $blRecommendedUskExists || $blRecommendedPegiExists ) {
                $oConfig = $this->getConfig();

                if ( $blRecommendedUskExists ) {
                    $aRecommendedAges[] = preg_replace( '/[^0-9]/','', $oAttribute->value );
                }

                if ( $blRecommendedPegiExists ) {
                    $aRecommendedAges[] = preg_replace( '/[^0-9]/','', $oAttribute->value );
                }
            }
        }
        
        return $aRecommendedAges;
    }
    
    
    /**
     * Checks if the age check already has been made (session) and if age of user is above configured value
     * 
     * @param array $aRecommendedAges
     * @return string
     */
    protected function _lvNeedToCheckAge( $aRecommendedAges ) {
        $oConfig        = $this->getConfig();
        $oSession       = $this->getSession();
        $oUtilsServer   = oxRegistry::get( 'oxUtilsServer' );
        
        $sConfiguredCheckAge = trim( $oConfig->getConfigParam( 'sLvCheckFromAge' ) );
        $iConfiguredCheckAge = (int)$sConfiguredCheckAge;

        $blNeedToCheck = false;
        foreach ( $aRecommendedAges as $sRecommendedAge ) {
            $iRecommendedAge = (int)$sRecommendedAge;
            
            if ( $iRecommendedAge >= $iConfiguredCheckAge ) {
                $blNeedToCheck = true;
            }
        }
        
        $sCheckAgeType = "approved";
        // if check is required we first need to check if check has already been performed
        if ( $blNeedToCheck ) {
            $sUserBirthdateTimestamp = $oSession->getVariable( $this->_sLvAgeSessionName );
            $sCookieBirthdayTimeStamp = $oUtilsServer->getOxCookie( $this->_sLvAgeCookieName );
            $iTimeStampOfRecommendedBirth   = strtotime( "-".(string)$iRecommendedAge." years" );
            
            if ( $sUserBirthdateTimestamp || $sCookieBirthdayTimeStamp ) {
                if ( $sUserBirthdateTimestamp ) {
                    $iUserBirthdateTimestamp        = (int)$sUserBirthdateTimestamp;
                }
                else {
                    // use cookie if age nto in current session
                    $iUserBirthdateTimestamp        = (int)$sCookieBirthdayTimeStamp;
                }
                
                if ( $iUserBirthdateTimestamp <= $iTimeStampOfRecommendedBirth ) {
                    $sCheckAgeType = 'approved';
                }
                else {
                    $sCheckAgeType = 'denied';
                }
            }
            else {
                $sCheckAgeType = "none";
            }
        }
        return $sCheckAgeType;
    }
    
}

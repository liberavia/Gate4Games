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
            $this->_lvRedirectToCheckPage( $sCheckAge );
        }
        
        return parent::render();
    }
    
    
    /**
     * Template getter returns an array of years til 100 years backwards from now
     * 
     * @param void
     * @return array
     */
    public function lvGetYears() {
        $iCurrentYear = (int)date( 'Y' );
        $iMaxYearDown = $iCurrentYear - 100;
        $aYears = array();
        
        for ( $iIndex=$iCurrentYear; $iIndex>=$iMaxYearDown; $iIndex-- ) {
            $aYears[] = $iIndex;
        }
        
        return $aYears;
    }


    /**
     * Template getter returns months from 1 to 12
     * 
     * @param void
     * @return array
     */
    public function lvGetMonths() {
        $aMonths = array();
        
        for ( $iIndex=1; $iIndex<=12; $iIndex++ ) {
            $aMonths[] = $iIndex;
        }
        
        return $aMonths;
    }
    

    /**
     * Template getter returns days from 1 to 31
     * 
     * @param void
     * @return array
     */
    public function lvGetDays() {
        $aDays = array();
        
        for ( $iIndex=1; $iIndex<=31; $iIndex++ ) {
            $aDays[] = $iIndex;
        }
        
        return $aDays;
    }

    
    /**
     * Redirects to age check page. If user is not allowed top see contend by age add relating
     * parameter to redirect url
     * 
     * @param string $sCheckAgeType
     */
    protected function _lvRedirectToCheckPage( $sCheckAgeType ) {
        $oUtils     = oxRegistry::getUtils();
        $oConfig    = $this->getConfig();

        // redirect to age check page
        $sShopUrl   = $oConfig->getShopUrl();
        $sAddToUrl  = "index.php?cl=lvagecheck";
        
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
        $aAttributes = $this->getAttributes();
        $aRecommendedAges = array();
        
        $blRecommendedUskExists     = isset( $aAttributes['RecommendedAgeUsk'] );
        $blRecommendedPegiExists    = isset( $aAttributes['RecommendedAgePegi'] );
        
        if ( $blRecommendedUskExists || $blRecommendedPegiExists ) {
            $oConfig = $this->getConfig();
            
            if ( $blRecommendedUskExists ) {
                $aRecommendedAges[] = $aAttributes['RecommendedAgeUsk']->value;
            }
            
            if ( $blRecommendedPegiExists ) {
                $aRecommendedAges[] = $aAttributes['RecommendedAgePegi']->value;
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
        $oConfig    = $this->getConfig();
        $oSession   = $this->getSession();
        
        $sConfiguredCheckAge = trim( $oConfig->getConfigParam( 'sLvCheckFromAge' ) );
        $iConfiguredCheckAge = (int)$sConfiguredCheckAge;

        $blNeedToCheck = false;
        foreach ( $aRecommendedAges as $sRecommendedAge ) {
            $iRecommendedAge = (int)$sRecommendedAge;
            
            if ( $iRecommendedAge >= $iConfiguredCheckAge ) {
                $blNeedToCheck = true;
            }
        }
        
        $sCheckAgeType = "none";
        // if check is required we first need to check if check has already been performed
        if ( $blNeedToCheck ) {
            $sUserBirthdateTimestamp = $oSession->getVariable( $this->_sLvAgeSessionName );

            if ( $sUserBirthdateTimestamp ) {
                $iUserBirthdateTimestamp        = (int)$sUserBirthdateTimestamp;
                $iTimeStampOfRecommendedBirth   = strtotime( "-".(string)$iRecommendedAge." years" );
                
                if ( $iUserBirthdateTimestamp <= $iTimeStampOfRecommendedBirth ) {
                    $sCheckAgeType = 'approved';
                }
                else {
                    $sCheckAgeType = 'denied';
                }
            }
        }
        
        return $sCheckAgeType;
    }
    
}

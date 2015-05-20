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
 * Description of lvagecheck
 *
 * @author Gate4Games
 * @author André Gregor-Herrmann
 */
class lvagecheck extends oxUBase {
    
    /**
     * Template to call for rendering
     * @var string
     */
    protected $_sThisTemplate = 'lvagecheck.tpl';
    
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
     * Time the cookie for age lasts
     * @var string
     */
    protected $_sCookieLasting = '+ 1 month';

    /**
     * Article Object of age redirecting product
     * @var object
     */
    protected $_oLvArticle = null;

    /**
     * Flag that signals if user has been denied by age
     * @var bool
     */
    protected $_blLvForbiddenByAge = false;
    
    
    /**
     * Render method which is in each oxid controller
     * 
     * @param void
     * @return string
     */
    public function render() {
        parent::render();
        
        $oConfig = $this->getConfig();
        
        $blForbidden    = (bool)$oConfig->getRequestParameter( 'forbidden' );
        $sArticleId     = $oConfig->getRequestParameter( 'anid' );
        $this->_blLvForbiddenByAge  = $blForbidden;
        if ( $sArticleId ) {
            $iCurrentLangId = oxRegistry::getLang()->getBaseLanguage();
            $this->_oLvArticle = oxNew( 'oxarticle' );
            $this->_oLvArticle->loadInLang( $iCurrentLangId, $sArticleId );
        }
        else {
            $oLang          = oxRegistry::getLang();
            $oUtilsView     = oxRecommList::get( 'oxUtilsView' );
            
            $sErrorMessage = $oLang->translateString( 'LV_AGECHECK_VALIDATION_ERROR' );
            $oUtilsView->addErrorToDisplay( $sErrorMessage );
        }
        
        return $this->_sThisTemplate;
    }
    
    
    public function getPageTitle() {
        $oLang = oxRegistry::getLang();
        $sLangTitlePrefix = $oLang->translateString( 'LV_AGECHECK_FOR' );
        $sGameTitle = $this->_oLvArticle->oxarticles__oxtitle->value;
        
        return $sLangTitlePrefix." ".$sGameTitle;
    }


    /**
     * Template getter for article object
     * 
     * @param void
     * @return mixed
     */
    public function lvGetArticle() {
        return $this->_oLvArticle;
    }

    

    /**
     * Template getter returns current return url
     * 
     * @param void
     * @return string
     */
    public function lvGetReturnUrl() {
        $sReturnLink = '';
        if ( $this->_oLvArticle ) {
            $sReturnLink = $this->_oLvArticle->getLink();
        }
        
        return $sReturnLink;
    }
    
    
    /**
     * Template getter returns if user access has been denied by requesting his age
     * 
     * @param void
     * @return bool
     */
    public function lvGetForbiddenByAge() {
        return $this->_blLvForbiddenByAge;
    }
    
    
    /**
     * Template getter returns url of coverimage
     * 
     * @param void
     * @return string
     */
    public function lvGetCoverImage() {
        $sReturnUrl = '';
        if ( $this->_oLvArticle ) {
            $sReturnUrl = $this->_oLvArticle->lvGetCoverPictureUrl();
        }
        
        return $sReturnUrl;
    }
    
    
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
     * Validating age entry set timestamp into session and redirect user to referer
     * 
     * @param void
     * @return void
     */
    public function lvValidateAge() {
        $oConfig = $this->getConfig();
        
        $aParams    = $oConfig->getRequestParameter( 'editval' );
        $sReturnUrl = urldecode( $oConfig->getRequestParameter( 'sReturnUrl' ) );
        
        if ( count( $aParams ) == 3 ) {
            $oSession       = $this->getSession();
            $oUtils         = oxRegistry::getUtils();
            $oUtilsServer   = oxRegistry::get( 'oxUtilsServer' );
            
            $sYear      = trim( $aParams['lvAgeYear'] );
            $sMonth     = trim( $aParams['lvAgeMonth'] );
            $sDay       = trim( $aParams['lvAgeDay'] );
            
            $sBirtdate = $sYear."-".$sMonth."-".$sDay;
            
            $iTimeStamp = strtotime( $sBirtdate );
            
            $oSession->setVariable( $this->_sLvAgeSessionName, $iTimeStamp );
            $oUtilsServer->setOxCookie( $this->_sLvAgeCookieName, $iTimeStamp, strtotime( $this->_sCookieLasting ) );
            
            // send user back and see if he matches age
            if ( $sReturnUrl && $sReturnUrl != '' ) {
                $sShopUrl = $oConfig->getShopUrl();
                $sConcatenate = '';
                
                if ( substr( $sShopUrl, -1 ) != '/' && substr( $sReturnUrl, 0, 1 ) != '/' ) {
                    $sConcatenate  = "/";
                }
                else if ( substr( $sShopUrl, -1 ) == '/' && substr( $sReturnUrl, 0, 1 ) == '/' ) {
                    $sShopUrl = substr($sShopUrl, 0, strlen( $sShopUrl )-1 );
                }
                
                $sUrl = $sShopUrl.$sConcatenate.$sReturnUrl;
            }
            else {
                $sUrl = $oUtilsServer->getServerVar( 'HTTP_REFERER' );
            }
            
            $oUtils->redirect( $sUrl, false );
        }
        else {
            $oLang          = oxRegistry::getLang();
            $oUtilsView     = oxRecommList::get( 'oxUtilsView' );
            
            $sErrorMessage = $oLang->translateString( 'LV_AGECHECK_VALIDATION_ERROR' );
            $oUtilsView->addErrorToDisplay( $sErrorMessage );
        }
    }
    
}

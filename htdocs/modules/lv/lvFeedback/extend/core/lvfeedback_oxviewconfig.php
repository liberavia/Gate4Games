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
 * Description of lvfeedback_oxviewconfig
 *
 * @author Gate4Games
 * @author André Gregor-Herrmann
 */
class lvfeedback_oxviewconfig extends lvfeedback_oxviewconfig_parent {
    
    /**
     * Template getter for configured secret-Key
     * 
     * @param void
     * @return string
     */
    public function lvGetReCaptchaWebsiteKey() {
        $oConfig = $this->getConfig();
        
        $sRecaptchaWebsiteKey = $oConfig->getConfigParam( 'sLvFeedbackWebsiteKey' );
        
        $sReturn = '';
        if ( $sRecaptchaWebsiteKey ) {
            $sReturn = $sRecaptchaWebsiteKey;
        }
        
        return $sReturn;
    }

    /**
     * Template getter for configured secret Key
     * 
     * @param void
     * @return string
     */
    public function lvGetReCaptchaSecretKey() {
        $oConfig = $this->getConfig();
        
        $sRecaptchaWebsiteKey = $oConfig->getConfigParam( 'sLvFeedbackSecretKey' );
        
        $sReturn = '';
        if ( $sRecaptchaWebsiteKey ) {
            $sReturn = $sRecaptchaWebsiteKey;
        }
        
        return $sReturn;
    }

    
    /**
     * Returns if recaptcha feature is used
     * 
     * @param void
     * @return bool
     */
    public function lvFeedbackRecaptchaActive() {
        $oConfig = $this->getConfig();
        
        $blRecaptchaActive = (bool)$oConfig->getConfigParam( 'blLvFeedbackUseCaptcha' );
        
        return $blRecaptchaActive;
    }
    
    
    /**
     * Template getter for receiving feedback image
     * 
     * @param void
     * @return string
     */
    public function lvGetFeedbackButtonImg() {
        $oConfig = $this->getConfig();
        
        $sShopUrl   = $oConfig->getShopUrl();
        $sPath      = "modules/lv/lvFeedback/out/img/Feedeback.png";
        $sImgUrl    = $sShopUrl.$sPath;
        
        return $sImgUrl;
    }
    

    /**
     * Template getter for receiving module src path (js and css)
     * 
     * @param void
     * @return string
     */
    public function lvGetFeedbackSrc() {
        $oConfig = $this->getConfig();
        
        $sShopUrl   = $oConfig->getShopUrl();
        $sPath      = "modules/lv/lvFeedback/out/src/";
        $sSrcUrl    = $sShopUrl.$sPath;
        
        return $sSrcUrl;
    }
    
    
    /**
     * Returns javascript url of feedback.js
     * 
     * @param void
     * @return string
     */
    public function lvGetFeedbackJs() {
        $sSrcUrl = $this->lvGetFeedbackSrc();
        
        $sReturn = $sSrcUrl."js/lvfeeback.js";
        
        return $sReturn;
    }
    

    /**
     * Returns javascript url of feedback.css
     * 
     * @param void
     * @return string
     */
    public function lvGetFeedbackCss() {
        $sSrcUrl = $this->lvGetFeedbackSrc();
        
        $sReturn = $sSrcUrl."css/lvfeeback.css";
        
        return $sReturn;
    }
}

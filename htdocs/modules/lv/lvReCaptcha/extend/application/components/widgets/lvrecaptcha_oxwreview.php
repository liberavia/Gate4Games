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
 * Description of lvrecaptcha_oxwreview
 *
 * @author Gate4Games
 * @author André Gregor-Herrmann
 */
class lvrecaptcha_oxwreview extends lvrecaptcha_oxwreview_parent {
    
    /**
     * Template getter for configured secret-Key
     * 
     * @param void
     * @return string
     */
    public function lvGetReCaptchaWebsiteKey() {
        $oConfig = $this->getConfig();
        
        $sRecaptchaWebsiteKey = $oConfig->getConfigParam( 'sLvRecaptchaWebsiteKey' );
        
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
        
        $sRecaptchaWebsiteKey = $oConfig->getConfigParam( 'sLvRecaptchaSecretKey' );
        
        $sReturn = '';
        if ( $sRecaptchaWebsiteKey ) {
            $sReturn = $sRecaptchaWebsiteKey;
        }
        
        return $sReturn;
    }
}

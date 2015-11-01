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
 * Description of lvsendfeedback
 *
 * @author Gate4Games
 * @author André Gregor-Herrmann
 */
class lvsendfeedback extends oxUBase {
    
    /**
     * Public POST receiver to send Feedback and return user to former page
     * 
     * @param void
     * @rerturn void
     */
    public function lvTriggerSendFeedback() {
        $oConfig                    = $this->getConfig();
        $aParams                    = $oConfig->getRequestParameter( 'editval' );
        $sReturnPage                = $oConfig->getRequestParameter( 'currentpage' );

        $blLvReCaptchaValidated = $this->_lvValidateReCaptchaResponse();
        
        if ( $blLvReCaptchaValidated === true ) {
            $this->_lvSendMessage( $aParams, $sReturnPage );
        }
        else {
            // send user right back with message, that he didn't pass the captcha
            $this->_lvSendUserBack( $sReturnPage, 2, $aParams );
        }
    }
    
    
    /**
     * Sends feedback mail to defined address
     * 
     * @param type $aParams
     * @param type $sReturnPage
     * @return void
     */
    protected function _lvSendMessage( $aParams, $sReturnPage ) {
        
        if ( isset( $aParams['message'] ) && is_string( $aParams['message'] ) && $aParams['message'] != '' ) {
            $oEmail = oxNew( 'oxemail' );
            $blSuccess = $oEmail->lvSendFeedbackMail( $aParams, $sReturnPage );
            if ( $blSuccess ) {
                // send user back with success message
                $this->_lvSendUserBack( $sReturnPage, 1 );
            }
            else {
                // tell user, that there were problems sending mail
                $this->_lvSendUserBack( $sReturnPage, 4, $aParams );
            }
        }
        else {
            // send user right back with message, that there is no message
            $this->_lvSendUserBack( $sReturnPage, 3, $aParams );
        }
    }
    
    
    
    /**
     * Method takes a feedback message and returns user to former page
     * 
     * @param int $iReturnMessage
     * @return void
     */
    protected function _lvSendUserBack( $sReturnUrl, $iReturnMessage, $aParams = null ) {
        $oUtils = oxRegistry::getUtils();
        
        if (strpos( $sReturnUrl, '?' ) === false ) {
            $sReturnUrl .= '?lvFeedbackReturnMessage='.(string)$iReturnMessage;
        }
        else {
            $sReturnUrl .= '&lvFeedbackReturnMessage='.(string)$iReturnMessage;
        }
        
        if ( is_array( $aParams ) ) {
            if ( $aParams['email'] != '' ) {
                $sReturnUrl .= '&lvFeedbackEmail='.urlencode($aParams['email']);
            }
            if ( $aParams['name'] != '' ) {
                $sReturnUrl .= '&lvFeedbackName='.urlencode($aParams['name']);
            }
            if ( $aParams['message'] != '' ) {
                $sReturnUrl .= '&lvFeedbackMessage='.urlencode($aParams['message']);
            }
        }
        
        $oUtils->redirect( $sReturnUrl, false );
    }
    
    /**
     * Validates POST response of recaptcha form
     * 
     * @param void
     * @rerurn bool
     */
    protected function _lvValidateReCaptchaResponse() {
        $blReturn                   = false;
        $oConfig                    = $this->getConfig();
        $sReCaptchaResponse         = $oConfig->getRequestParameter( 'g-recaptcha-response' );
        $sReCaptchaSecretKey        = $oConfig->getConfigParam( 'sLvRecaptchaSecretKey' );
        $sReCaptchaApiRequestUrl    = $oConfig->getConfigParam( 'sLvRecaptchaRequestUrl' );
        
        if ( $sReCaptchaResponse && $sReCaptchaSecretKey && $sReCaptchaApiRequestUrl ) {
            // preparing data to post
            $aFields = array(
                'secret'    => $sReCaptchaSecretKey,
                'response'  => $sReCaptchaResponse,
            );
            
            foreach( $aFields as $sKey=>$sValue ) { 
                $sFieldsString .= $sKey.'='.$sValue.'&'; 
            }

            $sFieldsString = rtrim( $sFieldsString, '&' );

            // initialize curl resource
            $resCurl = curl_init();
            curl_setopt( $resCurl, CURLOPT_URL, $sReCaptchaApiRequestUrl );
            curl_setopt( $resCurl, CURLOPT_POST, count( $aFields ) );
            curl_setopt( $resCurl, CURLOPT_POSTFIELDS, $sFieldsString ); 
            curl_setopt( $resCurl, CURLOPT_RETURNTRANSFER, 1 );
            
            $sResult = curl_exec( $resCurl );
            curl_close( $resCurl );
            
            // convert json answer
            $mResult = json_decode( $sResult, true );
            
            // validate answer
            if ( is_array( $mResult ) && (bool)$mResult['success'] === true ) {
                /**
                 * @todo validating possible error codes for displaying in frontend
                 */
                $blReturn = true;
            }
            else if ( is_integer( $mResult ) && (bool)$mResult === true ) {
                $blReturn = true;
            }
        }
        
        return $blReturn;
    }
    
}

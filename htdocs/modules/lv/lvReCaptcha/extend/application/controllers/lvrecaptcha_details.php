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
 * Description of lvrecaptcha_details
 *
 * @author Gate4Games
 * @author André Gregor-Herrmann
 */
class lvrecaptcha_details extends lvrecaptcha_details_parent {

    /**
     * Saves user ratings and review text (oxReview object)
     * LV: REMOVED need of beeing logged in to save a review. ADDED validation of Google Recaptcha innstead
     *
     * @return null
     */
    public function saveReview() {
        $blLvReCaptchaValidated = $this->_lvValidateReCaptchaResponse();
        
        if ( $blLvReCaptchaValidated === false ) {
            return;
        }
        
        if (!oxRegistry::getSession()->checkSessionChallenge()) {
            return;
        }

        $oProduct = $this->getProduct();
        
        if ( $this->canAcceptFormData() && $oProduct ) {

            $dRating = $this->getConfig()->getRequestParameter( 'artrating' );
            if ( $dRating !== null ) {
                $dRating = (int) $dRating;
            }

            //save rating
            if ($dRating !== null && $dRating >= 1 && $dRating <= 5) {
                $oRating = oxNew('oxrating');
                if ($oRating->allowRating($oUser->getId(), 'oxarticle', $oProduct->getId())) {
                    $oRating->oxratings__oxuserid = new oxField($oUser->getId());
                    $oRating->oxratings__oxtype = new oxField('oxarticle');
                    $oRating->oxratings__oxobjectid = new oxField($oProduct->getId());
                    $oRating->oxratings__oxrating = new oxField($dRating);
                    $oRating->save();
                    $oProduct->addToRatingAverage($dRating);
                }
            }

            if ( ( $sReviewText = trim(( string ) $this->getConfig()->getRequestParameter('rvw_txt', true) ) ) ) {
                $oReview = oxNew('oxReview');
                $oReview->oxreviews__oxobjectid = new oxField($oProduct->getId());
                $oReview->oxreviews__oxtype = new oxField('oxarticle');
                $oReview->oxreviews__oxtext = new oxField($sReviewText, oxField::T_RAW);
                $oReview->oxreviews__oxlang = new oxField(oxRegistry::getLang()->getBaseLanguage());
                $oReview->oxreviews__oxuserid = new oxField($oUser->getId());
                $oReview->oxreviews__oxrating = new oxField( ($dRating !== null) ? $dRating : 0 );
                $oReview->save();
            }
        }
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

            rtrim( $sFieldsString, '&' );
            
            // initialize curl resource
            $resCurl = curl_init();
            curl_setopt( $resCurl,CURLOPT_URL, $sReCaptchaApiRequestUrl );
            curl_setopt( $resCurl,CURLOPT_POST, count( $aFields ) );
            curl_setopt( $resCurl,CURLOPT_POSTFIELDS, $sFieldsString );            
            
            $sResult = curl_exec( $resCurl );
            curl_close( $resCurl );
            
            $aResult = json_decode( $sResult, true );
            
            if ( (bool)$aResult['success'] === true ) {
                $blReturn = true;
            }
        }
        
        return $blReturn;
    }
}

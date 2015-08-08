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
 * Description of lvfeedback_oxemail
 *
 * @author Gate4Games
 * @author André Gregor-Herrmann
 */
class lvfeedback_oxemail extends lvfeedback_oxemail_parent {
    
    /**
     * Sends feedback mail and returns success
     * 
     * @param type $aParams
     * @return bool
     */
    public function lvSendFeedbackMail( $aParams, $sFeedbackPage ) {
        $oShop      = $this->_getShop();
        $oConfig    = $this->getConfig();
        $sTo        = $oConfig->getConfigParam( 'sLvFeedbackEmail' );
        
        $sSubject = "User Feedback from ".$oShop->oxshops__oxname->getRawValue()."!";
        
        $sMessage  = "";
        $sMessage .= "\nE-Mail: ".(string)$aParams['email']."\nName: ".(string)$aParams['name']."\nVisited page: ".$sFeedbackPage;
        $sMessage .= "\n\nMessage:\n".(string)$aParams['message'];
        
        $blReturn = $this->sendEmail( $sTo, $sSubject, $sMessage );
        
        return (bool)$blReturn;
    }
    
}

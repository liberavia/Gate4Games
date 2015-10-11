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
 * Description of lvaffiliatenm_admin_main
 *
 * @author Gate4Games
 * @author André Gregor-Herrmann
 */
class lvaffiliatenm_admin_main extends lvaffiliatenm_admin_main_parent {
    
    /**
     * set the needed template
     */
    public function render() {
        $oConfig  = $this->getConfig();
        parent::render();
        
        $sOxid      = $oConfig->getRequestParameter( "oxid" );
        $sSavedID   = $oConfig->getRequestParameter( "saved_oxid" );
        
        if ( ( $sOxid == "-1" || !isset( $sOxid ) ) && isset( $sSavedID ) ) {
            $sOxid = $sSavedID;
            $oSession->deleteVariable( "saved_oxid");
            $this->_aViewData["oxid"] =  $sOxid;
            $this->_aViewData["updatelist"] =  "1";
        }

        if ( $sOxid != "-1" && isset( $sOxid ) ) {
            // load object
            $oNameMatching = oxNew( "lvaffiliatenm", getViewName( 'lvaffiliatenm' ) );
            $oNameMatching->loadInLang( $this->_iEditLang, $sOxid );

            $this->_aViewData["edit"] =  $oNameMatching;

            //Disable editing for derived items
            if ( $oNameMatching->isDerived() )
                $this->_aViewData['readonly'] = true;
        }

        return 'lvaffiliatenm_admin_main.tpl';
    }


    /**
     * Saves information about link (active, date, URL, description, etc.) to DB.
     *
     * @return mixed
     */
    public function save() {
        $oConfig = $this->getConfig();
        
        $oSession   = oxRegistry::get( 'oxSession' );
        $oLang      = oxRegistry::get( 'oxLang' );
        $oUtilsView = oxRegistry::get( 'oxUtilsView' );

        $sOxid      = $oConfig->getRequestParameter( "oxid");
        $aParams    = $oConfig->getRequestParameter( "editval");

        if ( !isset( $aParams['lvaffiliatenm__lvactive']))
            $aParams['lvaffiliatenm__fcactive'] = 0;

        $oSession->setVariable( 'blRedirectError', false );
        $iEditLanguage = $oConfig->getRequestParameter("editlanguage");
        
        $oNameMatching = oxNew( "lvaffiliatenm", getViewName( 'lvaffiliatenm' ) );

        if ( $sOxid != "-1") {
            $oNameMatching->loadInLang( $iEditLanguage, $sOxid );

            //Disable editing for derived items
            if ( $oNameMatching->isDerived() ) {
                return;
            }
        }
        else {
            $aParams['lvaffiliatenm__oxid'] = null;
        }


        $oNameMatching->setLanguage( 0 );
        $oNameMatching->assign( $aParams );
        $oNameMatching->setLanguage( $iEditLanguage );
        $oNameMatching->save();
        
        $this->_aViewData["updatelist"] = "1";

        // set oxid if inserted
        if ( $sOxid == "-1") {
            $oSession->setVariable( "saved_oxid", $oNameMatching->lvaffiliatenm__oxid->value );
        }
    }
}

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
 * Description of lvaffiliate_oxcategory
 *
 * @author Gate4Games
 * @author André Gregor-Herrmann
 */
class lvaffiliate_oxcategory extends lvaffiliate_oxcategory_parent {

    /**
     * Returns list of children contents for given load id
     * 
     * @param string $sParentLoadId
     * @return array
     */
    public function lvGetSubContentCats( $sParentLoadId ) {
        $aContents      = array();
        $oDb            = oxDb::getDb( MODE_FETCH_ASSOC );
        $sContentTable  = getViewName( 'oxcontents' );
        $iCurrentLangId = oxRegistry::getLang()->getBaseLanguage();
        
        $sQuery = "SELECT OXID FROM ".$sContentTable." WHERE LVPARENTLOADID='".$sParentLoadId."' AND OXACTIVE='1'";
        $oRs = $oDb->execute( $sQuery );
        
        if ( $oRs != false && $oRs->recordCount() > 0 ) {
            while ( !$oRs->EOF ) {
                $sOxid      = $oRs->fields['OXID'];
                
                if ( $sOxid ) {
                    $oContent = oxNew( 'oxcontent' );
                    $oContent->loadInLang( $iCurrentLangId, $sOxid );
                    $aContents[] = $oContent;
                }
                
                $oRs->moveNext();
            }
        }
        
        return $aContents;
    }
}

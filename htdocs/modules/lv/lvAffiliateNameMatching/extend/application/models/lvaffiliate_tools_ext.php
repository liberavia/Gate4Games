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
 * Description of lvaffiliate_tools_ext
 *
 * @author Gate4Games
 * @author André Gregor-Herrmann
 */
class lvaffiliate_tools_ext extends lvaffiliate_tools_ext_parent {
    
    /**
     * Method tries to normalize name so it can be better matched with existing articles
     * Overloaded method to first check
     * 
     * @param string $sTitleFromVendor
     * @return string
     */
    public function lvGetNormalizedName( $sTitleFromVendor ) {
        
        $sNormalizedTitle = parent::lvGetNormalizedName( $sTitleFromVendor );
        
        $sMatchedTitle = $this->_lvMatchTitle( $sNormalizedTitle );
        
        return $sMatchedTitle;
    }
    
    
    /**
     * Method tries to fetch current normalized title from list of manual matchings and translate them 
     * To target title. Else the input equals output
     * 
     * @param string $sNormalizedTitle
     * @return string
     */
    protected function _lvMatchTitle( $sNormalizedTitle ) {
        $oConfig        = $this->getConfig();
        $oDb            = oxDb::getDb( oxDb::FETCH_MODE_ASSOC );
        $sMatchTable    = getViewName( 'lvaffiliatenm' );
        
        $sQuery = "SELECT LVTONAME FROM ".$sMatchTable." WHERE LVFROMNAME=".$oDb->quote( $sNormalizedTitle )." LIMIT 1";
        
        $sToName = $oDb->GetOne( $sQuery );
        
        if ( $sToName ) {
            $sReturn = $sToName;
        }
        else {
            $sReturn = $sNormalizedTitle;
        }
        
        return $sReturn;
    }
    
    
}

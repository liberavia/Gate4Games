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
 * Description of lvaffiliate_oxviewconfig
 *
 * @author Gate4Games
 * @author André Gregor-Herrmann
 */
class lvaffiliate_oxviewconfig extends lvaffiliate_oxviewconfig_parent {
    
    /**
     * Returns amount of available articles
     * 
     * @param void
     * @return int
     */
    public function lvGetAmountArticles() {
        $oDb = oxDb::getDb( MODE_FETCH_ASSOC );
        
        $sQuery = "SELECT OXCOUNT FROM oxcounters WHERE OXIDENT='lvAvailableArticles'";
        
        $sAmount = $oDb->GetOne( $sQuery );
        
        return (int)$sAmount;
    }
    
    
    /**
     * Get configured facebook link
     * 
     * @param void
     * @return string
     */
    public function lvGetFbPageLink() {
        $oConfig = $this->getConfig();
        
        $sLvFbHomePage = $oConfig->getConfigParam( 'sLvFbHomePage' );

        return (string)$sLvFbHomePage;
    }
    
    
    /**
     * Returns image url of facebook logo
     * 
     * @param void
     * @return string
     */
    public function lvGetFbSearchLogoUrl() {
        $oConfig    = $this->getConfig();
        $sShopUrl   = $oConfig->getShopUrl();
        $sPath      = "modules/lv/lvAffiliate/out/img/fblogo_search.png";
        
        return $sShopUrl.$sPath;
    }
    
}

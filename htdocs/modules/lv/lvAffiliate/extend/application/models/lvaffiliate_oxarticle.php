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
 * Description of lvaffiliate_oxarticle
 *
 * @author Gate4Games
 * @author André Gregor-Herrmann
 */
class lvaffiliate_oxarticle extends lvaffiliate_oxarticle_parent {
    
    /**
     * Public getter delivers information for best affiliate offer
     * 
     * @param void
     * @return array
     */
    public function lvGetBestAffiliateDetails() {
        $aBestAffiliateForProduct = array();
        $sBestPriceId = $this->_lvGetBestPriceVariantId();
        
        if ( $sBestPriceId ) {
            $iCurrentLangId = oxRegistry::getLang()->getBaseLanguage();
            $oArticle = oxNew( 'oxarticle' );
            $oArticle->loadInLang( $iCurrentLangId, $sBestPriceId );

            if ( $oArticle ) {
                $aBestAffiliateForProduct['vendor']    = $oArticle->getVendor();
                $aBestAffiliateForProduct['product']   = $oArticle;
            }
        }
        
        return $aBestAffiliateForProduct;
    }
    
    
    /**
     * Public variant ids sorted by best price
     * 
     * @param void
     * @return string
     */
    protected function _lvGetBestPriceVariantId() {
        $sVariantId = '';
        $sOxid = $this->getId();
        
        if ( $sOxid ) {
            $oDb                = oxDb::getDb( FETCH_MODE_ASSOC );
            $sArticlesTable     = getViewName( 'oxarticles' );
            $sQuery = "SELECT OXID FROM ".$sArticlesTable." WHERE OXPARENTID='".$sOxid."' ORDER BY OXPRICE ASC LIMIT 1";
            
            $sResult = $oDb->GetOne( $sQuery );
            if ( $sResult ) {
                $sVariantId = $sResult;
            }
        }
        
        return $sVariantId;
    }
    
    
    /**
     * 
     * @param type $sOXID
     */
    protected function _deleteRecords($sOXID) {
        $resReturn = parent::_deleteRecords($sOXID);
        
        $oDb = oxDb::getDb();
        
        // oxmediaurls
        $sDelete = 'delete from oxmediaurls where oxobjectid = ' . $sOXID . ' ';
        $oDb->execute( $sDelete );
        
        return $resReturn;
    }
    
}

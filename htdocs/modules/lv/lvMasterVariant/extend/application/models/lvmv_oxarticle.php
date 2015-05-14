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
 * Description of lvmv_oxarticle
 *
 * @author Gate4Games
 * @author André Gregor-Herrmann
 */
class lvmv_oxarticle extends lvmv_oxarticle_parent {
    
    /**
     * Contains the article object of master variant
     * @var object
     */
    protected $_oLvMasterVariant = null;


    
    /**
     * Wrapper method for getting the product object
     * 
     * @param void
     * @return object
     */
    public function lvGetProduct() {
        $oMasterVariant = $this->_lvGetMasterVariant();
        
        if ( $oMasterVariant ) {
            $oReturn = $oMasterVariant;
        }
        else {
            $oReturn = parent::_lvGetProduct();
        }
        
        return $oReturn;
    }

    
    /**
     * Returns the master variant of article
     * 
     * @param void
     * @return mixed
     */
    protected function _lvGetMasterVariant() {
        if ( $this->_oLvMasterVariant === null ) {
            if ( $this->getVariantsCount() > 0 ) {
                
                $sMasterVariantOxid = $this->_lvGetMasterVariantId();
                
                if ( $sMasterVariantOxid ) {
                    $iCurrentLangId = oxRegistry::getLang()->getBaseLanguage();
                    $oArticle = oxNew( 'oxarticle' );
                    $oArticle->loadInLang( $iCurrentLangId, $sMasterVariantOxid );

                    if ( $oArticle ) {
                        $this->_oLvMasterVariant = $oArticle;
                    }
                }
            }
        }
        
        return $this->_oLvMasterVariant;
    }
    
    
    /**
     * Returns oxid of master variant
     * 
     * @param void
     * @return mixed
     */
    protected function _lvGetMasterVariantId() {
        // Maybe we are still in a variant. Return false in case
        $mReturn = false;
        
        if ( $this->oxarticles__oxparentid == '' ) {
            $sOxid = $this->getId();
            
            $oDb = oxDb::getDb( FETCH_MODE_ASSOC );
            $sArticleTable = getViewName( 'oxarticles' );
            
            $sQuery = "SELECT OXID, LVMASTERVARIANT FROM ".$sArticleTable." WHERE OXPARENTID='".$sOxid."'";
            
            $oResult = $oDb->Execute( $sQuery );
            
            if ( $oResult != false && $oResult->recordCount() > 0 ) {
                $iIndex = 0;
                while ( !$oResult->EOF ) {
                    // saving first variant oxid as fallback if there is no master
                    if ( $iIndex == 0 ) {
                        $mReturn = $oResult->fields['OXID'];
                    }
                    
                    $blIsMasterVariant = (bool)$oResult->fields['LVMASTERVARIANT'];
                    
                    if ( $blIsMasterVariant ) {
                        $mReturn = $oResult->fields['OXID'];
                    }
                    
                    $iIndex++;
                    $oResult->moveNext();
                }
            }
        }
        
        return $mReturn;
    }
}

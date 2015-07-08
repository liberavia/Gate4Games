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
            $oReturn = parent::lvGetProduct();
        }
        
        return $oReturn;
    }
    
    
    /**
     * Get article long description
     *
     * @return object $oField field object
     */
    public function getLongDescription()
    {
        $sMasterVariantOxid = $this->_lvGetMasterVariantId();
        if ( $sMasterVariantOxid ) {
            if ( $this->_oLongDesc === null ) {
                // initializing
                $this->_oLongDesc = new oxField();


                // choosing which to get..
                $sOxid = $sMasterVariantOxid;
                $sViewName = getViewName( 'oxartextends', $this->getLanguage() );

                $oDb = oxDb::getDb();
                $sDbValue = $oDb->getOne("select oxlongdesc from {$sViewName} where oxid = " . $oDb->quote($sOxid));

                if ($sDbValue != false) {
                    $this->_oLongDesc->setValue($sDbValue, oxField::T_RAW);
                } elseif ($this->oxarticles__oxparentid->value) {
                    if (!$this->isAdmin() || $this->_blLoadParentData) {
                        $oParent = $this->getParentArticle();
                        if ($oParent) {
                            $this->_oLongDesc->setValue($oParent->getLongDescription()->getRawValue(), oxField::T_RAW);
                        }
                    }
                }
            }

            return $this->_oLongDesc;
        }
        else {
            return parent::getLongDescription();
        }
    }
    
    
    /**
     * Template getter to receive short desc of article
     * 
     * @param void
     * @return string
     */
    public function lvGetShortDescription() {
        $sShortDesc = '';
        
        $oProduct = $this->lvGetProduct();
        
        if ( $oProduct ) {
            $sShortDesc = $oProduct->oxarticles__oxshortdesc->value;
        }

        // search all existing variants for shortdesc as default
        if ( !$sShortDesc ) {
            $sShortDesc = $this->_lvGetDefaultShortDesc();
        }
        
        return $sShortDesc;
    }
    
    
    /**
     * Searches all variants for a shortdesc
     * 
     * @param void
     * @return string
     */
    protected function _lvGetDefaultShortDesc() {
        $sShortDesc = '';
        $oLang              = oxRegistry::getLang();
        $sLangAbbr  = $oLang->getLanguageAbbr();
        
        if ( $this->oxarticles__oxparentid->value == '' ) {
            $oParentProduct = $this;
        }
        else {
            $oParentProduct = $this->getParentArticle();
        }
        
        $aVariants = $oParentProduct->getVariants();
        $blMatch = false;
        foreach ( $aVariants as $oVariant ) {
            if ( $blMatch === true || $oVariant->oxarticles__lvlangabbr->value != $sLangAbbr ) continue; 

            if ( $oVariant->oxarticles__oxshortdesc->value != '' ) {
                $sShortDesc = $oVariant->oxarticles__oxshortdesc->value;
                $blMatch = true;
            }
        }
        
        return $sShortDesc;
    }

    
    /**
     * Loads and returns attribute list associated with this article
     *
     * @return object
     */
    public function getAttributes()
    {
        $sMasterVariantOxid = $this->_lvGetMasterVariantId();
// echo "MasterVariantID:".$sMasterVariantOxid."<br>";        
        if ( $sMasterVariantOxid ) {
            if ($this->_oAttributeList === null) {
                $this->_oAttributeList = oxNew( 'oxattributelist' );
                $this->_oAttributeList->loadAttributes($sMasterVariantOxid, $this->getParentId());
            }

            return $this->_oAttributeList;
        }
        else {
            return parent::getAttributes();
        }
    }
    
    
    /**
     * Method delivers sum of all attributes of all variants of selected language
     * 
     * @param void
     * @return array
     */
    public function lvGetSummedAttributes() {
        $aSummedAttributes  = array();
        $oLang              = oxRegistry::getLang();
        $sLangAbbr          = $oLang->getLanguageAbbr();
        
        if ( $this->oxarticles__oxparentid->value == '' ) {
            $oParentProduct = $this;
        }
        else {
            $oParentProduct = $this->getParentArticle();
        }
        
        $aVariants = $oParentProduct->getVariants();
        
        // parse through each variant if of same language and merge with return array
        foreach ( $aVariants as $oVariant ) {
            if ( $oVariant->oxarticles__lvlangabbr->value != $sLangAbbr ) continue; 
            
            $oCurrentAttributeList = oxNew( 'oxattributelist' );
            $oCurrentAttributeList->loadAttributes( $oVariant->getId(), $oParentProduct->getId() );
            
            foreach ( $oCurrentAttributeList as $sKey=>$oAttribute ) {
                $sTitleHash = md5( $oAttribute->oxattribute__oxtitle->value );
                
                // check if key with title hash already exists. merge value if true, add new if false
                if ( isset( $aSummedAttributes[$sTitleHash] ) ) {
                    if ( $oAttribute->oxattribute__oxvalue->value != $aSummedAttributes[$sTitleHash]['value'] ) {
                        $aSummedAttributes[$sTitleHash]['value'] .= ', '.$oAttribute->oxattribute__oxvalue->value;
                    }
                }
                else {
                    $aSummedAttributes[$sTitleHash]['title'] = $oAttribute->oxattribute__oxtitle->value;
                    $aSummedAttributes[$sTitleHash]['value'] = $oAttribute->oxattribute__oxvalue->value;
                }
            }
        }
        
        return $aSummedAttributes;
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
        if ( !$this->getParentId() ) {
            $sOxid      = $this->getId();
            $oLang      = oxRegistry::getLang();
            $sLangAbbr  = $oLang->getLanguageAbbr();
            
            $oDb = oxDb::getDb( FETCH_MODE_ASSOC );
            $sArticleTable = getViewName( 'oxarticles' );
            
            $sQuery = "SELECT OXID, LVMASTERVARIANT FROM ".$sArticleTable." WHERE OXPARENTID='".$sOxid."' AND LVLANGABBR='".$sLangAbbr."'";
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
    
    
    /**
     * Public getter delivers highest tprice of all variants
     * 
     * @param void
     * @return object
     */
    public function lvGetMostExpansiveTPrice() {
        $dMaxTPrice     = 0;
        $oReturn        = false;
        $aVariants      = $this->getVariants();
        
        if ( count( $aVariants ) > 0 ) {
            foreach ( $aVariants as $oSimpleVariant ) {
                $oVariant = oxNew( 'oxarticle' );
                $oVariant->load( $oSimpleVariant->getId() );
                $dTPrice = $oVariant->oxarticles__oxtprice->value;
                $oVariantTPrice = oxNew( 'oxprice' );
                $oVariantTPrice->setBruttoPriceMode();
                $oVariantTPrice->setPrice( $dTPrice );
                
                if ( $oVariantTPrice ) {
                    $dVariantTPrice = $oVariantTPrice->getBruttoPrice();
                    if ( $dVariantTPrice > $dMaxTPrice ) {
                        $dMaxTPrice = $dVariantTPrice;
                        $oReturn = $oVariantTPrice;
                    }
                }
            }
        }
        else {
            $oReturn = $this->getTPrice();
        }
        
        return $oReturn;
    }
    
}

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
 * Description of lvgateosapi
 *
 * @author Gate4Games
 * @author André Gregor-Herrmann
 */
class lvgateosapi extends oxBase {
    
    /**
     * View for articles table
     * @var string
     */
    protected $_sArticlesTable = '';

    /**
     * View for oxobject2attribute table
     * @var string
     */
    protected $_sObject2AttributeTable = '';
    
    /**
     * Array of allowed compatibility attributes
     * @var array
     */
    protected $_aCompatibilityAttributes = array( 'CompatibilityTypeLin' );
    
    /**
     * Settings parameters
     * @var array
     */
    protected $_aParams = array();
    
    /**
     * Database object
     * @var object
     */
    protected $_oLvDb = null;
    
    
    public function __construct() {
        parent::__construct();
        
        $this->_sArticlesTable          = getViewName( 'oxarticles' );
        $this->_sObject2AttributeTable  = getViewName( 'oxobject2attribute' );
        $this->_oLvDb                   = oxDb::getDb( oxDb::FETCH_MODE_ASSOC );
    }
    
    /**
     * Performs a request and returns the result as xml
     * 
     * @param array $aParams
     * @return string
     */
    public function lvGetRequestResult( $aParams=null ) {
        $this->_lvSetParams( $aParams );
        $aArticles  = $this->_lvGetArticles();
        $sXml       = $this->_lvGetXml( $aArticles );
        
        return $sXml;
    }
    
    /**
     * Template getter delivers all information needed for affiliate products
     * 
     * @param void
     * @return array
     */
    public function lvGetAffiliateDetails() {
        $aAffiliatesForProduct = array();
        $aSortedVariantIds = $this->_lvGetSortedVariantIds();
        
        if ( count( $aSortedVariantIds ) > 0 ) {
            $iIndex = 0;
            $iCurrentLangId = oxRegistry::getLang()->getBaseLanguage();
            foreach ( $aSortedVariantIds as $sVariantId ) {
                $oArticle = oxNew( 'oxarticle' );
                $oArticle->loadInLang( $iCurrentLangId, $sVariantId );

                if ( $oArticle ) {
                    $aAffiliatesForProduct[$iIndex]['vendor']    = $oArticle->getVendor();
                    $aAffiliatesForProduct[$iIndex]['product']   = $oArticle;
                }
                $iIndex++;
            }
        }
        
        return $aAffiliatesForProduct;
    }
    
    
    /**
     * Returns variant ids sorted by best price
     * 
     * @param void
     * @return array
     */
    protected function _lvGetSortedVariantIds() {
        $aVariantIds = array();
        $sOxid = $this->getId();
        
        if ( $sOxid ) {
            $oDb                = oxDb::getDb( FETCH_MODE_ASSOC );
            $sArticlesTable     = getViewName( 'oxarticles' );
            $sQuery = "SELECT OXID FROM ".$sArticlesTable." WHERE OXPARENTID='".$sOxid."' AND OXACTIVE = '1' ORDER BY OXPRICE ASC";
            
            $oResult = $oDb->Execute( $sQuery );
            
            if ( $oResult != false && $oResult->recordCount() > 0 ) {
                while( !$oResult->EOF ) {
                    $aVariantIds[] = $oResult->fields['OXID'];
                    $oResult->moveNext();
                }
            }
        }
        
        return $aVariantIds;
    }

    /**
     * Returns the xml 
     * 
     * @param array $aArticles
     * @return string
     */
    protected function _lvGetXml( $aArticles ) {
        $sXml = '<?xml version="1.0" encoding="UTF-8"?>';
        if ( count( $aArticles ) > 0 ) {
            if ( isset( $this->_aParams['id'] ) ) {
                $oArticle       = $aArticles[0];
                $oManufacturer  = $oArticle->getManufacturer();
                $aMediaData     = $oArticle->lvGetAllMedia();
                
                $sXml .= '<product>';
                $sXml .= "\t".'<id>'.$oArticle->getId().'</id>';
                $sXml .= "\t".'<name><![CDATA['.$oArticle->oxarticles__oxtitle->value.']]></name>';
                $sXml .= "\t".'<currency>EUR</currency>';
                $sXml .= "\t".'<description><![CDATA['.$oArticle->lvGetShortDescription().']]></description>';
                $sXml .= "\t".'<longdesc><![CDATA['.$oArticle->lvGetShortDescription().']]></longdesc>';
                $sXml .= "\t".'<manufacturer><![CDATA['.$oManufacturer->getTitle().']]></manufacturer>';
                $sXml .= "\t".'<igdb>'.$oArticle->oxarticles__lvigdb_rating->value.'</igdb>';
                $sXml .= "\t".'<coverpic>'.$oArticle->lvGetCoverPictureUrl().'</coverpic>';
                $sXml .= "\t".'<fanart></fanart>';
                $sXml .= "\t".'<pictures>';
                foreach ( $aMediaData as $aMedia ) {
                    if ( $aMedia['mediatype'] == 'extpic' ) {
                        $sXml .= "\t\t".'<picture>'.$aMedia['detailsurl'].'</picture>';
                    }
                }
                $sXml .= "\t".'</pictures>';
                $sXml .= "\t".'<videos>';
                foreach ( $aMediaData as $aMedia ) {
                    if ( $aMedia['mediatype'] == 'youtube' ) {
                        $sXml .= "\t\t".'<video>'.$aMedia['url'].'</video>';
                    }
                }
                $sXml .= "\t".'</videos>';
                $sXml .= "\t".'<prices>';
                foreach ( $this->lvGetAffiliateDetails as $aAffiliate ) {
                    $sXml .= "\t\t".'<vendor>';
                    $sXml .= "\t\t\t".'<vendorname>'.$aAffiliate['vendor']->getTitle().'</vendorname>';
                    $sXml .= "\t\t\t".'<vendoricon>'.$aAffiliate['vendor']->getIconUrl().'</vendoricon>';
                    $sXml .= "\t\t\t".'<vendorprice>'.$aAffiliate['product']->getPrice()->getBruttoPrice().'</vendorprice>';
                    $sXml .= "\t\t\t".'<vendorlink>'.$aAffiliate['product']->oxarticles__oxexturl->rawValue.'</vendorlink>';
                    $sXml .= "\t\t".'</vendor>';                
                }
                $sXml .= "\t".'</prices>';
                $sXml .= '</product>';
            }
            else {
                $sXml .= '<products>';
                foreach ( $aArticles as $oArticle ) {
                    $sXml .= "\t".'<product>';
                    $sXml .= "\t\t".'<id>'.$oArticle->getId().'</id>';
                    $sXml .= "\t\t".'<name><![CDATA['.$oArticle->oxarticles__oxtitle->value.']]></name>';
                    $sXml .= "\t\t".'<fromprice>'.$oArticle->oxarticles__oxvarminprice->value.'</fromprice>';
                    $sXml .= "\t\t".'<currency>EUR</currency>';
                    $sXml .= "\t\t".'<coverpic>'.$oArticle->lvGetCoverPictureUrl().'</coverpic>';
                    $sXml .= "\t\t".'<fanart></fanart>';
                    $sXml .= "\t".'</product>';
                }
                $sXml .= '</products>';
            }
        }
    }
    
    
    /**
     * Set request parameters
     * 
     */
    protected function _lvSetParams( $aParams ) {
        if ( $aParams !== null && is_array( $aParams ) ) {
            $this->_aParams = $aParams;
        }
    }
    
    
    /**
     * Returns an array of article objects depending on params
     * 
     * @param void
     * @return array
     */
    protected function _lvGetArticles() {
        $aArticles  = array();
        $sQuery     = $this->_lvGetQuery();
        
        $oRs = $this->_oLvDb->Execute( $sQuery );
        
        if ( $oRs != false && $oRs->recordCount() > 0 ) {
            while ( !$oRs->EOF ) {
                $sOxid          = $oRs->fields['OXID'];
                $sOxParentId    = $oRs->fields['OXPARENTID'];
                if ( $sOxid ) {
                    $sArticleId = ($sOxParentId) ? $sOxParentId : $sOxid;
                    $oArticle = oxNew( 'oxarticle' );
                    $oArticle->load( $sArticleId );
                    
                    if ( $oArticle ) {
                        $aArticles[] = $oArticle;
                    }
                }
                $oRs->moveNext();
            }
        }
        
        return $aArticles;
    }
    
    
    /**
     * Returns the query for requesting articleids
     * 
     * @param void
     * @return string
     */
    protected function _lvGetQuery() {
        if ( isset( $this->_aParams['id'] ) ) {
            $sQuery = "SELECT OXID FROM ".$this->_sArticlesTable." WHERE OXID='".$this->_oLvDb->quote( $this->_aParams['id'] );
        }
        else {
            $iPage = 1;
            if ( isset( $this->_aParams['page'] ) && is_numeric( $this->_aParams['page'] ) ) {
                $iPage = (int)$this->_aParams['page'];
            }

            $iLimit = 25;
            if ( isset( $this->_aParams['limit'] ) && is_numeric( $this->_aParams['limit'] ) ) {
                $iLimit = (int)$this->_aParams['limit'];
            }

            if ( $iPage <= 1 ) {
                $iFrom = 0;
            }
            else {
                $iFrom = ( $iPage-1 ) * $iLimit;
            }
            
            // fetch allowed attributes
            $sAllowedAttributes = implode( ", ", $this->_oLvDb->quoteArray( $this->_aCompatibilityAttributes ) );

            $sQuery ="
                SELECT oa.OXID, oa.OXPARENTID
                FROM ".$this->_sObject2AttributeTable." o2a
                LEFT JOIN ".$this->_sArticlesTable." oa ON (o2a.OXOBJECTID=oa.OXID)
                WHERE o2a.OXATTRID IN ( ".$sAllowedAttributes." )
                ORDER BY oa.LVIGDB_RELEVANCE
                LIMIT ".$iFrom.",".$iLimit."
            ";
        }
        return $sQuery;
    }
    
    
}

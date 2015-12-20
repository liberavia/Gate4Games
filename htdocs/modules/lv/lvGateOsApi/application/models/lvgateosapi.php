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
    
    /**
     * New Line string
     * @var string
     */
    protected $_sNewLine = "\n";




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
    public function lvGetAffiliateDetails( $sOxid ) {
        $aAffiliatesForProduct = array();
        $aSortedVariantIds = $this->_lvGetSortedVariantIds( $sOxid );

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
     * Template getter returns an array with review videos
     * 
     * @param object $oProduct
     * @return array
     */
    public function lvGetReviewVideos( $oProduct ) {
        $aReturn    = array();
        $oArticle   = $oProduct->lvGetProduct();
        
        foreach ( $oArticle->getMediaUrls() as $oMediaUrl ) {
            if ( $oMediaUrl->oxmediaurls__lvmediatype->value != 'productreview' ) continue;
            
            $aReturn[] = $oMediaUrl->oxmediaurls__oxurl->value;            
        }
        
        return $aReturn;
    }
    
    
    /**
     * Returns variant ids sorted by best price
     * 
     * @param void
     * @return array
     */
    protected function _lvGetSortedVariantIds( $sOxid ) {
        $aVariantIds = array();
        
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
        $sXml = '<?xml version="1.0" encoding="UTF-8"?>'.$this->_sNewLine;
        if ( count( $aArticles ) > 0 ) {
            if ( isset( $this->_aParams['id'] ) ) {
                $oArticle       = $aArticles[0];
                $oManufacturer  = $oArticle->getManufacturer();
                $aMediaData     = $oArticle->lvGetAllMedia();
                
                $sXml .= '<product>'.$this->_sNewLine;
                $sXml .= "\t".'<id>'.$oArticle->getId().'</id>'.$this->_sNewLine;
                $sXml .= "\t".'<name><![CDATA['.$oArticle->oxarticles__oxtitle->value.']]></name>'.$this->_sNewLine;
                $sXml .= "\t".'<currency>EUR</currency>'.$this->_sNewLine;
                $sXml .= "\t".'<description><![CDATA['.$oArticle->lvGetShortDescription().']]></description>'.$this->_sNewLine;
                $sXml .= "\t".'<longdesc><![CDATA['.$oArticle->lvGetShortDescription().']]></longdesc>'.$this->_sNewLine;
                $sXml .= "\t".'<manufacturer><![CDATA['.$oManufacturer->getTitle().']]></manufacturer>'.$this->_sNewLine;
                $sXml .= "\t".'<igdb>'.$oArticle->oxarticles__lvigdb_rating->value.'</igdb>'.$this->_sNewLine;
                $sXml .= "\t".'<coverpic>'.$oArticle->lvGetCoverPictureUrl().'</coverpic>'.$this->_sNewLine;
                $sXml .= "\t".'<fanart></fanart>'.$this->_sNewLine;
                $sXml .= "\t".'<pictures>'.$this->_sNewLine;
                foreach ( $aMediaData as $aMedia ) {
                    if ( $aMedia['mediatype'] == 'extpic' ) {
                        $sXml .= "\t\t".'<picture>'.$aMedia['detailsurl'].'</picture>'.$this->_sNewLine;
                    }
                }
                $sXml .= "\t".'</pictures>'.$this->_sNewLine;
                $sXml .= "\t".'<trailers>'.$this->_sNewLine;
                foreach ( $aMediaData as $aMedia ) {
                    if ( $aMedia['mediatype'] == 'youtube' ) {
                        $sXml .= "\t\t".'<trailer>'.$aMedia['url'].'</trailer>'.$this->_sNewLine;
                    }
                }
                $sXml .= "\t".'</trailers>'.$this->_sNewLine;
                $sXml .= "\t".'<review_videos>'.$this->_sNewLine;
                foreach ( $this->lvGetReviewVideos( $oArticle ) as $sReviewUrl ) {
                    $sXml .= "\t\t".'<review_video>'.$sReviewUrl.'</review_video>'.$this->_sNewLine;
                }
                $sXml .= "\t".'</review_videos>'.$this->_sNewLine;
                $sXml .= "\t".'<prices>'.$this->_sNewLine;
                $aAffiliateDetails = $this->lvGetAffiliateDetails( $oArticle->getId() );
                foreach ( $aAffiliateDetails as $aAffiliate ) {
                    $sVendorLink    = $aAffiliate['product']->oxarticles__oxexturl->rawValue;
                    $sQrLink        = "https://chart.googleapis.com/chart?chs=500x500&cht=qr&choe=UTF-8&chl=".urlencode( $sVendorLink );
                    $sXml .= "\t\t".'<vendor>'.$this->_sNewLine;
                    $sXml .= "\t\t\t".'<vendorname>'.$aAffiliate['vendor']->getTitle().'</vendorname>'.$this->_sNewLine;
                    $sXml .= "\t\t\t".'<vendoricon>'.$aAffiliate['vendor']->getIconUrl().'</vendoricon>'.$this->_sNewLine;
                    $sXml .= "\t\t\t".'<vendorprice>'.$aAffiliate['product']->getPrice()->getBruttoPrice().'</vendorprice>'.$this->_sNewLine;
                    $sXml .= "\t\t\t".'<vendorlink>'.$sVendorLink.'</vendorlink>'.$this->_sNewLine;
                    $sXml .= "\t\t\t".'<vendorqrcode>'.$sQrLink.'</vendorlink>'.$this->_sNewLine;
                    $sXml .= "\t\t".'</vendor>'.$this->_sNewLine;                
                }
                $sXml .= "\t".'</prices>'.$this->_sNewLine;
                $sXml .= '</product>'.$this->_sNewLine;
            }
            else {
                $sXml .= '<result>'.$this->_sNewLine;
                $aListInfos = $this->_lvGetListInfos();
                $sXml .= "\t".'<listinfos>'.$this->_sNewLine;
                foreach ( $aListInfos as $sTag=>$sValue ) {
                    $sXml .= "\t\t".'<'.$sTag.'>'.$sValue.'</'.$sTag.'>'.$this->_sNewLine;
                }
                $sXml .= "\t".'</listinfos>'.$this->_sNewLine;
                $sXml .= "\t".'<products>'.$this->_sNewLine;
                foreach ( $aArticles as $oArticle ) {
                    $sXml .= "\t\t".'<product>'.$this->_sNewLine;
                    $sXml .= "\t\t\t".'<id>'.$oArticle->getId().'</id>'.$this->_sNewLine;
                    $sXml .= "\t\t\t".'<name><![CDATA['.$oArticle->oxarticles__oxtitle->value.']]></name>'.$this->_sNewLine;
                    $sXml .= "\t\t\t".'<fromprice>'.$oArticle->oxarticles__oxvarminprice->value.'</fromprice>'.$this->_sNewLine;
                    $sXml .= "\t\t\t".'<currency>EUR</currency>'.$this->_sNewLine;
                    $sXml .= "\t\t\t".'<coverpic>'.$oArticle->lvGetCoverPictureUrl().'</coverpic>'.$this->_sNewLine;
                    $sXml .= "\t\t\t".'<fanart></fanart>'.$this->_sNewLine;
                    $sXml .= "\t\t".'</product>'.$this->_sNewLine;
                }
                $sXml .= "\t".'</products>'.$this->_sNewLine;
                $sXml .= '</result>'.$this->_sNewLine;
            }
        }
        
        return $sXml;
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
     * Returns meta information for list to organize paging results etc.
     * 
     * @param void
     * @return array
     */
    protected function _lvGetListInfos() {
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
        
        // count result information
        $sQuery     = $this->_lvGetQuery( true );
        $oResult    = $this->_oLvDb->Execute( $sQuery );
        $iResults   = (int)$oResult->recordCount();
        $iMaxPage   = floor( ( $iResults/$iLimit ) );
        
        $aListInfos = array(
            'currentpage'       => $iPage,
            'maxpage'           => $iMaxPage,
            'resultsperpage'    => $iLimit,
            'resultssum'        => $iResults,
        );
        
        return $aListInfos;
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
    protected function _lvGetQuery( $blCount = false ) {
        if ( isset( $this->_aParams['id'] ) ) {
            $sQuery = "SELECT OXID FROM ".$this->_sArticlesTable." WHERE OXID=".$this->_oLvDb->quote( $this->_aParams['id'] );
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

            if ( $blCount ) {
                $sQuery ="
                    SELECT count(*)
                    FROM oxobject2attribute o2a
                    INNER JOIN oxarticles oa ON (o2a.OXOBJECTID=oa.OXID)
                    WHERE o2a.OXATTRID IN ( ".$sAllowedAttributes." )
                    GROUP BY oa.OXPARENTID
                ";
            }
            else {
                $sQuery ="
                    SELECT oa.OXID, oa.OXPARENTID, (SELECT LVIGDB_RELEVANCE FROM ".$this->_sArticlesTable." oa2 WHERE oa2.OXID=oa.OXPARENTID) as RELEVANCE
                    FROM ".$this->_sObject2AttributeTable." o2a
                    INNER JOIN ".$this->_sArticlesTable." oa ON (o2a.OXOBJECTID=oa.OXID)
                    WHERE o2a.OXATTRID IN ( ".$sAllowedAttributes." )
                    GROUP BY oa.OXPARENTID
                    ORDER BY RELEVANCE DESC
                    LIMIT ".$iFrom.",".$iLimit."
                ";
            }
        }

        return $sQuery;
    }
    
    
}

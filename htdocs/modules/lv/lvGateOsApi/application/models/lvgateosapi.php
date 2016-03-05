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
     * Array of allowed sort methods
     * @var array
     */
    protected $_aAllowedSortings = array( 
        'relevance'         => array(
            'select'    => ' (SELECT LVIGDB_RELEVANCE FROM %%ARTTABLE%% oa2 WHERE oa2.OXID=oa.OXPARENTID) as RELEVANCE ',
            'order'     => 'RELEVANCE',
        ), 
        'name'              => array(
            'select'    => ' (SELECT OXTITLE FROM %%ARTTABLE%% oa2 WHERE oa2.OXID=oa.OXPARENTID) as ARTNAME ',
            'order'     => 'ARTNAME',
        ), 
        'price'             => array(
            'select'    => '(SELECT OXVARMINPRICE FROM %%ARTTABLE%% oa2 WHERE oa2.OXID=oa.OXPARENTID) as PRICE ',
            'order'     => 'PRICE',
        ), 
        'igdb'              => array(
            'select'    => ' (SELECT LVIGDB_RATING FROM %%ARTTABLE%% oa2 WHERE oa2.OXID=oa.OXPARENTID) as RATING ',
            'order'     => 'RATING',
        ), 
        'release'       => array(
            'select'    => ' (SELECT LVIGDB_RELEASE_DATE FROM %%ARTTABLE%% oa2 WHERE oa2.OXID=oa.OXPARENTID) as RELEASEDATE ',
            'order'     => 'RELEASEDATE',
        ), 
    );
    
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
        $sXml       = $this->_lvGetInfoXml( $aArticles );
        
        return $sXml;
    }
    
    
    /**
     * Performs a request on the available gamegenre values
     * 
     * @param array $aParams
     * @return string
     */
    public function lvGetGenres( $aParams ) {
        $this->_lvSetParams( $aParams );
        $sXml = $this->_lvGetGenreXml();
        
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
     * Returns an xml of available genres
     * 
     * @param void
     * @return string
     */
    protected function _lvGetGenreXml() {
        $sXml = '<?xml version="1.0" encoding="UTF-8"?>'.$this->_sNewLine;
        $aGenres = $this->_lvGetGenres();
        if ( count( $aGenres ) > 0 ) {
            $sXml .= '<genres>'.$this->_sNewLine;
            foreach ( $aGenres as $sGenre ) {
                /**
                 * @todothe following replacement is just a workarround. It's mandatory to have a clean encoding instead
                 */
                $sGenre = $this->_lvRemoveCrap( $sGenre );
                $sXml .= "\t".'<genre>'.$this->_sNewLine;;
                $sXml .= "\t\t".'<name><![CDATA['.$sGenre.']]></name>'.$this->_sNewLine;
                $sXml .= "\t".'</genre>'.$this->_sNewLine;
            }
            $sXml .= '</genres>'.$this->_sNewLine;
        }
        
        return $sXml;
    }
    
    
    /**
     * Removes known crappy stuff from string
     * 
     * @param string $sCrappyIn
     * @return string
     */
    protected function _lvRemoveCrap( $sCrappyIn ) {
        $sOut = utf8_encode( $sCrappyIn );
        $sOut = str_replace( 'Ã´', 'o', $sOut );
        $sOut = str_replace( 'Ã©', 'e', $sOut );
        
        return $sOut;
    }
    
    
    /**
     * Returns the info xml 
     * 
     * @param array $aArticles
     * @return string
     */
    protected function _lvGetInfoXml( $aArticles ) {
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
                $sXml .= "\t".'<fanart>'.$oArticle->lvGetCoverPictureUrl().'</fanart>'.$this->_sNewLine;
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
                        $sXml .= "\t\t".'<trailer>'.$this->_sNewLine;
                        $sXml .= "\t\t\t".'<videourl>'.$aMedia['url'].'</videourl>'.$this->_sNewLine;
                        $sXml .= "\t\t".'</trailer>'.$this->_sNewLine;
                    }
                }
                $sXml .= "\t".'</trailers>'.$this->_sNewLine;
                $sXml .= "\t".'<review_videos>'.$this->_sNewLine;
                foreach ( $this->lvGetReviewVideos( $oArticle ) as $sReviewUrl ) {
                    $sXml .= "\t\t".'<review_video>'.$this->_sNewLine;
                    $sXml .= "\t\t\t".'<videourl>'.$sReviewUrl.'</videourl>'.$this->_sNewLine;
                    $sXml .= "\t\t".'</review_video>'.$this->_sNewLine;
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
                    $sXml .= "\t\t\t".'<vendorlink><![CDATA['.$sVendorLink.']]></vendorlink>'.$this->_sNewLine;
                    $sXml .= "\t\t\t".'<vendorqrcode><![CDATA['.$sQrLink.']]></vendorqrcode>'.$this->_sNewLine;
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
     * @param void
     * @return void
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
        $iMaxPage   = ceil( ( $iResults/$iLimit ) );
        
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
     * Returns an array of genres available
     * 
     * @param void
     * @return array
     */
    protected function _lvGetGenres() {
        $aGenres = array();
        // set attributes
        $aAttributes        = $this->_lvGetRequestAttributes();
        // filter values query
        $sFilterIdQuery     = $this->_lvGetFilteredIds( $aAttributes );
        
        $sQuery = "SELECT o2a.OXVALUE FROM ".$this->_sObject2AttributeTable." o2a WHERE o2a.OXATTRID='GameGenre' ".$sFilterIdQuery." GROUP BY o2a.OXVALUE ORDER BY o2a.OXVALUE ASC";

        $oRs = $this->_oLvDb->Execute( $sQuery );
        if ( $oRs != false && $oRs->recordCount() > 0 ) {
            while ( !$oRs->EOF ) {
                $aGenres[] = $oRs->fields['OXVALUE'];
                $oRs->moveNext();
            }
        }
        
        return $aGenres;
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
            
            // sorting
            $sSortSelect                = " (SELECT LVIGDB_RELEVANCE FROM ".$this->_sArticlesTable." oa2 WHERE oa2.OXID=oa.OXPARENTID) as RELEVANCE ";
            $sSortOrderBy               = "RELEVANCE";
            $sSortOrderDirection        = "DESC";
            
            if ( isset( $this->_aParams['sortby'] ) && in_array( $this->_aParams['sortby'], array_keys( $this->_aAllowedSortings ) ) ) {
                $sSortBy                = $this->_aParams['sortby'];
                $sSortSelect            = str_replace( '%%ARTTABLE%%', $this->_sArticlesTable, $this->_aAllowedSortings[$sSortBy]['select'] );
                $sSortOrderBy           = $this->_aAllowedSortings[$sSortBy]['order'];
                
                $sSortOrderDirection    = "DESC";
                if ( isset( $this->_aParams['sortdir'] ) && strtolower( $this->_aParams['sortdir'] ) == 'asc' ) {
                    $sSortOrderDirection    = "ASC";
                }
            }
            
            // set attributes
            $aAttributes                = $this->_lvGetRequestAttributes();
            // filter values query
            $sFilterIdQuery             = $this->_lvGetFilteredIds( $aAttributes );

            $sAllowedAttributes     = '';
            if ( count( $aAttributes ) > 0 ) {
                $aAttributeKeys         = array_keys( $aAttributes );
                $sAllowedAttributes = implode( ", ", $this->_oLvDb->quoteArray( $aAttributeKeys ) );
            }

            if ( $blCount ) {
                $sQuery ="
                    SELECT count(*)
                    FROM oxobject2attribute o2a
                    INNER JOIN oxarticles oa ON (o2a.OXOBJECTID=oa.OXID)
                    WHERE 1
                ";
                
                if ( $sAllowedAttributes ) {
                    $sQuery .= "
                        AND o2a.OXATTRID IN ( ".$sAllowedAttributes." )
                    ";
                    // filter values
                    $sQuery .= $sFilterIdQuery;
                }
                
                $sQuery .= "
                    GROUP BY oa.OXPARENTID
                ";
            }
            else {
                $sQuery  = "
                    SELECT 
                        oa.OXID, 
                        oa.OXPARENTID, 
                        ".$sSortSelect."
                    FROM ".$this->_sObject2AttributeTable." o2a
                    INNER JOIN ".$this->_sArticlesTable." oa ON (o2a.OXOBJECTID=oa.OXID)
                    WHERE 1
                ";
                
                if ( $sAllowedAttributes ) {
                    $sQuery .= "
                        AND o2a.OXATTRID IN ( ".$sAllowedAttributes." )
                    ";
                    // filter values
                    $sQuery .= $sFilterIdQuery;
                }
                
                $sQuery .= "
                    GROUP BY oa.OXPARENTID
                    ORDER BY ".$sSortOrderBy." ".$sSortOrderDirection."
                    LIMIT ".$iFrom.",".$iLimit."
                ";
            }
        }

        return $sQuery;
    }
    
    
    /**
     * Filters attributes to given attributes and their values
     * 
     * @param array $aAttributes
     * @return string
     */
    protected function _lvGetFilteredIds( $aAttributes ) {
        $sResultQuery               = "";
        $aCurrentlyFilteredIds      = array();
        
        foreach ( $aAttributes as $sAttributeId => $aAttribute ) {
            foreach ( $aAttribute as $sAttributeValue ) {
                if ( $sAttributeValue ) {
                    // SELECT OXOBJECTID FROM oxobject2attribute WHERE OXATTRID='CompatibilityTypeLin' AND OXVALUE='Ja' 
                    $sQuery = "SELECT OXOBJECTID FROM oxobject2attribute WHERE OXATTRID=".$this->_oLvDb->quote( $sAttributeId )." AND OXVALUE=".$this->_oLvDb->quote( $sAttributeValue );
                    // if we have results of a former filtering limit results to these ids
                    if ( count( $aCurrentlyFilteredIds ) > 0 ) {
                        $sCurrentlyFilteredIds = implode( ", ", $this->_oLvDb->quoteArray( $aCurrentlyFilteredIds ) );
                        $sQuery .= "
                            AND OXOBJECTID IN ( ".$sCurrentlyFilteredIds." )
                        ";
                    }

                    // refill filtered ids with new result
                    $aCurrentlyFilteredIds  = array();
                    $oRs                    = $this->_oLvDb->Execute( $sQuery );
                    
                    if ( $oRs != false && $oRs->recordCount() > 0 ) {
                        while ( !$oRs->EOF ) {
                            $aCurrentlyFilteredIds[] = $oRs->fields['OXOBJECTID'];
                            $oRs->moveNext();
                        }
                    }
                }
            }
        }
        
        // finally we have a set of ids which are completed filtered by given attribute values
        // bring this into the needed query form
        if ( count( $aCurrentlyFilteredIds ) > 0 ) {
            $sCurrentlyFilteredIds = implode( ", ", $this->_oLvDb->quoteArray( $aCurrentlyFilteredIds ) );
            $sResultQuery = "
                AND o2a.OXOBJECTID IN ( ".$sCurrentlyFilteredIds." )
            ";
        }
        
        return $sResultQuery;
    }




    /**
     * Builds attribute array of requested attributes
     * 
     * @param void
     * @return array
     */
    protected function _lvGetRequestAttributes() {
        $aAttributes = array();

        if ( isset( $this->_aParams['attributes'] ) && !empty( $this->_aParams['attributes'] ) ) {
            // explode attributes into groups for later fetching names and values
            $aAttributeGroups = explode( '|', $this->_aParams['attributes'] );
            foreach ( $aAttributeGroups as $sAttributeGroup ) {
                $aAttributeGroup = explode( "--", $sAttributeGroup );
                if ( count( $aAttributeGroup ) == 2 ) {
                    $aAttributeValues = explode( ',', $aAttributeGroup[1] );
                    if ( count( $aAttributeValues ) > 0 ) {
                        foreach ( $aAttributeValues as $sAttributeValue ) {
                            $aAttributes[$aAttributeGroup[0]]['values'] = $sAttributeValue;
                        }
                    }
                }
                else {
                    $aAttributes[$aAttributeGroup[0]]['values'] = array();
                }
            }
        }

        return $aAttributes;
    }
    
    
}

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
 * Description of lvusk
 *
 * @author Gate4Games
 * @author André Gregor-Herrmann
 */
class lvusk extends oxBase {
    
    /**
     * Database object
     * @var object
     */
    protected $_oLvDb = null;

    /**
     * Config object
     * @var object
     */
    protected $_oLvConfig = null;
    
    /**
     * Logfile used
     * @var string
     */
    protected $_sLogFile = 'lvusk.log';
    
    /**
     * Affiliate Tools from affiliate module
     * @var object
     */
    protected $_oAffiliateTools = null;

    /**
     * POST Values that need to be present on search request at USK
     * @var array
     */
    protected $_aLvUskPostValuesTemplate = array(
        "tx_uskdb_list[__referrer][extensionName]" => "UskDb",
        "tx_uskdb_list[__referrer][controllerName]" => "Title",
        "tx_uskdb_list[__referrer][actionName]"=> "newSearch",
        "tx_uskdb_list[paginatorData][page]" => "0",
        "tx_uskdb_list[search][genre]" => "",
        "tx_uskdb_list[search][platform]"=>"6", // PC
        "tx_uskdb_list[search][publisher]"=>"",
        "tx_uskdb_list[__hmac]"=>"a:4:{s:13:&quot;paginatorData&quot;;a:1:{s:4:&quot;page&quot;;i:1;}s:6:&quot;search&quot;;a:5:{s:11:&quot;ratingBound&quot;;i:1;s:5:&quot;title&quot;;i:1;s:9:&quot;publisher&quot;;i:1;s:5:&quot;genre&quot;;i:1;s:8:&quot;platform&quot;;i:1;}s:6:&quot;action&quot;;i:1;s:10:&quot;controller&quot;;i:1;}43ff359b7474ad5734ff84c7ac51c7189f75b340",
        "tx_uskdb_list[search][title]"=>"",
    ); 
    
    
    
    /**
     * Initiate needed objects and values
     */
    public function __construct() {
        parent::__construct();
        
        $this->_oLvConfig       = $this->getConfig();
        $this->_oLvDb           = oxDb::getDb( MODE_FETCH_ASSOC );
        $this->_oAffiliateTools = oxNew( 'lvaffiliate_tools' );
        
        $blLogActive    = (bool)$this->_oLvConfig->getConfigParam( 'blLvUskLogActive' );
        $iLvUskLogLevel = (int)$this->_oLvConfig->getConfigParam( 'sLvUskLogLevel' );
        
        $this->_oAffiliateTools->lvSetLogInformation( $blLogActive, $this->_sLogFile, $iLvUskLogLevel );
    }


    /**
     * Method takes a game title and returns usk information for the first hit
     * 
     * @param string $sTitle
     * @return mixed int/false
     */
    public function lvRequestAgeForTitle( $sTitle ) {
        $mUskAge = false;
        $sRequestUrl = $this->_oLvConfig->getConfigParam( 'sLvUskRequestBase' );
        if ( $sTitle && $sRequestUrl ) {
            $blLogActive = $this->_oLvConfig->getConfigParam( 'blLvUskLogActive' );
            $sTitle = urlencode( trim($sTitle) );
            $this->_aLvUskPostValuesTemplate['tx_uskdb_list[search][title]'] = $sTitle;
            $sResponse = $this->_oAffiliateTools->lvGetPostResult( $blLogActive, $sRequestUrl, $this->_aLvUskPostValuesTemplate );
            if ( $sResponse ) {
                $mUskAge = $this->_lvFetchUskAgeFromFirstMatch( $sResponse );
            }
        }
    }
    
    
    /**
     * Fetches recommended usk age from first match or false if no match has been found
     * 
     * @param string $sHtml
     * @return mixed int/false
     */
    protected function _lvFetchUskAgeFromFirstMatch( $sHtml ) {
        $mUskAge = false;
        $sSearchPattern = '/<img alt=".*" src="uploads\/tx_uskdb\/usk-([0-9]*).png" width="72" height="72" \/>/';
        $aResult        = array();
        
        preg_match_all( $sSearchPattern, $sHtml, $aResult );
        
        if ( isset( $aResult[1][0] ) && is_numeric( $aResult[1][0] ) ) {
            $mUskAge = (int)$aResult[1][0];
        }
        
        return $mUskAge;
    }
}

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
 * Description of lvwinehq
 *
 * @author Gate4Games
 * @author André Gregor-Herrmann
 */
class lvwinehq extends oxBase {
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
    protected $_sLogFile = 'lvwinehq.log';
    
    /**
     *
     * @var type 
     */
    protected $_sLvWineHqTable = 'lvwinehq';
    
    
    /**
     * Initiate needed objects and values
     */
    public function __construct() {
        parent::__construct();
        
        $this->_oLvConfig   = $this->getConfig();
        $this->_oLvDb       = oxDb::getDb( MODE_FETCH_ASSOC );
    }

    /**
     * Loggs a message depending on the defined loglevel. Default is debug-level
     * 
     * @param string $sMessage
     * @param int $iLogLevel
     * @return void
     */
    public function lvLog( $sMessage, $iLogLevel=4 ) {
        $oUtils = oxRegistry::getUtils();
        
        if ( $iLogLevel <= $this->_iLvAmzPnLogLevel ) {
            $sPrefix        = "[".date( 'Y-m-d H:i:s' )."] ";
            $sFullMessage   = $sPrefix.$sMessage."\n";
            
            $oUtils->writeToLog( $sFullMessage, $this->_sLogFile );
        }
    }


    /**
     * Fill database list by configuration
     * 
     * @param void
     * @return void
     */
    public function lvFillLists() {
        $sLvWineHqListRequestBase   = $this->_oLvConfig->getConfigParam( 'sLvWineHqListRequestBase' );
        $aLvWineHqRatings           = $this->_oLvConfig->getConfigParam( 'aLvWineHqRatings' );
        $sLvWineHqDetailsLinkBase   = $this->_oLvConfig->getConfigParam( 'sLvWineHqDetailsLinkBase' );
        
        foreach ( $aLvWineHqRatings as $sRating ) {
            $sRequestUrl = $sLvWineHqListRequestBase.$sRating;
            $sResponse = $this->_lvGetRequestResult( $sRequestUrl );
echo $sResponse;
die();
        }
    }
    
    
    /**
     * Performs a request on a webpage and delivers sourcecode back
     * 
     * @param string $sRequestUrl
     * @return array
     */
    protected function _lvGetRequestResult( $sRequestUrl ) {
        $aResponse = array();
        $resCurl = curl_init();
        
        // configuration
        curl_setopt_array( 
            $resCurl, 
            array(
                CURLOPT_RETURNTRANSFER => 1,
                CURLOPT_URL => $sRequestUrl,
            )
        );

        $sResponse = false;
        try {
            $sResponse = curl_exec( $resCurl );
        } 
        catch ( Exception $e ) {
            $this->lvLog( 'ERROR: Requesting url '.$sRequestUrl.'ended up with the following error:'.$e->getMessage(), 1 );
        }
        curl_close( $resCurl );
        
        return $sResponse;
    }
    
}

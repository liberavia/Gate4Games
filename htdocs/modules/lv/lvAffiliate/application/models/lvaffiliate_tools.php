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
 * Description of lvaffiliate_tools
 *
 * @author Gate4Games
 * @author André Gregor-Herrmann
 */
class lvaffiliate_tools extends oxBase {
    
    /**
     * Flag for active log
     * @var int
     */
    protected $_blLogActive = false;

    /**
     * Set log level default is 1 = Errors
     * @var int
     */
    protected $_iLogLevel = 1;
    
    /**
     * Logfile used
     * @var string
     */
    protected $_sLogFile = 'lvaffiliate_tools.log';

    
    /**
     * Sets logfile and loglevel to be using
     * 
     * @param type $sLogFile
     * @param type $iLogLevel
     * @return void
     */
    public function lvSetLogInformation( $blLogActive, $sLogFile, $iLogLevel ) {
        $this->_blLogActive = (bool)$blLogActive;
        $this->_sLogFile    = (string)$sLogFile;
        $this->_iLogLevel   = (int)$iLogLevel; 
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
        
        if ( $this->_blLogActive && $iLogLevel <= $this->_iLvAmzPnLogLevel ) {
            $sPrefix        = "[".date( 'Y-m-d H:i:s' )."] ";
            $sFullMessage   = $sPrefix.$sMessage."\n";
            
            $oUtils->writeToLog( $sFullMessage, $this->_sLogFile );
        }
    }
    
    
    /**
     * Performs a REST Request by given request url and returns demanded type of response 
     * 
     * @param string $sRequestUrl
     * @param string $sResponseType (XML|JSON|PLAIN=>Default)
     * @return mixed
     */
    public function lvGetRestRequestResult( $blLogActive, $sRequestUrl, $sResponseType='XML' ) {
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
        
        // format request
        $mResponse = null;
        
        switch ( $sResponseType ) {
            case 'XML':
                $mResonse   = new SimpleXMLElement( $sResponse );
                break;
            case 'JSON':
                $mResponse  = json_decode( $sResponse, true );
                break;
            default:
                $mResponse  = $sResponse;
        }
        
        return $oResonse;
    }
    
    /**
     * Guesses from title if the download is an addon
     * 
     * @param string $sTitle
     * @return boolean
     */
    public function lvFetchAddonFromTitle( $sTitle ) {
        $blAddon = false;
        
        if ( stripos( $sTitle, 'Add-on' ) ) {
            $blAddon = true;
        }
        
        return $blAddon;
    }
    
    
    /**
     * Guesses from title if download is DLC
     * 
     * @param string $sTitle
     * @return boolean
     */
    public function lvFetchDLCFromTitle( $sTitle ) {
        $blDLC = false;
        
        if ( strpos( $sTitle, 'DLC' ) ) {
            $blDLC = true;
        }
                
        return $blDLC;
    }
    
}

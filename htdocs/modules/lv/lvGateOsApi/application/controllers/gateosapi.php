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
 * Description of gateosapi
 *
 * @author Gate4Games
 * @author André Gregor-Herrmann
 */
class gateosapi extends oxUBase {

    /**
     * Init default if no fcn isset
     */
    public function init() {
        parent::init();
        $oConfig        = $this->getConfig();
        $sFnc           = $oConfig->getRequestParameter( 'fnc' );
        if ( !$sFnc ) {
            $this->lvGetInfo();
        }
    }
    
    /**
     * Get filtered and sorted xml list or details of product
     * 
     * @param void
     * @return string
     */
    public function lvGetInfo() {
        // init needed objects
        $oConfig        = $this->getConfig();
        $oLvGateOsApi   = oxNew( 'lvgateosapi' ); 
        
        $aParams        = array();
        
        // get parameters
        $sProductId     = $oConfig->getRequestParameter( 'id' );
        $sPage          = $oConfig->getRequestParameter( 'page' );
        $sLimit         = $oConfig->getRequestParameter( 'limit' );
        $sAttributes    = $oConfig->getRequestParameter( 'attributes' );
        $sSortBy        = $oConfig->getRequestParameter( 'sortby' );
        $sSortDir       = $oConfig->getRequestParameter( 'sortdir' );
        
        //assign params if exist
        if ( $sProductId ) {
            $aParams['id']          = trim( $sProductId );
        }
        if ( $sPage ) {
            $aParams['page']        = trim( $sPage );
        }
        if ( $sLimit ) {
            $aParams['limit']       = trim( $sLimit );
        }
        if ( $sAttributes ) {
            $aParams['attributes']  = trim( urldecode( $sAttributes ) );
        }
        if ( $sSortBy ) {
            $aParams['sortby']      = trim( urldecode( $sSortBy ) );
        }
        if ( $sSortDir ) {
            $aParams['sortdir']     = trim( urldecode( $sSortDir ) );
        }

        $sXml = $oLvGateOsApi->lvGetRequestResult( $aParams );
        
        exit( $sXml );
    }

    /**
     * Returns a list of genres as XML
     * 
     * @param void
     * @return string
     */
    public function lvGetGenres() {
        parent::init();
        
        $oConfig        = $this->getConfig();
        $oLvGateOsApi   = oxNew( 'lvgateosapi' ); 
        
        $aParams        = array();
        
        // get parameters
        $sAttributes    = $oConfig->getRequestParameter( 'attributes' );
        if ( $sAttributes ) {
            $aParams['attributes']  = trim( urldecode( $sAttributes ) );
        }
        $sXml           = $oLvGateOsApi->lvGetGenres( $aParams );
        
        exit( $sXml );
    }
    
}

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
    
    public function init() {
        parent::init();
        
        // init needed objects
        $oConfig        = $this->getConfig();
        $oLvGateOsApi   = oxNew( 'lvgateosapi' ); 
        
        $aParams        = array();
        // get parameters
        $sProductId     = $oConfig->getRequestParameter( 'id' );
        $sPage          = $oConfig->getRequestParameter( 'page' );
        $sLimit         = $oConfig->getRequestParameter( 'limit' );
        
        //assign params if exist
        if ( $sProductId ) {
            $aParams['id']      = trim( $sProductId );
        }
        if ( $sPage ) {
            $aParams['page']    = trim( $sPage );
        }
        if ( $sLimit ) {
            $aParams['limit']   = trim( $sLimit );
        }
        
        $sXml = $oLvGateOsApi->lvGetRequestResult( $aParams );
        
        exit( $sXml );
    }
    
}

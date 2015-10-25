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
 * Description of lvigdb_oxarticle
 *
 * @author Gate4Games
 * @author André Gregor-Herrmann
 */
class lvigdb_oxarticle extends lvigdb_oxarticle_parent {
    
    /**
     * Base URL
     * @var string
     */
    protected $_sLvIgdbBaseUrl = 'https://www.igdb.com/';
    
    /**
     * Link for igdb details page
     * @var mixed string/boolean/null
     */
    protected $_mIgdbLink = null;
    
    
    /**
     * Returns the IDGB-Details Link if there is one and false if not
     * 
     * @param void
     * @return mixed string/boolean
     */
    public function lvGetIGDBLink() {
        
        if ( $this->_mIgdbLink === null ) {
            $iLvIgdbId = (int)$this->oxarticles__lvigdb_id->value;

            if ( $iLvIgdbId > 0 ) {
                $this->_mIgdbLink = $this->_lvGetIGDBUrl( $iLvIgdbId );
            }
            else {
                $this->_mIgdbLink = false;
            }
        }
        
        return $this->_mIgdbLink;
    }
    
    
    /**
     * Returns the details url of game in IGDB.com or at least the base url if it fails
     * 
     * @param type $iLvIgdbId
     * @return string
     */
    protected function _lvGetIGDBUrl( $iLvIgdbId ) {
        $sReturn        = $this->_sLvIgdbBaseUrl;
        $oDb            = oxDb::getDb( oxDb::FETCH_MODE_ASSOC );
        
        $sQuery         = "SELECT LVIGDB_SLUG FROM lvigdb WHERE LVIGDBID='".$iLvIgdbId."' LIMIT 1";
        $sLvIgdbSlug    = $oDb->GetOne( $sQuery );
        
        if ( $sLvIgdbSlug ) {
            $sReturn    .= "games/";
            $sReturn    .= $sLvIgdbSlug;
        }
        
        return $sReturn;
    }
}

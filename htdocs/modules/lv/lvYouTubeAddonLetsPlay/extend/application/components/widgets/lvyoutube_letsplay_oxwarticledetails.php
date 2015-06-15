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
 * Description of lvyoutube_letsplay_oxwarticledetails
 *
 * @author Gate4Games
 * @author André Gregor-Herrmann
 */
class lvyoutube_letsplay_oxwarticledetails extends lvyoutube_letsplay_oxwarticledetails_parent {
    
    /**
     * Template getter returns an array with review videos
     * 
     * @param void
     * @return array
     */
    public function lvGetLetsPlayVideos() {
        $aReturn    = array();
        $oArticle   = $this->getProduct();
        
        foreach ( $oProduct->getMediaUrls() as $oMediaUrl ) {
            if ( $oMediaUrl->oxmediaurls__lvmediatype->value != 'productletsplay' ) continue;
            
            $aReturn[] = $oMediaUrl;            
        }
        
        return $aReturn;
    }
    
}

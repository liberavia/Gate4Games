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
 * Description of lvmv_oxwarticledetails
 *
 * @author Gate4Games
 * @author André Gregor-Herrmann
 */
class lvmv_oxwarticledetails extends lvmv_oxwarticledetails_parent {
    
    /**
     * Template getter delivers highest tprice of all variants
     * 
     * @param void
     * @return object
     */
    public function lvGetMostExpansiveTPrice() {
        $oProduct = $this->getProduct();
        
        return $oProduct->lvGetMostExpansiveTPrice();
    }
    
    
    /**
     * Template getter for attributes. Using summed attributes if parent and std if not
     * 
     * @param void
     * @return array
     */
    public function getAttributes() {
        $aAttributes    = array();
        $oProduct       = $this->getProduct();
        
        if ( $oProduct->oxarticles__oxparentid->value == '' ) {
            $aSummedAttributes = $oProduct->lvGetSummedAttributes();
            foreach ( $aSummedAttributes as $sKey=>$aSummedAttribute ) {
                $aAttributes[$sKey]         = new stdClass();
                $aAttributes[$sKey]->title  = $aSummedAttribute['title'];
                $aAttributes[$sKey]->value  = $aSummedAttribute['value'];
            }
        }
        else {
            $aAttributes = parent::getAttributes();
        }
        
        return $aAttributes;
    }
    
}

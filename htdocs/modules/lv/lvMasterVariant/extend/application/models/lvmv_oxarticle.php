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
 * Description of lvmv_oxarticle
 *
 * @author Gate4Games
 * @author André Gregor-Herrmann
 */
class lvmv_oxarticle extends lvmv_oxarticle_parent {
    
    /**
     * Contains the article object of master variant
     * @var object
     */
    protected $_oLvMasterVariant = null;


    
    /**
     * Wrapper method for getting the product object
     * 
     * @param void
     * @return object
     */
    protected function _lvGetProduct() {
        return $this->_lvGetMasterVariant();
    }

    
    /**
     * Returns the master variant of article
     * 
     * @param void
     * 
     */
    protected function _lvGetMasterVariant() {
        if ( $this->_oLvMasterVariant === null ) {
            if ( $this->getVariantsCount() > 0 ) {
                $iIndex = 0;
                foreach ( $this->getVariants() as $oVariant ) {
                    if ( $iIndex == 0 ) {
                        // we better save first variant as fallback
                        $oTargetVariant = $oVariant;
                    }
                    if ( $oVariant->oxarticles__lvmastervariant->value == '1' ) {
                        $oTargetVariant = $oVariant;
                    }
                    $iIndex++;
                }
                
                $this->_oLvMasterVariant = $oTargetVariant;
            }
            else {
                // is already variant so deliver parent call
                return parent::_lvGetMasterVariant();
            }
        }
        
        return $this->_oLvMasterVariant;
    }
}

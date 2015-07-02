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
 * Description of lvaffiliate_oxcontentlist
 *
 * @author Gate4Games
 * @author André Gregor-Herrmann
 */
class lvaffiliate_oxcontentlist extends lvaffiliate_oxcontentlist_parent {
    /**
     * Load Array of Menue items and change keys of aList to catid
     */
    public function loadCatMenues()
    {
        parent::loadCatMenues();
        
        $aArray = $this->_aArray;

        if ( is_array( $aArray ) && count( $aArray ) > 0 ) {
            // only let those categories in where no parentloadid is given
            foreach ( $aArray as $sCatId=>$aContents ) {
                foreach ( $aContents as $iIndex=>$oContent )  {
                    if ( $oContent->oxcontents__lvparentloadid->value != '' ) {
                        unset( $aArray[$sCatId][$iIndex] );
                    }
                }
            }
        }
        
        $this->_aArray = $aArray;
    }
}

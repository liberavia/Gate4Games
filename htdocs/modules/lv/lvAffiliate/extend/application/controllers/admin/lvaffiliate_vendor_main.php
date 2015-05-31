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
 * Description of lvaffiliate_main_vendor
 *
 * @author Gate4Games
 * @author André Gregor-Herrmann
 */
class lvaffiliate_vendor_main extends lvaffiliate_vendor_main_parent {
    
    /**
     * Saves selection list parameters changes.
     *
     * @return mixed
     */
    public function save()
    {
        $aParams = oxRegistry::getConfig()->getRequestParameter("editval");
        
        if ( $aParams['oxvendor__lvmainvendor'] == '1' ) {
            // main vendor will be set so reset value fior all vendors
            $this->_lvResetMainVendor();
        }
        
        parent::save();
    }
    
    
    /**
     * Resets all vendors to have lvmainvendor zero
     * 
     * @param void
     * @return void
     */
    protected function _lvResetMainVendor() {
        $oDb = oxDb::getDb();
        
        $sQuery = "UPDATE oxvendor SET lvmainvendor='0'";
        
        $oDb->execute( $sQuery );
    }
}

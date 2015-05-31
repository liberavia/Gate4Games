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
 * Model class for offering a uniform possibility to enter new products into shop 
 *
 * @author Gate4Games
 * @author André Gregor-Herrmann
 */
class lvaffiliate_import extends oxBase {
    
    /**
     * VendorId of current import
     * @var string
     */
    protected $_sLvVendorId = null;
    
    
    /**
     * Setter for vendor id
     * 
     * @param string $sVendorId
     */
    public function lvSetVendorId( $sVendorId ) { 
        $this->_sLvVendorId = $sVendorId;
    }


    public function lvAddArticle( $aArticleData ) {
        echo $this->_sLvVendorId."\n";
        print_r( $aArticleData );
    }
    
}

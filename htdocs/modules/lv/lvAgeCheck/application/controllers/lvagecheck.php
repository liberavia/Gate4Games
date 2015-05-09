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
 * Description of lvagecheck
 *
 * @author Gate4Games
 * @author André Gregor-Herrmann
 */
class lvagecheck extends oxUBase {
    
    protected $_sThisTemplate = 'lvagecheck.tpl';
    
    
    public function render() {
        parent::render();
        
        $oConfig = $this->getConfig();
        
        $sForbidden = $oConfig->getRequestParameter( 'forbidden' );
        
        $this->_aView['blLvForbiddenByAge'] = false;
        if ( $sForbidden !== false ) {
            $this->_aView['blLvForbiddenByAge'] = true;
        }
        
        return $this->_sThisTemplate;
    }
    
    
    public function lvValidateAge() {
        $oConfig = $this->getConfig();
        
        $aParams = $oConfig->getRequestParameter( 'editval' );
        print_r( $aParams );
        die();
    }
    
}

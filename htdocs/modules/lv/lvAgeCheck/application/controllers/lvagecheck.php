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
    
    /**
     * Template to call for rendering
     * @var string
     */
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
    
    /**
     * Template getter returns an array of years til 100 years backwards from now
     * 
     * @param void
     * @return array
     */
    public function lvGetYears() {
        $iCurrentYear = (int)date( 'Y' );
        $iMaxYearDown = $iCurrentYear - 100;
        $aYears = array();
        
        for ( $iIndex=$iCurrentYear; $iIndex>=$iMaxYearDown; $iIndex-- ) {
            $aYears[] = $iIndex;
        }
        
        return $aYears;
    }


    /**
     * Template getter returns months from 1 to 12
     * 
     * @param void
     * @return array
     */
    public function lvGetMonths() {
        $aMonths = array();
        
        for ( $iIndex=1; $iIndex<=12; $iIndex++ ) {
            $aMonths[] = $iIndex;
        }
        
        return $aMonths;
    }
    

    /**
     * Template getter returns days from 1 to 31
     * 
     * @param void
     * @return array
     */
    public function lvGetDays() {
        $aDays = array();
        
        for ( $iIndex=1; $iIndex<=31; $iIndex++ ) {
            $aDays[] = $iIndex;
        }
        
        return $aDays;
    }
    
    
    /**
     * Validating age entry set timestamp into session and redirect user to referer
     * 
     * @param void
     * @return void
     */
    public function lvValidateAge() {
        $oConfig = $this->getConfig();
        
        $aParams = $oConfig->getRequestParameter( 'editval' );
        print_r( $aParams );
        die();
    }
    
}

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
 * Description of lvckeditor_oxviewconfig
 *
 * @author Gate4Games
 * @author André Gregor-Herrmann
 */
class lvckeditor_oxviewconfig extends lvckeditor_oxviewconfig_parent {
    
    /**
     * Method returns html code of CKEditor
     * 
     * @param void
     * @return string
     */
    public function lvGetCKEditor() {
        $oConfig            = $this->getConfig();
        $sActiveClassName   = $this->getActiveClassName();
        $aAllowedClasses    = $oConfig->getConfigParam( 'aLvCKEditorClasses' );        
        $blEnabled          = in_array( $sActiveClassName, array_keys( $aAllowedClasses ) );
        
        if ( $blEnabled ) {
            $oLvCKEditor = oxNew( 'lvckeditor' );
            $sField = $aAllowedClasses[$sActiveClassName];
            $sReturn = $oLvCKEditor->lvGetCKEditor( $sField );
        }
        else {
            $sReturn = "";
        }
        
        return $sReturn;
    }
    
    
    /**
     * Returns if CKEditor can be used here
     * 
     * @param bool $blReturnText
     * @return bool/string
     */
    public function lvUseCKEditor( $blReturnText = false ) {
        $oConfig            = $this->getConfig();
        $sActiveClassName   = $this->getActiveClassName();
        $aAllowedClasses    = $oConfig->getConfigParam( 'aLvCKEditorClasses' );        
        $blEnabled          = in_array( $sActiveClassName, array_keys( $aAllowedClasses ) );
        
        if ( $blReturnText ) {
            if ( $blEnabled ) {
                $mReturn = "True";
            }
            else {
                $mReturn = "False";
            }
        }
        else {
            $mReturn = $blEnabled;
        }
        
        return $mReturn;
    }
    
}

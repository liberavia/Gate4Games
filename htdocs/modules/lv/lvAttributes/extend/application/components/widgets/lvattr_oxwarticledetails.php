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
 * Description of lvattr_oxwarticledetails
 *
 * @author Gate4Games
 * @author André Gregor-Herrmann
 */
class lvattr_oxwarticledetails extends lvattr_oxwarticledetails_parent {
    
    /**
     * Template getter returns an array with compatibility information
     * 
     * @param void
     * @return array
     */
    public function lvGetCompatibilityInformation() {
        $oArticle = $this->getProduct();
        return $oArticle->lvGetCompatibilityInformation();
    }
    
    
    /**
     * Sums up all compatibility information available of all variants
     * 
     * @param void
     * @return array
     */
    public function lvGetSumCompatibilityInformation() {
        $oArticle = $this->getProduct();
        return $oArticle->lvGetSumCompatibilityInformation();
    }

    
    /**
     * Template getter returns an array with age icons
     * 
     * @param void
     * @return array
     */
    public function lvGetAgeIcons() {
        $oArticle = $this->getProduct();
        return $oArticle->lvGetAgeIcons();
    }
    
}

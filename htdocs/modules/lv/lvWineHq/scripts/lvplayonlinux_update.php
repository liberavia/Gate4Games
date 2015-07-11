#!/usr/bin/php
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


if ( !function_exists( 'getShopBasePath' ) ) {
    function getShopBasePath() {
        return dirname(__FILE__)."/../../../../";
    }
}

require_once getShopBasePath()."bootstrap.php";

/**
 * Description of lvplayonlinux_update
 *
 * @author Gate4Games
 * @author André Gregor-Herrmann
 */
class lvplayonlinux_update extends oxBase {
    
    public function start() {
        $oLvPlayOnLinux = oxNew( 'lvplayonlinux' );
        
        if ( is_object( $oLvPlayOnLinux ) && method_exists( $oLvPlayOnLinux, 'lvFillLists' ) ) {
            $oLvWineHq->lvFillLists();
            // $oLvWineHq->lvUpdateProductAttributes();
        }
    }
}

$oScript = new lvplayonlinux_update();
$oScript->start();

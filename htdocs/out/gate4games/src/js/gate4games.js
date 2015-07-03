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

// Create a clone of the menu, right next to original.
//$('#header').addClass('original').clone().insertAfter('#header').addClass('cloned').css('position','fixed').css('top','0').css('margin-top','0').css('z-index','500').removeClass('original').hide();
//
//scrollIntervalID = setInterval(stickIt, 10);
//
//
//function stickIt() {
//
//  var orgElementPos = $('.original').offset();
//  orgElementTop = orgElementPos.top;               
//
//  if ($(window).scrollTop() >= (orgElementTop)) {
//    // scrolled past the original position; now only show the cloned, sticky element.
//
//    // Cloned element should always have same left position and width as original element.     
//    orgElement = $('.original');
//    coordsOrgElement = orgElement.offset();
//    leftOrgElement = coordsOrgElement.left;  
//    widthOrgElement = orgElement.css('width');
//    $('.cloned').css('left',leftOrgElement+'px').css('top',0).css('width',widthOrgElement).show();
//    $('.original').css('visibility','hidden');
//  } else {
//    // not scrolled past the menu; only show the original menu.
//    $('.cloned').hide();
//    $('.original').css('visibility','visible');
//  }
//}



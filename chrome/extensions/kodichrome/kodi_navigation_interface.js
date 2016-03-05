kodiNavigationInterface = '\
<div id="kodiChromeNavigation" class="kodiChromeBar kodiChromeBarHidden" style="display: none;">\
    <span id="kodiBarControls">\
        <input type="button" value="x" onclick="window.close();">\
        <input type="button" value="<-" onclick="window.history.back();">\
        <input type="button" value="->" onclick="window.history.forward();">\
    </span>\
    <span id="kodiBarUrl">\
        <input id="kodiChromeUrl" type="text">\
        <input type="button" id="kodiChromeLoadPageButton" value="Aufrufen">\
    </span>\
</div>';
$( document ).ready(function() {
    $("body").append(kodiNavigationInterface);
});


 

//$("body").append('Test');
// $("body").append(' <script type="text/javascript">function changeColor() { $(".myCheckClass").css("background","green");   $(".miniBasketBtn").remove(); }</script>'); 
// $("body").append('Mein Iframe, den ich kontrollieren will:<br>  <input type="button" onclick="changeColor()" value="Mach Gruen"><br>  <span class="myCheckClass"style="background:yellow;">Hallo</span><br>');
// $(".myCheckClass").css("background","green");
// $(".miniBasketBtn").remove();
kodiChromeBarContent = '\
<script type="text/javascript">\
$(document).keydown(function(event) { \
    if ( event.keyCode == 18 ) {\
        var CurrentClassName = $("#kodiChromeBar").attr("class"); \
        if ( CurrentClassName == "kodiChromeBarShow" ) {\
            $("#kodiChromeBar").removeClass("kodiChromeBarShow");\
            $("#kodiChromeBar").addClass("kodiChromeBarHidden");\
            $("#kodiChromeBar").slideUp("slow");\
        }\
        else {\
            $("#kodiChromeBar").removeClass("kodiChromeBarHidden");\
            $("#kodiChromeBar").addClass("kodiChromeBarShow");\
            $("#kodiChromeBar").slideDown("slow");\
        }\
    }\
});\
$( "#kodiChromeLoadPageButton" ).click(function() {\
    var TargetUrl = document.getElementById("kodiChromeUrl").value;\
    window.location.href = TargetUrl;\
});\
$("#kodiChromeUrl").keypress(function (e) {\
    var key = e.which;\
    if(key == 13) {\
        $("input[id = kodiChromeLoadPageButton]").click();\
        return false;\
    }\
});\
$( "#kodiChromeChangeColorButton" ).click(function() {\
    $(".miniBasketBtn").css("background","green");\
});\
</script>\
<br>\
<div id="kodiChromeBar" class="kodiChromeBarHidden" style="position: fixed;top:0px;right:0px;z-index:999999;width:100%;height:150px;background:purple;display:none;">\
    <input type="button" value="x" onclick="window.close();"><input type="button" value="<-" onclick="window.history.back();"><input type="button" value="->" onclick="window.history.forward();"><input id="kodiChromeChangeColorButton" type="button" value="Mach Gruen"><br>\
    <input id="kodiChromeUrl" type="text" size="200"><input type="button" id="kodiChromeLoadPageButton" value="Aufrufen">\
</div>';
$("body").append(kodiChromeBarContent)



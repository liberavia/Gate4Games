kodiControls = '\
<script type="text/javascript">\
$(document).on("keydown", function (e) {\
    if ( e.which === 8 || e.which === 13 ) {\
        e.preventDefault();\
    }\
});\
$(function () {\
    $("iframe").removeAttr("tabindex");\
    $("#kodiChromeNavigation").removeClass("kodiChromeBarDisplayed");\
    $("#kodiChromeNavigation").addClass("kodiChromeBarHidden");\
    $("#kodiChromeNavigation").slideUp();\
    var iKodiCurrentElementCount = 0;\
    var iDepthLevel = 0;\
    $(document).keyup(function(event) { \
        event.preventDefault();\
        var CurrentClassName = $("#kodiChromeNavigation").attr("class"); \
        if ( event.keyCode == 27 ) {\
            if ( CurrentClassName == "kodiChromeBar kodiChromeBarDisplayed" ) {\
                $("#kodiChromeNavigation").removeClass("kodiChromeBarDisplayed");\
                $("#kodiChromeNavigation").addClass("kodiChromeBarHidden");\
                $("#kodiChromeNavigation").slideUp("slow");\
            }\
            else {\
                $("#kodiChromeNavigation").removeClass("kodiChromeBarHidden");\
                $("#kodiChromeNavigation").addClass("kodiChromeBarDisplayed");\
                $("#kodiChromeNavigation").slideDown("slow");\
            }\
        }\
    });\
});\
$( "#kodiChromeLoadPageButton" ).click(function() {\
    var TargetUrl = document.getElementById("kodiChromeUrl").value;\
    var HttpEntered = TargetUrl.indexOf("http://");\
    if (HttpEntered == -1) {\
        TargetUrl = "http://" + TargetUrl;\
    }\
    window.location.href = TargetUrl;\
});\
$("#kodiChromeUrl").keyup(function (e) {\
    var key = e.which;\
    if(key == 13) {\
        var TargetUrl = document.getElementById("kodiChromeUrl").value;\
        var HttpEntered = TargetUrl.indexOf("http://");\
        if (HttpEntered == -1) {\
            TargetUrl = "http://" + TargetUrl;\
        }\
        window.location.href = TargetUrl;\
    }\
});\
</script>\
';
$( document ).ready(function() {
    $("body").append(kodiControls);
});

 

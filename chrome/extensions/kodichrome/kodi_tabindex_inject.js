kodiTabindexInject='\
<script type="text/javascript">\
$(function () {\
    var iKodiTabindexCount = 0;\
    $("iframe").removeAttr("tabindex");\
    $("[tabindex]").removeAttr("tabindex");\
    $( "body>table:visible,body>div:visible,body>section:visible,body>a:visible,body>input:visible,body>textarea:visible" ).each( function() {\
        var kodiElementId = $(this).attr("id");\
        var allowedTabindex = ( \
            (\
                typeof kodiElementId !== typeof undefined || \
                $(this).is("table") || \
                $(this).is("a") || \
                $(this).is("input") || \
                $(this).is("textarea") \
            ) &&  \
            $(this).css("visibility") != "hidden" \
        );\
        if ( allowedTabindex == true ) {\
            $(this).attr( "tabindex", iKodiTabindexCount );\
            iKodiTabindexCount++;\
        }\
    });\
    $("[tabIndex]").eq(0).addClass("kodiChromeActiveElement");\
});\
</script>\
';
$( document ).ready(function() {
    //$("body").append(kodiTabindexInject);
});
[{oxscript include="js/widgets/oxtopmenu.js" priority=10 }]
[{oxscript add="$('#navigation').oxTopMenu();"}]
[{oxstyle include="css/libs/superfish.css"}]
[{assign var="homeSelected" value="false"}]
[{if $oViewConf->getTopActionClassName() == 'start'}]
    [{assign var="homeSelected" value="true"}]
[{/if}]
[{assign var="oxcmp_categories" value=$oxcmp_categories }]
[{assign var="blShowMore" value=false }]
[{assign var="iCatCnt" value=0}]
<ul id="navigation" class="sf-menu">
    [{*<li [{if $homeSelected == 'true' }]class="current"[{/if}]><a [{if $homeSelected == 'true'}]class="current"[{/if}] href="[{$oViewConf->getHomeLink()}]">[{oxmultilang ident="HOME"}]</a></li>*}]
    [{foreach from=$oxcmp_categories item=ocat key=catkey name=root}]
      [{if $ocat->getIsVisible() }]
        [{assign var="iCatCnt" value=$iCatCnt+1 }]
        [{if $iCatCnt <= $oView->getTopNavigationCatCnt() }]
            <li [{if $homeSelected == 'false' && $ocat->expanded}]class="current"[{/if}]>
                <a  [{if $homeSelected == 'false' && $ocat->expanded}]class="current"[{/if}] href="[{$ocat->getLink()}]">[{$ocat->oxcategories__oxtitle->value}][{if $oView->showCategoryArticlesCount() && ($ocat->getNrOfArticles() > 0) }] ([{$ocat->getNrOfArticles()}])[{/if}]</a>
                [{if $ocat->getSubCats()}]
                    <ul>
                    [{foreach from=$ocat->getSubCats() item=osubcat key=subcatkey name=SubCat }]
                        [{if $osubcat->getIsVisible() }]
                            [{foreach from=$osubcat->getContentCats() item=ocont name=MoreCms}]
                                <li><a href="[{$ocont->getLink()}]">[{$ocont->oxcontents__oxtitle->value}]</a></li>
                            [{/foreach}]
                            [{if $osubcat->getIsVisible() }]
                                <li [{if $homeSelected == 'false' && $osubcat->expanded}]class="current"[{/if}] ><a [{if $homeSelected == 'false' && $osubcat->expanded}]class="current"[{/if}] href="[{$osubcat->getLink()}]">[{$osubcat->oxcategories__oxtitle->value}] [{if $oView->showCategoryArticlesCount() && ($osubcat->getNrOfArticles() > 0)}] ([{$osubcat->getNrOfArticles()}])[{/if}]</a></li>
                            [{/if}]
                        [{/if}]
                    [{/foreach}]
                    </ul>
                [{/if}]
            </li>
        [{else}]
            [{capture append="moreLinks"}]
               [{assign var="blShowMore" value=true }]
               <li [{if $homeSelected == 'false' && $ocat->expanded}]class="current"[{/if}]>
                    <a href="[{$ocat->getLink()}]">[{$ocat->oxcategories__oxtitle->value}][{if $oView->showCategoryArticlesCount() && ($ocat->getNrOfArticles() > 0)}] ([{$ocat->getNrOfArticles()}])[{/if}]</a>
               </li>
            [{/capture}]
        [{/if}]
      [{/if}]
    [{/foreach}]
    [{assign var="manufacturers" value=$oView->getManufacturerlist()}]
    [{if $manufacturers|count}]
        [{assign var="rootManufacturer" value=$oView->getRootManufacturer()}]
        <li>
            <a href="[{$rootManufacturer->getLink()}]">[{oxmultilang ident="ALL_BRANDS"}]</a>
            <ul>
                [{foreach from=$manufacturers item=_mnf name=manufacturers}]
                    [{if $_mnf->oxmanufacturers__lvtopmanufacturer->value == '1'}]
                        <li><a href="[{$_mnf->getLink()}]">[{$_mnf->oxmanufacturers__oxtitle->value}]</a></li>
                    [{/if}]
                [{/foreach}]    
                <li><a href="[{$rootManufacturer->getLink()}]">[{oxmultilang ident="LV_ALL_BRANDS"}]...</a></li>
            </ul>
        </li>
    [{/if}]

    [{foreach from=$oxcmp_categories item=ocat key=catkey name=root}]
        [{foreach from=$ocat->getContentCats() item=oTopCont name=MoreTopCms}]
            [{assign var="iCatCnt" value=$iCatCnt+1 }]
            [{if $iCatCnt <= $oView->getTopNavigationCatCnt()}]
                <li>
                    [{assign var="sLvCurrentLoadId" value=$oTopCont->oxcontents__oxloadid->value}]
                    [{if method_exists( $ocat, 'lvGetSubContentCats' ) && $ocat->lvGetSubContentCats("`$sLvCurrentLoadId`")}]
                        <a href="#">[{$oTopCont->oxcontents__oxtitle->value}]</a>
                        <ul>
                            [{foreach from=$ocat->lvGetSubContentCats("`$sLvCurrentLoadId`") item="ocontentsubcat" key=contentsubcatkey name=ContentSubCat}]
                                <li><a href="[{$ocontentsubcat->getLink()}]">[{$ocontentsubcat->oxcontents__oxtitle->value}]</a></li>
                            [{/foreach}]
                        </ul>
                    [{else}]
                        <a href="[{$oTopCont->getLink()}]">[{$oTopCont->oxcontents__oxtitle->value}]</a>
                    [{/if}]
                </li>
            [{else}]
                [{assign var="blShowMore" value=true }]
                [{capture append="moreLinks"}]
                    <li><a href="[{$oTopCont->getLink()}]">[{$oTopCont->oxcontents__oxtitle->value}]</a></li>
                [{/capture}]
            [{/if}]
        [{/foreach}]
    [{/foreach}]
    [{if $blShowMore }]
        <li>
            [{assign var="_catMoreUrl" value=$oView->getCatMoreUrl()}]
            <a href="[{oxgetseourl ident="`$_catMoreUrl`&amp;cl=alist" }]">[{oxmultilang ident="MORE" }]</a>
            <ul>
                [{foreach from=$moreLinks item=link}]
                   [{$link}]
                [{/foreach}]
            </ul>
        </li>
    [{/if}]
</ul>
[{oxscript widget=$oView->getClassName()}]
[{oxstyle widget=$oView->getClassName()}]
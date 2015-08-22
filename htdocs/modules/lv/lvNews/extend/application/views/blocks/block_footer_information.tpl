<dl id="footerInformation">
    <dt>[{oxmultilang ident="INFORMATION" }]</dt>
    <dd>
        <ul class="list services">
            <li><a href="[{oxgetseourl ident=$oViewConf->getSelfLink()|cat:"cl=lvnews_overview"}]">[{oxmultilang ident="LVNEWS_NEWS"}]</a></li>
            [{foreach from=$aServiceItems item=sItem}]
                [{if isset($aServices.$sItem)}]
                    <li><a href="[{$aServices.$sItem->getLink()}]">[{$aServices.$sItem->oxcontents__oxtitle->value}]</a></li>
                [{/if}]
            [{/foreach}]
[{*                
            <li><a href="[{ oxgetseourl ident=$oViewConf->getSelfLink()|cat:"cl=newsletter" }]" rel="nofollow">[{ oxmultilang ident="NEWSLETTER" }]</a></li>
*}]
        </ul>
    </dd>
</dl>

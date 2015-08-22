[{$smarty.block.parent}]
<tr>
    <td class="edittext">
        [{oxmultilang ident="LVNEWS_MAIN_SEOURL"}]
    </td>
    <td class="edittext">
        <input type="text" class="editinput" size="80" maxlength="255" name="lvnews_display_seo" value="[{$oView->lvGetSeoUrl()}]" disabled="true">
    </td>
</tr>
<tr>
    <td class="edittext">
        [{oxmultilang ident="LVNEWS_MAIN_TEASERTEXT"}]
    </td>
    <td class="edittext">
        [{$editor}]
        [{oxinputhelp ident="HELP_LVNEWS_MAIN_TEASERTEXT"}]
    </td>
</tr>

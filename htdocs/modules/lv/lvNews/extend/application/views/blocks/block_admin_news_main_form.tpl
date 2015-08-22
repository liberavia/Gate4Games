[{$smarty.block.parent}]
<tr>
    <td class="edittext">
    [{oxmultilang ident="LVNEWS_MAIN_SEOURL"}]
    </td>
    <td class="edittext">
    <input type="text" class="editinput" size="80" maxlength="[{$edit->oxnews__lvseourl->fldmax_length}]" name="editval[oxnews__lvseourl]" value="[{$edit->oxnews__lvseourl->value}]" [{$readonly}]>
    [{oxinputhelp ident="HELP_LVNEWS_MAIN_SEOURL"}]
    </td>
</tr>
<tr>
    <td class="edittext">
    [{oxmultilang ident="LVNEWS_MAIN_TEASERTEXT"}]
    </td>
    <td class="edittext">
        <textarea cols="40" rows="5" name="editval[oxnews__lvteasertext]" [{$readonly}]>[{$edit->oxnews__lvteasertext->value}]</textarea>
    [{oxinputhelp ident="HELP_LVNEWS_MAIN_TEASERTEXT"}]
    </td>
</tr>

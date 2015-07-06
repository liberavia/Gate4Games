[{$smarty.block.parent}]
<tr>
    <td class="edittext">
        [{ oxmultilang ident="LVAFFILIATE_PARENT_IDENT" }].
    </td>
    <td class="edittext">
        <input type="text" class="editinput" size="28" maxlength="[{$edit->oxcontents__lvparentloadid->fldmax_length}]" name="editval[oxcontents__lvparentloadid]" value="[{$edit->oxcontents__lvparentloadid->value}]" [{ $readonly }]>
    </td>
</tr>

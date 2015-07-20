[{$smarty.block.parent}]
<tr>
    <td class="edittext">
        [{oxmultilang ident="LVAFFILIATE_PARENT_IDENT"}].
    </td>
    <td class="edittext">
        <input type="text" class="editinput" size="28" maxlength="[{$edit->oxcontents__lvparentloadid->fldmax_length}]" name="editval[oxcontents__lvparentloadid]" value="[{$edit->oxcontents__lvparentloadid->value}]" [{ $readonly }]>
    </td>
</tr>

[{* FORM for uploading pictures*}]
<tr>
    <td class="edittext">
        [{oxmultilang ident="LVAFFILIATE_MEDIA_UPLOAD"}].
    </td>
    <td class="edittext">
        <form name="myedit2" id="myedit2" enctype="multipart/form-data" action="[{$oViewConf->getSelfLink()}]" method="post">
            [{ $oViewConf->getHiddenSid() }]
            <input type="hidden" name="cl" value="content_main">
            <input type="hidden" name="fnc" value="lvUploadPicture">
            <input type="file" name="mediaFile">
        </form>
    </td>
</tr>


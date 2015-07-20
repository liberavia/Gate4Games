[{* FORM for uploading pictures*}]
<table>
    <tr>
        <td class="edittext">
            [{oxmultilang ident="LVAFFILIATE_MEDIA_UPLOAD"}].
        </td>
        <td class="edittext">
            <form name="myedit2" enctype="multipart/form-data" action="[{$oViewConf->getSelfLink()}]" method="POST">
                <input type="hidden" name="MAX_FILE_SIZE" value="4000000" />
                [{$oViewConf->getHiddenSid()}]
                <input type="hidden" name="cl" value="content_main">
                <input type="hidden" name="fnc" value="lvUploadPicture">
                <input type="file" name="mediaFile">
                <input type="submit" value="[{oxmultilang ident="LVAFFILIATE_START_UPLOAD"}]">
            </form>
        </td>
    </tr>
</table>
[{$smarty.block.parent}]

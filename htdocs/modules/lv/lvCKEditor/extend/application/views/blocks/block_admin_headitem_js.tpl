<script type="text/javascript">
<!--
  // standard messages
  var sUnassignMessage = "[{ oxmultilang ident='GENERAL_YOUWANTTOUNASSIGN' }]";
  var sDeleteMessage   = "[{ oxmultilang ident='GENERAL_YOUWANTTODELETE' }]";;

  // class info
  var sDefClass = '[{ $default_edit }]';
  var sActClass = '[{$actlocation}]';

  [{ if $updatelist == 1}]
  window.onload = function ()
  {
      top.oxid.admin.updateList('[{ $oxid }]');
  }
  [{ /if}]


  var ajaxpopup = null;
  function showDialog( sParams )
  {
      ajaxpopup = window.open('[{ $oViewConf->getSelfLink()|replace:"&amp;":"&" }]'+sParams, 'ajaxpopup', 'width=800,height=680,scrollbars=yes,resizable=yes');
  }

  function focusPopup()
  {
      if ( ajaxpopup )ajaxpopup.focus();
  }

  window.onclick = focusPopup;

  function cleanupLongDesc( sValue )
  {
      if ( sValue == '<br>' || sValue == '<br />' ) {
          return '';
      }
      return sValue;
  }

  function copyLongDesc( sIdent )
  {
      var textVal = null;
      var varElemBody = null;

      try {
            if ( WPro.editors[sIdent] != null ) {
               WPro.editors[sIdent].prepareSubmission();
               textVal = cleanupLongDesc( WPro.editors[sIdent].getValue() );
            }
      } catch(err) {
            var varEl = document.getElementById(sIdent);
            if (varEl != null) {
                textVal = cleanupLongDesc( varEl.value );
            }
      }

      if (textVal == null) {
            var varName = 'editor_'+sIdent;
            var varEl = document.getElementById(varName);

            var varNameCKE = "cke_" + varName;
            var varElemCKE          = document.getElementById(varNameCKE);
            var varElemCKEIframe    = varElemCKE.getElementsByTagName('iframe');
            varElemBody             = varElemCKEIframe[0].contentDocument.body;

            if ( varElemBody != null ) {
                textVal = cleanupLongDesc( varElemBody.innerHTML );
            }
            else if (varEl != null) {
                textVal = cleanupLongDesc( varEl.value );
            }
      }

      if (textVal != null) {
            var oTarget = document.getElementsByName( 'editval['+ sIdent + ']' );
            if ( oTarget != null && ( oField = oTarget.item( 0 ) ) != null ) {
                oField.value = textVal;
            }
      }
  }
-->
</script>
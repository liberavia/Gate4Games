[{$smarty.block.parent}]
[{assign var="lvFeedbackJs" value=$oViewConf->getBaseDir()|cat:"modules/lv/lvFeedback/out/src/js/lvfeedback.js"}]
[{oxscript include=$lvFeedbackJs}]
<script src='https://www.google.com/recaptcha/api.js'></script>
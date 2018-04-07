<?php

##################
#
#	glz_custom_fields for Textpattern
#	version 2.0 – jools-r
#	Original version: Gerhard Lazu
#
##################

##################
#
#   CSS + JSS – For injecting into admin-side <head> area
#
##################


// -------------------------------------------------------------
// Contains minified output of glz_custom_fields.css file
// To update: copy the minified output of the actual css file into the function
function glz_custom_fields_head_css()
{
    $css = <<<'CSS'
.glz-cf-setup-switch{float:right}[dir=rtl] .glz-cf-setup-switch{float:left}#glz_custom_fields_container .txp-list-col-id{width:3em;text-align:center}#glz_custom_fields_container .txp-list-col-options,#glz_custom_fields_container .txp-list-col-position{width:5em}#glz_custom_fields_container .txp-list-col-title .cf-instructions.ui-icon{width:2em;height:17px;float:right;background-repeat:no-repeat;background-position:center 2px;opacity:.33;cursor:pointer}#glz_custom_fields_container .txp-list-col-title.disabled .cf-instructions{opacity:1!important;pointer-events:auto}#glz_custom_fields_container .txp-list-col-options{text-align:center}#glz_custom_fields_container .txp-list-col-options .ui-icon{width:4em;background-repeat:no-repeat;background-position:50%}#glz_custom_fields_container .txp-list-col-options .ui-icon:hover{-webkit-filter:brightness(0) saturate(100%) invert(17%) sepia(51%) saturate(5958%) hue-rotate(211deg) brightness(89%) contrast(101%);filter:brightness(0) saturate(100%) invert(17%) sepia(51%) saturate(5958%) hue-rotate(211deg) brightness(89%) contrast(101%)}#glz_custom_fields_container table.fixed-width{table-layout:fixed}#glz_custom_fields_container table.sortable .txp-list-col-sort{width:3em;text-align:center}#glz_custom_fields_container table.sortable .ui-sortable-handle{cursor:row-resize;text-align:center;opacity:.66}#glz_custom_fields_container table.sortable .txp-list-col-position{display:none}#glz_custom_fields_container .ui-sortable-helper,#glz_custom_fields_container .ui-sortable-placeholder{display:table}#add_edit_custom_field .hidden{display:none}@media screen and (min-width:47em){.txp-edit .txp-form-field .txp-form-field-instructions,.txp-tabs-vertical-group .txp-form-field-instructions{max-width:50%;padding-left:50%}}.check-path{float:right;font-size:.7em;font-weight:400}[dir=rtl] .check-path{float:left}.ui-tabs-nav .check-path{display:none}#prefs-glz_cf_css_asset_url,#prefs-glz_cf_js_asset_url{display:none}.glz-custom-field-reset.disabled:hover{text-decoration:none}.glz-custom-field-reset.disabled{cursor:default}.glz-custom-checkbox .txp-form-field-value label,.glz-custom-radio .txp-form-field-value label{cursor:pointer}
CSS;

    return '<style>'.n.$css.n.'</style>';
}


// -------------------------------------------------------------
// Contains minified output of glz_custom_fields.css file
// To update copy the minified output of the actual js file into the function
function glz_custom_fields_head_js()
{
    $js = <<<'JS'
$(function(){function e(){$(".glz-custom-radio").length>0&&$(".glz-custom-radio").each(function(){var e=$(this).find("input:first").attr("name");$(this).find("label:first").after(' <span class="small"><a href="#" class="glz-custom-field-reset" name="'+e+'">Reset</a></span>'),$("input:radio[name="+e+"]").is(":checked")||$(".glz-custom-field-reset[name="+e+"]").addClass("disabled")})}function t(){$glz_value_field.find("textarea#value").length&&(s.textarea_value=$glz_value_field.find("textarea#value").html(),$glz_value_field.find("textarea#value").remove()),$glz_value_field.find("input#value").length?0==$glz_value_field.find("input#value").prop("disabled")&&(s.path_value=$glz_value_field.find("input#value").attr("value")):$glz_value_field.find(".txp-form-field-value").prepend('<input type="text" id="value" name="value" />'),$glz_value_field.find("input#value").attr("value","-----").prop("disabled",!0),$glz_value_instructions.html("")}function l(){$glz_value_field.find("input#value").length&&(0==$glz_value_field.find("input#value").prop("disabled")&&(s.path_value=$glz_value_field.find("input#value").attr("value")),$glz_value_field.find("input#value").remove(),$glz_value_instructions.html("")),$glz_value_field.find("textarea#value").length||$(".edit-custom-set-value .txp-form-field-value").prepend('<textarea id="value" name="value" rows="5"></textarea>'),s.textarea_value&&$glz_value_field.find("textarea#value").html(s.textarea_value),$glz_value_instructions.html(s.messages.textarea)}function a(){$glz_value_field.find("textarea#value").length&&(s.textarea_value=$glz_value_field.find("textarea#value").html(),$glz_value_field.find("textarea#value").remove(),$glz_value_instructions.html("")),$glz_value_field.find("input#value").length||$glz_value_field.find(".txp-form-field-value").prepend('<input type="text" id="value" name="value" size="32" />'),"-----"==$glz_value_field.find("input#value").attr("value")&&$glz_value_field.find("input#value").attr("value",""),$glz_value_field.find("input#value").prop("disabled",!1),$glz_value_instructions.html(s.messages.customscriptpath),s.path_value&&$glz_value_field.find("input#value").attr("value",s.path_value)}function i(){-1!=$.inArray($("select#custom_set_type :selected").attr("value"),[].concat(s.special_custom_types,["multi-select","custom-script"]))?$glz_select_instructions.html('<a href="//'+window.location.host+window.location.pathname+'?event=prefs#prefs_group_glz_custom_f">'+s.messages.configure+"</a>"):$glz_select_instructions.html("")}textpattern.Relay.register("txpAsyncForm.success",e);var s;s={},s.special_custom_types=["date-picker","time-picker"],s.no_value_custom_types=["text_input","textarea"],$glz_value_field=$(".edit-custom-set-value"),$glz_value_instructions=$glz_value_field.find(".txp-form-field-instructions"),$glz_select_instructions=$(".edit-custom-set-type").find(".txp-form-field-instructions"),s.messages={textarea:$(".glz-custom-textarea-msg").html(),configure:$glz_select_instructions.text(),customscriptpath:$(".glz-custom-script-msg").text()},$(".glz-custom-script-msg").remove(),$(".glz-custom-textarea-msg").remove(),i(),-1!=$.inArray($("select#custom_set_type :selected").attr("value"),[].concat(s.special_custom_types,s.no_value_custom_types))?t():"custom-script"==$("select#custom_set_type :selected").attr("value")&&a(),$("select#custom_set_type").change(function(){i(),-1!=$.inArray($("select#custom_set_type :selected").attr("value"),[].concat(s.special_custom_types,s.no_value_custom_types))?t():"custom-script"==$("select#custom_set_type :selected").attr("value")?a():l()}),e(),$(".txp-layout").on("click",".glz-custom-field-reset",function(){if($(this).hasClass("disabled"))return!1;var e=$(this).attr("name");return $("input[name="+e+"]").prop("checked",!1),$(this).addClass("disabled"),0===$(this).siblings(".txp-form-radio-reset").length&&$(this).after('<input type="hidden" class="txp-form-radio-reset" value="" name="'+e+'" />'),!1}),$(".txp-layout").on("click",".glz-custom-radio .radio",function(){var e=$(this).attr("name");$this_reset_button=$(".glz-custom-field-reset[name="+e+"]"),$this_reset_button.hasClass("disabled")&&($("input[type=hidden][name="+e+"]").remove(),$this_reset_button.removeClass("disabled"))})});
JS;
    return '<script>'.n.$js.n.'</script>';
}

<?php

// this function doesn't seem to be currently used, remove or make it useful
function glz_next_empty_custom() {
  global $all_custom_sets;

  foreach ( $all_custom_sets as $custom => $custom_set ) {
    if ( empty($custom_set['name']) )
      return $custom;
  }
}


// -------------------------------------------------------------
// edit/delete buttons in custom_fields table require a form each
function glz_form_buttons($action, $value, $custom_set, $custom_set_name, $custom_set_type, $onsubmit='') {
  $onsubmit = ($onsubmit) ?
    'onsubmit="'.$onsubmit.'"' :
    '';

  return
    '<form method="post" action="index.php" '.$onsubmit.'>
      <input name="custom_set" value="'.$custom_set.'" type="hidden" />
      <input name="custom_set_name" value="'.$custom_set_name.'" type="hidden" />
      <input name="custom_set_type" value="'.$custom_set_type.'" type="hidden" />
      <input name="event" value="glz_custom_fields" type="hidden" />
      <input name="'.$action.'" value="'.$value.'" type="submit" />
    </form>';
}


// -------------------------------------------------------------
// the types our custom fields can take
function glz_custom_set_types() {
  return array(
    'normal' => array(
      'text_input',
      'select',
      'multi-select',
      'checkbox',
      'radio',
      'textarea'),
    'special' => array(
      'date-picker')
  );
}


// -------------------------------------------------------------
// outputs only custom fields that have been set, i.e. have a name assigned to them
function glz_check_custom_set($var) {
  return !empty($var['name']);
}


// -------------------------------------------------------------
// custom_set without "_set" e.g. custom_1_set => custom_1
// or custom set formatted for IDs e.g. custom-1
function glz_custom_number($custom_set, $delimiter="_") {
  $custom_field = substr($custom_set, 0, -4);

  if ($delimiter != "_")
    $custom_field = str_replace("_", $delimiter, $custom_field);

  return $custom_field;
}


// -------------------------------------------------------------
// custom_set digit e.g. custom_1_set => 1
function glz_custom_digit($custom_set) {
  $out = explode("_", $custom_set);
  // $out[0] will always be custom
  return $out[1];
}


// -------------------------------------------------------------
// removes empty values from arrays - used for new custom fields
function glz_arr_empty_values($value) {
  if ( !empty($value) )
    return $value;
}


// -------------------------------------------------------------
// returns the custom set from a custom set name e.g. "Rating" gives us custom_1_set
function glz_get_custom_set($value) {
  global $all_custom_sets;

  // go through all custom fields and see if the one we're looking for exists
  foreach ( $all_custom_sets as $custom => $custom_set ) {
    if ( $custom_set['name'] == $value )
      return $custom;
  }
  // if it doesn't, return error message
  trigger_error(glz_custom_fields_gTxt('doesnt_exist', array('{custom_set_name}' => $value)));
}


// -------------------------------------------------------------
// get the article ID, EVEN IF it's newly saved
function glz_get_article_id() {
  return ( !empty($GLOBALS['ID']) ?
    $GLOBALS['ID'] :
    gps('ID') );
}


// -------------------------------------------------------------
// helps with range formatting - just DRY
function glz_format_ranges($arr_values, $custom_set_name) {
  //initialize $out
  $out = '';
  foreach ( $arr_values as $key => $value ) {
    $out[$key] = ( strstr($custom_set_name, 'range') ) ?
      glz_custom_fields_range($value, $custom_set_name) :
      $value;
  }
  return $out;
}


// -------------------------------------------------------------
// acts as a callback for the above function
function glz_custom_fields_range($custom_value, $custom_set_name) {
  // last part of the string will be the range unit (e.g. $, &pound;, m<sup>3</sup> etc.)
  $nomenclature = array_pop(explode(' ', $custom_set_name));

  // see whether range unit should go after
  if ( strstr($nomenclature, '(after)') ) {
    // trim '(after)' from the measuring unit
    $nomenclature = substr($nomenclature, 0, -7);
    $after = 1;
  }

  // check whether it's a range or single value
  $arr_value = explode('-', $custom_value);
  if ( is_array($arr_value) ) {
    // initialize $out
    $out = '';
    foreach ( $arr_value as $value ) {
      // check whether nomenclature goes before or after
      $out[] = ( !isset($after) ) ?
        $nomenclature.number_format($value) :
        number_format($value).$nomenclature;
    }
    return implode('-', $out);
  }
  // our range is a single value
  else {
    // check whether nomenclature goes before or after
    return ( !isset($after) ) ?
      $nomenclature.number_format($value) :
      number_format($value).$nomenclature;
  }
}


// -------------------------------------------------------------
// returns the next available number for custom set
function glz_custom_next($arr_custom_sets) {
  // if the array is exactly 10, our next custom field is 11
  if ( count($arr_custom_sets) == 10 ) {
    return 11;
  }
  // if not, slice the array with an offset of 10 (we don't want to look in custom fields < 10)
  else {
    $arr_extra_custom_sets = array();
    foreach ( array_keys(array_slice($arr_custom_sets, 10)) as $extra_custom_set) {
      // strip on _ and keep only the digit e.g. custom_11_set
      $digit = split("_", $extra_custom_set);
      $arr_extra_custom_sets[] = $digit['1'];
    }
    // order the array
    sort($arr_extra_custom_sets);

    foreach ( $arr_extra_custom_sets as $extra_custom_set ) {
      if (!in_array($extra_custom_set+1, $arr_extra_custom_sets))
        return $extra_custom_set+1;
    }
  }
}


// -------------------------------------------------------------
// checks if the custom field name isn't already taken
function glz_check_custom_set_name($arr_custom_fields, $custom_set_name, $custom_set='') {
  foreach ( $arr_custom_fields as $custom => $arr_custom_set ) {
    if ( ($custom_set_name == $arr_custom_set['name']) && (!empty($custom_set) && $custom_set != $custom) )
      return TRUE;
  }

  return FALSE;
}


// -------------------------------------------------------------
// formats the custom set output based on its type
function glz_format_custom_set_by_type($custom, $custom_id, $custom_set_type, $arr_custom_field_values, $custom_value = "") {
  if ( is_array($arr_custom_field_values) )
    $arr_custom_field_values = array_map('glz_array_stripslashes', $arr_custom_field_values);

  switch ( $custom_set_type ) {
    // these are the normal custom fields
    case "text_input":
      return array(
        fInput("text", $custom, $custom_value, "edit", "", "", "22", "", $custom_id),
        'glz_custom_field'
      );

    case "select":
      return array(
        glz_selectInput($custom, $custom_id, $arr_custom_field_values, $custom_value, 1),
        'glz_custom_select_field'
      );

    case "multi-select":
      return array(
        glz_selectInput($custom, $custom_id, $arr_custom_field_values, $custom_value, '', 1),
        'glz_custom_multi-select_field'
      );

    case "checkbox":
      return array(
        glz_checkbox($custom, $arr_custom_field_values, $custom_value),
        'glz_custom_checkbox_field'
      );

    case "radio":
      return array(
        glz_radio($custom, $custom_id, $arr_custom_field_values, $custom_value),
        'glz_custom_radio_field'
      );

    case "textarea":
      return array(
        text_area($custom, 100, 500, $custom_value, $custom_id),
        'glz_text_area_field'
      );

    // here start the special custom fields, might need to refactor the return, starting to repeat itself
    case "date-picker":
      return array(
        fInput("text", $custom, $custom_value, "edit date-picker", "", "", "22", "", $custom_id),
        'glz_custom_date-picker_field clearfix'
      );

    // a type has been passed that is not supported yet
    default:
      return array(
        glz_custom_fields_gTxt('type_not_supported'),
        'glz_custom_field'
      );
  }
}


// -------------------------------------------------------------
// had to duplicate the default selectInput() because trimming \t and \n didn't work + some other mods
function glz_selectInput($name = '', $id = '', $arr_values = '', $custom_value = '', $blank_first = '', $multi = '') {
  if ( is_array($arr_values) ) {
    $out = array();

    foreach ($arr_values as $key => $value) {
      $selected = glz_selected_checked('selected', $key, $custom_value);
      $out[] = "<option value=\"$key\"{$selected}>$value</option>";
    }

    // we'll need the extra attributes as well as a name that will produce an array
    /**
      TODO Make this user-configurable
    */
    if ($multi) {
      $multi = ' multiple="multiple" size="3"';
      $name .= "[]";
    }

    return "<select id=\"".glz_idify($id)."\" name=\"$name\" class=\"list\"$multi>".
      ($blank_first ? "<option value=\"\"$selected>&nbsp;</option>" : '').
      ( $out ? join('', $out) : '').
      "</select>";
  }
  else
    return glz_custom_fields_gTxt('field_problems', array('{custom_set_name}' => $name));
}


// -------------------------------------------------------------
// had to duplicate the default checkbox() to keep the looping in here and check against existing value/s
function glz_checkbox($name = '', $arr_values = '', $custom_value = '') {
  $out = array();

  foreach ( $arr_values as $key => $value ) {
    $checked = glz_selected_checked('checked', $key, $custom_value);

    // Putting an additional span around the input and label combination so the two can be floated together as a pair for left-right, left-right,... arrangement of checkboxes and radio buttons. Thanks Julian!
    $out[] = "<span><input type=\"checkbox\" name=\"{$name}[]\" value=\"$key\" class=\"checkbox\" id=\"".glz_idify($key)."\"{$checked} /><label for=\"".glz_idify($key)."\">$value</label></span><br />";
  }

  return join('', $out);
}


// -------------------------------------------------------------
// had to duplicate the default radio() to keep the looping in here and check against existing value
function glz_radio($name = '', $id = '', $arr_values = '', $custom_value = '') {
  $out = array();

  foreach ( $arr_values as $key => $value ) {
    $checked = glz_selected_checked('checked', $key, $custom_value);

    // Putting an additional span around the input and label combination so the two can be floated together as a pair for left-right, left-right,... arrangement of checkboxes and radio buttons. Thanks Julian!
    $out[] = "<span><input type=\"radio\" name=\"$name\" value=\"$key\" class=\"radio\" id=\"{$id}_".glz_idify($key)."\"{$checked} /><label for=\"{$id}_".glz_idify($key)."\">$value</label></span><br />";
  }

  return join('', $out);
}


// -------------------------------------------------------------
// checking if this custom field has selected or checked values
function glz_selected_checked($nomenclature, $value, $custom_value = '') {
  // we're comparing against a key which is a "clean" value
  $custom_value = htmlspecialchars($custom_value);

  // make an array if $custom_value contains multiple values
  if ( strpos($custom_value, '|') )
    $arr_custom_value = explode('|', $custom_value);

  if ( isset($arr_custom_value) )
    $out = ( in_array($value, $arr_custom_value) ) ? " $nomenclature=\"$nomenclature\"" : "";
  else
    $out = ($value == $custom_value) ? " $nomenclature=\"$nomenclature\"" : "";

  return $out;
}


//-------------------------------------------------------------
// button gets more consistent styling across browsers rather than input type="submit"
// included in this plugin until in makes it into TXP - if that ever happens...
function glz_fButton($type, $name, $contents='Submit', $value, $class='', $id='', $title='', $onClick='', $disabled = false) {
  $o  = '<button type="'.$type.'" name="'.$name.'"';
  $o .= ' value="'.htmlspecialchars($value).'"';
  $o .= ($class)    ? ' class="'.$class.'"' : '';
  $o .= ($id)       ? ' id="'.$id.'"' : '';
  $o .= ($title)    ? ' title="'.$title.'"' : '';
  $o .= ($onClick)  ? ' onclick="'.$onClick.'"' : '';
  $o .= ($disabled) ? ' disabled="disabled"' : '';
  $o .= '>';
  $o .= $contents;
  $o .= '</button>';
  return $o;
}


// -------------------------------------------------------------
// PHP4 doesn't come with array_combine... Thank you redbot!
function php4_array_combine($keys, $values) {
  $result = array(); // initializing the array

  foreach ( array_map(null, $keys, $values) as $pair ) {
    $result[$pair[0]] = $pair[1];
  }

  return $result;
}


// -------------------------------------------------------------
// converts all values into id safe ones
function glz_idify($value) {
  $patterns[0] = "/\s/";
  $replacements[0] = "-";
  $patterns[1] = "/[^a-zA-Z0-9\-]/";
  $replacements[1] = "";

  return preg_replace($patterns, $replacements, strtolower($value));
}


// -------------------------------------------------------------
// strips slashes in arrays, used in conjuction with e.g. array_map
function glz_array_stripslashes(&$value) {
  return stripslashes($value);
}


// -------------------------------------------------------------
// returns all sections/categories that are searchable
function glz_all_searchable_sections_categories($type) {
  $type = (in_array($type, array('category', 'section')) ? $type : 'section');
  $condition = "";

  if ( $type == "section" )
    $condition .= "searchable='1'";
  else
    $condition .= "name <> 'root' AND type='article'";

  $result = safe_rows('*', "txp_{$type}", $condition);

  $out = array();
  foreach ($result as $value) {
    $out[$value['name']] = $value['title'];
  }

  return $out;
}

?>


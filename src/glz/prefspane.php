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
#   PREFERENCES PANE – Functions for preferences + preferences pane
#
##################


function glz_cf_prefs_install()
{
    global $prefs, $txpcfg;

    $position = 200;
    $base_url = (empty($txpcfg['admin_url'])) ? hu : ahu;
    $base_path = (empty($txpcfg['admin_url'])) ? $prefs['path_to_site'] : str_replace("public", "admin", $prefs['path_to_site']);

    // array: old_prefname => array('pref.subevent', 'html', 'default-value')
    $plugin_prefs = array(
        'values_ordering'        => array('', 'glz_prefs_orderby', 'custom'),
        'multiselect_size'       => array('', 'glz_text_input_small', '5'),
        'css_asset_url'          => array('', 'glz_url_input', $base_url.'plugins/glz_custom_fields'),
        'js_asset_url'           => array('', 'glz_url_input', $base_url.'plugins/glz_custom_fields'),
        'custom_scripts_path'    => array('', 'glz_url_input', $base_path.'/plugins/glz_custom_fields'),
        'use_sortable'           => array('', 'yesnoradio', 1),
        'permit_full_deinstall'  => array('', 'yesnoradio', 0),
        'datepicker_url'         => array('glz_cf_datepicker', 'glz_url_input', $base_url.'plugins/glz_custom_fields/jquery.datePicker'),
        'datepicker_format'      => array('glz_cf_datepicker', 'glz_prefs_datepicker_format', 'dd/mm/yyyy'),
        'datepicker_first_day'   => array('glz_cf_datepicker', 'glz_prefs_datepicker_firstday', 1),
        'datepicker_start_date'  => array('glz_cf_datepicker', 'glz_input_start_date', '01/01/2018'),
        'timepicker_url'         => array('glz_cf_timepicker', 'glz_url_input', $base_url.'plugins/glz_custom_fields/jquery.timePicker'),
        'timepicker_start_time'  => array('glz_cf_timepicker', 'glz_text_input_small', '00:00'),
        'timepicker_end_time'    => array('glz_cf_timepicker', 'glz_text_input_small', '23:30'),
        'timepicker_step'        => array('glz_cf_timepicker', 'glz_text_input_small', 30),
        'timepicker_show_24'     => array('glz_cf_timepicker', 'glz_prefs_timepicker_format', true)
    );

    foreach ($plugin_prefs as $name => $val) {
        if (get_pref($name, false) === false) {
            // If pref is new, create new pref with 'glz_cf_' prefix
            create_pref('glz_cf_'.$name, $val[2], 'glz_custom_f'.($val[0] ? '.'.$val[0] : ''), PREF_PLUGIN, $val[1], $position, '');
        } else {
            // If pref exists, add 'glz_cf_' prefix to name, reassign position and html type and set to type PREF_PLUGIN
            safe_update(
                'txp_prefs',
                "name = 'glz_cf_".$name."',
                 event = 'glz_custom_f".($val[0] ? ".".$val[0] : "")."',
                 html = '".$val[1]."',
                 type = ".PREF_PLUGIN.",
                 position = ".$position,
                "name = '".$name."'"
            );
        }
        $position++;
    }

    // Make some $prefs hidden (for safety and troubleshooting)
    foreach (array(
        'use_sortable',
        'permit_full_deinstall'
    ) as $name) {
        safe_update(
            'txp_prefs',
            "type = ".PREF_HIDDEN,
            "name = 'glz_cf_".$name."'"
        );
    }

    // Set 'migrated' pref to 'glz_cf_migrated' and to hidden (type = 2);
    if (get_pref('migrated')) {
        safe_update(
            'txp_prefs',
            "name = 'glz_cf_migrated',
             type = ".PREF_HIDDEN,
            "name = 'migrated'"
        );
    }

    // Remove no longer needed 'max_custom_fields' pref
    safe_delete(
        'txp_prefs',
        "name = 'max_custom_fields'"
    );
}


/**
 * Renders a HTML choice of GLZ value ordering.
 *
 * @param  string $name HTML name and id of the widget
 * @param  string $val  Initial (or current) selected item
 * @return string HTML
 */
function glz_prefs_orderby($name, $val)
{
    $vals = array(
        'ascending'   => gTxt('glz_cf_prefs_value_asc'),
        'descending'  => gTxt('glz_cf_prefs_value_desc'),
        'custom'      => gTxt('glz_cf_prefs_value_custom')
    );
    return selectInput($name, $vals, $val, '', '', $name);
}

/**
 * Renders a HTML choice of date formats.
 *
 * @param  string $name HTML name and id of the widget
 * @param  string $val  Initial (or current) selected item
 * @return string HTML
 */
function glz_prefs_datepicker_format($name, $val)
{
    $vals = array(
        "dd/mm/yyyy"  => gTxt('glz_cf_prefs_slash_dmyy'),
        "mm/dd/yyyy"  => gTxt('glz_cf_prefs_slash_mdyy'),
        "yyyy-mm-dd"  => gTxt('glz_cf_prefs_dash_yymd'),
        "dd mm yy"    => gTxt('glz_cf_prefs_space_dmy'),
        "dd.mm.yyyy"  => gTxt('glz_cf_prefs_dot_dmyy')
    );
    return selectInput($name, $vals, $val, '', '', $name);
}

/**
 * Renders a HTML choice of weekdays.
 *
 * @param  string $name HTML name and id of the widget
 * @param  string $val  Initial (or current) selected item
 * @return string HTML
 */
function glz_prefs_datepicker_firstday($name, $val)
{
    $vals = array(
        0             => gTxt('glz_cf_prefs_sunday'),
        1             => gTxt('glz_cf_prefs_monday'),
        2             => gTxt('glz_cf_prefs_tuesday'),
        3             => gTxt('glz_cf_prefs_wednesday'),
        4             => gTxt('glz_cf_prefs_thursday'),
        5             => gTxt('glz_cf_prefs_friday'),
        6             => gTxt('glz_cf_prefs_saturday')
    );
    return selectInput($name, $vals, $val, '', '', $name);
}

/**
 * Renders a HTML choice of time formats.
 *
 * @param  string $name HTML name and id of the widget
 * @param  string $val  Initial (or current) selected item
 * @return string HTML
 */
function glz_prefs_timepicker_format($name, $val)
{
    $vals = array(
        'true'        => gTxt('glz_cf_prefs_24_hours'),
        'false'       => gTxt('glz_cf_prefs_12_hours')
    );
    return selectInput($name, $vals, $val, '', '', $name);
}


/**
 * Renders a small-width HTML &lt;input&gt; element.
 * Checks if start date matches current datepicker date format
 *
 * @param  string $name HTML name and id of the text box
 * @param  string $val  Initial (or current) content of the text box
 * @return string HTML
 */
function glz_input_start_date($name, $val)
{
    $out = text_input($name, $val, INPUT_SMALL);
    // Output error notice if start date does not match date format
    if (!glz_is_valid_start_date($val)) {
        $out .= '<br><span class="error"><span class="ui-icon ui-icon-alert"></span> '.gTxt('glz_cf_datepicker_start_date_error').'</span>';
    }
    return $out;
}


/**
 * Renders a medium-width HTML &lt;input&gt; element.
 *
 * @param  string $name HTML name and id of the text box
 * @param  string $val  Initial (or current) content of the text box
 * @return string HTML
 */
function glz_text_input_medium($name, $val)
{
    return text_input($name, $val, INPUT_MEDIUM);
}

/**
 * Renders a small-width HTML &lt;input&gt; element.
 *
 * @param  string $name HTML name and id of the text box
 * @param  string $val  Initial (or current) content of the text box
 * @return string HTML
 */
function glz_text_input_small($name, $val)
{
    return text_input($name, $val, INPUT_SMALL);
}

/**
 * Renders a regular-width HTML &lt;input&gt; element for an URL with path check.
 *
 * @param  string $name HTML name and id of the text box
 * @param  string $val  Initial (or current) content of the text box
 * @return string HTML
 */
function glz_url_input($name, $val)
{
    global $use_minified;
    $min = ($use_minified === true) ? '.min' : '';
    $check_paths = (gps('check_paths') == "1") ? true : false;

    // Output regular-width text_input for url
    $out  = fInput('text', $name, $val, '', '', '', INPUT_REGULAR, '', $name);

    // Array of possible expected url inputs and corresponding files and error-msg-stubs
    // 'pref_name' => array('/targetfilename.ext', 'gTxt_folder (inserted into error msg)')
    // paths do not require a target filename, urls do.
    $glz_cf_url_inputs = array(
        'glz_cf_css_asset_url'       => array('/glz_custom_fields'.$min.'.css', 'glz_cf_css_folder'),
        'glz_cf_js_asset_url'        => array('/glz_custom_fields'.$min.'.js',  'glz_cf_js_folder'),
        'glz_cf_datepicker_url'      => array('/datePicker'.$min.'.js',         'glz_cf_datepicker_folder'),
        'glz_cf_timepicker_url'      => array('/timePicker'.$min.'.js',         'glz_cf_timepicker_folder'),
        'glz_cf_custom_scripts_path' => array('',                               'glz_cf_custom_folder')
    );
    // File url or path to test = prefs_val (=url/path) + targetfilename (first item in array)
    $glz_cf_url_to_test          = $val.$glz_cf_url_inputs[$name][0];
    // gTxt string ref for folder name for error message (second item in array)
    $glz_cf_url_input_error_stub = $glz_cf_url_inputs[$name][1];

    // See if url / path is readable. If not, produce error message
    if ($glz_cf_url_to_test && $check_paths == true) {
        // permit relative URLs but conduct url test with hostname
        if (strstr($name, 'url')) {
            $glz_cf_url_to_test = glz_relative_url($glz_cf_url_to_test, $addhost = true);
        }
        $url_error = (@fopen($glz_cf_url_to_test, "r")) ? '' : gTxt('glz_cf_folder_error', array('{folder}' => gTxt($glz_cf_url_input_error_stub) ));

        // Output error notice if one exists, else success notice
        $out .= (!empty($url_error)) ?
            '<br><span class="error"><span class="ui-icon ui-icon-alert"></span> '.$url_error.'</span>' :
            '<br><span class="success"><span class="ui-icon ui-icon-check"></span> '.gTxt('glz_cf_folder_success').'</span>';
    }

    return $out;
}

<?php
// $Id$

/**
 * Add CSS and JS files. aof
 */

$theme_path = drupal_get_path('theme', 'rubik');
$jquery_ui = FALSE;

if ( module_exists('jquery_ui') ) {
  $jquery_ui = TRUE;
  drupal_add_css( drupal_get_path('module', 'jquery_ui') . '/jquery.ui/themes/base/ui.all.css' );
  jquery_ui_add('ui.tabs');
}

// Add JS with information about images sizes (image.module) and jquery_ui.module

drupal_add_js(
  array(
  'tne' => array(
    'ckeditor' => array(
      'images' => variable_get( 'image_sizes', 0 )
      ),
    'jquery_ui' => $jquery_ui
    )
  ),
  'setting'
);

/**
 * Implementation of hook_theme().
 */
function rubik_tne_theme() {
  $items = array();

  // Form layout: default (2 column).
  $items['block_add_block_form'] =
  $items['block_admin_configure'] =
  $items['comment_form'] =
  $items['contact_admin_edit'] =
  $items['contact_mail_page'] =
  $items['contact_mail_user'] =
  $items['filter_admin_format_form'] =
  $items['forum_form'] =
  $items['locale_languages_edit_form'] =
  $items['locale_languages_configure_form'] =
  $items['menu_edit_menu'] =
  $items['menu_edit_item'] =
  $items['node_type_form'] =
  $items['path_admin_form'] =
  $items['system_settings_form'] =
  $items['system_themes_form'] =
  $items['system_modules'] =
  $items['system_actions_configure'] =
  $items['taxonomy_form_term'] =
  $items['taxonomy_form_vocabulary'] =
  $items['user_pass'] =
  $items['user_login'] =
  $items['user_register'] =
  $items['user_profile_form'] =
  $items['user_admin_access_add_form'] = array(
    'arguments' => array('form' => array()),
    'path' => drupal_get_path('theme', 'rubik_tne') .'/templates',
    'template' => 'tne-form-default',
  );

  $items['node_form'] = array(
    'arguments' => array('form' => array()),
    'path' => drupal_get_path('theme', 'rubik_tne') .'/templates',
    'template' => 'tne-form-default',
    'preprocess functions' => array(
      'rubik_tne_preprocess_form_node',
    ),
  );
  return $items;
}

function rubik_tne_preprocess_page(&$vars) {
  if ( in_array( 'page-node-add', $vars['template_files'] ) ) {
    $vars['attr']['class'] .= ' page-node-add';
  }
  if ( in_array('page-node-edit', $vars['template_files'] ) ) {
    $vars['attr']['class'] .= ' page-node-edit';
  }
}

/**
 * Preprocessor for theme('fieldset').
 */
function rubik_tne_preprocess_fieldset(&$vars) {
  $element = $vars['element'];
  $module  = strtolower (preg_replace( '[\W]', '-', $element['#title'] ) );

  $attr = isset( $vars['attr'] ) ? $vars['attr'] : array();

  $attr['class'] = !empty( $attr['class'] ) ? $attr['class'] . ' ' . $module  : $module;
  $vars['attr'] = $attr;

  if (!empty($vars['element']['#collapsible'])) {
    if ( $module != 'attached-images' ) {
      $vars['title'] = "<span class='icon'></span>" . $vars['title'];
    }
  }
}

/**
 * Preprocessor for theme('node_form').
 */
function rubik_tne_preprocess_form_node(&$vars) {
  $vars['sidebar'] = isset($vars['sidebar']) ? $vars['sidebar'] : array();

  if ( isset( $vars['form']['taxonomy'] ) ) {
    $vars['sidebar']['taxonomy'] = $vars['form']['taxonomy'];
      unset($vars['form']['taxonomy']);
  }

  $sidebar_fields[] = 'field_contributed_piece';
  $sidebar_fields[] = 'field_cluster';
  $sidebar_fields[] = 'field_contributors';
  $sidebar_fields[] = 'field_period';
  $sidebar_fields[] = 'field_reference';
  $sidebar_fields[] = 'field_reference';
  $sidebar_fields[] = 'image_attach';
  $sidebar_fields[] = 'field_pubstat';
  $sidebar_fields[] = 'field_additional_authors';

  foreach ( $sidebar_fields as $field ) {
    if ( isset( $vars['form'][$field] ) ) {
      $vars['sidebar'][$field] = $vars['form'][$field];
      unset ($vars['form'][$field] );
    }
  }
}

/**
 * Override of theme('button').
 */

function rubik_tne_button( $element ) {
  /*
   * Change value for "contributed piece" and "contributors" from "Add another piece" to XXX.
   */

  if ($element['#type'] == 'submit' && $element['#name'] == 'field_contributors_add_more') {
    $element['#value'] = t('Add another contributor');
  }
  else if ($element['#type'] == 'submit' && $element['#name'] == 'field_contributed_piece_add_more') {
    $element['#value'] = t('Add another piece');
  }
  else if ($element['#type'] == 'submit' && $element['#name'] == 'field_additional_authors_add_more') {
    $element['#value'] = t('Add another author row');
  }

  /*
   * Make sure not to overwrite classes.
   */

  if ( isset( $element['#attributes']['class'] ) ) {
    $element['#attributes']['class'] = 'form-'. $element['#button_type'] .' '. $element['#attributes']['class'];
  }
  else {
    $element['#attributes']['class'] = 'form-'. $element['#button_type'];
  }
  return '<input type="submit" '. (empty($element['#name']) ? '' : 'name="'. $element['#name'] .'" ') .'id="'. $element['#id'] .'"' . drupal_attributes($element['#attributes']) ." value='". check_plain($element['#value']) . "' />\n";
}
<?php

/**
 * @file
 * CleanTalk module admin functions.
 */

/**
 * Cleantalk settings form.
 */
function cleantalk_settings_form($form, &$form_state) {
  $ct_comments_min = 1;
  $ct_comments_default = 3;

  $ct_key = variable_get('ct_key', 'enter key');
  $ct_comments = variable_get('ct_comments', $ct_comments_default);
  if ($ct_comments < $ct_comments_min) {
    $ct_comments = $ct_comments_min;
    variable_set('ct_comments', $ct_comments_min);
  }
  $ct_automod = variable_get('ct_automod', 1);

  $form['ct_key'] = array(
    '#type' => 'textfield',
    '#title' => t('Access key'),
    '#size' => 10,
    '#maxlength' => 10,
    '#default_value' => $ct_key,
    '#description' => t(
      'Click <a target="__blank" href="!ct_link">here</a> to get access key.',
      array(
        '!ct_link' => url('http://cleantalk.org/register?platform=drupal'),
      )
    ),
  );

  $form['ct_comments'] = array(
    '#type' => 'textfield',
    '#title' => t('Minimum approved comments per registered user'),
    '#size' => 5,
    '#maxlength' => 5,
    '#default_value' => $ct_comments,
    '#description' => t('Moderate messages of guests and registered users who have approved messages less than this value (must be more than 0).'),
  );

  $form['ct_automod'] = array(
    '#type' => 'checkbox',
    '#title' => t('Enable automoderation'),
    '#default_value' => $ct_automod,
    '#description' => t('Automatically publish good messages and put bad ones to admin approvement.<br /><span class="admin-enabled">Note: It overrides "Skip comment approval" permissions.</span>'),
  );

  return system_settings_form($form);
}

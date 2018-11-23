<?php

/**
 * @file
 * CleanTalk module admin functions.
 */

/**
 * Cleantalk settings form.
 */
function cleantalk_settings_form($form, &$form_state) { 

  $form['cleantalk_authkey'] = array(
    '#type' => 'textfield',
    '#title' => t('Access key'),
    '#size' => 20,
    '#maxlength' => 20,
    '#default_value' => variable_get('cleantalk_authkey', ''),
    '#description' => t(
      'Click <a target="_blank" href="!ct_link">here</a> to get access key.',
      array(
        '!ct_link' => url('http://cleantalk.org/register?platform=drupal'),
      )
    ),
  );

  $form['cleantalk_comments'] = array(
    '#type' => 'fieldset',
    '#title' => t('Comments'),
  );

  $form['cleantalk_comments']['cleantalk_check_comments'] = array(
    '#type' => 'checkbox',
    '#title' => t('Check comments'),
    '#default_value' => variable_get('cleantalk_check_comments', 0),
    '#description' => t('Enabling this option will allow you to check all comments on your website.'),   
  ); 

  $form['cleantalk_comments']['cleantalk_check_comments_automod'] = array(
    '#type' => 'checkbox',
    '#title' => t('Enable automoderation'),
    '#default_value' => variable_get('cleantalk_check_comments_automod', 0),
    '#description' => t('Automatically put suspicious comments which may not be 100% spam to manual approvement and block obvious spam comments.').
    '<br /><span class="admin-disabled">' .
    t('Note: If disabled, all suspicious comments will be automatically blocked!') .
    '</span>', 
    '#states' => array(
        // Only show this field when the value when checking comments is enabled
        'disabled' => array(
            ':input[name="cleantalk_check_comments"]' => array('checked' => FALSE),
        ),
    ),          
  );   

  $form['cleantalk_comments']['cleantalk_check_comments_min_approved'] = array(
    '#type' => 'textfield',
    '#title' => t('Minimum approved comments per registered user'),
    '#size' => 5,
    '#maxlength' => 5,
    '#default_value' => variable_get('cleantalk_check_comments_min_approved', 3),
    '#element_validate' => array('element_validate_integer_positive'),
    '#description' => t('Moderate messages of guests and registered users who have approved messages less than this value (must be more than 0).'),
    '#states' => array(
        // Only show this field when the value when checking comments is enabled
        'disabled' => array(
            ':input[name="cleantalk_check_comments"]' => array('checked' => FALSE),
        ),
    ),    
  );  

  $form['cleantalk_check_register'] = array(
    '#type' => 'checkbox',
    '#title' => t('Check registrations'),
    '#default_value' => variable_get('cleantalk_check_register', 0),
    '#description' => t('Enabling this option will allow you to check all registrations on your website.'),
  );

  $form['cleantalk_check_webforms'] = array(
    '#type' => 'checkbox',
    '#title' => t('Check webforms'),
    '#default_value' => variable_get('cleantalk_check_webforms', 0),
    '#description' => t('Enabling this option will allow you to check all webforms on your website.'),
  );

  $form['cleantalk_check_contact_forms'] = array(
    '#type' => 'checkbox',
    '#title' => t('Check contact forms'),
    '#default_value' => variable_get('cleantalk_check_contact_forms', 0),
    '#description' => t('Enabling this option will allow you to check all contact forms on your website.'),
  );

  $form['cleantalk_check_ccf'] = array(
    '#type' => 'checkbox',
    '#title' => t('Check custom forms'),
    '#default_value' => variable_get('cleantalk_check_ccf', 0),
    '#description' => t('Enabling this option will allow you to check all forms on your website.') .
    '<br /><span class="admin-disabled">' .
    t('Note: May cause conflicts!') .
    '</span>',
  );
  
  $form['cleantalk_sfw'] = array(
    '#type' => 'checkbox',
    '#title' => t('Spam FireWall'),
    '#default_value' => variable_get('cleantalk_sfw', 0),
    '#description' => t('This option allows to filter spam bots before they access website. Also reduces CPU usage on hosting server and accelerates pages load time.'),
  );

  $form['cleantalk_link'] = array(
    '#type' => 'checkbox',
    '#title' => t('Tell others about CleanTalk'),
    '#default_value' => variable_get('cleantalk_link', 0),
    '#description' => t('Checking this box places a small link under the comment form that lets others know what anti-spam tool protects your site.'),
  );

  return system_settings_form($form);
}

function cleantalk_settings_form_validate($form, &$form_state) { 
  if ($form_state['values']['cleantalk_authkey']){
    $is_valid = \Drupal\cleantalk\CleantalkHelper::api_method__notice_validate_key($form_state['values']['cleantalk_authkey']);
    if ($is_valid['valid'] !== 1)
      form_set_error('cleantalk_authkey', t('Access key is not valid.'));
  }
}
function cleantalk_settings_form_submit($form, &$form_state){
  \Drupal\cleantalk\CleantalkHelper::api_method_send_empty_feedback($form_state['values']['cleantalk_authkey'], CLEANTALK_USER_AGENT);
  if ($form_state['values']['cleantalk_sfw'] === 1)
  {
    $sfw = new \Drupal\cleantalk\CleantalkSFW();
    $sfw->sfw_update($form_state['values']['cleantalk_authkey']);
    $sfw->send_logs($form_state['values']['cleantalk_authkey']);
    variable_set('ct_sfw_last_logs_sent', time());
    variable_set('ct_sfw_last_updated', time());        
  }
}
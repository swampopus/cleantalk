<?php

/**
 * @file
 * CleanTalk module admin functions.
 */

/**
 * Cleantalk settings form.
 */
function cleantalk_settings_form($form, &$form_state) {
  $ct_comments_default = 3;

  $ct_authkey = variable_get('cleantalk_authkey', '');
  $ct_check_comments_min_approved = variable_get('cleantalk_check_comments_min_approved', $ct_comments_default);
  $ct_check_comments = variable_get('cleantalk_check_comments', 0);
  $ct_check_comments_automod = variable_get('cleantalk_check_comments_automod', 0);
  $ct_check_register = variable_get('cleantalk_check_register', 0);  
  $ct_check_wf = variable_get('cleantalk_check_wf', 0);  
  $ct_check_contact_forms = variable_get('cleantalk_check_contact_forms', 0);  
  $ct_ccf = variable_get('cleantalk_ccf', 0);
  $ct_sfw = variable_get('cleantalk_sfw', 0);
  $ct_ssl_on = variable_get('cleantalk_ssl_on', 0);
  $ct_link = variable_get('cleantalk_link', 0);  


  $form['cleantalk_authkey'] = array(
    '#type' => 'textfield',
    '#title' => t('Access key'),
    '#size' => 20,
    '#maxlength' => 20,
    '#default_value' => $ct_authkey ? $ct_authkey : '',
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
    '#default_value' => $ct_check_comments,
    '#description' => t('Enabling this option will allow you to check all comments on your website.'),   
  ); 
  $form['cleantalk_comments']['cleantalk_check_comments_automod'] = array(
    '#type' => 'checkbox',
    '#title' => t('Enable automoderation'),
    '#default_value' => $ct_check_comments_automod,
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
    '#default_value' => $ct_check_comments_min_approved,
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
    '#default_value' => $ct_check_register,
    '#description' => t('Enabling this option will allow you to check all registrations on your website.'),
  );


  $form['cleantalk_check_wf'] = array(
    '#type' => 'checkbox',
    '#title' => t('Check webforms'),
    '#default_value' => $ct_check_wf,
    '#description' => t('Enabling this option will allow you to check all webforms on your website.'),
  );

  $form['cleantalk_check_contact_forms'] = array(
    '#type' => 'checkbox',
    '#title' => t('Check contact forms'),
    '#default_value' => $ct_check_contact_forms,
    '#description' => t('Enabling this option will allow you to check all contact forms on your website.'),
  );

  $form['cleantalk_ccf'] = array(
    '#type' => 'checkbox',
    '#title' => t('Enable custom contact forms checking'),
    '#default_value' => $ct_ccf,
    '#description' => t('Enabling this option will allow you to check all forms on your website.') .
    '<br /><span class="admin-disabled">' .
    t('Note: May cause conflicts!') .
    '</span>',
  );
  
  $form['cleantalk_sfw'] = array(
    '#type' => 'checkbox',
    '#title' => t('Spam FireWall'),
    '#default_value' => $ct_sfw,
    '#description' => t('This option allows to filter spam bots before they access website. Also reduces CPU usage on hosting server and accelerates pages load time.'),
  );

  $form['cleantalk_ssl_on'] = array(
    '#type' => 'checkbox',
    '#title' => t('Enable SSL secure connection'),
    '#default_value' => $ct_ssl_on,
    '#description' => t('Enabling this option will allow you to secure your connection.'),
  );

  $form['cleantalk_link'] = array(
    '#type' => 'checkbox',
    '#title' => t('Tell others about CleanTalk'),
    '#default_value' => $ct_link,
    '#description' => t('Checking this box places a small link under the comment form that lets others know what anti-spam tool protects your site.'),
  );

  return system_settings_form($form);
}

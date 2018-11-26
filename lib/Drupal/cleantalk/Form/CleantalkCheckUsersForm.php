<?php

/**
 * @file
 * CleanTalk module admin functions.
 */

/**
 * Cleantalk check users form.
 */
function cleantalk_check_users_form($form, &$form_state) { 
	if (variable_get('cleantalk_authkey', '') != '')
	{
		$form_state['storage']['cleantalk_spam_users'] = cleantalk_find_spam_users();

		if (isset($form_state['storage']['cleantalk_spam_users']) && count($form_state['storage']['cleantalk_spam_users']) > 0)
		{
			$form['cleantalk_spam_users'] = array(
				'#tree' => TRUE,
				'#theme' => 'table_form',
			);

			$spam_users = $form_state['storage']['cleantalk_spam_users'];

			foreach ($spam_users as $spam_user) {
				$form['cleantalk_spam_users'][$spam_user->name]['spam_user_name'] = array(
				  '#type' => 'item',
				  '#title' => 'Name',
				  '#markup' => $spam_user->name,
				);
				$form['cleantalk_spam_users'][$spam_user->mail]['spam_user_email'] = array(
				  '#type' => 'item',
				  '#title' => 'E-mail',
				  '#markup' => $spam_user->mail,
				);
				$form['cleantalk_spam_users'][$spam_user->created]['spam_user_created'] = array(
				  '#type' => 'item',
				  '#title' => 'Created ',
				  '#markup' => date("Y-m-d H:i:s", $spam_user->created),
				);
				$form['cleantalk_spam_users'][$spam_user->status]['spam_user_status'] = array(
				  '#type' => 'item',
				  '#title' => 'Status',
				  '#markup' => ($spam_user->status == 1) ? 'Active':'Inactive',
				);										
				$form['cleantalk_spam_users'][$spam_user->uid]['spam_user_delete'] = array(
				  '#type' => 'submit',
				  '#value' => 'Delete',
				  '#name' => 'remove_' . $spam_user->uid,
				  '#submit' => array('cleantalk_remove_user'),
				);
			}
			$data = array();
			foreach ($spam_users as $user)
				array_push($data,$user->uid);
			$form['submit'] = array(
				'#type' => 'submit',
				'#value' => 'Delete all',
				'#name' => 'delete_all_' . implode('_',$data),
				'#submit' => array('cleantalk_delete_all_spammers_users'),			
			);			
		}
			
	}
	else drupal_set_message('Access key is not valid.','error');
	return $form;
}

function cleantalk_find_spam_users()
{
	if (variable_get('cleantalk_authkey', '') != '')
	{
        // Get all accounts
        $accounts = user_load_multiple(FALSE);	
		$data = array();
		$spam_users=array();	
        foreach ($accounts as $account) 
        {
            // Skip adding the role to the user if they already have it.
            if ($account !== FALSE && isset($account->mail)) 
                array_push($data, $account->mail);
        }
        $data=implode(',',$data);
        $result=\Drupal\cleantalk\CleantalkHelper::api_method__spam_check_cms(variable_get('cleantalk_authkey', ''), $data);	
        if(isset($result['error_message']))
            drupal_set_message($result['error_message'],'error');
        else
        {
			foreach($result as $key => $value)
			{
				if ($value['appears'] == '1' )
				{
					foreach ($accounts as $account)
					{
						if ($account->mail == $key)
							$spam_users[] = $account;
					}
				}              
			}        	
        }
        return $spam_users;	
	}
}
function cleantalk_remove_user($form, &$form_state) {
	user_cancel(array(), $form_state['triggering_element']['#array_parents'][1], 'user_cancel_delete');
}
function cleantalk_delete_all_spammers_users($form, &$form_state){
	$post_array = str_replace('delete_all_', '', $form_state['triggering_element']['#name']);
	$ids = explode('_',$post_array);
	foreach ($ids as $id)
		user_cancel(array(), $id, 'user_cancel_delete'); 
}
/**
 * Implements hook_theme().
 */
function cleantalk_theme() {
  return array(
    'table_form' => array(
      'render element' => 'form',
    ),
  );
}
/**
 * Format form as table.
 */
function theme_table_form($vars) {
  $form = $vars['form'];
  $header = isset($vars['#header']) ? $vars['#header'] : array();
  $header_created = (bool)$header;
  $rows = array();
 
  foreach (element_children($form) as $key) {
    foreach (element_children($form[$key]) as $name) {
      // Create header
      if (!$header_created) {
        $header[] = $form[$key][$name]['#title'];
      }
 
      // Hide title
      $form[$key][$name]['#title_display'] = 'invisible';
 
      // Cell data
      $cell = array('data' => drupal_render($form[$key][$name]));
      if (isset($form[$key][$name]['#td_attributes'])) {
        $cell += $form[$key][$name]['#td_attributes'];
      }
 
      $rows[$key]['data'][] = $cell;
    }
 
    $header_created = TRUE;
 
    if (isset($form[$key]['#attributes'])) {
      $rows[$key] += $form[$key]['#attributes'];
    }
  }
 
  return theme('table', array(
    'rows' => $rows,
    'header' => $header,
    'attributes' => isset($form['#attributes']) ? $form['#attributes'] : array(),
    'caption'    => isset($form['#caption'])    ? $form['#caption']   : NULL,
    'colgroups'  => isset($form['#colgroups'])  ? $form['#colgroups'] : NULL,
    'sticky'     => isset($form['#sticky'])     ? $form['#sticky']    : NULL,
    'empty'      => isset($form['#empty'])      ? $form['#empty']     : NULL,
  ));
}
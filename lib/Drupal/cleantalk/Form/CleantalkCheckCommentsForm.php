<?php

/**
 * @file
 * CleanTalk module admin functions.
 */

/**
 * Cleantalk check comments form.
 */
function cleantalk_check_comments_form($form, &$form_state) { 
	if (module_exists('comment'))
	{
		if (variable_get('cleantalk_authkey', '') != '')
		{
			$form_state['storage']['cleantalk_spam_comments'] = cleantalk_find_spam_comments();

			if (isset($form_state['storage']['cleantalk_spam_comments']) && count($form_state['storage']['cleantalk_spam_comments']) > 0)
			{
				$form['cleantalk_spam_comments'] = array(
					'#tree' => TRUE,
					'#theme' => 'table_form',
				);

				$spam_comments = $form_state['storage']['cleantalk_spam_comments'];

				foreach ($spam_comments as $spam_comment) {
					$form['cleantalk_spam_comments'][$spam_comment->name]['spam_comment_name'] = array(
					  '#type' => 'item',
					  '#title' => 'Name',
					  '#markup' => $spam_comment->name,
					);
					$form['cleantalk_spam_comments'][$spam_comment->mail]['spam_comment_email'] = array(
					  '#type' => 'item',
					  '#title' => 'E-mail',
					  '#markup' => $spam_comment->mail,
					);
					$form['cleantalk_spam_comments'][$spam_comment->subject]['spam_comment_subject'] = array(
					  '#type' => 'item',
					  '#title' => 'Subject',
					  '#markup' => $spam_comment->subject,
					);				
					$form['cleantalk_spam_comments'][$spam_comment->name]['spam_comment_created'] = array(
					  '#type' => 'item',
					  '#title' => 'Created ',
					  '#markup' => date("Y-m-d H:i:s", $spam_comment->created),
					);
					$form['cleantalk_spam_comments'][$spam_comment->status]['spam_comment_status'] = array(
					  '#type' => 'item',
					  '#title' => 'Status',
					  '#markup' => ($spam_comment->status == 1) ? 'Active':'Inactive',
					);										
					$form['cleantalk_spam_comments'][$spam_comment->cid]['spam_comment_delete'] = array(
					  '#type' => 'submit',
					  '#value' => 'Delete',
					  '#name' => 'remove_' . $spam_comment->cid,
					  '#submit' => array('cleantalk_remove_comment'),
					);
				}
				$data = array();
				foreach ($spam_comments as $comment)
					array_push($data,$comment->uid);
				$form['submit'] = array(
					'#type' => 'submit',
					'#value' => 'Delete all',
					'#name' => 'delete_all_' . implode('_',$data),
					'#submit' => array('cleantalk_delete_all_spammers_comments'),			
				);			
			}
				
		}
		else drupal_set_message('Access key is not valid.','error');		
	}
	else drupal_set_message('Comments module is disabled.', 'error');

	return $form;
}

function cleantalk_find_spam_comments()
{
	if (variable_get('cleantalk_authkey', '') != '')
	{		
        // Get all comments
        $comments = db_query("SELECT c.cid, c.uid, u.mail, c.name, c.subject, c.created, c.status FROM {comment} c INNER JOIN {users} u on c.uid = u.uid");
		$data = array();
		$spam_comments = array();	
        foreach ($comments as $comment) 
        {
            // Skip adding the role to the user if they already have it.
            if ($comment !== FALSE && isset($comment->mail)) 
                array_push($data, $comment->mail);
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
					foreach ($comments as $comment)
					{
						if ($comment->mail == $key)
							$spam_comments[] = $comment;
					}
				}              
			}        	
        }
        return $spam_comments;	
	}
}
function cleantalk_remove_comment($form, &$form_state) {
	comment_delete($form_state['triggering_element']['#array_parents'][1]);
}
function cleantalk_delete_all_spammers_comments($form, &$form_state){
	$post_array = str_replace('delete_all_', '', $form_state['triggering_element']['#name']);
	$ids = explode('_',$post_array);
	comment_delete_multiple($ids);
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
<?php
require_once(dirname(__FILE__) . '/CleantalkHelper.php');
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
			$spam_comments = cleantalk_find_spam_comments();
			$rows = array();
			$header = array(
				'spam_comment_name' => t('Name'),
				'spam_comment_email' => t('E-mail'),
				'spam_comment_subject' => t('Subject'),
				'spam_comment_created' => t('Created'),
				'spam_comment_status' => t('Status'),
			);
			foreach ($spam_comments as $spam_comment)
			{
				$rows[] = array(
			    	'spam_comment_name' => $spam_comment->name,
			    	'spam_comment_email' => $spam_comment->mail,
			    	'spam_comment_subject' => $spam_comment->subject,
			    	'spam_comment_created' => date("Y-m-d H:i:s", $spam_comment->created),
			    	'spam_comment_status' => ($spam_comment->status == 1) ? 'Active':'Inactive',
			    	'#attributes' => array('comment_id' => $spam_comment->cid, 'class' => array('cleantalk-spam-comments-row')),
				);				
			}
			$form['cleantalk_spam_comments_table_actions'] = array(
				'#type' => 'select',
				'#title' => t('Actions'),
				'#options' => array(
					1 => t('Delete'),
				),
			);	
			$form['cleantalk_spam_comments_table'] = array(
			  '#type' => 'tableselect',
			  '#header' => $header,
			  '#options' => $rows,
			  '#empty' => t('No spam comments found'),
			  '#attributes' => array('class' => array('cleantalk_spam_comments_table')),
			);
			$form['submit'] = array(
				'#type' => 'submit',
				'#value' => t('Submit'),				
			);		
			return $form;													
		}
		else drupal_set_message('Access key is not valid.','error');		
	}
	else drupal_set_message('Comments module is disabled.', 'error');
}
function cleantalk_check_comments_form_submit($form, &$form_state)
{
	$action = $form_state['values']['cleantalk_spam_comments_table_actions'];
	$values = array();
	foreach ($form_state['values']['cleantalk_spam_comments_table'] as $key => $value)
	{
		if (is_string($value))
			$values[] = $form_state['complete form']['cleantalk_spam_comments_table']['#options'][$value];
	}
	//Delete comment
	if ($action == 1)
	{
		foreach ($values as $comment)
			comment_delete($comment['#attributes']['comment_id']);
	}
}
function cleantalk_find_spam_comments()
{		
    // Get all comments
    $comments = db_query("SELECT c.cid, c.uid, u.mail, c.name, c.subject, c.created, c.status FROM {comment} c INNER JOIN {users} u on c.uid = u.uid")->fetchAll();
	$spam_comments = array();	

    if ($comments && count($comments) > 0)
    {
		$data = array();

	    foreach ($comments as $comment) 
	    {
	        // Skip adding the role to the user if they already have it.
	        if ($comment !== FALSE && isset($comment->mail)) 
	            array_push($data, $comment->mail);
	    }
	    $data=implode(',',$data);
	    $result=\Drupal\cleantalk\CleantalkHelper::api_method__spam_check_cms(trim(variable_get('cleantalk_authkey', '')), $data);	

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
    }

	return $spam_comments;	
}
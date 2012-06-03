<?php

	/**
	 * @Table(name="TBGModulesTable")
	 */
	class TBGJabber extends TBGModule
	{

		/**
		 * Notify the user when an issue I posted gets updated
		 */
		const NOTIFY_ISSUE_POSTED_UPDATED = 'notify_issue_posted_updated';
		
		/**
		 * Only notify me once per issue
		 */
		const NOTIFY_ISSUE_ONCE = 'notify_issue_once';
		
		/**
		 * Notify the user when an issue I'm assigned to gets updated
		 */
		const NOTIFY_ISSUE_ASSIGNED_UPDATED = 'notify_issue_assigned_updated';
		
		/**
		 * Notify the user when he updates an issue
		 */
		const NOTIFY_ISSUE_UPDATED_SELF = 'notify_issue_updated_self';
		
		/**
		 * Notify the user when an issue assigned to one of my teams is updated
		 */
		const NOTIFY_ISSUE_TEAMASSIGNED_UPDATED = 'notify_issue_teamassigned_updated';
		
		/**
		 * Notify the user when an issue related to one of my team assigned projects is updated
		 */
		const NOTIFY_ISSUE_RELATED_PROJECT_TEAMASSIGNED = 'notify_issue_related_project_teamassigned';
		
		/**
		 * Notify the user when an issue related to one of my assigned projects is updated
		 */
		const NOTIFY_ISSUE_PROJECT_ASSIGNED = 'notify_issue_project_vip';

		/**
		 * Notify the user when an issue he commented on is updated
		 */
		const NOTIFY_ISSUE_COMMENTED_ON = 'notify_issue_commented_on';
		
		const NOTIFY_JID = 'jid';
		
		protected $_longname = 'Jabber communication';
		
		protected $_description = 'Enables outgoing jabber functionality';
		
		protected $_module_config_title = 'Jabber communication';
		
		protected $_module_config_description = 'Set up outgoing jabber communication from this section';
		
		protected $_account_settings_name = 'Notification settings';
		
		protected $_account_settings_logo = 'notification_settings.png';
		
		protected $_has_account_settings = true;

		protected $_has_config_settings = true;
		
		protected $_module_version = '1.0';

		protected $jabber = null;

		/**
		 * Get an instance of this module
		 * 
		 * @return TBGJabber
		 */
		public static function getModule()
		{
			return TBGContext::getModule('jabber');
		}
		
		protected function _initialize()
		{
		}
		
		protected function _addListeners()
		{
			//TBGEvent::listen('core', 'TBGUser::_postSave', array($this, 'listen_registerUser'));
			//TBGEvent::listen('core', 'password_reset', array($this, 'listen_forgottenPassword'));
			//TBGEvent::listen('core', 'login_form_pane', array($this, 'listen_loginPane'));
			//TBGEvent::listen('core', 'login_form_tab', array($this, 'listen_loginTab'));
			TBGEvent::listen('core', 'TBGUser::addScope', array($this, 'listen_addScope'));
			TBGEvent::listen('core', 'TBGIssue::createNew', array($this, 'listen_issueCreate'));
			TBGEvent::listen('core', 'TBGUser::_postSave', array($this, 'listen_createUser'));
			TBGEvent::listen('core', 'TBGIssue::addSystemComment', array($this, 'listen_TBGComment_createNew'));
			TBGEvent::listen('core', 'TBGComment::createNew', array($this, 'listen_TBGComment_createNew'));
			TBGEvent::listen('core', 'header_begins', array($this, 'listen_headerBegins'));
			TBGEvent::listen('core', 'viewissue', array($this, 'listen_viewissue'));
			//TBGEvent::listen('core', 'user_dropdown_anon', array($this, 'listen_userDropdownAnon'));
			//TBGEvent::listen('core', 'config_project_tabs', array($this, 'listen_projectconfig_tab'));
			//TBGEvent::listen('core', 'config_project_panes', array($this, 'listen_projectconfig_panel'));
			TBGEvent::listen('core', 'get_backdrop_partial', array($this, 'listen_get_backdrop_partial'));
		}

		protected function _addRoutes()
		{
			//$this->addRoute('forgot', '/forgot', 'forgot');
			$this->addRoute('jabber_test_message', '/jabber/test', 'testMessage');
		}
		
		protected function _install($scope)
		{
			$this->saveSetting('jid', '');
			$this->saveSetting('host', '');
			$this->saveSetting('port', 5222);
			$this->saveSetting('resource', '');
		}
		
		protected function _uninstall()
		{
			parent::_uninstall();
		}

		public function postConfigSettings(TBGRequest $request)
		{
			TBGContext::loadLibrary('common');
			$settings = array('jid', 'host', 'port', 'resource', 'password', 'enable_outgoing_notifications', 'use_queue');
			foreach ($settings as $setting)
			{
				if ($request->getParameter($setting) !== null)
				{
					$value = $request->getParameter($setting);
					
					// TODO: validate
					
					$this->saveSetting($setting, $value);
				}
			}
		}

		public function listen_createUser(TBGEvent $event)
		{
			$uid = $event->getSubject()->getID();
			$settings = array(self::NOTIFY_ISSUE_ASSIGNED_UPDATED, self::NOTIFY_ISSUE_ONCE, self::NOTIFY_ISSUE_POSTED_UPDATED, self::NOTIFY_ISSUE_PROJECT_ASSIGNED, self::NOTIFY_ISSUE_RELATED_PROJECT_TEAMASSIGNED, self::NOTIFY_ISSUE_TEAMASSIGNED_UPDATED, self::NOTIFY_ISSUE_COMMENTED_ON);

			foreach ($settings as $setting)
				$this->saveSetting($setting, 1, $uid);
		}
		
		//public function listen_registerUser(TBGEvent $event)
		//{
		//	if ($this->isActivationNeeded())
		//	{
		//		$user = $event->getSubject();
		//		$password = TBGUser::createPassword(8);
		//		$user->setPassword($password);
		//		$user->save();
		//		if ($this->isOutgoingNotificationsEnabled())
		//		{
		//			$subject = TBGContext::getI18n()->__('User account registered with The Bug Genie');
		//			$message = $this->createNewTBGMimemailFromTemplate($subject, 'registeruser', array('user' => $user, 'password' => $password), null, array(array('name' => $user->getBuddyname(), 'address' => $user->getEmail())));
		//
		//			$message->addReplacementValues(array('%user_buddyname%' => $user->getBuddyname()));
		//			$message->addReplacementValues(array('%user_username%' => $user->getUsername()));
		//			$message->addReplacementValues(array('%password%' => $password));
		//
		//			try
		//			{
		//				$this->sendMessage($message);
		//				$event->setProcessed();
		//			}
		//			catch (Exception $e)
		//			{
		//				throw $e;
		//			}
		//		}
		//	}
		//}

		public function listen_addScope(TBGEvent $event)
		{
			if ($this->isOutgoingNotificationsEnabled())
			{
				$user = $event->getSubject();
				
				$jid = $this->getSetting('jid', $user->getID());
				if(!$jid){
					$this->setProcessed();
					return;
				}
				
				$scope = $event->getParameter('scope');
				
				$message = $this->createMessageFromTemplate('addtoscope', array(
					'user_buddyname' => $user->getBuddyname(),
					'user_username' => $user->getUsername(),
					'scope'=>$scope
				));

				try
				{
					$this->sendMessage($jid, $message);
					$event->setProcessed();
				}
				catch (Exception $e)
				{
					throw $e;
				}
			}
		}

		//public function listen_loginPane(TBGEvent $event)
		//{
		//	if ($this->isOutgoingNotificationsEnabled())
		//	{
		//		TBGActionComponent::includeComponent('jabber/forgotPasswordPane', $event->getParameters());
		//	}
		//}
		//
		//public function listen_loginTab(TBGEvent $event)
		//{
		//	if ($this->isOutgoingNotificationsEnabled())
		//	{
		//		TBGActionComponent::includeComponent('jabber/forgotPasswordTab', $event->getParameters());
		//	}
		//}			
		//
		//public function listen_forgottenPassword(TBGEvent $event)
		//{
		//	if ($this->isOutgoingNotificationsEnabled())
		//	{
		//		$this->_sendToUsers($event->getSubject(), 'passwordreset', array(
		//			'password'=>$event->getParameter('password')
		//		));
		//	}
		//}
		
		public function listen_headerBegins(TBGEvent $event)
		{

		}
		
		public function listen_userDropdownAnon(TBGEvent $event)
		{
			if ($this->isOutgoingNotificationsEnabled())
			{
				TBGActionComponent::includeTemplate('jabber/userDropdownAnon', $event->getParameters());
			}
		}
		
		//public function sendforgottenPasswordMessage($user)
		//{
		//	if ($this->isOutgoingNotificationsEnabled())
		//	{
		//		$this->_sendToUsers($user, 'forgottenpassword', array('user' => $user));
		//	}
		//}
		
		public function sendTestMessage($jid)
		{
			if ($this->isOutgoingNotificationsEnabled())
			{
				try
				{
					$message = $this->createMessageFromTemplate('testmessage');
					return $this->sendMessage($jid, $message);
				}
				catch (Exception $e)
				{
					throw $e;
				}
			}
			else
			{
				throw new Exception(TBGContext::getI18n()->__('The jabber module is not configured for outgoing messages'));
			}
		}

		protected function _getIssueRelatedUsers(TBGIssue $issue)
		{
			$uids = array();
			$cu = TBGContext::getUser()->getID();
			$ns = $this->getSetting(self::NOTIFY_ISSUE_UPDATED_SELF, $cu);
	
			// Add all users who's marked this issue as interesting
			$uids = TBGUserIssuesTable::getTable()->getUserIDsByIssueID($issue->getID());
	
			// Add all users from the team owning the issue if valid
			// or add the owning user if a user owns the issue
			if ($issue->getOwner() instanceof TBGTeam)
			{
				foreach ($issue->getOwner()->getMembers() as $member)
				{
					if ($member->getID() == $cu && !$ns) continue;
					$uids[$member->getID()] = $member->getID();
				}
			}
			elseif ($issue->getOwner() instanceof TBGUser)
			{
				if (!($issue->getOwner()->getID() == $cu && !$ns))
					$uids[$issue->getOwner()->getID()] = $issue->getOwner()->getID();
			}

			// Add the poster
			if ($this->getSetting(self::NOTIFY_ISSUE_POSTED_UPDATED, $issue->getPostedByID()))
			{
				if (!($issue->getPostedByID() == $cu && !$ns))
					$uids[$issue->getPostedByID()] = $issue->getPostedByID();
			}

			// Add any users who created a comment
			$cmts = $issue->getComments();
			foreach ($cmts as $cmt)
			{
				$pbid = $cmt->getPostedByID();
				if ($pbid && $this->getSetting(self::NOTIFY_ISSUE_COMMENTED_ON, $pbid))
					$uids[$pbid] = $pbid;
			}

			// Add all users from the team assigned to the issue if valid
			// or add the assigned user if a user is assigned to the issue
			if ($issue->getAssignee() instanceof TBGTeam)
			{
				// Get team member IDs
				foreach ($issue->getAssignee()->getMembers() as $member)
				{
					if ($member->getID() == $cu && !$ns) continue;
					if (!$this->getSetting(self::NOTIFY_ISSUE_TEAMASSIGNED_UPDATED, $member->getID())) continue;
					$uids[$member->getID()] = $member->getID();
				}
			}
			elseif ($issue->getAssignee() instanceof TBGUser)
			{
				if (!($issue->getAssignee()->getID() == $cu && !$ns) && !(!$this->getSetting(self::NOTIFY_ISSUE_ASSIGNED_UPDATED, $issue->getAssignee()->getID())))
					$uids[$issue->getAssignee()->getID()] = $issue->getAssignee()->getID();
			}
			
			// Add all users in the team who leads the project, if valid
			// or add the user who leads the project, if valid
			if ($issue->getProject()->getLeader() instanceof TBGTeam)
			{
				foreach ($issue->getProject()->getLeader()->getMembers() as $member)
				{
					if ($member->getID() == $cu && !$ns) continue;
					if (!$this->getSetting(self::NOTIFY_ISSUE_RELATED_PROJECT_TEAMASSIGNED, $member->getID())) continue;
					$uids[$member->getID()] = $member->getID();
				}
			}
			elseif ($issue->getProject()->getLeader() instanceof TBGUser)
			{
				$lid = $issue->getProject()->getLeader()->getID();
				if (!($lid == $cu && !$ns) && !(!$this->getSetting(self::NOTIFY_ISSUE_PROJECT_ASSIGNED, $lid)))
					$uids[$lid] = $lid;
			}
	
			// Same for QA
			if ($issue->getProject()->getQaResponsible() instanceof TBGTeam)
			{
				foreach ($issue->getProject()->getQaResponsible()->getMembers() as $member)
				{
					if ($member->getID() == $cu && !$ns) continue;
					if (!$this->getSetting(self::NOTIFY_ISSUE_RELATED_PROJECT_TEAMASSIGNED, $member->getID())) continue;
					$uids[$member->getID()] = $member->getID();
				}
			}
			elseif ($issue->getProject()->getQaResponsible() instanceof TBGUser)
			{
				$qaid = $issue->getProject()->getQaResponsible()->getID();
				if (!($qaid == $cu && !$ns) && !(!$this->getSetting(self::NOTIFY_ISSUE_PROJECT_ASSIGNED, $qaid)))
					$uids[$qaid] = $qaid;
			}
			
			foreach ($issue->getProject()->getAssignedTeams() as $team_id => $assignments)
			{
				foreach (TBGContext::factory()->TBGTeam($team_id)->getMembers() as $member)
				{
					if ($member->getID() == $cu && !$ns) continue;
					if (!$this->getSetting(self::NOTIFY_ISSUE_RELATED_PROJECT_TEAMASSIGNED, $member->getID())) continue;
					$uids[$member->getID()] = $member->getID();
				}
			}
			foreach ($issue->getProject()->getAssignedUsers() as $user_id => $assignments)
			{
				$member = TBGContext::factory()->TBGUser($user_id);
				if (!($member->getID() == $cu && !$ns) && !(!$this->getSetting(self::NOTIFY_ISSUE_PROJECT_ASSIGNED, $member->getID())))
					$uids[$member->getID()] = $member->getID();
			}
			
			// Add all users relevant for all affected editions
			foreach ($issue->getEditions() as $edition_list)
			{
				if ($edition_list['edition']->getLeader() instanceof TBGTeam)
				{
					foreach ($edition_list['edition']->getLeader()->getMembers() as $member)
					{
						if ($member->getID() == $cu && !$ns) continue;
						if (!$this->getSetting(self::NOTIFY_ISSUE_RELATED_PROJECT_TEAMASSIGNED, $member->getID())) continue;
						$uids[$member->getID()] = $member->getID();
					}
				}
				elseif ($edition_list['edition']->getLeader() instanceof TBGUser)
				{
					if (!($edition_list['edition']->getLeaderID() == $cu && !$ns) && !(!$this->getSetting(self::NOTIFY_ISSUE_PROJECT_ASSIGNED, $edition_list['edition']->getLeaderID())))
						$uids[$edition_list['edition']->getLeaderID()] = $edition_list['edition']->getLeaderID();
				}
				
				if ($edition_list['edition']->getQaResponsible() instanceof TBGTeam)
				{
					foreach ($edition_list['edition']->getQaResponsible()->getMembers() as $member)
					{
						if ($member->getID() == $cu && !$ns) continue;
						if (!$this->getSetting(self::NOTIFY_ISSUE_RELATED_PROJECT_TEAMASSIGNED, $member->getID())) continue;
						$uids[$member->getID()] = $member->getID();
					}
				}
				elseif ($edition_list['edition']->getQaResponsible() instanceof TBGUser)
				{
					if (!($edition_list['edition']->getQaResponsibleID() == $cu && !$ns) && !(!$this->getSetting(self::NOTIFY_ISSUE_PROJECT_ASSIGNED, $edition_list['edition']->getQaResponsibleID())))
						$uids[$edition_list['edition']->getQaResponsibleID()] = $edition_list['edition']->getQaResponsibleID();
				}
				foreach ($edition_list['edition']->getAssignedTeams() as $team_id => $assignments)
				{
					foreach (TBGContext::factory()->TBGTeam($team_id)->getMembers() as $member)
					{
						if ($member->getID() == $cu && !$ns) continue;
						if (!$this->getSetting(self::NOTIFY_ISSUE_RELATED_PROJECT_TEAMASSIGNED, $member->getID())) continue;
						$uids[$member->getID()] = $member->getID();
					}
				}
				foreach ($edition_list['edition']->getAssignedUsers() as $user_id => $assignments)
				{
					$member = TBGContext::factory()->TBGUser($user_id);
					if ($member->getID() == $cu && !$ns) continue;
					if (!$this->getSetting(self::NOTIFY_ISSUE_PROJECT_ASSIGNED, $member->getID())) continue;
					$uids[$member->getID()] = $member->getID();
				}
			}
			
			// Add all users relevant for all affected components
			foreach ($issue->getComponents() as $component_list)
			{
				foreach ($component_list['component']->getAssignedTeams() as $team_id => $assignments)
				{
					foreach (TBGContext::factory()->TBGTeam($team_id)->getMembers() as $member)
					{
						if ($member->getID() == $cu && !$ns) continue;
						if (!$this->getSetting(self::NOTIFY_ISSUE_RELATED_PROJECT_TEAMASSIGNED, $member->getID())) continue;
						$uids[$member->getID()] = $member->getID();
					}
				}
				foreach ($component_list['component']->getAssignedUsers() as $user_id => $assignments)
				{
					$member = TBGContext::factory()->TBGUser($user_id);
					if ($member->getID() == $cu && !$ns) continue;
					if (!$this->getSetting(self::NOTIFY_ISSUE_PROJECT_ASSIGNED, $member->getID())) continue;
					$uids[$member->getID()] = $member->getID();
				}
			}
			
			foreach ($uids as $uid => $val)
			{
				if ($this->getSetting(self::NOTIFY_ISSUE_ONCE, $uid))
				{
					if ($this->getSetting(self::NOTIFY_ISSUE_ONCE . '_' . $issue->getID(), $uid))
					{
						unset($uids[$uid]);
						continue;
					}
					else
					{
						$this->saveSetting(self::NOTIFY_ISSUE_ONCE . '_' . $issue->getID(), 1, $uid);
					}
				}
				$uids[$uid] = TBGContext::factory()->TBGUser($uid);
			}
			
			return $uids;
		}
		
		public function listen_viewissue(TBGEvent $event)
		{
			if ($this->getSetting(self::NOTIFY_ISSUE_ONCE))
			{
				$this->deleteSetting(self::NOTIFY_ISSUE_ONCE . '_' . $event->getSubject()->getID(), $uid);
			}
		}
		
		public function listen_issueCreate(TBGEvent $event)
		{
			if ($this->isOutgoingNotificationsEnabled())
			{
				$issue = $event->getSubject();
				if ($issue instanceof TBGIssue)
				{
					$to_users = $this->_getIssueRelatedUsers($issue);
					$this->_sendToUsers($to_users, 'issuecreate', array('issue'=>$issue));
				}
			}
		}
		
		protected function _sendToUsers($to_users, $template, $params = array())
		{
			if ($this->isOutgoingNotificationsEnabled())
			{
				if (!is_array($to_users))
				{
					$to_users = array($to_users);
				}
				foreach ($to_users as $user)
				{
					if ($user instanceof TBGUser && $user->isEnabled() && $user->isActivated() && !$user->isDeleted() && !$user->isGuest())
					{
						
						$jid = $this->getSetting('jid', $user->getID());
						if(!$jid){
							continue;
						}
						
						$params = array_merge($params, array(
							'user_buddyname' => $user->getBuddyname(),
							'user_username' => $user->getUsername())
						);
						
						$message = $this->createMessageFromTemplate($template, $params);
						
						try
						{
							$this->sendMessage($jid, $message);
						}
						catch (Exception $e)
						{
							$this->log("There was an error when trying to send email to some recipients:\n" . $e->getMessage(), TBGLogging::LEVEL_NOTICE);
						}
					}
				}
			}
		}

		/*public function listen_projectconfig_tab(TBGEvent $event)
		{
			TBGActionComponent::includeTemplate('jabber/projectconfig_tab', array('selected_tab' => $event->getParameter('selected_tab')));
		}
		
		public function listen_projectconfig_panel(TBGEvent $event)
		{
			TBGActionComponent::includeTemplate('jabber/projectconfig_panel', array('selected_tab' => $event->getParameter('selected_tab'), 'access_level' => $event->getParameter('access_level'), 'project' => $event->getParameter('project')));
		}*/
		
		public function listen_TBGComment_createNew(TBGEvent $event)
		{
			if ($this->isOutgoingNotificationsEnabled())
			{
				$comment = $event->getParameter('comment');
				if ($comment instanceof TBGComment && $comment->getTargetType() == TBGComment::TYPE_ISSUE)
				{
					try
					{
						$issue = $event->getSubject();
						$title = $comment->getTitle();
						$content = $comment->getContent();
						$to_users = $this->_getIssueRelatedUsers($issue);
						
						$this->_sendToUsers($to_users, 'issueupdate', array('issue' => $issue, 'comment' => $content, 'updated_by' => $comment->getPostedBy()));
					}
					catch (Exception $e)
					{
						throw $e;
					}
				}
			}
		}
		
		public function listen_issueSave(TBGEvent $event)
		{
			if ($this->isOutgoingNotificationsEnabled())
			{
				$issue = $event->getSubject();

				if ($issue instanceof TBGIssue)
				{
					$to_users = $this->_getIssueRelatedUsers($issue);
					
					$message = $this->createMessageFromTemplate('issueupdate', array('issue' => $issue, 'comment_lines' => $event->getParameter('comment_lines'), 'updated_by' => $event->getParameter('updated_by')));
					$this->_sendToUsers($to_users, $message);
				}
			}
		}

		/**
		 * Retrieve the instantiated and configured mailer object
		 *
		 * @return TBGJabber
		 */
		public function getJabber()
		{
			if ($this->jabber === null)
			{
				$this->jabber = new TBGMessenger(array(
					'jid'=>$this->getSetting('jid'),
					'password'=>$this->getSetting('password'),
					'resource'=>$this->getSetting('resource'),
					'port'=>$this->getSetting('port'),
					'host'=>$this->getSetting('host')
				));
			}

			return $this->jabber;
		}

		public function createMessageFromTemplate($template, $variables = array()){
			$variables['thebuggenie_url'] = TBGContext::getRouting()->generate('home', array(), false);
			
			return TBGAction::returnTemplateHTML("jabber/{$template}.text", $variables); 
		}
		
		public function sendMessage($jid, $message, $debug = false)
		{
			if ($this->isOutgoingNotificationsEnabled())
			{
				if ($this->usesMessageQueue())
				{
					TBGJabberQueueTable::getTable()->addMessageToQueue($jid, $message);
					return true;
				}
				else
				{
					$sender = $this->getJabber();
					$retval = $sender->send($jid, $message);
				}

				return $retval;
			}
		}

		public function postAccountSettings(TBGRequest $request)
		{
			$uid = TBGContext::getUser()->getID();
			
			switch ($request['jabber_notification_settings_preset'])
			{
				case 'silent':
					$this->saveSetting(TBGJabber::NOTIFY_ISSUE_POSTED_UPDATED, true, $uid);
					$this->saveSetting(TBGJabber::NOTIFY_ISSUE_ONCE, true, $uid); 
					$this->saveSetting(TBGJabber::NOTIFY_ISSUE_ASSIGNED_UPDATED, false, $uid); 
					$this->saveSetting(TBGJabber::NOTIFY_ISSUE_UPDATED_SELF, false, $uid); 
					$this->saveSetting(TBGJabber::NOTIFY_ISSUE_TEAMASSIGNED_UPDATED, false, $uid); 
					$this->saveSetting(TBGJabber::NOTIFY_ISSUE_RELATED_PROJECT_TEAMASSIGNED, false, $uid); 
					$this->saveSetting(TBGJabber::NOTIFY_ISSUE_PROJECT_ASSIGNED, false, $uid); 
					$this->saveSetting(TBGJabber::NOTIFY_ISSUE_COMMENTED_ON, false, $uid); 
					break;
				case 'recommended':
					$this->saveSetting(TBGJabber::NOTIFY_ISSUE_POSTED_UPDATED, true, $uid);
					$this->saveSetting(TBGJabber::NOTIFY_ISSUE_ONCE, true, $uid); 
					$this->saveSetting(TBGJabber::NOTIFY_ISSUE_ASSIGNED_UPDATED, true, $uid); 
					$this->saveSetting(TBGJabber::NOTIFY_ISSUE_UPDATED_SELF, false, $uid); 
					$this->saveSetting(TBGJabber::NOTIFY_ISSUE_TEAMASSIGNED_UPDATED, true, $uid); 
					$this->saveSetting(TBGJabber::NOTIFY_ISSUE_RELATED_PROJECT_TEAMASSIGNED, false, $uid); 
					$this->saveSetting(TBGJabber::NOTIFY_ISSUE_PROJECT_ASSIGNED, true, $uid); 
					$this->saveSetting(TBGJabber::NOTIFY_ISSUE_COMMENTED_ON, true, $uid); 
					break;
				case 'verbose':
					$this->saveSetting(TBGJabber::NOTIFY_ISSUE_POSTED_UPDATED, true, $uid);
					$this->saveSetting(TBGJabber::NOTIFY_ISSUE_ONCE, false, $uid); 
					$this->saveSetting(TBGJabber::NOTIFY_ISSUE_ASSIGNED_UPDATED, true, $uid); 
					$this->saveSetting(TBGJabber::NOTIFY_ISSUE_UPDATED_SELF, true, $uid); 
					$this->saveSetting(TBGJabber::NOTIFY_ISSUE_TEAMASSIGNED_UPDATED, true, $uid); 
					$this->saveSetting(TBGJabber::NOTIFY_ISSUE_RELATED_PROJECT_TEAMASSIGNED, true, $uid); 
					$this->saveSetting(TBGJabber::NOTIFY_ISSUE_PROJECT_ASSIGNED, true, $uid); 
					$this->saveSetting(TBGJabber::NOTIFY_ISSUE_COMMENTED_ON, true, $uid); 
					break;
				default:
					$settings = array(
						self::NOTIFY_ISSUE_ASSIGNED_UPDATED,
						self::NOTIFY_ISSUE_ONCE,
						self::NOTIFY_ISSUE_POSTED_UPDATED,
						self::NOTIFY_ISSUE_PROJECT_ASSIGNED,
						self::NOTIFY_ISSUE_RELATED_PROJECT_TEAMASSIGNED,
						self::NOTIFY_ISSUE_TEAMASSIGNED_UPDATED,
						self::NOTIFY_ISSUE_UPDATED_SELF,
						self::NOTIFY_ISSUE_COMMENTED_ON
					);
					
					foreach ($settings as $setting)
					{
						
						// d'oh, one form, remove prefix
						$this->saveSetting($setting, (int) $request->getParameter('jabber_'.$setting, 0), $uid);
					}
			}
			
			$jid = $request->getParameter('jabber_jid', 0);
			if(!filter_var($jid, FILTER_VALIDATE_EMAIL)){
				$jid = '';
			}
			
			$this->saveSetting(self::NOTIFY_JID, $jid, $uid);
			
			return true;
		}

		public function isOutgoingNotificationsEnabled()
		{
			return (bool) $this->getSetting('enable_outgoing_notifications');
		}

		public function usesMessageQueue()
		{
			return (bool) $this->getSetting('use_queue');
		}

		public function setOutgoingNotificationsEnabled($enabled = true)
		{
			$this->saveSetting('enable_outgoing_notifications', $enabled);
		}
		
		protected function addDefaultSettingsToAllUsers()
		{
			$settings = array(self::NOTIFY_ISSUE_ASSIGNED_UPDATED, self::NOTIFY_ISSUE_ONCE, self::NOTIFY_ISSUE_POSTED_UPDATED, self::NOTIFY_ISSUE_PROJECT_ASSIGNED, self::NOTIFY_ISSUE_RELATED_PROJECT_TEAMASSIGNED, self::NOTIFY_ISSUE_TEAMASSIGNED_UPDATED, self::NOTIFY_ISSUE_COMMENTED_ON);
			foreach (TBGUsersTable::getTable()->getAllUserIDs() as $uid)
			{
				foreach ($settings as $setting)
				{
					$this->saveSetting($setting, 1, $uid);
				}
			}
		}

	}

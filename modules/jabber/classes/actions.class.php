<?php

	class jabberActions extends TBGAction
	{

		/**
		 * Forgotten password logic (AJAX call)
		 *
		 * @param TBGRequest $request
		 */
		//public function runForgot(TBGRequest $request)
		//{
		//	$i18n = TBGContext::getI18n();
		//
		//	try
		//	{
		//		$username = str_replace('%2E', '.', $request['forgot_password_username']);
		//		if (!empty($username))
		//		{
		//			if (($user = TBGUser::getByUsername($username)) instanceof TBGUser)
		//			{
		//				$jid = TBGJabber::getModule()->getSetting('jid', $user->getID());
		//				
		//				if($jid && $user->isEnabled() && !$user->isDeleted())
		//				{
		//					TBGJabber::getModule()->sendforgottenPasswordMessage($user);
		//					return $this->renderJSON(array('message' => $i18n->__('Please use the link in the message you received')));
		//				}
		//				else
		//				{
		//					throw new Exception($i18n->__('Forbidden for this username, please contact your administrator'));
		//				}
		//			}
		//			else
		//			{
		//				throw new Exception($i18n->__('This username does not exist'));
		//			}
		//		}
		//		else
		//		{
		//			throw new Exception($i18n->__('Please enter an username'));
		//		}
		//	}
		//	catch (Exception $e)
		//	{
		//		$this->getResponse()->setHttpStatus(400);
		//		return $this->renderJSON(array('error' => $e->getMessage()));
		//	}
		//}

		/**
		 * Send a test message
		 *
		 * @param TBGRequest $request
		 */
		public function runTestMessage(TBGRequest $request)
		{
			if ($jid = $request['jid'])
			{
				try
				{
					if (TBGJabber::getModule()->sendTestMessage($jid))
					{
						TBGContext::setMessage('module_message', TBGContext::getI18n()->__('The message was successfully accepted for delivery'));
					}
					else
					{
						TBGContext::setMessage('module_error', TBGContext::getI18n()->__('The message was not sent'));
						TBGContext::setMessage('module_error_details', TBGLogging::getMessagesForCategory('jabber', TBGLogging::LEVEL_NOTICE));
					}
				}
				catch (Exception $e)
				{
					TBGContext::setMessage('module_error', TBGContext::getI18n()->__('The message was not sent'));
					TBGContext::setMessage('module_error_details', $e->getMessage());
				}
			}
			else
			{
				TBGContext::setMessage('module_error', TBGContext::getI18n()->__('Please specify a JID'));
			}
			$this->forward(TBGContext::getRouting()->generate('configure_module', array('config_module' => 'jabber')));
		}
	}

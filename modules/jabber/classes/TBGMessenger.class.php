<?php

	/**
	 * Mailer class
	 *
	 * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
	 * @version 3.1
	 * @license http://www.opensource.org/licenses/mozilla1.1.php Mozilla Public License 1.1 (MPL 1.1)
	 * @package thebuggenie
	 * @subpackage mailing
	 */

	/**
	 * Mailer class
	 *
	 * @package thebuggenie
	 * @subpackage mailing
	 */
	class TBGMessenger
	{
		protected $debug = false;
		protected $conn = null;

		public function __construct($options)
		{
			
			static $defaultOptions = array(
				'jid'=>'',
				'port'=>5222,
				'password'=>'',
				'host'=>'',
				'resource'=>'TheBugGenie'
			);
			
			$options = array_merge($defaultOptions, $options);
			
			$jid = explode('@', $options['jid']);
			if(empty($options['host'])){
				$options['host'] = $jid[1];
			}
			
			require_once __DIR__.'/XMPPHP/XMPP.php';
			$this->conn = new XMPPHP_XMPP($options['host'], $options['port'], $jid[0], $options['password'], $options['resource'], $jid[1], $printlog=false, $loglevel=XMPPHP_Log::LEVEL_INFO);
			
			$this->conn->connect();
			//$this->conn->presence();
			$this->conn->processUntil('session_start');
		}
		
		function __destruct(){
			// TODO: in vcs postcommit doesn't work 
			$this->conn->disconnect();
		}

		
		public function send($jid, $message)
		{
			try
			{
				TBGContext::getI18n();
			}
			catch (Exception $e)
			{
				TBGContext::reinitializeI18n(null);
			}
			
			$this->conn->message($jid, $message);
			
			return true;
		}


	}

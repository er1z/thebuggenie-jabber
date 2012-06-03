<?php

	use b2db\Core,
		b2db\Criteria,
		b2db\Criterion;

	/**
	 * @Table(name="mailing_queue")
	 */
	class TBGJabberQueueTable extends TBGB2DBTable
	{
		
		const B2DB_TABLE_VERSION = 1;
		const B2DBNAME = 'jabber_queue';
		const ID = 'jabber_queue.id';
		const JID = 'jabber_queue.content';
		const MESSAGE = 'jabber_queue.content';
		const DATE = 'jabber_queue.date';
		const SCOPE = 'jabber_queue.scope';

		protected function _initialize()
		{
			parent::_setup(self::B2DBNAME, self::ID);
			parent::_addText(self::MESSAGE);
			parent::_addText(self::JID);
			parent::_addInteger(self::DATE, 10);
			parent::_addForeignKeyColumn(self::SCOPE, TBGScopesTable::getTable(), TBGScopesTable::ID);
		}

		public function addMessageToQueue($jid, $message)
		{
			$crit = $this->getCriteria();
			$crit->addInsert(self::MESSAGE, $message);
			$crit->addInsert(self::JID, $jid);
			$crit->addInsert(self::DATE, time());
			$crit->addInsert(self::SCOPE, TBGContext::getScope()->getID());

			$res = $this->doInsert($crit);

			return $res->getInsertID();
		}

		public function getQueuedMessages($limit = null)
		{
			$crit = $this->getCriteria();
			$crit->addWhere(self::SCOPE, TBGContext::getScope()->getID());
			if ($limit !== null)
			{
				$crit->setLimit($limit);
			}
			$crit->addOrderBy(self::DATE, Criteria::SORT_ASC);

			$messages = array();
			$res = $this->doSelect($crit);

			if ($res)
			{
				while ($row = $res->getNextRow())
				{
					$messages[$row->get(self::ID)] = array(
            'jid'=>$row->get(self::JID),
            'message'=>$row->get(self::MESSAGE)
					);
				}
			}

			return $messages;
		}

		public function deleteProcessedMessages($ids)
		{
			$crit = $this->getCriteria();
			$crit->addWhere(self::ID, (array) $ids, Criteria::DB_IN);
			$crit->addWhere(self::SCOPE, TBGContext::getScope()->getID());

			$res = $this->doDelete($crit);
		}
		
	}
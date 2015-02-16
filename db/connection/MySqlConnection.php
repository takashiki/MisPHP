<?php
namespace mis\db\connection;

use mis\db\grammar\MySqlGrammar as QueryGrammar;

class MySqlConnectio extends Connection
{
  /**
	 * Get the default query grammar instance.
	 *
	 * @return \mis\db\grammar\MySqlGrammar
	 */
	protected function getDefaultQueryGrammar() {
		return $this->withTablePrefix(new QueryGrammar);
	}
}
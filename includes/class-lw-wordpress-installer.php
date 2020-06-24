<?php


class LwWordpressInstaller {

	private static $instance;


	public static function init()
	{
		return new self;
	}

	public static function getInstance()
	{
		if (null === self::$instance) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	public function checkIfTableExists($tableName)
	{
		global $wpdb;

		$tableResult = $wpdb->get_results('
            SELECT
                `TABLE_NAME`
            FROM
                `information_schema`.`TABLES`
            WHERE
                `TABLE_SCHEMA` = "'.esc_sql($wpdb->dbname).'"
                AND
                `TABLE_NAME` = "'.esc_sql($tableName).'"
        ');

		if (!empty($tableResult[0]->TABLE_NAME)) {
			return true;
		} else {
			return false;
		}
	}
}
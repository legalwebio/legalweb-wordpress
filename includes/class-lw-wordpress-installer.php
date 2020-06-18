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

	public function getCreateTableStatementCommissionLog($tableName, $charsetCollate)
	{
		return "CREATE TABLE IF NOT EXISTS ".$tableName." (
            `log_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
            `user_id` int(11) NOT NULL,
            `wordpress_id` varchar(35) DEFAULT NULL,
            `commission_value` DECIMAL(10,2),
            `commission_rate` int(11),
            `commission_date` datetime DEFAULT NULL,
            `order_id` int(11),
            `is_rejected` int(1) unsigned NOT NULL DEFAULT '0',
            `is_paid_out` int(1) unsigned NOT NULL DEFAULT '0',
            `is_deleted` int(1) unsigned NOT NULL DEFAULT '0',
            `is_sumo` int(1) unsigned NOT NULL DEFAULT '0',
            PRIMARY KEY (`log_id`),
            KEY `user_id` (`user_id`)
        ) ".$charsetCollate.";";
	}

	public function getCreateTableStatementVisitLog($tableName, $charsetCollate)
	{
		return "CREATE TABLE IF NOT EXISTS ".$tableName." (
            `log_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
            `user_id` int(11) NOT NULL,
            `visit_date` datetime DEFAULT NULL,
            `target_url` varchar(255),
            `referring_url` varchar(255),
            PRIMARY KEY (`log_id`),
            KEY `user_id` (`user_id`)
        ) ".$charsetCollate.";";
	}
}
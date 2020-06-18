<?php


class LwWordpressDatabaseApi {

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

	public function __construct()
	{
		require_once ABSPATH.'wp-admin/includes/upgrade.php';
	}


	public function checkTable($tableName, $sqlCreateStatement)
	{
		global $wpdb;

		$data = [
			'success' => true,
			'message' => '',
		];

		if (!LwWordpressInstaller::getInstance()->checkIfTableExists($tableName)) {

			// Try to install the table
			dbDelta($sqlCreateStatement);

			// Check again
			if (!LwWordpressInstaller::getInstance()->checkIfTableExists($tableName)) {
				$data = [
					'success' => false,
					'message' => sprintf(__('The table <strong>%s</strong> could not be created, please check your server error logs for more details.', 'lw-wordpress'), $tableName),
				];
			}
		}

		return $data;
	}

	public function checkTableCommissionLog()
	{
		global $wpdb;

		$charsetCollate = $wpdb->get_charset_collate();
		$tableName = $wpdb->prefix.LwWordpressConstants::AFFILIATE_COMMISSION_LOG_TABLE_NAME;

		$sqlCreateTable = LwWordpressInstaller::getInstance()->getCreateTableStatementCommissionLog($tableName, $charsetCollate);

		$data = $this->checkTable($tableName, $sqlCreateTable);

		return $data;
	}

	public function getCommissionLogOfUser($userId, $dateFrom, $dateTo, $adminMode = false, $includeDeleted = false)
	{
		//error_log("($userId, $dateFrom, $dateTo, $adminMode = false, $includeDeleted = false)");
		global $wpdb;
		$tableName = $wpdb->prefix.LwWordpressConstants::AFFILIATE_COMMISSION_LOG_TABLE_NAME;

		$logItems = $wpdb->get_results('
                    SELECT
                        `log_id`,
			            `user_id`,
			            `wordpress_id`,
			            `commission_value`,
			            `commission_rate`,
			            `commission_date`,
			            `order_id`,
			            `is_rejected`,
			            `is_paid_out`,
			            `is_deleted`
                    FROM
                        `'.$tableName.'`
                    WHERE
                        `user_id` = "'.esc_sql($userId).'" AND
                        `commission_date` >= STR_TO_DATE("'.esc_sql($dateFrom).' 00:00","%d-%m-%Y %H:%i") AND
                        `commission_date` <= STR_TO_DATE("'.esc_sql($dateTo).' 23:59","%d-%m-%Y %H:%i") AND
                        `is_deleted` = 0
                    ORDER BY
                         `log_id` desc
                ');

		$logHistory = [];
		foreach ($logItems as $logItem) {
			$logHistory[] = [
				'id' => $logItem->log_id,
				'userId' =>  $logItem->user_id,
				'orderId' => $logItem->order_id,
				'commissionValue' => $logItem->commission_value,
				'commissionRate' => $logItem->commission_rate,
				'commissionDate' => strtotime($logItem->commission_date),
				'isRejected' => $logItem->is_rejected,
				'isPaidOut' => $logItem->is_paid_out,
				'isDeleted' => $logItem->is_deleted
			];
		}

		return $logHistory;
	}

	public function getCommissionLogOfAllUsers($dateFrom, $dateTo, $includeDeleted = false)
	{
		//error_log("getCommissionLogOfAllUsers($dateFrom, $dateTo, $includeDeleted)");
		global $wpdb;
		$tableName = $wpdb->prefix.LwWordpressConstants::AFFILIATE_COMMISSION_LOG_TABLE_NAME;

		$logItems = $wpdb->get_results('
                    SELECT
                        `log_id`,
			            `user_id`,
			            `wordpress_id`,
			            `commission_value`,
			            `commission_rate`,
			            `commission_date`,
			            `order_id`,
			            `is_rejected`,
			            `is_paid_out`,
			            `is_deleted`
                    FROM
                        `'.$tableName.'`
                    WHERE
                        `commission_date` >= STR_TO_DATE("'.esc_sql($dateFrom).' 00:00","%d-%m-%Y %H:%i") AND
                        `commission_date` <= STR_TO_DATE("'.esc_sql($dateTo).' 23:59","%d-%m-%Y %H:%i") AND
                        `is_deleted` = 0
                    ORDER BY
                         `log_id` desc
                ');

		$logHistory = [];
		foreach ($logItems as $logItem) {
			$logHistory[] = [
				'id' => $logItem->log_id,
				'userId' =>  $logItem->user_id,
				'orderId' => $logItem->order_id,
				'commissionValue' => $logItem->commission_value,
				'commissionRate' => $logItem->commission_rate,
				'commissionDate' => strtotime($logItem->commission_date),
				'isRejected' => $logItem->is_rejected,
				'isPaidOut' => $logItem->is_paid_out,
				'isDeleted' => $logItem->is_deleted
			];
		}

		return $logHistory;
	}

	public function getTotalCommissionOfUser($userId, $dateFrom, $dateTo)
	{
		global $wpdb;
		$tableName = $wpdb->prefix.LwWordpressConstants::AFFILIATE_COMMISSION_LOG_TABLE_NAME;

		$result = $wpdb->get_results('
                    SELECT
                        SUM(commission_value) as total
                    FROM
                        `'.$tableName.'`
                    WHERE
                        `user_id` = '.esc_sql($userId).' AND
                        `commission_date` >= STR_TO_DATE("'.esc_sql($dateFrom).' 00:00","%d-%m-%Y %H:%i") AND
                        `commission_date` <= STR_TO_DATE("'.esc_sql($dateTo).' 23:59","%d-%m-%Y %H:%i")
                ');

		$total = $result[0]->total;
		/*
		$settings = LwWordpresssUserManager::getInstance()->getUserSettings($userId);
		if ($settings['sumoTotal'] > 0)
		{
			$total += $settings['sumoVisits'];
		}
		*/

		return $total;
	}

	public function getTotalCommissionCountOfUser($userId, $dateFrom, $dateTo)
	{
		global $wpdb;
		$tableName = $wpdb->prefix.LwWordpressConstants::AFFILIATE_COMMISSION_LOG_TABLE_NAME;

		$result = $wpdb->get_results('
                    SELECT
                        COUNT(commission_value) as total
                    FROM
                        `'.$tableName.'`
                    WHERE
                        `user_id` = '.esc_sql($userId).' AND
                        `commission_date` >= STR_TO_DATE("'.esc_sql($dateFrom).' 00:00","%d-%m-%Y %H:%i") AND
                        `commission_date` <= STR_TO_DATE("'.esc_sql($dateTo).' 23:59","%d-%m-%Y %H:%i")
                ');

		$total = $result[0]->total;
		/*
		$settings = LwWordpresssUserManager::getInstance()->getUserSettings($userId);
		if ($settings['sumoTotal'] > 0)
		{
			$total += $settings['sumoVisits'];
		}
		*/

		return $total;
	}

	public function insertCommissionOfUser($userId, $wordpressId, $orderId, $commissionRate, $commisionValue, $commissionDate = '', $isSumo = 0)
	{
		//error_log("insertCommissionOfUser ($userId, $wordpressId, $orderId, $commissionRate, $commisionValue, $commissionDate, $isSumo) called");

		global $wpdb;
		$tableName = $wpdb->prefix.LwWordpressConstants::AFFILIATE_COMMISSION_LOG_TABLE_NAME;

		if (empty($commissionDate)) $commissionDate = date('d-m-Y H:i');

		// Insert log
		$wpdb->query('
                        INSERT INTO
                            `'.$tableName.'`
                        (
                            `log_id`,
				            `user_id`,
				            `wordpress_id`,
				            `commission_value`,
				            `commission_rate`,
				            `commission_date`,
				            `order_id`,
				            `is_rejected`,
				            `is_paid_out`,
				            `is_deleted`,
				            `is_sumo`
                        )
                        VALUES
                        (
                            null,
                            "'.esc_sql($userId).'",
                            "'.esc_sql($wordpressId).'",
                            "'.$commisionValue.'",
                            "'.$commissionRate.'",
                            STR_TO_DATE("'.esc_sql($commissionDate).'","%d-%m-%Y %H:%i"),
                            "'.$orderId.'",
                            "0",
                            "0",
                            "0",
                            "'.$isSumo.'"
                        )
                    ');
	}

	public function checkTableVisitLog()
	{
		global $wpdb;

		$charsetCollate = $wpdb->get_charset_collate();
		$tableName = $wpdb->prefix.LwWordpressConstants::AFFILIATE_VISIT_LOG_TABLE_NAME;

		$sqlCreateTable = LwWordpressInstaller::getInstance()->getCreateTableStatementVisitLog($tableName, $charsetCollate);

		$data = $this->checkTable($tableName, $sqlCreateTable);

		return $data;
	}

	public function insertVisitLOfUser($userId, $referringUrl, $targetUrl)
	{
		global $wpdb;
		$tableName = $wpdb->prefix.LwWordpressConstants::AFFILIATE_VISIT_LOG_TABLE_NAME;

		// Insert log
		$wpdb->query('
                        INSERT INTO
                            `'.$tableName.'`
                        (
                            `log_id`,
				            `user_id`,
				            `visit_date`,
				            `target_url`,
				            `referring_url`
                        )
                        VALUES
                        (
                            null,
                            "'.esc_sql($userId).'",
                            NOW(),
                            "'.esc_sql(stripslashes($targetUrl)).'",
                            "'.esc_sql(stripslashes($referringUrl)).'"                           
                        )
                    ');
	}

	public function getVisitLogOfUser($userId, $dateFrom, $dateTo)
	{
		global $wpdb;
		$tableName = $wpdb->prefix.LwWordpressConstants::AFFILIATE_VISIT_LOG_TABLE_NAME;

		$logItems = $wpdb->get_results('
                    SELECT
                        `log_id`,
			            `user_id`,
			            `visit_date`,
			            `target_url`,
			            `referring_url`
                    FROM
                        `'.$tableName.'`
                    WHERE
                        `user_id` = "'.esc_sql($userId).'" AND
                        `visit_date` >= STR_TO_DATE("'.esc_sql($dateFrom).' 00:00","%d-%m-%Y %H:%i") AND
                        `visit_date` <= STR_TO_DATE("'.esc_sql($dateTo).' 23:59","%d-%m-%Y %H:%i")
                    ORDER BY
                         `log_id` desc
                ');

		$logHistory = [];
		foreach ($logItems as $logItem) {
			$logHistory[] = [
				'id' => $logItem->log_id,
				'userId' =>  $logItem->user_id,
				'commissionDate' => strtotime($logItem->visit_date),
				'targetUrl' => $logItem->target_url,
				'referringUrl' => $logItem->referring_url
			];
		}

		return $logHistory;
	}

	public function getVisitCountOfUser($userId, $dateFrom, $dateTo)
	{
		global $wpdb;
		$tableName = $wpdb->prefix.LwWordpressConstants::AFFILIATE_VISIT_LOG_TABLE_NAME;

		$result = $wpdb->get_results('
                    SELECT
                        COUNT(*) as total
                    FROM
                        `'.$tableName.'`
                    WHERE
                        `user_id` = '.esc_sql($userId).' AND
                        `visit_date` >= STR_TO_DATE("'.esc_sql($dateFrom).' 00:00","%d-%m-%Y %H:%i") AND
                        `visit_date` <= STR_TO_DATE("'.esc_sql($dateTo).' 23:59","%d-%m-%Y %H:%i")
                ');

		$total = $result[0]->total;
		$settings = LwWordpresssUserManager::getInstance()->getUserSettings($userId);
		if ($settings['sumoVisits'] > 0)
		{
			$total += $settings['sumoVisits'];
		}

		return $total;
	}

	public function getAllUsersWithCounts()
	{
		global $wpdb;
		$tableName = $wpdb->prefix. 'usermeta';

		$allMetas = $wpdb->get_results('
                    SELECT
			            `user_id`,
			            `meta_value`
                    FROM
                        `'.$tableName.'`
                    WHERE
                        `meta_key` = "lw_wordpress"
                    ORDER BY
                         `user_id` desc
                ');

		$result = [];
		foreach ($allMetas as $metaItem) {

			$userId = $metaItem->user_id;
			$meta = unserialize($metaItem->meta_value);

			$user = get_user_by('id', $userId);

			$totalAmount = $this->getTotalCommissionOfUser($userId, '01-01-1970', '01-01-2100');
			$totalCountCommissions = $this->getTotalCommissionCountOfUser($userId, '01-01-1970', '01-01-2100');
			$totalVisits = $this->getVisitCountOfUser($userId, '01-01-1970', '01-01-2100');

			$efficiency = 0;
			try {
				if ($totalVisits == 0 || $totalCountCommissions == 0) $efficiency = 0;
				 else $efficiency =  round(   ($totalCountCommissions / $totalVisits)*100, 2 );
			}catch (Exception $e) {$efficiency = 0;}

			$payoutInfoError = false;
			if ($meta['payoutType'] == 'paypal' && empty($meta['paypalAccount'] ) ) $payoutInfoError = true;
			if ($meta['payoutType'] == 'bank' && empty($meta['bankIban'] ) ) $payoutInfoError = true;

			$resultItem = array('userId' => $userId,
			                    'userName' => $user->nickname,
			                    'fullName' => "$user->first_name $user->last_name",
								'email' => $user->user_email,
								'totalAmount' => $totalAmount,
								'totalVisits' => $totalVisits,
								'commissionRate' => $meta['commissionRate'],
								'active' => $meta['enabled'],
								'totalCountCommissions' => $totalCountCommissions,
								'efficiency' => $efficiency,
								'payoutInfoError' => $payoutInfoError,
								'payoutType' => $meta['payoutType']);

			$result[] = $resultItem;
		}

		return $result;
	}

	public function getCommissionsOfAllUsersForPayment($payoutType, $dateFrom, $dateTo)
	{
		global $wpdb;
		$tableName = $wpdb->prefix. 'usermeta';

		$allMetas = $wpdb->get_results('
                    SELECT
			            `user_id`,
			            `meta_value`
                    FROM
                        `'.$tableName.'`
                    WHERE
                        `meta_key` = "lw_wordpress"
                    ORDER BY
                         `user_id` desc
                ');

		$result = [];
		foreach ($allMetas as $metaItem) {

			$userId = $metaItem->user_id;
			$userSettings = unserialize($metaItem->meta_value);

			$user = get_user_by('id', $userId);
			$defaultSettings = LwWordpresssUserManager::getInstance()->getDefaultUserSettings($userId);
			$userSettings = array_merge($defaultSettings, $userSettings);

			$payoutTypeOfUser = $userSettings['payoutType'];
			if ($payoutType != $payoutTypeOfUser) continue;

			$totalAmount = $this->getTotalCommissionOfUser($userId, $dateFrom, $dateTo);

			$resultItem = array('userId' => $userId,
			                    'totalAmount' => $totalAmount);

			$result[] = $resultItem;
		}

		return $result;
	}

	public function migrateSumoUsers()
	{
		//error_log('migrateSumoUsers called');
		global $wpdb;

		$tableName = $wpdb->prefix.'posts';
		$result = $wpdb->get_results("SELECT `id`, `post_author`  FROM `".$tableName."` WHERE `post_type` ='sumowordpresss' and `post_status` = 'sumoactive'");
		$migrationOrderCount = 0;
		$migrationUserCount = 0;

		foreach ($result as $sumoRefItem) {

			$migrationUserCount += 1;
			$sumoRefId =  $sumoRefItem->id;
			$userId =  $sumoRefItem->post_author;
			//error_log("migrating user $userId with refId $sumoRefId");

			$settings = LwWordpresssUserManager::getInstance()->getUserSettings($userId);
			$settings['enabled'] = '1';
			$settings['hasSumo'] = '1';
			$settings['sumoId'] = $sumoRefId;
			$settings['commissionRate'] = "20"; // sumo had 20 %

			// store amounts
			//$totalAmount = LwWordpressSumoApi::getInstance()->sumo_wordpresss_get_total_paid_commissions($sumoRefId);
			//if ($totalAmount > 0) {
			//	$settings['sumoTotal'] = $totalAmount;
			//}

			$totalAmount = LwWordpressSumoApi::getInstance()->sumo_wordpresss_get_total_visits($sumoRefId);
			if ($totalAmount > 0) {
				$settings['sumoVisits'] = $totalAmount;
				//$this->insertVisitLOfUser( $userId,'migration', '');
			}

			// migrate payment data
			$payment_method         = get_post_meta( $sumoRefId , 'fp_sumo_wordpresss_payment_method' , true ) ;
			$paypalemail            = get_post_meta( $sumoRefId , 'fp_sumo_wordpresss_paypal_email' , true ) ;
			$custom_payment_details = get_post_meta( $sumoRefId , 'fp_sumo_wordpresss_custom_payment' , true ) ;
			$payment_info           = $payment_method == 1 ? $paypalemail : $custom_payment_details ;
			if ($payment_method == 1) // paypal
			{
				$settings['payoutType'] = 'paypal';
				$settings['paypalAccount'] = $paypalemail;
			} else
			{
				$paymentInfoArray = preg_split ('/\r\n|\n|\r/', $custom_payment_details);
				$settings['payoutType'] = 'bank';
				$settings['bankOwner'] = $this->getArrayValueIfPresent($paymentInfoArray, 'Name');
				$settings['bankIban'] = $this->getArrayValueIfPresent($paymentInfoArray, 'IBAN');
				$settings['bankSwift'] = $this->getArrayValueIfPresent($paymentInfoArray, 'BIC');
				$settings['bankName'] = '';
				$settings['uid'] = $this->getArrayValueIfPresent($paymentInfoArray, 'UID / VAT / Umsatzsteuernummer');
			}

			LwWordpresssUserManager::getInstance()->saveUserSettings($userId, $settings);
		}

		// MIGRATE COMMISSIONS
		// clear old entries
		$tableName = $wpdb->prefix.LwWordpressConstants::AFFILIATE_COMMISSION_LOG_TABLE_NAME;
		$wpdb->query('DELETE FROM `'.$tableName.'` WHERE `is_sumo` = 1');

		$tableNamePostMeta = $wpdb->prefix.'postmeta';
		$tableNamePosts = $wpdb->prefix.'posts';
		$sumoRefOrders = $wpdb->get_results("
				select `post_id`, `meta_value` 
					from `".$tableNamePostMeta."`
					join `".$tableNamePosts."` on `".$tableNamePosts."`.`id` =`".$tableNamePostMeta."`.`post_id`
					where `wp_posts`.`post_status` = 'wc-completed' and `".$tableNamePostMeta."`.`meta_key` = 'sumo_wordpress_id'
				");
		// process every order and calc commission, insert into order log
		$totalAmount = 0 ;
		foreach ($sumoRefOrders as $sumoRefOrderId) {

			//error_log("migrating  order $sumoRefOrderId->post_id of user $userId with refId $sumoRefId");
			$migrationOrderCount += 1;
			try {
				$orderId = $sumoRefOrderId->post_id;
				$sumoAffId = $sumoRefOrderId->meta_value;
				$userId = LwWordpressSumoApi::getInstance()->sumo_wordpresss_get_user_id_from_wordpress_id( $sumoAffId );

				$settings = LwWordpresssUserManager::getInstance()->getUserSettings($userId);
				$order      = new WC_Order( $orderId );
				$commission = LwWordpressOrderApi::getInstance()->calculateCommissionValue( $settings, $order );
				$totalAmount += $commission;
				$commisionDate = $order->get_date_created();
				$commisionDateString =  $commisionDate->format( 't-m-Y H:i' );

				//error_log("inserting commission value $commission of order $sumoRefOrderId->post_id from $commisionDateString of user $userId with refId $sumoRefId");
				$this->insertCommissionOfUser( $userId, $sumoAffId, $orderId, $settings['commissionRate'], $commission, $commisionDateString, 1 );
				//error_log("insertCommissionOfUser done");
			}catch (Exception $e)
			{
				error_log('Exception abgefangen: '.  $e->getMessage());
			}
		}

		error_log("migration completed. users:$migrationUserCount, orders: $migrationOrderCount");
	}

	public function setCommissionsPaidStatus($dateFrom, $dateTo, $paidStatus)
	{
		global $wpdb;
		$tableName = $wpdb->prefix.LwWordpressConstants::AFFILIATE_COMMISSION_LOG_TABLE_NAME;

		$wpdb->query('
                UPDATE
                 `'.$tableName.'`
                SET
		            `is_paid_out` = '.$paidStatus.'
                WHERE
                    `commission_date` >= STR_TO_DATE("'.esc_sql($dateFrom).' 00:00","%d-%m-%Y %H:%i") AND
                    `commission_date` <= STR_TO_DATE("'.esc_sql($dateTo).' 23:59","%d-%m-%Y %H:%i") AND
                    `is_deleted` = 0
            ');
	}

	private function getArrayValueIfPresent($arr, $key, $default = '') {

		try {

			$index = array_search($key, $arr);
			if ($index >= 0)
			{
				return $arr[$index+1];
			}
		}catch (Exception $ex)
		{
			return $default;
		}
	}
}
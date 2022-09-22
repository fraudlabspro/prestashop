<?php
/**
 * 2012-2022 FraudLabs Pro.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 *  @copyright FraudLabs Pro
 *  @license http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  International Registered Trademark & Property of FraudLabs Pro
 */
if (!defined('_PS_VERSION_')) {
	exit;
}

class fraudlabspro extends Module
{
	protected $_html = '';
	protected $_postErrors = [];

	public function __construct()
	{
		$this->name = 'fraudlabspro';
		$this->tab = 'payment_security';
		$this->version = '1.15.1';
		$this->author = 'FraudLabs Pro';
		$this->controllers = ['payment', 'validation'];
		$this->module_key = 'cdb22a61c7ec8d1f900f6c162ad96caa';

		$this->bootstrap = true;
		parent::__construct();

		$this->displayName = $this->l('FraudLabs Pro Fraud Prevention');
		$this->description = $this->l('FraudLabs Pro screens transaction for online frauds to protect your store from fraud attempts.');
	}

	public function install()
	{
		if (!parent::install() || !$this->registerHook('newOrder') || !$this->registerHook('adminOrder') || !$this->registerHook('cart') || !$this->registerHook('footer')) {
			return false;
		}

		Configuration::updateValue('FLP_ENABLED', '1');
		Configuration::updateValue('FLP_LICENSE_KEY', '');

		Db::getInstance()->Execute('CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'orders_fraudlabspro` (
            `id_order` INT(10) UNSIGNED NOT NULL,
            `is_country_match` CHAR(2) NOT NULL DEFAULT \'\' COLLATE \'utf8_bin\',
            `is_high_risk_country` CHAR(2) NOT NULL DEFAULT \'\' COLLATE \'utf8_bin\',
            `distance_in_km` VARCHAR(10) NOT NULL DEFAULT \'\' COLLATE \'utf8_bin\',
            `distance_in_mile` VARCHAR(10) NOT NULL DEFAULT \'\' COLLATE \'utf8_bin\',
            `ip_address` VARCHAR(39) NOT NULL DEFAULT \'\' COLLATE \'utf8_bin\',
            `ip_country` VARCHAR(2) NOT NULL DEFAULT \'\' COLLATE \'utf8_bin\',
            `ip_continent` VARCHAR(20) NOT NULL DEFAULT \'\' COLLATE \'utf8_bin\',
            `ip_region` VARCHAR(21) NOT NULL DEFAULT \'\' COLLATE \'utf8_bin\',
            `ip_city` VARCHAR(21) NOT NULL DEFAULT \'\' COLLATE \'utf8_bin\',
            `ip_latitude` VARCHAR(21) NOT NULL DEFAULT \'\' COLLATE \'utf8_bin\',
            `ip_longitude` VARCHAR(21) NOT NULL DEFAULT \'\' COLLATE \'utf8_bin\',
            `ip_timezone` VARCHAR(10) NOT NULL DEFAULT \'\' COLLATE \'utf8_bin\',
            `ip_elevation` VARCHAR(10) NOT NULL DEFAULT \'\' COLLATE \'utf8_bin\',
            `ip_domain` VARCHAR(50) NOT NULL DEFAULT \'\' COLLATE \'utf8_bin\',
            `ip_mobile_mnc` VARCHAR(100) NOT NULL DEFAULT \'\' COLLATE \'utf8_bin\',
            `ip_mobile_mcc` VARCHAR(100) NOT NULL DEFAULT \'\' COLLATE \'utf8_bin\',
            `ip_mobile_brand` VARCHAR(100) NOT NULL DEFAULT \'\' COLLATE \'utf8_bin\',
            `ip_netspeed` VARCHAR(10) NOT NULL DEFAULT \'\' COLLATE \'utf8_bin\',
            `ip_isp_name` VARCHAR(50) NOT NULL DEFAULT \'\' COLLATE \'utf8_bin\',
            `ip_usage_type` VARCHAR(30) NOT NULL DEFAULT \'\' COLLATE \'utf8_bin\',
            `is_free_email` CHAR(2) NOT NULL DEFAULT \'\' COLLATE \'utf8_bin\',
            `is_new_domain_name` CHAR(2) NOT NULL DEFAULT \'\' COLLATE \'utf8_bin\',
            `is_proxy_ip_address` CHAR(2) NOT NULL DEFAULT \'\' COLLATE \'utf8_bin\',
            `is_bin_found` CHAR(2) NOT NULL DEFAULT \'\' COLLATE \'utf8_bin\',
            `is_bin_country_match` CHAR(2) NOT NULL DEFAULT \'\' COLLATE \'utf8_bin\',
            `is_bin_name_match` CHAR(2) NOT NULL DEFAULT \'\' COLLATE \'utf8_bin\',
            `is_bin_phone_match` CHAR(2) NOT NULL DEFAULT \'\' COLLATE \'utf8_bin\',
            `is_bin_prepaid` CHAR(2) NOT NULL DEFAULT \'\' COLLATE \'utf8_bin\',
            `is_address_ship_forward` CHAR(2) NOT NULL DEFAULT \'\' COLLATE \'utf8_bin\',
            `is_bill_ship_city_match` CHAR(2) NOT NULL DEFAULT \'\' COLLATE \'utf8_bin\',
            `is_bill_ship_state_match` CHAR(2) NOT NULL DEFAULT \'\' COLLATE \'utf8_bin\',
            `is_bill_ship_country_match` CHAR(2) NOT NULL DEFAULT \'\' COLLATE \'utf8_bin\',
            `is_bill_ship_postal_match` CHAR(2) NOT NULL DEFAULT \'\' COLLATE \'utf8_bin\',
            `is_ip_blacklist` CHAR(2) NOT NULL DEFAULT \'\' COLLATE \'utf8_bin\',
            `is_email_blacklist` CHAR(2) NOT NULL DEFAULT \'\' COLLATE \'utf8_bin\',
            `is_credit_card_blacklist` CHAR(2) NOT NULL DEFAULT \'\' COLLATE \'utf8_bin\',
            `is_device_blacklist` CHAR(2) NOT NULL DEFAULT \'\' COLLATE \'utf8_bin\',
            `is_user_blacklist` CHAR(2) NOT NULL DEFAULT \'\' COLLATE \'utf8_bin\',
            `flp_score` CHAR(3) NOT NULL DEFAULT \'\' COLLATE \'utf8_bin\',
            `flp_distribution` CHAR(3) NOT NULL DEFAULT \'\' COLLATE \'utf8_bin\',
            `flp_status` CHAR(10) NOT NULL DEFAULT \'\' COLLATE \'utf8_bin\',
			`flp_rules` VARCHAR(255) NOT NULL DEFAULT \'\' COLLATE \'utf8_bin\',
            `flp_id` CHAR(15) NOT NULL DEFAULT \'\' COLLATE \'utf8_bin\',
            `flp_error_code` CHAR(3) NOT NULL DEFAULT \'\' COLLATE \'utf8_bin\',
            `flp_message` VARCHAR(50) NOT NULL DEFAULT \'\' COLLATE \'utf8_bin\',
            `flp_credits` VARCHAR(10) NOT NULL DEFAULT \'\' COLLATE \'utf8_bin\',
            `api_key` CHAR(32) NOT NULL DEFAULT \'\' COLLATE \'utf8_bin\',
            `is_blacklisted` CHAR(1) NOT NULL DEFAULT \'0\' COLLATE \'utf8_bin\',
            `is_phone_verified` VARCHAR(100) NOT NULL DEFAULT \'0\' COLLATE \'utf8_bin\',
            INDEX `id_order` (`id_order`)
        ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;');

		Db::getInstance()->Execute('CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'flp_order_ip` (
              `id_cart` INT NOT NULL,
            `ip` VARCHAR(39) NOT NULL,
            PRIMARY KEY (`id_cart`)
        ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;');

		return true;
	}

	public function getContent()
	{
		if (Tools::isSubmit('btnSubmit')) {
			$this->_postValidation();
			if (!count($this->_postErrors)) {
				$this->_postProcess();
			} else {
				foreach ($this->_postErrors as $err) {
					$this->_html .= $this->displayError($err);
				}
			}
		} else {
			$this->_html .= '<br />';
		}

		$this->_html .= $this->renderForm();

		return $this->_html;
	}

	public function hookCart($params)
	{
		if (!Validate::isLoadedObject($params['cart'])) {
			return;
		}

		Db::getInstance()->Execute('INSERT IGNORE INTO `' . _DB_PREFIX_ . 'flp_order_ip` VALUES(' . $params['cart']->id . ', "' . $this->getIP() . '")');
	}

	public function hookNewOrder($params)
	{
		if (!Configuration::get('PS_SHOP_ENABLE') || !Configuration::get('FLP_LICENSE_KEY') || !Configuration::get('FLP_ENABLED')) {
			return;
		}

		$customer = new Customer((int) $params['order']->id_customer);
		$address_delivery = new Address((int) $params['order']->id_address_delivery);
		$address_invoice = new Address((int) $params['order']->id_address_invoice);
		$default_currency = new Currency((int) Configuration::get('PS_CURRENCY_DEFAULT'));

		if ($address_delivery->id_state !== null || $address_delivery->id_state != '') {
			$delivery_state = new State((int) $address_delivery->id_state);
		}

		$product_list = $params['order']->getProductsDetail();

		$quantity = 0;

		foreach ($product_list as $p) {
			$quantity += $p['product_quantity'];
		}

		$ip = Db::getInstance()->getValue('SELECT `ip` FROM  `' . _DB_PREFIX_ . 'flp_order_ip` WHERE `id_cart` = "' . ((int) $params['cart']->id) . '"');
		$ip = (!$ip) ? $this->getIP() : $ip;

		$bill_state = '';

		if ($address_invoice->id_state !== null || $address_invoice->id_state != '') {
			$State = new State((int) $address_invoice->id_state);
			$bill_state = $State->iso_code;
		}

		$response = Tools::file_get_contents('https://api.fraudlabspro.com/v1/order/screen?' . http_build_query([
			'key'                => Configuration::get('FLP_LICENSE_KEY'),
			'ip'                 => $ip,
			'first_name'         => $address_invoice->firstname,
			'last_name'          => $address_invoice->lastname,
			'bill_city'          => $address_invoice->city,
			'bill_state'         => $bill_state,
			'bill_country'       => Country::getIsoById((int) $address_invoice->id_country),
			'bill_zip_code'      => $address_invoice->postcode,
			'email_domain'       => Tools::substr($customer->email, strpos($customer->email, '@') + 1),
			'email_hash'         => $this->hastIt($customer->email),
			'email'              => $customer->email,
			'user_phone'         => $address_invoice->phone,
			'ship_addr'          => trim($address_delivery->address1 . ' ' . $address_delivery->address2),
			'ship_city'          => $address_delivery->city,
			'ship_state'         => (Tools::getIsset($delivery_state->iso_code)) ? $delivery_state->iso_code : '',
			'ship_zip_code'      => $address_delivery->postcode,
			'ship_country'       => Country::getIsoById((int) $address_delivery->id_country),
			'amount'             => $params['order']->total_paid,
			'quantity'           => $quantity,
			'currency'           => $default_currency->iso_code,
			'user_order_id'      => $params['order']->id,
			'device_fingerprint' => (isset($_COOKIE['flp_device'])) ? $_COOKIE['flp_device'] : '',
			'flp_checksum'       => Context::getContext()->cookie->flp_checksum,
			'format'             => 'json',
			'source'             => 'prestashop',
			'source_version'     => $this->version,
		]), false, stream_context_create([
			'http' => ['timeout' => 10],
		]));

		if (($json = Tools::jsonDecode($response)) !== null) {
			$data = [
				$params['order']->id, $json->is_country_match, $json->is_high_risk_country, $json->distance_in_km, $json->distance_in_mile, $ip, $json->ip_country, $json->ip_continent, $json->ip_region, $json->ip_city, $json->ip_latitude, $json->ip_longitude, $json->ip_timezone, $json->ip_elevation, $json->ip_domain, $json->ip_mobile_mnc, $json->ip_mobile_mcc, $json->ip_mobile_brand, $json->ip_netspeed, $json->ip_isp_name, $json->ip_usage_type, $json->is_free_email, $json->is_new_domain_name, $json->is_proxy_ip_address, $json->is_bin_found, $json->is_bin_country_match, $json->is_bin_name_match, $json->is_bin_phone_match, $json->is_bin_prepaid, $json->is_address_ship_forward, $json->is_bill_ship_city_match, $json->is_bill_ship_state_match, $json->is_bill_ship_country_match, $json->is_bill_ship_postal_match, $json->is_ip_blacklist, $json->is_email_blacklist, $json->is_credit_card_blacklist, $json->is_device_blacklist, $json->is_user_blacklist, $json->fraudlabspro_score, $json->fraudlabspro_distribution, $json->fraudlabspro_status, $json->fraudlabspro_rules, $json->fraudlabspro_id, $json->fraudlabspro_error_code, $json->fraudlabspro_message, $json->fraudlabspro_credits, Configuration::get('FLP_LICENSE_KEY'),
			];

			Db::getInstance()->execute('INSERT INTO `' . _DB_PREFIX_ . 'orders_fraudlabspro` (`id_order`, `is_country_match`, `is_high_risk_country`, `distance_in_km`, `distance_in_mile`, `ip_address`, `ip_country`, `ip_continent`, `ip_region`, `ip_city`, `ip_latitude`, `ip_longitude`, `ip_timezone`, `ip_elevation`, `ip_domain`, `ip_mobile_mnc`, `ip_mobile_mcc`, `ip_mobile_brand`, `ip_netspeed`, `ip_isp_name`, `ip_usage_type`, `is_free_email`, `is_new_domain_name`, `is_proxy_ip_address`, `is_bin_found`, `is_bin_country_match`, `is_bin_name_match`, `is_bin_phone_match`, `is_bin_prepaid`, `is_address_ship_forward`, `is_bill_ship_city_match`, `is_bill_ship_state_match`, `is_bill_ship_country_match`, `is_bill_ship_postal_match`, `is_ip_blacklist`, `is_email_blacklist`, `is_credit_card_blacklist`, `is_device_blacklist`, `is_user_blacklist`, `flp_score`, `flp_distribution`, `flp_status`, `flp_rules`, `flp_id`, `flp_error_code`, `flp_message`, `flp_credits`, `api_key`) VALUES (\'' . implode('\', \'', $data) . '\')');

			if (Configuration::get('FLP_APPROVE_STATUS_ID') && $json->fraudlabspro_status == 'APPROVE') {
				$history = new OrderHistory();
				$history->id_order = $params['order']->id;
				$history->changeIdOrderState((int) Configuration::get('FLP_APPROVE_STATUS_ID'), $params['order'], true);
				$history->add();
			}

			if (Configuration::get('FLP_REVIEW_STATUS_ID') && $json->fraudlabspro_status == 'REVIEW') {
				$history = new OrderHistory();
				$history->id_order = $params['order']->id;
				$history->changeIdOrderState((int) Configuration::get('FLP_REVIEW_STATUS_ID'), $params['order'], true);
				$history->add();
			}

			if (Configuration::get('FLP_REJECT_STATUS_ID') && $json->fraudlabspro_status == 'REJECT') {
				$history = new OrderHistory();
				$history->id_order = $params['order']->id;
				$history->changeIdOrderState((int) Configuration::get('FLP_REJECT_STATUS_ID'), $params['order'], true);
				$history->add();
			}

			return true;
		}

		return true;
	}

	public function hookFooter()
	{
		return $this->display(__FILE__, 'footer.tpl');
	}

	public function hookAdminOrder($params)
	{
		if (Tools::getValue('transactionId')) {
			if (Tools::getValue('approve')) {
				if ($this->feedback('APPROVE', Tools::getValue('transactionId'))) {
					Db::getInstance()->execute('UPDATE `' . _DB_PREFIX_ . 'orders_fraudlabspro` SET flp_status=\'APPROVE\' WHERE id_order=' . (int) $params['id_order'] . ' LIMIT 1');
				}
			} elseif (Tools::getValue('reject')) {
				if ($this->feedback('REJECT', Tools::getValue('transactionId'))) {
					Db::getInstance()->execute('UPDATE `' . _DB_PREFIX_ . 'orders_fraudlabspro` SET flp_status=\'REJECT\' WHERE id_order=' . (int) $params['id_order'] . ' LIMIT 1');
				}
			} elseif (Tools::getValue('blacklist')) {
				if ($this->feedback('REJECT_BLACKLIST', Tools::getValue('transactionId'), Tools::getValue('reason'))) {
					Db::getInstance()->execute('UPDATE `' . _DB_PREFIX_ . 'orders_fraudlabspro` SET flp_status=\'REJECT\', is_blacklisted=\'1\' WHERE id_order=' . (int) $params['id_order'] . ' LIMIT 1');
				}
			}
		}

		$row = Db::getInstance()->getRow('SELECT * FROM `' . _DB_PREFIX_ . 'orders_fraudlabspro` WHERE id_order = ' . (int) $params['id_order']);

		if ($row) {
			if (!isset($row['is_blacklisted'])) {
				Db::getInstance()->Execute('ALTER TABLE `' . _DB_PREFIX_ . 'orders_fraudlabspro` ADD COLUMN `is_blacklisted` CHAR(1) NOT NULL DEFAULT "0" AFTER `api_key`;');
				$row['is_blacklisted'] = 0;
			}

			if (!isset($row['flp_rules'])) {
				Db::getInstance()->Execute('ALTER TABLE `' . _DB_PREFIX_ . 'orders_fraudlabspro` ADD COLUMN `flp_rules` VARCHAR(255) NOT NULL DEFAULT "" AFTER `flp_status`;');
				$row['flp_rules'] = '';
			}

			if (!isset($row['is_phone_verified'])) {
				Db::getInstance()->Execute('ALTER TABLE `' . _DB_PREFIX_ . 'orders_fraudlabspro` ADD COLUMN `is_phone_verified` VARCHAR(100) NOT NULL DEFAULT "0" AFTER `api_key`;');
				$row['is_phone_verified'] = 0;
			}

			$location = [$row['ip_continent'], $row['ip_country'], $row['ip_region'], $row['ip_city']];
			$location = implode(', ', array_unique(array_diff($location, [''])));

			$triggeredRules = '';

			$response = Tools::file_get_contents('https://api.fraudlabspro.com/v1/plan?' . http_build_query([
				'key'    => Configuration::get('FLP_LICENSE_KEY'),
				'format' => 'json',
			]), false, stream_context_create([
				'http' => ['timeout' => 10],
			]));

			if (($json = Tools::jsonDecode($response)) !== null) {
				if (preg_match('/Micro/', $json->plan_name)) {
					$triggeredRules = '<br><div class="alert alert-info">Available for <a href="https://www.fraudlabspro.com/pricing" target="_blank">Mini plan</a> onward. Please <a href="https://www.fraudlabspro.com/merchant/login" target="_blank">upgrade</a>.</div>';
				} elseif (isset($row['flp_rules'])) {
					$triggeredRules = $row['flp_rules'];
				}
			}

			$this->smarty->assign([
				'no_result'                  => false,
				'fraud_score'                => $row['flp_score'],
				'fraud_status'               => '<span style="font-size:1.5em;font-weight:bold;color:#' . (($row['flp_status'] == 'APPROVE') ? '339933' : (($row['flp_status'] == 'REVIEW') ? 'ff7f27' : 'f00')) . '">' . (($row['flp_status'] == 'APPROVE') ? 'APPROVED' : (($row['flp_status'] == 'REJECT') ? 'REJECTED' : $row['flp_status'])) . '</span>',
				'remaining_credits'          => number_format($row['flp_credits'], 0, '', ','),
				'client_ip'                  => $row['ip_address'],
				'location'                   => $location,
				'coordinate'                 => ($row['ip_latitude'] != '-') ? ($row['ip_latitude'] . ', ' . $row['ip_longitude']) : 'N/A',
				'isp'                        => ($row['ip_isp_name'] != '-') ? $row['ip_isp_name'] : 'N/A',
				'domain'                     => ($row['ip_domain'] != '-') ? $row['ip_domain'] : 'N/A',
				'net_speed'                  => ($row['ip_netspeed'] != '-') ? $row['ip_netspeed'] : 'N/A',
				'is_proxy'                   => ($row['is_proxy_ip_address'] == 'Y') ? 'Yes' : (($row['is_proxy_ip_address'] == 'N') ? 'No' : 'N/A'),
				'usage_type'                 => ($row['ip_usage_type'] != '-') ? $row['ip_usage_type'] : 'N/A',
				'time_zone'                  => ($row['ip_timezone'] != '-') ? ('UTC ' . $row['ip_timezone']) : 'N/A',
				'distance'                   => (($row['distance_in_mile'] != '-') ? (number_format($row['distance_in_mile'], 2, '.', ',') . ' Miles') : '') . (($row['distance_in_km'] != '-') ? (' (' . number_format($row['distance_in_km'], 2, '.', ',') . ' KM)') : ''),
				'is_high_risk_country'       => ($row['is_high_risk_country'] == 'Y') ? 'Yes' : (($row['is_high_risk_country'] == 'N') ? 'No' : 'N/A'),
				'is_free_email'              => ($row['is_free_email'] == 'Y') ? 'Yes' : (($row['is_free_email'] == 'N') ? 'No' : 'N/A'),
				'is_ship_forward'            => ($row['is_address_ship_forward'] == 'Y') ? 'Yes' : (($row['is_address_ship_forward'] == 'N') ? 'No' : 'N/A'),
				'is_email_blacklist'         => ($row['is_email_blacklist'] == 'Y') ? 'Yes' : (($row['is_email_blacklist'] == 'N') ? 'No' : 'N/A'),
				'is_card_blacklist'          => ($row['is_credit_card_blacklist'] == 'Y') ? 'Yes' : (($row['is_credit_card_blacklist'] == 'N') ? 'No' : 'N/A'),
				'is_bin_found'               => ($row['is_bin_found'] == 'Y') ? 'Yes' : (($row['is_bin_found'] == 'N') ? 'No' : 'N/A'),
				'is_ip_blacklist'            => ($row['is_ip_blacklist'] == 'Y') ? 'Yes' : (($row['is_ip_blacklist'] == 'N') ? 'No' : 'N/A'),
				'is_device_blacklist'        => ($row['is_device_blacklist'] == 'Y') ? 'Yes' : (($row['is_device_blacklist'] == 'N') ? 'No' : 'N/A'),
				'triggered_rules'            => $triggeredRules,
				'is_phone_verified'          => ($row['is_phone_verified']) ? $row['is_phone_verified'] : 'No',
				'transaction_id'             => $row['flp_id'],
				'error_message'              => ($row['flp_message']) ? $row['flp_message'] : '(None)',
				'show_approve_reject_button' => ($row['flp_status'] == 'REVIEW') ? true : false,
				'show_blacklist_button'      => ($row['is_blacklisted']) ? false : true,
			]);
		} else {
			$this->smarty->assign([
				'no_result' => true,
			]);
		}

		return $this->display(__FILE__, 'admin_order.tpl');
	}

	public function renderForm()
	{
		$fields_form = [
			'form' => [
				'legend' => [
					'title' => $this->l('Settings'),
					'icon'  => 'icon-cog',
				],
				'input' => [
					[
						'type'   => 'checkbox',
						'name'   => 'FLP_ENABLED',
						'values' => [
							'query' => [
								[
									'id'   => 'on',
									'name' => $this->l('Enable'),
									'val'  => '1',
								],
							],
							'id'   => 'id',
							'name' => 'name',
						],
					],
					[
						'type'     => 'text',
						'label'    => $this->l('API Key'),
						'name'     => 'FLP_LICENSE_KEY',
						'desc'     => $this->l('Enter your FraudLabs Pro API key. You can register a free license key at http://www.fraudlabspro.com/sign-up if you do not have one.'),
						'required' => true,
					],
					[
						'type'     => 'select',
						'label'    => $this->l('Approve Status'),
						'name'     => 'FLP_APPROVE_STATUS_ID',
						'required' => false,
						'options'  => [
							'query' => array_merge(['id_order_state' => 0], OrderState::getOrderStates((int) $this->context->language->id)),
							'id'    => 'id_order_state',
							'name'  => 'name',
						],
						'desc' => $this->l('Change order to this state if marked as Approve by FraudLabs Pro.'),
					],
					[
						'type'     => 'select',
						'label'    => $this->l('Review Status'),
						'name'     => 'FLP_REVIEW_STATUS_ID',
						'required' => false,
						'options'  => [
							'query' => array_merge(['id_order_state' => 0], OrderState::getOrderStates((int) $this->context->language->id)),
							'id'    => 'id_order_state',
							'name'  => 'name',
						],
						'desc' => $this->l('Change order to this state if marked as Review by FraudLabs Pro.'),
					],
					[
						'type'     => 'select',
						'label'    => $this->l('Reject Status'),
						'name'     => 'FLP_REJECT_STATUS_ID',
						'required' => false,
						'options'  => [
							'query' => array_merge(['id_order_state' => 0], OrderState::getOrderStates((int) $this->context->language->id)),
							'id'    => 'id_order_state',
							'name'  => 'name',
						],
						'desc' => $this->l('Change order to this state if marked as Reject by FraudLabs Pro.'),
					],
					[
						'type'   => 'checkbox',
						'name'   => 'FLP_GET_FORWARDED_IP',
						'values' => [
							'query' => [
								[
									'id'   => 'on',
									'name' => $this->l('Get X-Forwarded-For IP address.'),
									'val'  => '1',
								],
							],
							'id'   => 'id',
							'name' => 'name',
						],
						'desc' => $this->l('Enable this option to get the original IP address behind the anonymous proxy.'),
					],
					[
						'type'   => 'checkbox',
						'name'   => 'FLP_PURGE',
						'values' => [
							'query' => [
								[
									'id'   => 'on',
									'name' => $this->l('Enable this option ONLY if you want to erase all FraudLabs Pro data. Caution: The data will be permanently erased upon clicking the SAVE button.'),
									'val'  => '1',
								],
							],
							'id'   => 'id',
							'name' => 'name',
						],
					],
				],
				'submit' => [
					'title' => $this->l('Save'),
				],
			],
		];

		$helper = new HelperForm();
		$helper->show_toolbar = false;
		$helper->table = $this->table;
		$lang = new Language((int) Configuration::get('PS_LANG_DEFAULT'));
		$helper->default_form_language = $lang->id;
		$helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ?: 0;
		$this->fields_form = [];
		$helper->id = (int) Tools::getValue('id_carrier');
		$helper->identifier = $this->identifier;
		$helper->submit_action = 'btnSubmit';
		$helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false) . '&configure=' . $this->name . '&tab_module=' . $this->tab . '&module_name=' . $this->name;
		$helper->token = Tools::getAdminTokenLite('AdminModules');
		$helper->tpl_vars = [
			'fields_value' => $this->getConfigFieldsValues(),
			'languages'    => $this->context->controller->getLanguages(),
			'id_language'  => $this->context->language->id,
		];

		return $helper->generateForm([$fields_form]);
	}

	public function getConfigFieldsValues()
	{
		return [
			'FLP_ENABLED_on'          => Tools::getValue('FLP_ENABLED_on', Configuration::get('FLP_ENABLED')),
			'FLP_LICENSE_KEY'         => Tools::getValue('FLP_LICENSE_KEY', Configuration::get('FLP_LICENSE_KEY')),
			'FLP_APPROVE_STATUS_ID'   => Tools::getValue('FLP_APPROVE_STATUS_ID', Configuration::get('FLP_APPROVE_STATUS_ID')),
			'FLP_REVIEW_STATUS_ID'    => Tools::getValue('FLP_REVIEW_STATUS_ID', Configuration::get('FLP_REVIEW_STATUS_ID')),
			'FLP_REJECT_STATUS_ID'    => Tools::getValue('FLP_REJECT_STATUS_ID', Configuration::get('FLP_REJECT_STATUS_ID')),
			'FLP_GET_FORWARDED_IP_on' => Tools::getValue('FLP_GET_FORWARDED_IP_on', Configuration::get('FLP_GET_FORWARDED_IP')),
			'FLP_PURGE_on'            => Tools::getValue('FLP_PURGE_on', Configuration::get('FLP_PURGE')),
		];
	}

	protected function _postProcess()
	{
		if (Tools::isSubmit('btnSubmit')) {
			Configuration::updateValue('FLP_ENABLED', Tools::getValue('FLP_ENABLED_on'));
			Configuration::updateValue('FLP_LICENSE_KEY', Tools::getValue('FLP_LICENSE_KEY'));
			Configuration::updateValue('FLP_APPROVE_STATUS_ID', Tools::getValue('FLP_APPROVE_STATUS_ID'));
			Configuration::updateValue('FLP_REVIEW_STATUS_ID', Tools::getValue('FLP_REVIEW_STATUS_ID'));
			Configuration::updateValue('FLP_REJECT_STATUS_ID', Tools::getValue('FLP_REJECT_STATUS_ID'));
			Configuration::updateValue('FLP_GET_FORWARDED_IP', Tools::getValue('FLP_GET_FORWARDED_IP_on'));

			if (Tools::getValue('FLP_PURGE_on') == '1') {
				Db::getInstance()->Execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'orders_fraudlabspro`');
				$this->_html .= $this->displayConfirmation($this->l('FraudLabs Pro records cleared.'));
			}
		}

		$this->_html .= $this->displayConfirmation($this->l('Settings updated'));
	}

	protected function _postValidation()
	{
		if (Tools::isSubmit('btnSubmit')) {
			if (!Tools::getValue('FLP_LICENSE_KEY')) {
				$this->_postErrors[] = $this->l('FraudLabs Pro API key is required.');
			}
		}
	}

	private function feedback($action, $id, $note = '')
	{
		$stream_context = stream_context_create(['http' => ['timeout' => 10]]);

		Tools::file_get_contents('https://api.fraudlabspro.com/v1/order/feedback?' . http_build_query([
			'key'    => Configuration::get('FLP_LICENSE_KEY'),
			'action' => $action,
			'id'     => $id,
			'note'   => $note,
			'format' => 'json',
		]), false, $stream_context);

		return true;
	}

	private function getIP()
	{
		// For development usage
		if (isset($_SERVER['DEV_MODE'])) {
			do {
				$ip = mt_rand(0, 255) . '.' . mt_rand(0, 255) . '.' . mt_rand(0, 255) . '.' . mt_rand(0, 255);
			} while (!filter_var($ip, \FILTER_VALIDATE_IP, \FILTER_FLAG_NO_PRIV_RANGE | \FILTER_FLAG_NO_RES_RANGE));

			return $ip;
		}

		if (Configuration::get('FLP_GET_FORWARDED_IP')) {
			$headers = [
				'HTTP_CF_CONNECTING_IP', 'X-Real-IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_INCAP_CLIENT_IP', 'HTTP_X_SUCURI_CLIENTIP',
			];

			foreach ($headers as $header) {
				if (!isset($_SERVER[$header])) {
					continue;
				}

				if (!filter_var($_SERVER[$header], \FILTER_VALIDATE_IP, \FILTER_FLAG_NO_PRIV_RANGE | \FILTER_FLAG_NO_RES_RANGE)) {
					continue;
				}

				return $_SERVER[$header];
			}
		}

		return $_SERVER['REMOTE_ADDR'];
	}

	private function hastIt($s, $prefix = 'fraudlabspro_')
	{
		$hash = $prefix . $s;
		for ($i = 0; $i < 65536; ++$i) {
			$hash = sha1($prefix . $hash);
		}

		return $hash;
	}
}

<?php

/*
* 2013-2023 FraudLabs Pro
*
* Permission is hereby granted, free of charge, to any person obtaining a copy of this software
* and associated documentation files (the "Software"), to deal in the Software without restriction,
* including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense,
* and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so,
* subject to the following conditions:
*
* The above copyright notice and this permission notice shall be included in all copies or substantial
* portions of the Software.
*
* THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT
* LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT.
* IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY,
* WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE
* SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
*
*  @author FraudLabs Pro <support@fraudlabspro.com>
*  @copyright  2013-2023 FraudLabs Pro
*  @license https://opensource.org/licenses/MIT
*/

if (!defined('_PS_VERSION_')) {
	exit;
}

require 'vendor/autoload.php';

class Fraudlabspro extends Module
{
	protected $_postErrors = [];

	/**
	 * @var ServiceContainer
	 */
	private $container;

	public function __construct()
	{
		$this->name = 'fraudlabspro';
		$this->tab = 'payment_security';
		$this->version = '2.0.2';
		$this->author = 'FraudLabs Pro';
		$this->emailSupport = 'support@fraudlabspro.com';
		$this->module_key = 'cdb22a61c7ec8d1f900f6c162ad96caa';
		$this->need_instance = 0;

		$this->ps_versions_compliancy = [
			'min' => '1.7.8.0',
			'max' => '8.99.99',
		];
		$this->bootstrap = true;

		parent::__construct();

		$this->displayName = $this->l('FraudLabs Pro Fraud Prevention');
		$this->description = $this->l('FraudLabs Pro screens transaction for online frauds to protect your store from fraud attempts.');

		$this->confirmUninstall = $this->l('Are you sure to uninstall this module?');

		$this->uri_path = Tools::substr($this->context->link->getBaseLink(null, null, true), 0, -1);
		$this->images_dir = $this->uri_path . $this->getPathUri() . 'views/img/';
		$this->template_dir = $this->getLocalPath() . 'views/templates/admin/';

		if ($this->container === null) {
			$this->container = new \PrestaShop\ModuleLibServiceContainer\DependencyInjection\ServiceContainer(
				$this->name,
				$this->getLocalPath()
			);
		}
	}

	/**
	 * Retrieve service.
	 *
	 * @param string $serviceName
	 *
	 * @return mixed
	 */
	public function getService($serviceName)
	{
		return $this->container->getService($serviceName);
	}

	public function install()
	{
		if (!parent::install() || !$this->registerHook('newOrder') || !$this->registerHook('adminOrder') || !$this->registerHook('cart') || !$this->registerHook('footer') || !$this->getService('ps_accounts.installer')->install()) {
			return false;
		}

		Configuration::updateValue('FLP_ENABLED', '1');
		Configuration::updateValue('FLP_API_KEY', '');

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

	public function uninstall()
	{
		if (!parent::uninstall()) {
			return false;
		}

		return true;
	}

	public function getContent()
	{
		// Allow to auto-install Account
		$accountsInstaller = $this->getService('ps_accounts.installer');
		$accountsInstaller->install();

		try {
			// Account
			$accountsFacade = $this->getService('ps_accounts.facade');
			$accountsService = $accountsFacade->getPsAccountsService();
			Media::addJsDef([
				'contextPsAccounts' => $accountsFacade->getPsAccountsPresenter()
					->present($this->name),
			]);

			// Retrieve Account CDN
			$this->context->smarty->assign('urlAccountsVueCdn', $accountsService->getAccountsVueCdn());

			$billingFacade = $this->getService('ps_billings.facade');
			$partnerLogo = $this->getLocalPath() . 'views/img/logo.png';

			// Billing
			Media::addJsDef($billingFacade->present([
				'logo'         => $partnerLogo,
				'tosLink'      => 'https://www.fraudlabspro.com/terms-of-service',
				'privacyLink'  => 'https://www.fraudlabspro.com/privacy-policy',
				'emailSupport' => $this->emailSupport,
			]));

			$this->context->smarty->assign('pathVendor', $this->getPathUri() . 'views/js/chunk-vendors-fraudlabspro.' . $this->version . '.js');
			$this->context->smarty->assign('pathApp', $this->getPathUri() . 'views/js/app-fraudlabspro.' . $this->version . '.js');
		} catch (Exception $e) {
			$this->context->controller->errors[] = $e->getMessage();

			return '';
		}

		$apiKey = Tools::getValue('flp_api_key');

		if (Tools::isSubmit('erase')) {
			Db::getInstance()->Execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'orders_fraudlabspro`');
			$this->context->smarty->assign('message_success', 'FraudLabs Pro records has been cleared.');
		}

		if (Tools::isSubmit('save')) {
			if (!preg_match('/^[A-Z0-9]{32}$/', Tools::getValue('api_key'))) {
				$this->context->controller->errors[] = $this->trans(
					'Invalid API key.',
					[],
					'Modules.FraudLabsPro.Admin'
				);
			}

			if (!count($this->context->controller->errors)) {
				Configuration::updateValue('FLP_IS_ENABLED', Tools::getValue('module_is_enabled'));
				Configuration::updateValue('FLP_API_KEY', Tools::getValue('api_key'));
				Configuration::updateValue('FLP_APPROVE_STAGE_ID', Tools::getValue('approve_stage_id'));
				Configuration::updateValue('FLP_REVIEW_STAGE_ID', Tools::getValue('review_stage_id'));
				Configuration::updateValue('FLP_REJECT_STAGE_ID', Tools::getValue('reject_stage_id'));

				$this->context->smarty->assign('message_success', 'The settings has been updated.');
			}
		}

		$this->context->smarty->assign([
			'current_url'                   => $this->context->link->getAdminLink('AdminModules') . '&configure=fraudlabspro&tab_module=front_office_features&module_name=fraudlabspro',
			'module_is_enabled'             => Configuration::get('FLP_IS_ENABLED'),
			'api_key'                       => Configuration::get('FLP_API_KEY'),
			'approve_stage_id'              => Configuration::get('FLP_APPROVE_STAGE_ID'),
			'review_stage_id'               => Configuration::get('FLP_REVIEW_STAGE_ID'),
			'reject_stage_id'               => Configuration::get('FLP_REJECT_STAGE_ID'),
			'order_stages'                  => OrderState::getOrderStates((int) $this->context->language->id),
		]);

		return $this->context->smarty->fetch($this->template_dir . 'fraudlabspro.tpl');
	}

	public function hookCart($params)
	{
		if (!Validate::isLoadedObject($params['cart'])) {
			return;
		}

		Db::getInstance()->Execute('INSERT IGNORE INTO `' . _DB_PREFIX_ . 'flp_order_ip` VALUES(' . (int) $params['cart']->id . ', "' . pSQL($this->getClientIp()) . '")');
	}

	public function hookNewOrder($params)
	{
		if (!Configuration::get('PS_SHOP_ENABLE') || !Configuration::get('FLP_API_KEY') || !Configuration::get('FLP_ENABLED')) {
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

		$items = [];

		foreach ($product_list as $product) {
			$quantity += $product['product_quantity'];

			$items[] = $product['reference'] . ':' . $product['product_quantity'] . ':' . (($product['is_virtual'] == '1') ? 'virtual' : (($product['download_hash']) ? 'downloadable' : 'physical'));
		}

		$ip = Db::getInstance()->getValue('SELECT `ip` FROM  `' . _DB_PREFIX_ . 'flp_order_ip` WHERE `id_cart` = "' . ((int) $params['cart']->id) . '"');
		$ip = (!$ip) ? $this->getClientIp() : $ip;

		$bill_state = '';

		if ($address_invoice->id_state !== null || $address_invoice->id_state != '') {
			$State = new State((int) $address_invoice->id_state);
			$bill_state = $State->iso_code;
		}

		$response = Tools::file_get_contents('https://api.fraudlabspro.com/v1/order/screen?' . http_build_query([
			'key'                => Configuration::get('FLP_API_KEY'),
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
			'payment_gateway'    => $params['order']->payment,
			'items'              => implode(',', $items),
			'flp_checksum'       => Context::getContext()->cookie->flp_checksum,
			'format'             => 'json',
			'source'             => 'prestashop',
			'source_version'     => $this->version,
		]), false, stream_context_create([
			'http' => ['timeout' => 10],
		]));

		if (($json = Tools::jsonDecode($response)) !== null) {
			$data = [
				$params['order']->id, $json->is_country_match, $json->is_high_risk_country, $json->distance_in_km, $json->distance_in_mile, $ip, $json->ip_country, $json->ip_continent, $json->ip_region, $json->ip_city, $json->ip_latitude, $json->ip_longitude, $json->ip_timezone, $json->ip_elevation, $json->ip_domain, $json->ip_mobile_mnc, $json->ip_mobile_mcc, $json->ip_mobile_brand, $json->ip_netspeed, $json->ip_isp_name, $json->ip_usage_type, $json->is_free_email, $json->is_new_domain_name, $json->is_proxy_ip_address, $json->is_bin_found, $json->is_bin_country_match, $json->is_bin_name_match, $json->is_bin_phone_match, $json->is_bin_prepaid, $json->is_address_ship_forward, $json->is_bill_ship_city_match, $json->is_bill_ship_state_match, $json->is_bill_ship_country_match, $json->is_bill_ship_postal_match, $json->is_ip_blacklist, $json->is_email_blacklist, $json->is_credit_card_blacklist, $json->is_device_blacklist, $json->is_user_blacklist, $json->fraudlabspro_score, $json->fraudlabspro_distribution, $json->fraudlabspro_status, $json->fraudlabspro_rules, $json->fraudlabspro_id, $json->fraudlabspro_error_code, $json->fraudlabspro_message, $json->fraudlabspro_credits, Configuration::get('FLP_API_KEY'),
			];

			Db::getInstance()->execute('INSERT INTO `' . _DB_PREFIX_ . 'orders_fraudlabspro` (`id_order`, `is_country_match`, `is_high_risk_country`, `distance_in_km`, `distance_in_mile`, `ip_address`, `ip_country`, `ip_continent`, `ip_region`, `ip_city`, `ip_latitude`, `ip_longitude`, `ip_timezone`, `ip_elevation`, `ip_domain`, `ip_mobile_mnc`, `ip_mobile_mcc`, `ip_mobile_brand`, `ip_netspeed`, `ip_isp_name`, `ip_usage_type`, `is_free_email`, `is_new_domain_name`, `is_proxy_ip_address`, `is_bin_found`, `is_bin_country_match`, `is_bin_name_match`, `is_bin_phone_match`, `is_bin_prepaid`, `is_address_ship_forward`, `is_bill_ship_city_match`, `is_bill_ship_state_match`, `is_bill_ship_country_match`, `is_bill_ship_postal_match`, `is_ip_blacklist`, `is_email_blacklist`, `is_credit_card_blacklist`, `is_device_blacklist`, `is_user_blacklist`, `flp_score`, `flp_distribution`, `flp_status`, `flp_rules`, `flp_id`, `flp_error_code`, `flp_message`, `flp_credits`, `api_key`) VALUES (\'' . implode('\', \'', array_map('pSQL', $data)) . '\')');

			if (Configuration::get('FLP_APPROVE_STAGE_ID') && $json->fraudlabspro_status == 'APPROVE') {
				$history = new OrderHistory();
				$history->id_order = $params['order']->id;
				$history->changeIdOrderState((int) Configuration::get('FLP_APPROVE_STAGE_ID'), $params['order'], true);
				$history->add();
			}

			if (Configuration::get('FLP_REVIEW_STAGE_ID') && $json->fraudlabspro_status == 'REVIEW') {
				$history = new OrderHistory();
				$history->id_order = $params['order']->id;
				$history->changeIdOrderState((int) Configuration::get('FLP_REVIEW_STAGE_ID'), $params['order'], true);
				$history->add();
			}

			if (Configuration::get('FLP_REJECT_STAGE_ID') && $json->fraudlabspro_status == 'REJECT') {
				$history = new OrderHistory();
				$history->id_order = $params['order']->id;
				$history->changeIdOrderState((int) Configuration::get('FLP_REJECT_STAGE_ID'), $params['order'], true);
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
				'key'    => Configuration::get('FLP_API_KEY'),
				'format' => 'json',
			]), false, stream_context_create([
				'http' => ['timeout' => 10],
			]));

			$showUpgrade = false;

			if (($json = Tools::jsonDecode($response)) !== null) {
				if (preg_match('/Micro/', $json->plan_name)) {
					$showUpgrade = true;
				} elseif (isset($row['flp_rules'])) {
					$triggeredRules = $row['flp_rules'];
				}
			}

			$this->smarty->assign([
				'no_result'                  => false,
				'fraud_score'                => $row['flp_score'],
				'color'                      => ($row['flp_status'] == 'APPROVE') ? '339933' : (($row['flp_status'] == 'REVIEW') ? 'ff7f27' : 'f00'),
				'fraud_status'               => ($row['flp_status'] == 'APPROVE') ? 'APPROVED' : (($row['flp_status'] == 'REJECT') ? 'REJECTED' : $row['flp_status']),
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
				'show_upgrade'               => $showUpgrade,
				'triggered_rules'            => ($triggeredRules) ? $triggeredRules : '-',
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

	private function feedback($action, $id, $note = '')
	{
		$stream_context = stream_context_create(['http' => ['timeout' => 10]]);

		Tools::file_get_contents('https://api.fraudlabspro.com/v1/order/feedback?' . http_build_query([
			'key'    => Configuration::get('FLP_API_KEY'),
			'action' => $action,
			'id'     => $id,
			'note'   => $note,
			'format' => 'json',
		]), false, $stream_context);

		return true;
	}

	private function getClientIp()
	{
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

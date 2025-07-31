<?php

/*
* 2013-2025 FraudLabs Pro
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
* @author FraudLabs Pro <support@fraudlabspro.com>
* @copyright  2013-2025 FraudLabs Pro
* @license https://opensource.org/licenses/MIT
*/

use PrestaShop\PrestaShop\Core\Addon\Module\ModuleManagerBuilder;

if (!defined('_PS_VERSION_')) {
	exit;
}

require 'vendor/autoload.php';

class FraudLabsPro extends Module
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
		$this->version = '2.4.0';
		$this->author = 'FraudLabs Pro';
		$this->emailSupport = 'support@fraudlabspro.com';
		$this->module_key = 'cdb22a61c7ec8d1f900f6c162ad96caa';
		$this->need_instance = 0;
		$this->ps_versions_compliancy = ['min' => '1.7.0', 'max' => _PS_VERSION_];
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

	public function install()
	{
		// Test if MBO is installed
		// For more information, check the readme of mbo-lib-installer
		$mboStatus = (new Prestashop\ModuleLibMboInstaller\Presenter())->present();

		if (!$mboStatus['isInstalled']) {
			try {
				$mboInstaller = new Prestashop\ModuleLibMboInstaller\Installer(_PS_VERSION_);
				/** @var bool */
				$result = $mboInstaller->installModule();

				// Call the installation of PrestaShop Integration Framework components
				$this->installDependencies();

				Configuration::updateValue('FLP_ENABLED', '1');
				Configuration::updateValue('FLP_API_KEY', '');
				Configuration::updateValue('FLP_REAL_IP_HEADER', '');

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
					`is_blacklisted` CHAR(1) NOT NULL DEFAULT "0" COLLATE \'utf8_bin\',
					`is_phone_verified` VARCHAR(100) NOT NULL DEFAULT "0" COLLATE \'utf8_bin\',
					INDEX `id_order` (`id_order`)
				) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;');

				Db::getInstance()->Execute('CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'flp_order_ip` (
					`id_cart` INT NOT NULL,
					`ip` VARCHAR(39) NOT NULL,
					PRIMARY KEY (`id_cart`)
				) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;');

				$this->registerHook('newOrder');
				$this->registerHook('adminOrder');
				$this->registerHook('cart');
				$this->registerHook('footer');
			} catch (\Exception $e) {
				// Some errors can happen, i.e during initialization or download of the module
				$this->context->controller->errors[] = $e->getMessage();

				return 'Error during MBO installation';
			}
		} else {
			$this->installDependencies();
		}

		return parent::install();
	}

	/**
	 * Install PrestaShop Integration Framework Components.
	 */
	public function installDependencies()
	{
		$moduleManager = ModuleManagerBuilder::getInstance()->build();

		/* PS Account */
		if (!$moduleManager->isInstalled('ps_accounts')) {
			$moduleManager->install('ps_accounts');
		} elseif (!$moduleManager->isEnabled('ps_accounts')) {
			$moduleManager->enable('ps_accounts');
			$moduleManager->upgrade('ps_accounts');
		} else {
			$moduleManager->upgrade('ps_accounts');
		}

		/* Cloud Sync - PS Eventbus */
		if (!$moduleManager->isInstalled('ps_eventbus')) {
			$moduleManager->install('ps_eventbus');
		} elseif (!$moduleManager->isEnabled('ps_eventbus')) {
			$moduleManager->enable('ps_eventbus');
			$moduleManager->upgrade('ps_eventbus');
		} else {
			$moduleManager->upgrade('ps_eventbus');
		}
	}

	public function uninstall()
	{
		return parent::uninstall();
	}

	/**
	 * Retrieve service.
	 *
	 * @param string $serviceName
	 */
	public function getService($serviceName)
	{
		return $this->container->getService($serviceName);
	}

	public function getContent()
	{
		$this->context->smarty->assign('module_dir', $this->_path);
		$moduleManager = ModuleManagerBuilder::getInstance()->build();

		$accountsService = null;

		try {
			$accountsFacade = $this->getService('fraudlabspro.ps_accounts_facade');
			$accountsService = $accountsFacade->getPsAccountsService();
		} catch (\PrestaShop\PsAccountsInstaller\Installer\Exception\InstallerException $e) {
			$accountsInstaller = $this->getService('fraudlabspro.ps_accounts_installer');
			$accountsInstaller->install();
			$accountsFacade = $this->getService('fraudlabspro.ps_accounts_facade');
			$accountsService = $accountsFacade->getPsAccountsService();
		}

		try {
			Media::addJsDef([
				'contextPsAccounts' => $accountsFacade->getPsAccountsPresenter()
					->present($this->name),
			]);

			// Retrieve Account CDN
			$this->context->smarty->assign('urlAccountsCdn', $accountsService->getAccountsCdn());
		} catch (Exception $e) {
			$this->context->controller->errors[] = $e->getMessage();

			return '';
		}

		if ($moduleManager->isInstalled('ps_eventbus')) {
			$eventbusModule = \Module::getInstanceByName('ps_eventbus');
			if (version_compare($eventbusModule->version, '1.9.0', '>=')) {
				$eventbusPresenterService = $eventbusModule->getService('PrestaShop\Module\PsEventbus\Service\PresenterService');

				$this->context->smarty->assign('urlCloudsync', 'https://assets.prestashop3.com/ext/cloudsync-merchant-sync-consent/latest/cloudsync-cdc.js');

				Media::addJsDef([
					'contextPsEventbus' => $eventbusPresenterService->expose($this, ['info', 'modules', 'themes']),
				]);
			}
		}

		/**********************
		 * PrestaShop Billing *
		 * *******************/

		// Load context for PsBilling
		$billingFacade = $this->getService('fraudlabspro.ps_billings_facade');
		// $partnerLogo = $this->getLocalPath() . 'logo.png';
		$partnerLogo = $this->getLocalPath() . 'views/img/logo.png';

		// Billing
		Media::addJsDef($billingFacade->present([
			'logo'         => $partnerLogo,
			'tosUrl'       => 'https://www.fraudlabspro.com/terms-of-service',
			'privacyUrl'   => 'https://www.fraudlabspro.com/privacy-policy',
			'emailSupport' => $this->emailSupport,
		]));

		$this->context->smarty->assign('urlBilling', 'https://unpkg.com/@prestashopcorp/billing-cdc/dist/bundle.js');

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
				Configuration::updateValue('FLP_REAL_IP_HEADER', Tools::getValue('real_ip_header'));
				Configuration::updateValue('FLP_APPROVE_STAGE_ID', Tools::getValue('approve_stage_id'));
				Configuration::updateValue('FLP_REVIEW_STAGE_ID', Tools::getValue('review_stage_id'));
				Configuration::updateValue('FLP_REJECT_STAGE_ID', Tools::getValue('reject_stage_id'));

				$this->context->smarty->assign('message_success', 'The settings has been updated.');
			}
		}

		$this->context->smarty->assign([
			'current_url'       => $this->context->link->getAdminLink('AdminModules') . '&configure=fraudlabspro&tab_module=front_office_features&module_name=fraudlabspro',
			'module_is_enabled' => Configuration::get('FLP_IS_ENABLED'),
			'api_key'           => Configuration::get('FLP_API_KEY'),
			'real_ip_header'    => Configuration::get('FLP_REAL_IP_HEADER'),
			'approve_stage_id'  => Configuration::get('FLP_APPROVE_STAGE_ID'),
			'review_stage_id'   => Configuration::get('FLP_REVIEW_STAGE_ID'),
			'reject_stage_id'   => Configuration::get('FLP_REJECT_STAGE_ID'),
			'order_stages'      => OrderState::getOrderStates((int) $this->context->language->id),
			'headers'           => ['REMOTE_ADDR', 'HTTP_CF_CONNECTING_IP', 'HTTP_CLIENT_IP', 'HTTP_FORWARDED', 'HTTP_INCAP_CLIENT_IP', 'HTTP_X_FORWARDED', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_REAL_IP', 'HTTP_X_SUCURI_CLIENTIP'],
		]);

		return $this->context->smarty->fetch($this->template_dir . 'configure.tpl');
	}

	public function hookCart($params)
	{
		if (!Validate::isLoadedObject($params['cart'])) {
			return;
		}

		// This now uses the improved getClientIp() method for consistent IP detection.
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

		$ip_from_cart = Db::getInstance()->getValue('SELECT `ip` FROM  `' . _DB_PREFIX_ . 'flp_order_ip` WHERE `id_cart` = "' . ((int) $params['cart']->id) . '"');
		$ip = ($ip_from_cart) ? $ip_from_cart : $this->getClientIp();

		$bill_state = '';

		if ($address_invoice->id_state !== null || $address_invoice->id_state != '') {
			$State = new State((int) $address_invoice->id_state);
			$bill_state = $State->iso_code;
		}

		$response = Tools::file_get_contents('https://api.fraudlabspro.com/v2/order/screen', false, stream_context_create([
			'http' => [
				'timeout' => 10,
				'method'  => 'POST',
				'header'  => 'Content-Type: application/x-www-form-urlencoded',
				'content' => http_build_query([
					'key'             => Configuration::get('FLP_API_KEY'),
					'ip'              => $ip,
					'first_name'      => $address_invoice->firstname,
					'last_name'       => $address_invoice->lastname,
					'bill_city'       => $address_invoice->city,
					'bill_state'      => $bill_state,
					'bill_country'    => Country::getIsoById((int) $address_invoice->id_country),
					'bill_zip_code'   => $address_invoice->postcode,
					'email_domain'    => Tools::substr($customer->email, strpos($customer->email, '@') + 1),
					'email_hash'      => $this->hastIt($customer->email),
					'email'           => $customer->email,
					'user_phone'      => $address_invoice->phone,
					'ship_addr'       => trim($address_delivery->address1 . ' ' . $address_delivery->address2),
					'ship_city'       => $address_delivery->city,
					'ship_state'      => (Tools::getIsset($delivery_state->iso_code)) ? $delivery_state->iso_code : '',
					'ship_zip_code'   => $address_delivery->postcode,
					'ship_country'    => Country::getIsoById((int) $address_delivery->id_country),
					'amount'          => $params['order']->total_paid,
					'quantity'        => $quantity,
					'currency'        => $default_currency->iso_code,
					'user_order_id'   => $params['order']->id,
					'payment_gateway' => $params['order']->payment,
					'items'           => implode(',', $items),
					'flp_checksum'    => Context::getContext()->cookie->flp_checksum,
					'format'          => 'json',
					'source'          => 'prestashop',
					'source_version'  => $this->version,
				]),
			],
		]));

		if (($json = Tools::jsonDecode($response)) !== null) {
			$data = [
				'id_order'                   => (int) $params['order']->id,
				'is_country_match'           => ($json->billing_address->is_ip_country_match) ? 'Y' : 'N',
				'is_high_risk_country'       => ($json->ip_geolocation->is_high_risk_country) ? 'Y' : 'N',
				'distance_in_km'             => pSQL($json->billing_address->ip_distance_in_km),
				'distance_in_mile'           => pSQL($json->billing_address->ip_distance_in_mile),
				'ip_address'                 => pSQL($ip),
				'ip_country'                 => pSQL($json->ip_geolocation->country_code),
				'ip_continent'               => pSQL($json->ip_geolocation->continent),
				'ip_region'                  => pSQL($json->ip_geolocation->region),
				'ip_city'                    => pSQL($json->ip_geolocation->city),
				'ip_latitude'                => pSQL($json->ip_geolocation->latitude),
				'ip_longitude'               => pSQL($json->ip_geolocation->longitude),
				'ip_timezone'                => pSQL($json->ip_geolocation->timezone),
				'ip_elevation'               => pSQL($json->ip_geolocation->elevation),
				'ip_domain'                  => pSQL($json->ip_geolocation->domain),
				'ip_mobile_mnc'              => pSQL($json->ip_geolocation->mobile_mnc),
				'ip_mobile_mcc'              => pSQL($json->ip_geolocation->mobile_mcc),
				'ip_mobile_brand'            => pSQL($json->ip_geolocation->mobile_brand),
				'ip_netspeed'                => pSQL($json->ip_geolocation->netspeed),
				'ip_isp_name'                => pSQL($json->ip_geolocation->isp_name),
				'ip_usage_type'              => pSQL(implode(', ', $json->ip_geolocation->usage_type)),
				'is_free_email'              => ($json->email_address->is_free) ? 'Y' : 'N',
				'is_new_domain_name'         => ($json->email_address->is_new_domain_name) ? 'Y' : 'N',
				'is_proxy_ip_address'        => ($json->ip_geolocation->is_proxy) ? 'Y' : 'N',
				'is_bin_found'               => ($json->credit_card->is_bin_exist) ? 'Y' : 'N',
				'is_bin_country_match'       => ($json->credit_card->is_bin_country_match) ? 'Y' : 'N',
				'is_bin_name_match'          => ($json->credit_card->is_bin_name_match) ? 'Y' : 'N',
				'is_bin_phone_match'         => ($json->credit_card->is_bin_phone_match) ? 'Y' : 'N',
				'is_bin_prepaid'             => ($json->credit_card->is_prepaid) ? 'Y' : 'N',
				'is_address_ship_forward'    => ($json->shipping_address->is_address_ship_forward) ? 'Y' : 'N',
				'is_bill_ship_city_match'    => ($json->shipping_address->is_bill_city_match) ? 'Y' : 'N',
				'is_bill_ship_state_match'   => ($json->shipping_address->is_state_city_match) ? 'Y' : 'N',
				'is_bill_ship_country_match' => ($json->shipping_address->is_bill_country_match) ? 'Y' : 'N',
				'is_bill_ship_postal_match'  => ($json->shipping_address->is_bill_postcode_match) ? 'Y' : 'N',
				'is_ip_blacklist'            => ($json->ip_geolocation->is_in_blacklist) ? 'Y' : 'N',
				'is_email_blacklist'         => ($json->email_address->is_in_blacklist) ? 'Y' : 'N',
				'is_credit_card_blacklist'   => ($json->credit_card->is_in_blacklist) ? 'Y' : 'N',
				'is_device_blacklist'        => ($json->device->is_in_blacklist) ? 'Y' : 'N',
				'is_user_blacklist'          => ($json->username->is_in_blacklist) ? 'Y' : 'N',
				'flp_score'                  => pSQL($json->fraudlabspro_score),
				'flp_distribution'           => '0',
				'flp_status'                 => pSQL($json->fraudlabspro_status),
				'flp_rules'                  => pSQL(implode(', ', $json->fraudlabspro_rules)),
				'flp_id'                     => pSQL($json->fraudlabspro_id),
				'flp_error_code'             => pSQL($json->fraudlabspro->error->error_code ?? ''),
				'flp_message'                => pSQL($json->fraudlabspro->error->error_message ?? ''),
				'flp_credits'                => pSQL($json->fraudlabspro_credits),
				'api_key'                    => pSQL(Configuration::get('FLP_API_KEY')),
			];

			Db::getInstance()->insert('orders_fraudlabspro', $data);

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
			$row['is_blacklisted'] = $row['is_blacklisted'] ?? 0;
			$row['flp_rules'] = $row['flp_rules'] ?? '';
			$row['is_phone_verified'] = $row['is_phone_verified'] ?? 0;

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

			/*
			 * Note for developers:
			 * To prevent potential Stored XSS vulnerabilities, ensure all variables printed
			 * in the 'admin_order.tpl' template are properly escaped using Smarty's escape modifier.
			 * Example: {$client_ip|escape:'html':'UTF-8'}
			 */
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
		$id = preg_replace('/[^a-zA-Z0-9-]/', '', $id);
		$note = strip_tags((string)$note);

		Tools::file_get_contents('https://api.fraudlabspro.com/v2/order/feedback', false, stream_context_create([
			'http' => [
				'timeout' => 10,
				'method'  => 'POST',
				'header'  => 'Content-Type: application/x-www-form-urlencoded',
				'content' => http_build_query([
					'key'    => Configuration::get('FLP_API_KEY'),
					'action' => $action,
					'id'     => $id,
					'note'   => $note,
					'format' => 'json',
				]),
			],
		]));

		return true;
	}

	private function getClientIp()
	{
		$realIpHeader = Configuration::get('FLP_REAL_IP_HEADER');

		// Check if a custom header is configured and exists.
		if ($realIpHeader && isset($_SERVER[$realIpHeader])) {
			// Headers like X-Forwarded-For can contain a list of IPs. The client IP is typically the first one.
			$ips = explode(',', $_SERVER[$realIpHeader]);
			$ip = trim($ips[0]);

			// Validate that the retrieved value is a valid IP address.
			if (filter_var($ip, FILTER_VALIDATE_IP)) {
				return $ip;
			}
		}

		// Fallback to the standard remote address.
		return $_SERVER['REMOTE_ADDR'];
	}

	private function hastIt($s)
	{
		$hash = 'fraudlabspro_' . $s;

		for ($i = 0; $i < 65536; $i++) {
			$hash = sha1('fraudlabspro_' . $hash);
		}

		return hash('sha256', $hash);
	}
}
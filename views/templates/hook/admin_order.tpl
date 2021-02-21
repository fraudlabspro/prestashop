{*
* 2012-2020 FraudLabs Pro
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
*  @author FraudLabs Pro <support@fraudlabspro.com>
*  @copyright  2012-2020 FraudLabs Prod
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of FraudLabs Pro
*}

<div class="panel">
	<div class="panel-heading">
		<h3><img src="https://www.fraudlabspro.com/images/app-icon.png" height="24" width="24" align="absmiddle" /> FraudLabs Pro Result</h3>
	</div>

	{if $no_result}
	<div class="alert alert-warning">
		This order is not processed by FraudLabs Pro.
	</div>
	{else}
	<div class="table-responsive">
		<table class="table" width="100%" border="0" cellspacing="0" cellpadding="3" style="border-collapse:collapse">
			<col width="10%">
			<col width="10%">
			<col width="15%">
			<col width="2%">
			<col width="10%">
			<col width="15%">
			<col width="2%">
			<col width="10%">
			<col width="15%">
			<tr>
				<td rowspan="10" valign="top" align="center" style="vertical-align:top">
					<img src="https://fraudlabspro.hexa-soft.com/images/fraudscore/fraudlabsproscore{$fraud_score}.png" width="110" height="86" border="0">
					<p><span style="font-size:1.5em;font-weight:bold;color:#f00">{$fraud_status}</span></p>
					<hr/>
					<p><strong>Remaining Credits:</strong></p>
					<p>{$remaining_credits}</p>
				</td>
				<td><strong>Client IP</strong></td>
				<td>{$client_ip}</td>
				<td>&nbsp;</td>
				<td><strong>IP Location</strong></td>
				<td colspan="4"><a href="http://www.geolocation.com/{$client_ip}" target="_blank">{$location} </a><div style="font-size:12px">{$coordinate}</div></td>
			</tr>
			<tr>
				<td><strong>ISP</strong></td>
				<td>{$isp}</td>
				<td>&nbsp;</td>
				<td><strong>IP Domain</strong></td>
				<td>{$domain}</td>
				<td>&nbsp;</td>
				<td><strong>Net Speed</strong></td>
				<td>{$net_speed}</td>
			</tr>
			<tr>
				<td><strong>Using Proxy</strong><div style="font-size:10px;color:#4b4b4b">Whether IP address is from Anonymous Proxy Server.</div></td>
				<td>{$is_proxy}</td>
				<td>&nbsp;</td>
				<td><strong>IP Usage</strong></td>
				<td>{$usage_type}</td>
				<td>&nbsp;</td>
				<td><strong>Time Zone</strong></td>
				<td>{$time_zone}</td>
			</tr>
			<tr>
				<td><strong>Distance</strong><div style="font-size:10px;color:#4b4b4b">Distance from IP address to Billing Location.</div></td>
				<td>{$distance}</td>
				<td colspan="6">&nbsp;</td>
			</tr>
			<tr>
				<td><strong>High Risk Country</strong></td>
				<td>{$is_high_risk_country}</td>
				<td>&nbsp;</td>
				<td><strong>Free Email</strong><div style="font-size:10px;color:#4b4b4b">Whether email is from free email provider.</div></td>
				<td>{$is_free_email}</td>
				<td>&nbsp;</td>
				<td><strong>Ship Forward</strong><div style="font-size:10px;color:#4b4b4b">Whether shipping address is in database of known mail drops.</div></td>
				<td>{$is_ship_forward}</td>
			</tr>
			<tr>
				<td><strong>Email Blacklist</strong><div style="font-size:10px;color:#4b4b4b">Whether the email address is in our blacklist database.</div></td>
				<td>{$is_email_blacklist}</td>
				<td>&nbsp;</td>
				<td><strong>Credit Card Blacklist</strong><div style="font-size:10px;color:#4b4b4b">Whether the credit card is in our blacklist database.</div></td>
				<td>{$is_card_blacklist}</td>
				<td>&nbsp;</td>
				<td><strong>BIN Found</strong><div style="font-size:10px;color:#4b4b4b">Whether the BIN information matches our BIN list.</div></td>
				<td>{$is_bin_found}</td>
			</tr>
			<tr>
				<td><strong>IP Blacklist</strong><div style="font-size:10px;color:#4b4b4b">Whether the client IP is in our blacklist database.</div></td>
				<td>{$is_ip_blacklist}</td>
				<td>&nbsp;</td>
				<td><strong>Device Blacklist</strong><div style="font-size:10px;color:#4b4b4b">Whether the client device is in our blacklist database.</div></td>
				<td>{$is_device_blacklist}</td>
				<td colspan="3">&nbsp;</td>
			</tr>
			<tr>
				<td><strong>Triggered Rules</strong></td>
				<td colspan="8">{$triggered_rules}</td>
			</tr>
			<tr>
				<td><strong>Transaction ID</strong></td>
				<td colspan="8"><a href="https://www.fraudlabspro.com/merchant/transaction-details/{$transaction_id}" target="_blank">{$transaction_id}</a></td>
			</tr>
			<tr>
				<td><strong>Error Message</strong></td>
				<td colspan="7">{$error_message}</td>
			</tr>
		</table>
	</div>
	<p>
		<div class="pull-right">
			{if $show_approve_reject_button }
			<button class="btn btn-success" id="btn-approve" data-id="{$transaction_id}">Approve Order</button>
			<button class="btn btn-danger" id="btn-reject" data-id="{$transaction_id}">Reject Order</button>
			{/if}
			{if $show_blacklist_button }
			<button class="btn btn-default" id="btn-blacklist" data-id="{$transaction_id}">Blacklist Order</button>
			{/if}
		</div>
		
		<div class="clearfix"></div>
	</p>
	{/if}
</div>

<script>
	$(function(){
		$('#btn-approve').on('click', function(){
			var $form = $('<form method="post">').html('<input type="hidden" name="approve" value="true"><input type="hidden" name="transactionId" value="' + $(this).attr('data-id') + '">');
			$('body').append($form);
			$form.submit();
		});

		$('#btn-reject').on('click', function(){
			var $form = $('<form method="post">').html('<input type="hidden" name="reject" value="true"><input type="hidden" name="transactionId" value="' + $(this).attr('data-id') + '">');
			$('body').append($form);
			$form.submit();
		});

		$('#btn-blacklist').on('click', function(e){
			e.preventDefault();

			var reason = prompt('Please enter the reason(s) for blacklisting this order.', '');
			
			if(reason == null){
				return;
			}

			var $form = $('<form method="post">').html('<input type="hidden" name="blacklist" value="true"><input type="hidden" name="reason" value="' + reason + '"><input type="hidden" name="transactionId" value="' + $(this).attr('data-id') + '">');
			$('body').append($form);
			$form.submit();
		});
	});
</script>
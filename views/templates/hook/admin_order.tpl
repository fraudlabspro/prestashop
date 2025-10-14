{**
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
 * @license https://opensource.org/licenses/MIT (MIT)
*}
<style>
.card, .module-addons-item-grid .module-addons-item-wrapper-grid, .module-item-wrapper-grid, .module-placeholders-wrapper .timeline-item-wrapper {
    position: relative;
    display: block;
    margin-bottom: .625rem;
    background-color: #fff;
    border: 1px solid #dbe6e9;
    border-radius: 5px;
    -webkit-box-shadow: 0 0 4px 0 rgba(0, 0, 0, .06);
    box-shadow: 0 0 4px 0 rgba(0, 0, 0, .06);
        -webkit-box-orient: vertical;
    -webkit-box-direction: normal;
    min-width: 0;
    word-wrap: break-word;
    background-clip: border-box;
}

.card-header:first-child {
    border-radius: 5px 5px 0 0;
}

.card-header {
    padding: .625rem;
    background-color: #fafbfc;
    border-bottom: 1px solid #dbe6e9;
        margin-bottom: 0;
}

.card-header, .card-header .card-header-title {
    font-weight: 600;
    line-height: 1.5rem;
}

.card-header h3{
    margin-bottom: 0;
        font-size: 1rem;
}
.card-body {
    -webkit-box-flex: 1;
    -ms-flex: 1 1 auto;
    flex: 1 1 auto;
    min-height: 1px;
    padding: .625rem;
}
.table-responsive
 {
    display: block;
    width: 100%;
    overflow-x: auto;
}
</style>
<div class="card" style="margin-top:2em">
        <div class="card-header">
                <h3 class="card-header-title"><img src="https://www.fraudlabspro.com/images/app-icon.png" height="24" width="24" align="absmiddle" /> FraudLabs Pro Result</h3>
        </div>
        <div class="card-body" style="padding:1.5rem;">
        {if $no_result}
        <div class="alert alert-warning">
                This order is not processed by FraudLabs Pro.
        </div>
        {else}
        <div class="table-responsive">
                <table class="table" width="100%" border="0" cellspacing="0" cellpadding="3" style="border-collapse:collapse;margin-bottom:1rem;">
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
                                <td rowspan="10" valign="top" align="center" style="vertical-align:top;border-top:0;padding-right:1.2rem;">
                                        <img src="https://fraudlabspro.hexa-soft.com/images/fraudscore/fraudlabsproscore{$fraud_score|escape:'htmlall':'UTF-8'}.png" width="160" height="126" border="0">
                                        <p style="margin-bottom:0;"><span style="font-size:1.5em;font-weight:bold;color:#{$color|escape:'htmlall':'UTF-8'}">{$fraud_status|escape:'htmlall':'UTF-8'}</span></p>
                                        <hr style="margin-top:1.5rem;margin-bottom:1.5rem;"/>
                                        <p style="margin-bottom:0.5rem;"><strong>Remaining Credits:</strong></p>
                                        <p>{$remaining_credits|escape:'htmlall':'UTF-8'}</p>
                                </td>
                                <td style="border-top:0;"><strong>Transaction ID</strong></td>
                                <td colspan="8" style="border-top:0;"><a href="https://www.fraudlabspro.com/merchant/transaction-details/{$transaction_id|escape:'htmlall':'UTF-8'}" target="_blank">{$transaction_id|escape:'htmlall':'UTF-8'}</a></td>
                        </tr>
                        <tr>
                                <td><strong>Client IP</strong></td>
                                <td>{$client_ip|escape:'htmlall':'UTF-8'}</td>
                                <td>&nbsp;</td>
                                <td><strong>IP Location</strong></td>
                                <td colspan="4"><a href="http://www.geolocation.com/{$client_ip|escape:'htmlall':'UTF-8'}" target="_blank">{$location|escape:'htmlall':'UTF-8'} </a><div style="font-size:12px">{$coordinate|escape:'htmlall':'UTF-8'}</div></td>
                        </tr>

                        <tr>
                                <td><strong>Time Zone</strong></td>
                                <td>{$time_zone|escape:'htmlall':'UTF-8'}</td>
                                <td>&nbsp;</td>
                                <td><strong>Distance</strong><div style="font-size:10px;color:#4b4b4b">Distance from IP address to Billing Location.</div></td>
                                <td colspan="4">{$distance|escape:'htmlall':'UTF-8'}</td>
                        </tr>
                        <tr>
                                <td><strong>ISP</strong></td>
                                <td>{$isp|escape:'htmlall':'UTF-8'}</td>
                                <td>&nbsp;</td>
                                <td><strong>IP Domain</strong></td>
                                <td colspan="4">{$domain|escape:'htmlall':'UTF-8'}</td>
                        </tr>
                        <tr>
                                <td><strong>Net Speed</strong></td>
                                <td>{$net_speed|escape:'htmlall':'UTF-8'}</td>
                                <td>&nbsp;</td>
                                <td><strong>IP Usage</strong></td>
                                <td colspan="4">{$usage_type|escape:'htmlall':'UTF-8'}</td>
                        </tr>
                        <tr>
                                <td><strong>Using Proxy</strong><div style="font-size:10px;color:#4b4b4b">Whether IP address is from Anonymous Proxy Server.</div></td>
                                <td>{$is_proxy|escape:'htmlall':'UTF-8'}</td>
                                <td>&nbsp;</td>
                                <td><strong>Free Email</strong><div style="font-size:10px;color:#4b4b4b">Whether email is from free email provider.</div></td>
                                <td>{$is_free_email|escape:'htmlall':'UTF-8'}</td>
                                <td>&nbsp;</td>
                                <td><strong>Ship Forward</strong><div style="font-size:10px;color:#4b4b4b">Whether shipping address is in database of known mail drops.</div></td>
                                <td>{$is_ship_forward|escape:'htmlall':'UTF-8'}</td>
                        </tr>
                        <tr>
                                <td><strong>Email Blacklist</strong><div style="font-size:10px;color:#4b4b4b">Whether the email address is in our blacklist database.</div></td>
                                <td>{$is_email_blacklist|escape:'htmlall':'UTF-8'}</td>
                                <td>&nbsp;</td>
                                <td><strong>Credit Card Blacklist</strong><div style="font-size:10px;color:#4b4b4b">Whether the credit card is in our blacklist database.</div></td>
                                <td>{$is_card_blacklist|escape:'htmlall':'UTF-8'}</td>
                                <td>&nbsp;</td>
                                <td><strong>BIN Found</strong><div style="font-size:10px;color:#4b4b4b">Whether the BIN information matches our BIN list.</div></td>
                                <td>{$is_bin_found|escape:'htmlall':'UTF-8'}</td>
                        </tr>
                        <tr>
                                <td><strong>IP Blacklist</strong><div style="font-size:10px;color:#4b4b4b">Whether the client IP is in our blacklist database.</div></td>
                                <td>{$is_ip_blacklist|escape:'htmlall':'UTF-8'}</td>
                                <td>&nbsp;</td>
                                <td><strong>Device Blacklist</strong><div style="font-size:10px;color:#4b4b4b">Whether the client device is in our blacklist database.</div></td>
                                <td>{$is_device_blacklist|escape:'htmlall':'UTF-8'}</td>
                                <td>&nbsp;</td>
                                <td><strong>Phone Verified</strong><div style="font-size:10px;color:#4b4b4b">Whether the phone number is verified by the client.</div></td>
                                <td>{$is_phone_verified|escape:'htmlall':'UTF-8'}</td>
                        </tr>
                        <tr>
                                <td><strong>Triggered Rules</strong></td>
                                <td colspan="8">
                                {if $show_upgrade}
                                        <br><div class="alert alert-info">Available for <a href="https://www.fraudlabspro.com/pricing" target="_blank">Mini plan</a> onward. Please <a href="https://www.fraudlabspro.com/merchant/login" target="_blank">upgrade</a>.</div>
                                {else}
                                        {$triggered_rules|escape:'htmlall':'UTF-8'}
                                {/if}
                                </td>
                        </tr>
                        <tr>
                                <td><strong>Error Message</strong></td>
                                <td colspan="7">{$error_message|escape:'htmlall':'UTF-8'}</td>
                        </tr>
                </table>
        </div>
        <p style="font-size:0.8rem;">Please <a href="https://www.fraudlabspro.com/merchant/transaction-details/{$transaction_id|escape:'htmlall':'UTF-8'}" target="_blank"> login to FraudLabs Pro Merchant Area</a> for full report about this order.</p>
                <div style="float:right;">
                        {if $show_approve_reject_button }
                        <button class="btn btn-success" style="margin-right:0.5rem;" id="btn-approve" data-id="{$transaction_id|escape:'htmlall':'UTF-8'}">Approve Order</button>
                        <button class="btn btn-danger" style="margin-right:0.5rem;" id="btn-reject" data-id="{$transaction_id|escape:'htmlall':'UTF-8'}">Reject Order</button>
                        {/if}
                        {if $show_blacklist_button }
                        <button class="btn btn-default" id="btn-blacklist" data-id="{$transaction_id|escape:'htmlall':'UTF-8'}">Blacklist Order</button>
                        {/if}
                </div>

                <div class="clearfix"></div>

        {/if}
    </div>
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


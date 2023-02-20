{**
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
 * @author FraudLabs Pro <support@fraudlabspro.com>
 * @copyright  2013-2023 FraudLabs Pro
 * @license https://opensource.org/licenses/MIT (MIT)
*}

<link href="{$pathVendor|escape:'htmlall':'UTF-8'}" rel=preload as=script>
<link href="{$pathApp|escape:'htmlall':'UTF-8'}" rel=preload as=script>
<link href="{$urlAccountsVueCdn|escape:'htmlall':'UTF-8'}" rel=preload as=script>

{if isset($message_success)} <div class="bootstrap"> <div class="module_confirmation conf confirm alert alert-success"> <button type="button" class="close" data-dismiss="alert">&times;</button> {$message_success|escape:'htmlall':'UTF-8'} </div> </div> {/if}

<div id="app"></div>
<script src="{$pathVendor|escape:'htmlall':'UTF-8'}"></script>
<script src="{$pathApp|escape:'htmlall':'UTF-8'}"></script>
<script src="{$urlAccountsVueCdn|escape:'htmlall':'UTF-8'}" type="text/javascript"></script>

<style>
  /** Hide native multistore module activation panel, because of visual regressions on non-bootstrap content */
  #content.nobootstrap div.bootstrap.panel {
    display: none;
  }
</style>

<div class="panel">
  <h3><i class="icon-cogs"></i> {l s='Settings' d='Modules.FraudLabsPro.Admin'}</h3>
  <form action="{$current_url}" method="post" class="form-horizontal">
    <div class="form-group">
      <label class="control-label col-md-3">{l s='Enabled' d='Modules.FraudLabsPro.Admin'}</label>
      <div class="col-md-9">
        <span class="switch prestashop-switch fixed-width-lg">
          <input type="radio" name="module_is_enabled" id="module_is_enabled_on" value="1"{if $module_is_enabled} checked{/if}/>
          <label for="module_is_enabled_on">Yes</label>
          <input type="radio" name="module_is_enabled" id="module_is_enabled_off" value="0"{if !$module_is_enabled} checked{/if}/>
          <label for="module_is_enabled_off">No</label>
          <a class="slide-button btn"></a>
        </span>
        <p class="help-block">{l s='Enable or disable FraudLabs Pro module.' d='Modules.FraudLabsPro.Admin'}</p>
      </div>
    </div>
    <div class="form-group">
      <label class="control-label col-md-3">{l s='API Key' d='Modules.FraudLabsPro.Admin'}</label>
      <div class="col-md-9">
        <input type="text" name="api_key" value="{$api_key|escape:'htmlall':'UTF-8'}" class="form-control" />
        <p class="help-block">{l s='The API key obtained from FraudLabs Pro.' d='Modules.FraudLabsPro.Admin'}</p>
      </div>
    </div>
    <div class="form-group">
      <label class="control-label col-md-3">{l s='Approve Status' d='Modules.FraudLabsPro.Admin'}</label>
      <div class="col-md-9">
        <select name="approve_stage_id" class="form-control">
          <option value="0"{if !$approve_stage_id} selected{/if}>{l s='Do not change' d='Modules.FraudLabsPro.Admin'}</option>
          {foreach from=$order_stages item=stage}
            <option value="{$stage.id_order_state|escape:'htmlall':'UTF-8'}"{if $approve_stage_id == $stage.id_order_state} selected{/if}>{$stage.name|escape:'htmlall':'UTF-8'}</option>
          {/foreach}
        </select>
        <p class="help-block">{l s='Change order to this stage when transaction is approved by FraudLabs Pro.' d='Modules.FraudLabsPro.Admin'}</p>
      </div>
    </div>
    <div class="form-group">
      <label class="control-label col-md-3">{l s='Review Status' d='Modules.FraudLabsPro.Admin'}</label>
      <div class="col-md-9">
        <select name="review_stage_id" class="form-control">
          <option value="0"{if !$review_stage_id} selected{/if}>{l s='Do not change' d='Modules.FraudLabsPro.Admin'}</option>
          {foreach from=$order_stages item=stage}
            <option value="{$stage.id_order_state|escape:'htmlall':'UTF-8'}"{if $review_stage_id == $stage.id_order_state} selected{/if}>{$stage.name|escape:'htmlall':'UTF-8'}</option>
          {/foreach}
        </select>
        <p class="help-block">{l s='Change order to this stage when transaction is marked as review by FraudLabs Pro.' d='Modules.FraudLabsPro.Admin'}</p>
      </div>
    </div>
    <div class="form-group">
      <label class="control-label col-md-3">{l s='Reject Status' d='Modules.FraudLabsPro.Admin'}</label>
      <div class="col-md-9">
        <select name="reject_stage_id" class="form-control">
          <option value="0"{if !$reject_stage_id} selected{/if}>{l s='Do not change' d='Modules.FraudLabsPro.Admin'}</option>
          {foreach from=$order_stages item=stage}
            <option value="{$stage.id_order_state|escape:'htmlall':'UTF-8'}"{if $reject_stage_id == $stage.id_order_state} selected{/if}>{$stage.name|escape:'htmlall':'UTF-8'}</option>
          {/foreach}
        </select>
        <p class="help-block">{l s='Change order to this stage when transaction is rejected by FraudLabs Pro.' d='Modules.FraudLabsPro.Admin'}</p>
      </div>
    </div>
    <div class="form-group">
      <label class="control-label col-md-3"></label>
      <div class="col-md-9">
        <button type="submit" name="erase" class="btn btn-danger" onclick="return confirm('Confirm to erase all FraudLabs Pro records?');">Erase All Data</button>
        <div>
          <p class="help-block">{l s='Erase all FraudLabs Pro records from PrestaShop.' d='Modules.FraudLabsPro.Admin'}</p>
        </div>
      </div>
    </div>
    <div class="text-right">
      <button type="submit" name="save" class="btn btn-primary">Save</button>
    </div>
  </form>
</div>
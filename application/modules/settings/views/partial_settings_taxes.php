<div class="tab-info">

    <div class="form-group">
        <label for="settings[default_invoice_tax_rate]" class="control-label">
            <?php echo lang('default_invoice_tax_rate'); ?>
        </label>
        <select name="settings[default_invoice_tax_rate]" class="input-sm form-control">
            <option value=""><?php echo lang('none'); ?></option>
            <?php foreach ($tax_rates as $tax_rate) { ?>
                <option value="<?php echo $tax_rate->tax_rate_id; ?>"
                        <?php if ($this->mdl_settings->setting('default_invoice_tax_rate') == $tax_rate->tax_rate_id) { ?>selected="selected"<?php } ?>><?php echo $tax_rate->tax_rate_percent . '% - ' . $tax_rate->tax_rate_name; ?></option>
            <?php } ?>
        </select>
    </div>

    <div class="form-group">
        <label for="settings[default_include_item_tax]" class="control-label">
            <?php echo lang('default_invoice_tax_rate_placement'); ?>
        </label>
        <select name="settings[default_include_item_tax]" class="input-sm form-control">
            <option value=""><?php echo lang('none'); ?></option>
            <option value="0"
                    <?php if ($this->mdl_settings->setting('default_include_item_tax') === '0') { ?>selected="selected"<?php } ?>><?php echo lang('apply_before_item_tax'); ?></option>
            <option value="1"
                    <?php if ($this->mdl_settings->setting('default_include_item_tax') === '1') { ?>selected="selected"<?php } ?>><?php echo lang('apply_after_item_tax'); ?></option>
        </select>
    </div>

    <div class="form-group">
        <label for="settings[default_item_tax_rate]" class="control-label">
            <?php echo lang('default_item_tax_rate'); ?>
        </label>
        <select name="settings[default_item_tax_rate]" class="input-sm form-control">
            <option value=""><?php echo lang('none'); ?></option>
            <?php foreach ($tax_rates as $tax_rate) { ?>
                <option value="<?php echo $tax_rate->tax_rate_id; ?>"
                        <?php if ($this->mdl_settings->setting('default_item_tax_rate') == $tax_rate->tax_rate_id) { ?>selected="selected"<?php } ?>><?php echo $tax_rate->tax_rate_percent . '% - ' . $tax_rate->tax_rate_name; ?></option>
            <?php } ?>
        </select>
    </div>

    <div class="form-group">
        <label for="settings[tax_excluded]" class="control-label">
            <?php echo lang('tax_excluded'); ?>
        </label>

        <select name="settings[tax_excluded]" class="input-sm form-control">
            <option value="0"
                    <?php if (!$this->mdl_settings->setting('tax_excluded')) { ?>selected="selected"<?php } ?>><?php echo lang('no'); ?></option>
            <option value="1"
                    <?php if ($this->mdl_settings->setting('tax_excluded')) { ?>selected="selected"<?php } ?>><?php echo lang('yes'); ?></option>
        </select>
    </div>
</div>
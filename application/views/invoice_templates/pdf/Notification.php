<html lang="<?php echo lang('cldr'); ?>">
<head>
    <meta charset="utf-8">
    <title><?php echo lang('invoice'); ?></title>
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/default/css/templates.css">
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/default/css/custom-pdf.css">
</head>

<body>

<header>
    <table cellpadding="5" cellspacing="0" style="width: 100%; margin-top: 20px; border: 1px solid #000; border-width: 1px 0px 0px 1px; text-align: left;">
        <tr style="text-align: center">
            <td colspan="2" style="border-bottom: 1px solid #000">
                <strong>УВЕДОМЛЕНИЕ О НЕОБХОДИМОСТИ ОПЛАТЫ УСЛУГ</strong>
            </td>
        </tr>
        <tr>
            <td style="border-width: 0px 1px 1px 0px; border-color: #000; border-style: solid">КОМУ:</td>
            <td style="border-width: 0px 1px 1px 0px; border-color: #000; border-style: solid"><?=$invoice->client_name?></td>
        </tr>

        <tr>
            <td style="border-width: 0px 1px 1px 0px; border-color: #000; border-style: solid">ОТ КОГО:</td>
            <td style="border-width: 0px 1px 1px 0px; border-color: #000; border-style: solid"><?=$invoice->user_company?></td>
        </tr>

        <tr>
            <td style="border-width: 0px 1px 1px 0px; border-color: #000; border-style: solid">ДАТА ОТКЛЮЧЕНИЯ:</td>
            <td style="border-width: 0px 1px 1px 0px; border-color: #000; border-style: solid"><?=date_from_mysql($invoice->invoice_date_due)?></td>
        </tr>
    </table>
</header>

<main>
    <p><strong>Уведомление №<?=$invoice->invoice_number;?> от <?php echo date_from_mysql($invoice->invoice_date_created, true); ?></strong></p>
    <hr/>
    <p>Услуги, подлежащие оплате</p>
    <table cellpadding="5" cellspacing="0" style="width: 100%; border: 1px solid #000; text-align: center;">
        <tr style="font-weight: bold; background-color: beige">
            <td style="border-width: 0px 1px 1px 0px; border-color: #000; border-style: solid">№</td>
            <td style="border-width: 0px 1px 1px 0px; border-color: #000; border-style: solid"><?php echo lang('item'); ?></td>
            <td style="border-width: 0px 1px 1px 0px; border-color: #000; border-style: solid"><?php echo lang('qty'); ?></td>
            <td style="border-width: 0px 1px 1px 0px; border-color: #000; border-style: solid"><?php echo lang('price'); ?></td>
            <td style="border-width: 0px 1px 1px 0px; border-color: #000; border-style: solid"><?php echo lang('total'); ?></td>
            <?php if ($show_discounts) :?>
                <td style="border-width: 0px 1px 1px 0px; border-color: #000; border-style: solid"><?php echo lang('discount'); ?></td>
            <?php endif; ?>
        </tr>

        <?
        $invoice_tax_total = 0;
        foreach ($invoice_tax_rates as $invoice_tax_rate) {
            $invoice_tax_total += $invoice_tax_rate->invoice_tax_rate_amount;
        }
        ?>

        <?foreach ($items as $index=>$item) {
            $item->item_price += $item->item_tax_total + $invoice_tax_total / count($items);
            $item->item_subtotal +=  $item->item_tax_total + $invoice_tax_total / count($items);
            ?>
        <tr>
            <td style="border-width: 0px 1px 1px 0px; border-color: #000; border-style: solid"><?=$index+1?></td>
            <td style="border-width: 0px 1px 1px 0px; border-color: #000; border-style: solid"><?php echo $item->item_name; ?></td>
            <td style="border-width: 0px 1px 1px 0px; border-color: #000; border-style: solid"><?php echo format_amount($item->item_quantity); ?></td>
            <td style="border-width: 0px 1px 1px 0px; border-color: #000; border-style: solid"><?php echo format_currency($item->item_price); ?></td>
            <?php if ($show_discounts) :?>
                <td style="border-width: 0px 1px 1px 0px; border-color: #000; border-style: solid"><?php echo format_currency($item->item_discount); ?></td>
            <?php endif; ?>
            <td style="border-width: 0px 1px 1px 0px; border-color: #000; border-style: solid"><?php echo format_currency($item->item_subtotal); ?></td>
        </tr>
        <?php } ?>

        <tr>
            <td colspan="3" rowspan="2" style="border-width: 0px 1px 1px 0px; border-color: #000; border-style: solid">&nbsp;</td>
            <td style="border-width: 0px 1px 1px 0px; border-color: #000; border-style: solid; text-align: right"><strong>ИТОГО:</strong></td>
            <td style="border-width: 0px 1px 1px 0px; border-color: #000; border-style: solid"><?php echo format_currency($invoice->invoice_total); ?></td>
        </tr>

        <tr>
            <td style="border-width: 0px 1px 1px 0px; border-color: #000; border-style: solid; text-align: left" colspan="2">без налога (НДС)</td>
        </tr>

        <tr>
            <td style="border-width: 0px 1px 1px 0px; border-color: #000; border-style: solid; text-align: left" colspan="4">Всего к оплате:</td>
            <td style="border-width: 0px 1px 1px 0px; border-color: #000; border-style: solid"><?php echo format_currency($invoice->invoice_total); ?></td>
        </tr>
    </table>
</main>


<footer>
    <?php if ($invoice->invoice_terms) : ?>
        <div class="notes">
            <b><?php echo lang('terms'); ?></b><br/>
            <?php echo nl2br($invoice->invoice_terms); ?>
        </div>
    <?php endif; ?>
</footer>

<p>Данное уведомление предназначено для физических лиц. Если вы хотите получать счета на оплату со счета организации, сообщите об этом по электронной почте <a href="mailto:info@mrjohn.ru">info@mrjohn.ru</a>, приложив к письму реквизиты вашей организации. В этом случае сумма счета будет увеличена на 8%</p>

</body>
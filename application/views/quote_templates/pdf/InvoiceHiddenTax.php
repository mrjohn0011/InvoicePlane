<html lang="<?php echo lang('cldr'); ?>">
<head>
    <meta charset="utf-8">
    <title><?php echo lang('quote'); ?></title>
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/default/css/templates.css">
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/default/css/custom-pdf.css">
</head>
<body>
<h2>Акт №<?=$quote->quote_number;?> от <?php echo date_from_mysql($quote->quote_date_created, true); ?></h2>
<table cellspacing="5" cellpadding="5" style="border-width: 1px 0px; border-color: #000; border-style: solid; width: 100%; margin-bottom: 20px">
    <tr>
        <td>Исполнитель:</td>
        <td><?php echo $quote->user_company; ?>, ИНН <?=$quote->user_vat_id.", ".$quote->user_zip.", г. ", $quote->user_city.", ул. ".$quote->user_address_1." ".$quote->user_address_2?></td>
    </tr>
    <tr>
        <td>Заказчик:</td>
        <td><?php echo $quote->client_name; ?></td>
    </tr>
    <tr>
        <td>Основание:</td>
        <td>Основной договор</td>
    </tr>
</table>

<table cellspacing="0" cellpadding="5" style="width: 100%; margin-top: 20px; border: 1px solid #000; border-width: 1px 0px 0px 1px; text-align: left;">
    <tr>
        <th style="border-width: 0px 1px 1px 0px; border-color: #000; border-style: solid">№</th>
        <th style="border-width: 0px 1px 1px 0px; border-color: #000; border-style: solid"><?php echo lang('item'); ?></th>
        <th style="border-width: 0px 1px 1px 0px; border-color: #000; border-style: solid"><?php echo lang('qty'); ?></th>
        <th style="border-width: 0px 1px 1px 0px; border-color: #000; border-style: solid">Ед.</th>
        <th style="border-width: 0px 1px 1px 0px; border-color: #000; border-style: solid"><?php echo lang('price'); ?></th>
        <th style="border-width: 0px 1px 1px 0px; border-color: #000; border-style: solid"><?php echo lang('total'); ?></th>
    </tr>

    <?
    $quote_tax_total = 0;
    foreach ($quote_tax_rates as $quote_tax_rate) {
        $quote_tax_total += $quote_tax_rate->quote_tax_rate_amount;
    }
    ?>

    <?foreach ($items as $index=>$item) {
    $item->item_price += $item->item_tax_total + $quote_tax_total / count($items);
    $item->item_subtotal +=  $item->item_tax_total + $quote_tax_total / count($items);
    ?>

    <tr>
        <td style="border-width: 0px 1px 1px 0px; border-color: #000; border-style: solid"><?=$index+1?></td>
        <td style="border-width: 0px 1px 1px 0px; border-color: #000; border-style: solid"><?php echo $item->item_name; ?></td>
        <td style="border-width: 0px 1px 1px 0px; border-color: #000; border-style: solid"><?php echo format_amount($item->item_quantity); ?></td>
        <td style="border-width: 0px 1px 1px 0px; border-color: #000; border-style: solid">шт.</td>
        <td style="border-width: 0px 1px 1px 0px; border-color: #000; border-style: solid"><?php echo format_currency($item->item_price); ?></td>
        <td style="border-width: 0px 1px 1px 0px; border-color: #000; border-style: solid"><?php echo format_currency($item->item_subtotal); ?></td>
    </tr>

    <?php } ?>

</table>

<p style="text-align: right; font-weight: bold">ИТОГО: <?php echo format_currency($quote->quote_total); ?><br/>Без НДС: -</p>
<p>
    Всего оказано услуг <?=count($items)?>, на сумму <?php echo format_currency($quote->quote_total);?><br/>
    <strong><?=num2str($quote->quote_total)?></strong>
</p>

<p>Вышеперечисленные услуги выполнены полностью и в срок. Заказчик претензий по объему, качеству и срокам оказания услуг не имеет.</p>
<hr/>

<table style="width: 100%;">
    <tr>
        <td style="text-align: left">
            <p><strong>ИСПОЛНИТЕЛЬ</strong></p>
            <p><?php echo $quote->user_name; ?></p>
            <p>________________________</p>
        </td>

        <td style="text-align: right">
            <p><strong>ЗАКАЗЧИК</strong></p>
            <p><?php echo $quote->client_name; ?></p>
            <p>________________________</p>
        </td>
    </tr>
</table>
</body>
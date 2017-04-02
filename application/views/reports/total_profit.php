<html>
	<head>
		<title><?php echo lang('total_profit'); ?></title>
		<link rel="stylesheet" href="<?php echo base_url(); ?>assets/default/css/reports.css" type="text/css">
        <style>
            table td, table th {
                padding: 5px;
                border: 1px solid black;
                border-top: none
                border-right: none;
            }

            .amount {
                text-align: center;
            }

            table th {
                border-top: 1px solid black;
            }
        </style>
	</head>
	<body>
		
		<h3 style="text-align: center" class="report_title"><?php echo lang('total_profit'); ?></h3>
        <p style="text-align: center">За период с <?=$results['dateFrom']?> по <?=$results['dateTo']?></p>

		<table cellpadding="0" cellspacing="0">
			<tr>
				<th>Клиент</th>
				<th>Всего поступило</th>
				<th>Отдано партнерам</th>
				<th>Чистая прибыль (налог удержан)</th>
			</tr>
            <? $totals = array('payments' => 0, 'partner'=> 0, 'profit' => 0); ?>
			<?php
            foreach ($results['table'] as $result) {
                $totals['payments'] += $result->total_payment;
                $totals['partner'] += $result->total_partner;
                $totals['profit'] += $result->profit;
            ?>
			<tr>
				<td><?=$result->client_name?></td>
				<td class="amount"><?=format_currency($result->total_payment)?></td>
				<td class="amount"><?=format_currency($result->total_partner)?></td>
				<td class="amount"><?=format_currency($result->profit)?></td>
			</tr>
			<?php } ?>
            <tr>
                <th>ИТОГО</th>
                <th class="amount"><?=format_currency($totals['payments'])?></th>
                <th class="amount"><?=format_currency($totals['partner'])?></th>
                <th class="amount"><?=format_currency($totals['profit'])?></th>
            </tr>
		</table>
	</body>
</html>
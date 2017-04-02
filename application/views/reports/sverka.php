<html>
	<head>
		<title><?php echo lang('sverka'); ?></title>
		<link rel="stylesheet" href="<?php echo base_url(); ?>assets/default/css/reports.css" type="text/css">
        <style>
            body {
                font-size: 12px;
            }
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

            tr.top-offset td {
                padding-top: 50px;
            }

            table tr.no-border td {
                border: none;
            }
        </style>
	</head>
	<body>
		
		<h3 style="text-align: center" class="report_title"><?php echo lang('sverka'); ?></h3>
		<p>Взаимных расчетов по состоянию на <?=$results['dateTo']?> между <?=$results['client_name']?> и <?=$this->session->userdata('user_company')?></p>
		<p>Мы, нижеподписавшиеся, <?=$results['client_name']?>, с одной стороны, и <?=$this->session->userdata('user_company')?>, с другой стороны, составили настоящий акт сверки в том, что состояние взаимных расчетов по данным учета следующее:</p>

		<table cellpadding="0" cellspacing="0">
			<tr class="no-border">
				<td colspan="4">
					По данным <?=$results['client_name']?>, руб.
				</td>
				<td colspan="4">
					По данным <?=$this->session->userdata('user_company')?>, руб.
				</td>
			</tr>
			<tr>
				<th>№ п/п</th>
				<th>Наименование операции, документы</th>
				<th>Дебет</th>
				<th>Кредит</th>

				<th>№ п/п</th>
				<th>Наименование операции, документы</th>
				<th>Дебет</th>
				<th>Кредит</th>
			</tr>
			<tr>
				<td>&nbsp;</td>
				<td>Сальдо на <?=$results['dateFrom']?></td>
				<td class="amount"><?=format_amount($results['saldo']['credit'])?></td>
				<td class="amount"><?=format_amount($results['saldo']['debit'])?></td>

				<td>&nbsp;</td>
				<td>Сальдо на <?=$results['dateFrom']?></td>
				<td class="amount"><?=format_amount($results['saldo']['debit'])?></td>
				<td class="amount"><?=format_amount($results['saldo']['credit'])?></td>
			</tr>
            <? $totals = array('debit' => 0, 'credit'=> 0); ?>
			<?php
            foreach ($results['table'] as $index=>$result) {
                if ($result->type === 'debit'){
                    $totals['debit'] += $result->amount;
                } else {
                    $totals['credit'] += $result->amount;
                }
            ?>
			<tr>
				<td style="text-align: center"><?=($index+1)?></td>
				<td>
					<?
					echo $result->type == 'debit' ? 'Приемка ' : 'Исходящий платеж ';
					echo '('.date_from_mysql($result->date, true);
					echo $result->type == 'debit' ? ' №'.$result->number : '';
					echo ')'
					?>
				</td>
				<td class="amount"><?=$result->type == 'credit' ? format_amount($result->amount) : '&nbsp;'?></td>
				<td class="amount"><?=$result->type == 'debit' ? format_amount($result->amount) : '&nbsp;'?></td>

				<td style="text-align: center"><?=($index+1)?></td>
				<td>
					<?
					echo $result->type == 'debit' ? 'Продажа ' : 'Входящий платеж ';
					echo '('.date_from_mysql($result->date, true);
					echo $result->type == 'debit' ? ' №'.$result->number : '';
					echo ')'
					?>
				</td>
				<td class="amount"><?=$result->type == 'debit' ? format_amount($result->amount) : '&nbsp;'?></td>
				<td class="amount"><?=$result->type == 'credit' ? format_amount($result->amount) : '&nbsp;'?></td>
			</tr>
			<?php } ?>
            <tr>
                <td colspan="2">Обороты за период</td>
                <td class="amount"><?=format_amount($totals['credit'])?></td>
                <td class="amount"><?=format_amount($totals['debit'])?></td>

                <td colspan="2">Обороты за период</td>
                <td class="amount"><?=format_amount($totals['debit'])?></td>
                <td class="amount"><?=format_amount($totals['credit'])?></td>
            </tr>
            <tr>
                <?
                $saldo = $totals['debit'] + $results['saldo']['debit'] - $totals['credit'] - $results['saldo']['credit'];
                ?>
                <td colspan="2">Сальдо на <?=$results['dateTo']?></td>
                <td class="amount"><?=format_amount($saldo < 0 ? abs($saldo) : 0)?></td>
                <td class="amount"><?=format_amount($saldo > 0 ? $saldo : 0)?></td>

                <td colspan="2">Сальдо на <?=$results['dateTo']?></td>
                <td class="amount"><?=format_amount($saldo > 0 ? $saldo : 0)?></td>
                <td class="amount"><?=format_amount($saldo < 0 ? abs($saldo) : 0)?></td>
            </tr>

            <tr  class="no-border top-offset">
                <td colspan="4">
                    <p>По данным <?=$results['client_name']?></p>
                    <p>
                        На <?=$results['dateTo']?> задолженность
                        <? echo $saldo < 0 ? ' в пользу '.$results['client_name'].' составляет '.format_currency(abs($saldo)) : ''; ?>
                        <? echo $saldo > 0 ? ' в пользу '.$this->session->userdata('user_name').' составляет '.format_currency($saldo) : ''; ?>
                        <? echo $saldo == 0 ? ' отсутствует.' : ''; ?>
                    </p>
                </td>

                <td colspan="4">
                    <p>По данным <?=$this->session->userdata('user_name')?></p>
                    <p>
                        На <?=$results['dateTo']?> задолженность
                        <? echo $saldo < 0 ? ' в пользу '.$results['client_name'].' составляет '.format_currency(abs($saldo)) : ''; ?>
                        <? echo $saldo > 0 ? ' в пользу '.$this->session->userdata('user_name').' составляет '.format_currency($saldo) : ''; ?>
                        <? echo $saldo == 0 ? ' отсутствует.' : ''; ?>
                    </p>
                </td>
            </tr>

            <tr class="no-border top-offset">
                <td colspan="4">
                    <p>От <?=$results['client_name']?></p>
                    <p>&nbsp;</p>
                    <p>________________(____________________)</p>
                    <p>М.П.</p>
                </td>
                <td colspan="4">
                    <p>От <?=$this->session->userdata('user_name')?></p>
                    <p>&nbsp;</p>
                    <p>________________(____________________)</p>
                    <p>М.П.</p>
                </td>
            </tr>
		</table>
	</body>
</html>
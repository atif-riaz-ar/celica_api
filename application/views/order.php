<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
		"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="X-UA-Compatible" content="IE=edge"/>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
	<title>Print Invoice</title>
	<style type="text/css" media="print">
		.bdr {
			border: 1px solid #000000;
			padding-top: 0;
			margin-top: 0;
			margin-bottom: 0;
			padding-bottom: 0;
			vertical-align: top;
		}
	</style>
</head>
<body>
<table border="0" cellspacing="0" cellpadding="5">
	<tr>
		<td width="51%"><img src="<?php echo MEMBER_URL . 'assets/img/logo_original.png' ?>" width="150"/></td>
		<td width="49%" align="right">
			<table width="362" border="0" cellspacing="2" cellpadding="2">
				<tr>
					<td colspan="2"><span class="style1">TAX INVOICE/OFFICIAL RECEIPT</span></td>
				</tr>
				<tr>
					<td width="196">Co. Registration No.:<br/></td>
					<td width="152">T11LL0417L</td>
				</tr>
				<tr>
					<td>Invoice / Receipt No.:</td>
					<td>
						<?php echo $salesInfo['invoice_no']; ?>
					</td>
				</tr>
				<tr>
					<td>Invoice / Receipt Date:</td>
					<td>
						<?php
						$findate = date("d M Y", strtotime($salesInfo['order_date']));
						echo $findate;
						?>
					</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td class="bdr">
			<table width="100%" border="0" cellspacing="2" cellpadding="0">
				<tr>
					<td colspan="2"><strong>Invoiced To</strong></td>
				</tr>
				<tr>
					<td width="100">Name:</td>
					<td>
						<?php echo $salesInfo['f_name'] . ' ' . $salesInfo['l_name']; ?>
					</td>
				</tr>
				<tr>
					<td>Address:</td>
					<td>--</td>
				</tr>
				<tr>
					<td>&nbsp;</td>
					<td>
						<?php echo $country; ?>
					</td>
				</tr>
				<tr>
					<td>Contact:</td>
					<td>
						<?php echo $salesInfo['mobile']; ?>
					</td>
				</tr>
			</table>
		</td>
		<td class="bdr">
			<table width="100%" border="0" cellspacing="2" cellpadding="2">
				<tr>
					<td colspan="2">&nbsp;</td>
				</tr>
				<tr>
					<td width="150">ID Code:</td>
					<td width="292"><?php echo $salesInfo['userid']; ?>
					</td>
				</tr>
				<tr>
					<td>Sponsor ID:</td>
					<td>
						<?php echo STR_PAD($salesInfo['sponsorid'], 7, '0', STR_PAD_LEFT); ?>
					</td>
				</tr>
				<tr>
					<td colspan="2">&nbsp;</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td colspan="2">&nbsp;</td>
	</tr>
	<tr>
		<td colspan="2" class="bdr">
			<table width="99%" border="0" cellspacing="2" cellpadding="0">
				<tr>
					<td width="81"><strong>S/N</strong></td>
					<td width="122"><strong>PDT CODE</strong></td>
					<td><strong>PRODUCT DESCRIPTION </strong></td>
					<td width="109"><strong>QTY</strong></td>
					<td width="136"><strong>UNIT $</strong></td>
					<td width="136"><strong>TOTAL $</strong></td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td id="content" colspan="2" class="bdr">
			<?php
			$ctr = 0;
			$totquant = 0;
			$totpv = 0;
			$runtot = 0;
			$totgst = 0;
			foreach ($rsProducts as $key => $row) {
				$ctr++;
				?>
				<table width="99%" border="0" cellpadding="0">
					<tr>
						<td width="81">
							<?php echo $ctr; ?>
						</td>
						<td width="122">
							<?php echo $row['code']; ?>
						</td>
						<td>
							<?php echo $row['name']; ?>
						</td>
						<td width="109">
							<?php echo $row['qty']; ?>
						</td>
						<td width="136">
							<?php echo number_format($row['unit_price'], 2); ?>
						</td>
						<td width="136">
							<?php echo number_format($row['qty'] * $row['unit_price'], 2); ?>
						</td>
					</tr>
				</table>
				<?php

				$totquant += $row['qty'];
			}
			?>
		</td>
	</tr>
	<tr>
		<td colspan="2">&nbsp;</td>
	</tr>
	<tr>
		<td height="126" colspan="2" class="bdr">
			<table width="100%" border="0" cellspacing="2" cellpadding="0">
				<tr>
					<td>
						<strong>Total Quantity:</strong>
					</td>
					<td>
						<?php echo $totquant; ?>
					</td>
					<td>
						<strong>GRAND TOTAL $:</strong>
					</td>
					<td>
						<?php echo number_format($salesInfo['total_amount'], 2); ?>
					</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td height="129" colspan="2">
			<table width="100%" border="0" cellspacing="2" cellpadding="0">
				<tr>
					<td width="642" height="30"><strong>PAYMENT DETAILS</strong></td>
					<td width="342">&nbsp;</td>
				</tr>
			</table>
			<table width="100%" border="0" cellspacing="2" cellpadding="0">
				<tr>
					<td height="24"><strong>Amount Paid $:</strong></td>
					<td>
						<?php echo number_format($salesInfo['total_amount'], 2); ?>
					</td>
					<td><strong>Balance Payment $0</strong></td>
				</tr>
			</table>
			<table width="100%" border="0" cellspacing="2" cellpadding="0">
				<tr>
					<td height="24" width="300"><strong>Payment Mode:</strong></td>
					<td align="left"><?php echo $salesInfo['payment_type']; ?>
					</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td colspan="2">&nbsp;</td>
	</tr>
</table>
<table width="99%" border="0" cellpadding="5" cellspacing="0" class="bdr">
	<tr>
		<td width="70%" rowspan="2">Thank you for your order. Sales are governed by the Company LLP Policies and
			Procedures.
		</td>
		<td height="35" colspan="2" class="bdr"><p>&nbsp;</p>
			I confirmed this order is accurate and I accept the accompanying terms and conditions including
			overleaf.
		</td>
	</tr>
	<tr>
		<td width="15%" align="center" class="bdr"><strong>Signature</strong></td>
		<td align="center" class="bdr"><strong>Date</strong></td>
	</tr>
</table>
<table width="870" border="0" cellspacing="0" cellpadding="5">
	<tr>
		<td width="936">Computer generated receipt, no signature required.</td>
	</tr>
</table>
</body>
</html>

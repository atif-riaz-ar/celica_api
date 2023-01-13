<html>
<head>
	<title>Print Delivery Order</title>
	<style type="text/css">
		body, td, th {
			font-family: Verdana, Arial, Helvetica, sans-serif;
			font-size: 12px;
			height: absolute;
			table-layout: fixed;
		}

		.style1 {
			font-size: 16px;
			font-weight: bold;
		}

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
<table width="906" border="0" cellspacing="0" cellpadding="5">
	<tr>
		<td width="49%"><img src="<?php echo MEMBER_URL . 'assets/img/logo_original.png' ?>" width="150"/></td>
		<td width="51%" align="right">
			<table width="359" border="0" cellspacing="2" cellpadding="2">
				<tr>
					<td colspan="2"><span class="style1">SALES DELIVERY ORDER</span></td>
				</tr>
				<tr>
					<td width="196"> Co. / GST Registration No.:<br/></td>
					<td width="149">200302621E</td>
				</tr>
				<tr>
					<td>Delivery Order No.:</td>
					<td><?php echo $salesInfo['order_num']; ?></td>
				</tr>
				<tr>
					<td>Delivery Order Date:</td>
					<td><?php
						list($date, $time) = explode(' ', $salesInfo['order_date']);
						list($yr, $mo, $day) = explode('-', $date);
						$fdate = mktime(0, 0, 0, $mo, $day, $yr);
						$findate = date("d M", $fdate);
						echo $findate . ' ' . $yr;
						?></td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td class="bdr">
			<table width="100%" border="0" cellspacing="2" cellpadding="2">
				<tr>
					<td colspan="2"><strong>Sold &amp; Delivered To</strong></td>
				</tr>
				<tr>
					<td width="100">Name:</td>
					<td><?php echo $salesInfo['l_name'] . ' ' . $salesInfo['f_name']; ?></td>
				</tr>
				<tr>
					<td>&nbsp;</td>
					<td><?php
						echo $country;
						?></td>
				</tr>
			</table>
		</td>
		<td class="bdr">
			<table width="94%" border="0" cellspacing="2" cellpadding="2">
				<tr>
					<td colspan="2">&nbsp;</td>
				</tr>
				<tr>
					<td width="175">ID Code:</td>
					<td width="260"><?php echo $salesInfo['userid']; ?></td>
				</tr>
				<tr>
					<td>Distributor Order Form:</td>
					<td><?php echo $salesInfo['order_num']; ?></td>
				</tr>
				<tr>
					<td colspan="2">&nbsp;</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td colspan="2"><strong>Delivered:</strong></td>
	</tr>
	<tr>
		<td colspan="2" class="bdr">
			<table width="98%" border="0" cellspacing="2" cellpadding="0">
				<tr>
					<td width="92"><strong>S/N</strong></td>
					<td width="92"><strong>PDT</strong></td>
					<td width="447"><strong>PRODUCT DESCRIPTION</strong></td>
					<td width="184"><strong>SERIAL NO.</strong></td>
					<td width="96"><strong>QTY</strong></td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td colspan="2" class="bdr">
			<?php
			$suctot = 0;
			$suclnctr = 0;
			$penlnctr = 0;

			if ($salesInfo['rejected_date'] == null) {

				foreach ($rsProducts as $key => $row) {
					$suclnctr++;
					?>
					<table class="dmn" width="926" border="0" cellpadding="0" vspace="1">
						<tr>
							<td width="97"><?php echo $suclnctr; ?></td>
							<td width="101"><?php echo $row['code']; ?></td>
							<td width="456"><?php echo $row['name']; ?></td>
							<td width="168"></td>
							<td width="92"><?php echo $row['qty']; ?></td>
						</tr>
					</table>
					<?php

					$suctot += $row['qty'];
				}
			} else {
				?>
				<table width="927" height="5" border="0" cellpadding="0" class="dmn" vspace="1">
					<tr>
						<td width="97"></td>
						<td width="109"></td>
						<td width="492"></td>
						<td width="196"></td>
						<td width="94"></td>
					</tr>
				</table>

			<?php } ?>
		</td>
	</tr>
	<tr>
		<td colspan="2">
			<table width="98%" border="0" cellspacing="2" cellpadding="0">
				<tr>

					<td width="641">&nbsp;</td>
					<td width="110"><strong>TOTAL</strong></td>
					<td width="162"><?php echo $suctot; ?></td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td colspan="2"><strong>Outstanding:</strong></td>
	</tr>
	<tr>
		<td colspan="2" class="bdr">

			<table width="98%" border="0" cellspacing="2" cellpadding="0">
				<tr>
					<td width="74"><strong>S/N</strong></td>
					<td width="107"><strong>PDT CODE</strong></td>
					<td width="449"><strong>PRODUCT DESCRIPTION</strong></td>
					<td width="181">&nbsp;</td>
					<td width="97"><strong>QTY</strong></td>
				</tr>
			</table>

		</td>
	</tr>
	<tr>
		<td colspan="2" class="bdr">
			<?php
			$pentot = 0;
			if ($salesInfo['rejected_date'] != null) {


				foreach ($rsProducts as $key => $row) {
					$penlnctr++;
					?>
					<table class="dmn" width="921" border="0" cellpadding="0" vspace="1">
						<tr>
							<td width="80"><?php echo $penlnctr; ?></td>
							<td width="116"><?php echo $row['product_code']; ?></td>
							<td width="252"><?php echo $row['product_name']; ?></td>
							<td width="113">&nbsp;</td>
							<td width="125">&nbsp;</td>
							<td width="125">&nbsp;</td>
							<td width="94"><?php echo $row['qty']; ?></td>
						</tr>
					</table>
					<?php
					$pentot += $row['qty'];
				}
			} else {
				?>
				<table class="dmn" width="918" border="0" cellpadding="0" vspace="1">
					<tr>
						<td width="81"></td>
						<td width="114"></td>
						<td width="253"></td>
						<td width="114">&nbsp;</td>
						<td width="125">&nbsp;</td>
						<td width="123">&nbsp;</td>
						<td width="92"></td>
					</tr>
				</table>
			<?php } ?>
		</td>
	</tr>
	<tr>
		<td colspan="2">
			<table width="100%" border="0" cellspacing="2" cellpadding="0">
				<tr>
					<td>&nbsp;</td>
					<td width="200"><strong>TOTAL</strong></td>
					<td width="100"><?php echo $pentot; ?></td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td colspan="2">&nbsp;</td>
	</tr>
</table>
<table width="939" border="0" cellpadding="5" cellspacing="0" class="bdr">
	<tr>
		<td width="70%" rowspan="2">Products are deemed delivered with consent of purchaser to non-purchaser,
			where applicable, whose particulars first appeared herein. The purchaser
			shall bear full responsibility to take proper subsequent delivery where applicable. Enyouth shall deem
			delivery to be complete and final to
			purchaser named in Tax Invoice/ Official Receipt at its sole discretion.
			For partially delivered purchase, please arrange collection of outstanding
			produts within 7 days, after which an inventory holding fee at 0.5% per day
			shall be levied on the outstanding invoiced amount. Holding fees do not apply for products unavailable
			for collection. Sales are governed by the
			Company Policies and Procedures. All products are delivered in new, unopened,
			good order and condition. Should you disagree with the condition of the
			products, please immediately notify us and request for an exchange. Any
			exchange request made 7 days after the sales will not be entertained.
		</td>
		<td height="150" colspan="2" class="bdr"><p>&nbsp;</p>
			I confirmed this order is accurate
			and I accept the accompanying
			terms and conditions. I have received the above products in good order &amp; condition including
			overleaf.
		</td>
	</tr>
	<tr>
		<td width="15%" align="center" class="bdr"><strong>Signature</strong></td>
		<td align="center" class="bdr"><strong>Date</strong></td>
	</tr>
</table>
<table width="937" border="0" cellspacing="0" cellpadding="5">
	<tr>
		<td width="927">Computer generated receipt, no signature required.</td>
	</tr>
</table>
</body>
</html>

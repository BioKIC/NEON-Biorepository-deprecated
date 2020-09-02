<form name="reportsform" onsubmit="return ProcessReport();" method="post" target="_blank">
	<fieldset>
		<legend>Generate Loan Paperwork</legend>
		<div style="float:right;">
			<b>Mailing Account #:</b> <input type="text" autocomplete="off" name="mailaccnum" maxlength="32" style="width:100px;" value="" />
		</div>
		<div style="padding-bottom:2px;">
			<b>Print Method:</b>
			<input type="radio" name="outputmode" id="printbrowser" value="browser" checked /> Print in Browser
			<input type="radio" name="outputmode" id="printdoc" value="doc" /> Export to DOCX
		</div>
		<div style="padding-bottom:8px;">
			<b>Invoice Language:</b> <input type="radio" name="languagedef" value="0" checked /> English
			<input type="radio" name="languagedef" value="1" /> English/Spanish
			<input type="radio" name="languagedef" value="2" /> Spanish
		</div>
		<input name="loantype" type="hidden" value="<?php echo $loanType; ?>" />
		<input name="collid" type="hidden" value="<?php echo $collid; ?>" />
		<input name="identifier" type="hidden" value="<?php echo $identifier; ?>" />
		<button name="formsubmit" type="submit" onclick="this.form.action ='reports/defaultinvoice.php'" value="invoice">Invoice</button>
		<?php
		if(isset($specimenTotal) && $specimenTotal){
			?>
			<button name="formsubmit" type="submit" onclick="this.form.action ='reports/defaultspecimenlist.php'" value="spec">Specimen List</button>
			<?php
		}
		?>
		<button name="formsubmit" type="submit" onclick="this.form.action ='reports/defaultmailinglabel.php'" value="label">Mailing Label</button>
		<button name="formsubmit" type="submit" onclick="this.form.action ='reports/defaultenvelope.php'" value="envelope">Envelope</button>
	</fieldset>
</form>
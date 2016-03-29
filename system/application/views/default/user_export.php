<div class="title title-spacing">
    <h2>Export users</h2>
    <span style="color:#000; font-size:14px">Export users in <b><?= $exportfmt?></b> format.</span>
</div>
<?php !empty($flash) ? flash($flash) : '' ?>
<form action="<?= site_url("admin/user/export/$exportfmt") ?>" name="exportform" method="post" id="importfrm"  >
<?php if ($exportfmt == 'csv') { ?>
<table width="98%" border="1" class="csvoptable" >
  <tr>
    <td colspan="2"><p><b>CSV options</b><br />Please check your csv file and determine the enclosure character and field delimiter character.<br />
    Output format: <em class="em2" >month-day-year-users.csv</em></p></td>
  </tr>
  <tr>
    <td width="96">Enclosed by</td>
    <td width="828"><input type="text" name="enclosure" size="4" value='"' /></td>
  </tr>
  <tr>
    <td>Delimiter</td>
    <td><input type="text" name="delimiter" size="4" value="," /></td>
  </tr>
  <tr>
    <td align="right">&nbsp;</td>
    <td align="right"><input type="submit" name="export" value="Export" onClick="document.form['exportform'].submit()" /></td>
  </tr>
</table>
<?php } else { ?>
<div class="btn2" >
<input type="submit" name="export" value="Export" />
</div>
<?php } ?>
<input type="hidden" name="exportfmt" value="<?= $exportfmt ?>"  />
</form>
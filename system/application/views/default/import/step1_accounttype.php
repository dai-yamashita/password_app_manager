<div class="title title-spacing">
    <h2>Import accounttypes</h2>
    <span style="color:#000; font-size:14px">Import accounttypes in <b><?= $importfmt?></b> format.</span><br /><br />
	<?php if ($importfmt == 'csv') { ?>    
	<a href="<?= base_url() ?>uploads/sample/sample-accounttypes.csv" >Click here to Download sample <?= $importfmt?> format &raquo;.</a>
    <?php } elseif ($importfmt == 'xml') { ?>
	<a href="<?= base_url() ?>uploads/sample/sample-accounttypes.xml" >Click here to Download sample <?= $importfmt?> format &raquo;.</a>    
    <?php } ?>
    
</div>
<?php !empty($flash) ? flash($flash) : '' ?>

<form method="post" action="<?= site_url('admin/accounttype/import_step/csv/step/1') ?>"  id="exportfrm"  enctype="multipart/form-data" >
<?php if ( $importfmt == 'csv') { ?>
<div class="title title-spacing">
<h2>Step 1</h2>
</div>
<input type="file" name="userfile" class="field text large"   />
 <table width="99%" border="1" class="csvoptable" >
  <tr>
    <td colspan="2"> <b>CSV options</b><br />Please check your csv file and determine the enclosure character and field delimiter character.</td>
    </tr>
  <tr>
    <td width="92">Enclosed by</td>
    <td width="826"><input type="text" name="enclosure" size="4" value='"' /></td>
  </tr>
  <tr>
    <td>Delimiter</td>
    <td><input type="text" name="delimiter" size="4" value="," /></td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td><span style="padding-top:15px">
      <input type="submit" name="btnimport" value="Next"  />
    </span></td>
  </tr>
</table> 
<?php } else { ?>
<input type="file" name="userfile" class="field text large"   />
<p>Please browse your xml file and click Import button.</p>
&nbsp;&nbsp;<input type="submit" name="btnimport" value="Import" />
<?php } ?>

<input type="hidden" name="importfmt" value="<?= $importfmt ?>"  />
</form>
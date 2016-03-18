<div class="title title-spacing">
<h2>Step 3</h2>
<a href="<?= site_url("admin/accounttype/import_step/csv/step/2")?>" >&laquo; Back to step2</a>
</div>
<?php
 ?>
<form method="post" action="<?= site_url( "admin/accounttype/import_step/csv/step/3") ?>" enctype="multipart/form-data" >
<div class="hastable"  >

<p>Uncheck the checkbox that you dont wan't to import the data.</p>
<table width="100%" border="1"   id="sort-table" >
<thead>
  <tr>
    <th><input type="checkbox" name="checkall" value="" id="checkall" checked="checked" /></th>
    <th>acctype </th>
    <th>description</th>
    </tr>
</thead>    
<?php
 if ( isset($imported_csvdata) ) :
 	for($n=0; $n < count($imported_csvdata); $n++) :
	 
?> 

  <tr>
    <td width="3%"><input type="checkbox" name="ischecked[]" value="<?= $n ?>"  checked="checked" /></td>
    <td width="63%"><?= isset($imported_csvdata[$n]['acctype']) ? $imported_csvdata[$n]['acctype'] : '&nbsp;' ?></td>
    <td width="34%"><?= isset($imported_csvdata[$n]['desc']) ? $imported_csvdata[$n]['desc'] : '&nbsp;' ?></td>
    </tr>
<?php
	endfor;
endif;
?>

</table>  
</div>

<br />
<input type="submit" name="submit" value="Next" />

<input type="hidden" name="step" value=""  />

</form>
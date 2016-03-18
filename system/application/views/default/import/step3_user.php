<div class="title title-spacing">
<h2>Step 3</h2>
<a href="<?= site_url("admin/user/import_step/csv/step/2")?>" >&laquo; Back to step2</a>
</div>
<?php
 ?>
<form method="post" action="<?= site_url( "admin/user/import_step/csv/step/3") ?>" enctype="multipart/form-data" >
<div class="hastable"  >

<p>Uncheck the checkbox that you dont wan't to import the data.</p>
<table width="100%" border="1"   id="sort-table" >
<thead>
  <tr>
    <th><input type="checkbox" name="checkall" value="" id="checkall" checked="checked" /></th>
    <th>id</th>
    <th>role_id</th>
    <th>username</th>
    <th>email</th>
    <th>firstname</th>
    <th>lastname</th>
    </tr>
</thead>    
<?php
 if ( isset($imported_csvdata) ) :
 	for($n=0; $n < count($imported_csvdata); $n++) :
	 
?> 

  <tr>
    <td width="3%"><input type="checkbox" name="ischecked[]" value="<?= $n ?>"  checked="checked" /></td>
    <td width="6%"><?= isset($imported_csvdata[$n]['tmpid']) ? $imported_csvdata[$n]['tmpid'] : '&nbsp;' ?></td>
    <td width="10%"><?= isset($imported_csvdata[$n]['role_id']) ? $imported_csvdata[$n]['role_id'] : '&nbsp;' ?></td>
    <td width="17%"><?= isset($imported_csvdata[$n]['username']) ? $imported_csvdata[$n]['username'] : '&nbsp;' ?></td>
    <td width="16%"><?= isset($imported_csvdata[$n]['email']) ? $imported_csvdata[$n]['email'] : '&nbsp;' ?></td>
    <td width="19%"><?= isset($imported_csvdata[$n]['firstname']) ? $imported_csvdata[$n]['firstname'] : '&nbsp;' ?></td>
    <td width="29%"><?= isset($imported_csvdata[$n]['lastname']) ? $imported_csvdata[$n]['lastname'] : '&nbsp;' ?></td>
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
<div class="title title-spacing">
<h2>Step 2</h2>
<a href="<?= site_url("admin/project/import_step/csv/step/1")?>" >&laquo; Back to step1</a>
</div>
<?php
 ?>
<form method="post" action="<?= site_url( "admin/project/import_step/csv/step/2") ?>" enctype="multipart/form-data" >
<p>Select a field that match in the table field.</p>
<table width="100%" border="1"  >
  <tr>
    <td><b>Data</b></td>
    <td><b>Select a column to map to the table</b></td>
    </tr>
<?php
//print_r($_POST['field_list']);
if ( isset($csv_total_fields) ) :
	for($i=0; $i < $csv_total_fields; $i++) :
?> 

  <tr>
    <td width="15%"><input type="text" name="csv_field[<?= $i ?>]" value="<?= isset( $_POST['csv_field'][$i] ) ? $_POST['csv_field'][$i] : $csv_sample_data[$i] ?>" style="width:98%;" readonly="readonly"  /></td>
    <td width="85%"><select name="field_list[<?= $i ?>]"   >
					<option value="-1" >please select</option>    
		<?php foreach( $field_list as $k => $f ) : ?>
					<option value="<?= $f ?>" <?= isset( $_POST['field_list'][$i] ) && ($_POST['field_list'][$i] == $f) ? 'selected="selected"' :  (isset( $csv_fields ) && ($csv_fields[$i] == $f) ? 'selected="selected"' : '' )  ?>  ><?= $f ?></option>        
		<?php endforeach; ?>
					</select></td>
    </tr>
<?php
	endfor;
endif;
?>

</table>
<br />
<input type="submit" name="submit" value="Next" />

<input type="hidden" name="step" value=""  />

</form>
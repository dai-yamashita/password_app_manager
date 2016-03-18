<div class="title title-spacing">
<h2>Step 3</h2>
<a href="<?= site_url("admin/mydomain/import_step/csv/step/2")?>" >&laquo; Back to step2</a>
</div>
<?php
 ?>
<form method="post" action="<?= site_url( "admin/mydomain/import_step/csv/step/3") ?>" enctype="multipart/form-data" >
<div class="hastable"  >

<table width="100%" border="1"   id="sort-table" >
<thead>
  <tr>
    <th><input type="checkbox" name="checkall" value="" id="checkall" checked="checked" /></th>
    <th>project_id </th>
    <th>type</th>
    <th>templateid</th>
    <th>importance</th>
    <th>customtemplate</th>
    <th>url</th>
    <th>username</th>
    <th>changefreq</th>
    </tr>
</thead>    
<?php
pre($imported_csvdata);
if ( isset($imported_csvdata) ) :
	echo "c=" . count($imported_csvdata);
	for($n=0; $n < count($imported_csvdata); $n++) :
	 
?> 

  <tr>
    <td width="3%"><input type="checkbox" name="ischecked[]" value="<?= $n ?>"  checked="checked" /></td>
    <td width="8%"><?= isset($imported_csvdata[$n]['project_id']) ? $imported_csvdata[$n]['project_id'] : '&nbsp;' ?></td>
    <td width="7%"><?= isset($imported_csvdata[$n]['type']) ? $imported_csvdata[$n]['type'] : '&nbsp;' ?></td>
    <td width="8%"><?= isset($imported_csvdata[$n]['templateid']) ? $imported_csvdata[$n]['templateid'] : '&nbsp;' ?></td>
    <td width="16%"><?= isset($imported_csvdata[$n]['importance']) ? $imported_csvdata[$n]['importance'] : '&nbsp;' ?></td>
    <td width="16%"><?= isset($imported_csvdata[$n]['customtemplate']) ? $imported_csvdata[$n]['customtemplate'] : '&nbsp;' ?></td>
    <td width="11%"><?= isset($imported_csvdata[$n]['url']) ? $imported_csvdata[$n]['url'] : '&nbsp;' ?></td>
    <td width="13%"><?= isset($imported_csvdata[$n]['username']) ? $imported_csvdata[$n]['username'] : '&nbsp;' ?></td>
    <td width="18%"><?= isset($imported_csvdata[$n]['changefreq']) ? $imported_csvdata[$n]['changefreq'] : '&nbsp;' ?></td>
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
<div class="title title-spacing">
<h2>Search results</h2>
</div>

<?php !empty($flash) ? flash($flash) : '' ?>

<form action="<?php echo site_url('admin/domain/search') ?>" method="post" >
<ul>
    <li>
        <label  class="desc">Select a User</label>
        <div><select tabindex="3" class="field select small" name="deptid" >
      	    <?php 
foreach($department as $k => $v): 
?>
      	    <option value="<?= $v['deptid'] ?>" <?= ($v['deptid'] == $deptid) ? 'selected=selected' : '' ?> >
      	      <?= $v['department'] ?>
   	        </option>
      	    <?php endforeach; ?>
   	      </select></div>
    </li>
    
        
    
    <li class="buttons">
        <input type="submit" value="Search domain access" class="submit" />
        <input type="button" value="Cancel" class="submit" onclick="document.location.href='<?= site_url('admin/project/browse')?>'" />
    </li>
</ul>
<input type="hidden" name="projectid" value="<?= !empty($results['projectid']) ? $results['projectid'] : '' ?>" />
</form>


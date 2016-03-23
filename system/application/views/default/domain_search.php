<div class="title title-spacing">
<h2>Search domain access</h2>
</div>

<?php !empty($flash) ? flash($flash) : '' ?>

<form action="<?php echo site_url('admin/domain/search') ?>" method="post" >
<ul>
    <li>
        <label  class="desc">Select a User</label>
        <div><select tabindex="3" class="field select small" name="userid" >
      	    <option value="-1" >- select -</option>        
      <?php 
foreach($allusers as $k => $v): 
?>
      	    <option value="<?= $v['id'] ?>" <?= ( isset($_POST['userid']) && $_POST['userid'] == $v['id']) ? 'selected=selected' : ($v['id'] == $userid ? 'selected=selected' : '' ) ?> >
      	      <?= $v['firstname'] ?>
   	        </option>
      	    <?php endforeach; ?>
   	      </select></div>
    </li>
    
<li>
        <label  class="desc">Select a project</label>
        <div><select tabindex="3" class="field select small" name="projectid" >
      	    <option value="-1" >- select -</option>
      	    <?php 
foreach($allprojects as $k => $v): 
?>
      	    <option value="<?= $v['projectid'] ?>" <?= ( isset($_POST['projectid']) && $_POST['projectid'] == $v['projectid']) ? 'selected=selected' : ($v['projectid'] == $projectid ? 'selected=selected' : '') ?> >
      	      <?= $v['project'] ?>
   	        </option>
      	    <?php endforeach; ?>
   	      </select></div>
    </li>

<li>
        <label  class="desc">Select a type</label>
        <div><select tabindex="3" class="field select small" name="type_id" >
      	    <option value="-1" >- select -</option>
      	    <?php 
foreach($allaccounttypes as $k => $v): 
?>
      	    <option value="<?= $v['type_id'] ?>" <?= ( isset($_POST['type_id']) && $_POST['type_id'] == $v['type_id']) ? 'selected=selected' : ($v['type_id'] == $type_id ? 'selected=selected' : '') ?> >
      	      <?= $v['acctype'] ?>
   	        </option>
      	    <?php endforeach; ?>
   	      </select></div>
    </li>            
    
<li class="buttons">
        <input type="submit" value="Search domain access" class="submit" />        
    </li>
</ul>

</form>

<h1 style="font-size:18px">Search results</h1>
<div class="hastable"  >
<table width="100%" border="1"  id="sort-table" >
<thead> 
  <tr>
    <th width="15%">Domain</th>
    <th width="8%">Type</th>
    <th width="9%">Importance</th>
    <th width="15%">URL</th>
    <th width="8%">Username</th>
    <th width="9%">Change<br />frequency </th>
    <th width="10%">Last Changed</th>
    <th width="10%">&nbsp;</th>
    </tr>
</thead>  
<?php
$alt = 0;
if ($results) {
foreach($results as $v): 
$id = $v['domain_id'];
?>
  <tr class="<?= (++$alt%2 == 0) ?  'alt' : '' ?>">
    <td><?= $v['project'] ?></td>
    <td><?= $v['acctype'] ?></td>
    <td><?= $v['importance'] ?></td>
    <td><?= $v['url'] ?></td>
    <td><?= $v['username'] ?></td>
    <td><?= $v['changefreq'] ?></td>
    <td><?= !empty($v['last_modified']) ? date('M d, Y g:i a', $v['last_modified']) : '' ?></td>
    <td>
    
<a class="btn_no_text btn ui-state-default ui-corner-all tooltip" title="view full details" href="<?php echo site_url("admin/domain/view/$id") ?>" >
<span class="ui-icon ui-icon-circle-zoomin"></span>
</a>

  <a class="btn_no_text btn ui-state-default ui-corner-all tooltip" title="People who access" href="<?php echo site_url("admin/domain/assign_user_project/$id") ?>" >
  <span class="ui-icon ui-icon-person"></span>
  </a>
  
      
    </td>
    </tr>
<?php endforeach; ?>  
<?php } else { ?>
	<tr><td colspan="8" ><div align="center">No record found.</div></td></tr>
<?php } ?>
</table>
</div>


<div class="clearfix"></div>
<ul class="pagination">
<?php echo isset($pagination) ? $pagination : '' ?>
</ul>


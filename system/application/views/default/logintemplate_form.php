<div class="title title-spacing">
<?php echo (!empty($results['templateid'])) ? '<h2>Edit logintemplate</h2>' :  '<h2>Add logintemplate</h2>' ?>
</div>

<?php !empty($flash) ? flash($flash) : '' ?>
<?php
$templateid 		= set_value('templateid');
$templateid			= !empty($templateid) ? $templateid : (isset($results['templateid']) ? $results['templateid'] : ''); 
$name 				= set_value('name');
$name				= !empty($name) ? $name : (isset($results['name']) ? $results['name'] : ''); 
$template 			= set_value('template');
$template			= !empty($template) ? $template : (isset($results['template']) ? $results['template'] : ''); 

?>
<form action="<?php echo site_url('admin/logintemplate/form') ?>" method="post" >
<ul>
    <li>
        <label  class="desc">Login template name</label>
        <div><input type="text" tabindex="1" maxlength="255" value="<?= $name  ?>" class="field text small" name="name" /></div>
    </li>
    
    <li>
    <label class="desc">Template Body</label>
    <div>
      <textarea name="template" rows="5" cols="40" class="field textarea small" style="height:300px" ><?= $template ?></textarea>
    </div>
    </li>    
    
    <li class="buttons">
        <input type="submit" value="Save" class="submit" />
        <input type="button" value="Cancel" class="submit" onclick="document.location.href='<?= site_url('admin/logintemplate/browse')?>'" />
    </li>
</ul>
<input type="hidden" name="templateid" value="<?= $templateid?>" />
</form>


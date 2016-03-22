<?php

$title		= isset($results['title']) ? $results['title'] : '';
$alert		= isset($results['alert']) ? $results['alert'] : '';
$created	= isset($results['created']) ? $results['created'] : '';
$from		= isset($results['from']) ? $results['from'] : '';
$isread		= isset($results['isread']) ? $results['isread'] : '';

?><div class="title title-spacing">
    <h2>View alert message</h2>
</div>
 
<ul>
<li><label class="desc">Title</label>
    <div class="val" >
	<?= $title ?>  
    </div>
</li>
<li><label class="desc">Message</label>
<div class="val" >
  	<?= $alert ?>  
</div>
</li>
 
</ul>

<input type="button" value="&laquo;Back" class="submit" onclick="document.location.href='<?= site_url('admin/alert/browse')?>'" />
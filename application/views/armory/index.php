<h2>Characters linked to account</h2>
<ul>
<?php 
if(!is_array($characters)){
    $characters = array();?>
    <li><strong>No exiles found linked to your account.</strong> <a href="/armory/import">Import?</a></li>
<?php
}
foreach($characters as $character){?>
    <li><a href="/exile/<?php echo $character['name'];?>"><?php echo $character['name'];?></a> (<?php echo $character['level']?>) <?php echo $character['league'];?> <?php echo $character['class'];?></li>
<?php }?>
</ul>
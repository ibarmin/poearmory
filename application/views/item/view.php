<?php if($item['type_line']){?><h3><?php echo $item['type_line'];?></h3><?php }?>
<?php if($item['flavour']){?><strong><?php echo $item['flavour'];?></strong><?php }?>
<h3>Owners: *</h3>
<ul>
<?php foreach($characters as $character){?>
    <li><a href="/exile/<?php echo $character['name'];?>"><?php echo $character['name'];?></a> (<?php echo date("d.m.Y", strtotime($character['equipped']));?>)</li>
<?php } ?>
</ul>
<p class="hint">* Item identity is based on name, type, rarity, sockets, properties, implicit and explicit mods, requirements. Date is date when item was first equipped on exile.</p>
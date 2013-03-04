<h2><?php echo $character['league'] . ' ' . $character['class'] . ' (' . $character['level'] . ')';?></h2>
<div id="character" class="<?php echo strtolower($character['class']);?>-character">
<?php foreach($character['inventory'] as $name => $items){?>
    <div class="inventory inventory-<?php echo strtolower($name);?>">
    <?php foreach($items as $item){?>
        <a href="/item/<?php echo $item['id'];?>" class="<?php echo $item['type'];?> iteminfo" rel="item-<?php echo $item['id'];?>"><img src="<?php echo $item['icon'];?>" alt="<?php echo $item['name'] . ' ' . $item['type_line'];?>" /></a></li>
    <?php }?>
    </div>
<?php }?>
</div>
<div id="item-holder">
<?php foreach($character['inventory'] as $items){?>
    <?php foreach($items as $item){?>
    <div id="item-<?php echo $item['id']?>" class="<?php echo $item['type'];?>">
        <div class="itemHeader">
            <div class="itemName"><h3><?php echo $item['name'];?></h3></div>
            <div class="typeLine"><?php echo $item['type_line'];?></div>
        </div>
        <div class="itemBody">
        <?php foreach($item['property'] as $i){?>
            <div class="property"><?php echo $i['name'];?>: <?php print_r($i['values']);?></div>
        <?php }
        if(count($item['requirement'])){?>
            <hr />
            <div class="requirement">Requires <?php echo implode(', ', $item['requirement']);?></div>
<?php   }?>
        <?php if(count($item['implicit'])){?>
            <hr />
        <?php 
            }
            foreach($item['implicit'] as $i){?>
            <div class="implicit"><?php echo $i['name'];?></div>
        <?php }
            if(count($item['explicit'])){?>
            <hr />
        <?php 
            }
            foreach($item['explicit'] as $i){?>
            <div class="explicit"><?php echo $i['name'];?></div>
        <?php }?>
        </div>
    </div>
    <?php }?>
<?php }?>
    
</div>
[<a href="/armory/refresh/<?php echo $character['name'];?>">refresh</a>]
<a href="<?php echo $character['url'];?>">Passive Tree</a>

<script type="text/javascript">
    $(function(){
        $('#character').tooltip({items: '.iteminfo', content: function(){
            return $('#' + $(this).attr('rel')).html();
        }});
    });
</script>
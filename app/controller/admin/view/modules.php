<!-- currently, too few items for carousel to work properly but code is place when more moduels are added -->
<div class="module-container carousel slide" id="module-slide">
	<!-- <div class="arrowl"><a class="left" href="#module-slide" data-slide="prev"><img src="/images/left.jpg" width="28" height="38" />&lsaquo;</a></div> -->
	<div class="carousel-inner">
		<div class="item active">
			<?php foreach ($this->modules as $m):?>
				<div class="module" style="background-color:#<?php echo $m['color'] ? $m['color'] : '3A87AD';?>" id="module-<?php echo $this->escape($m['id']);?>" draggable="true" ondragstart="ss.admin.timeline.drag(event)" data-module-id="<?php echo $this->escape($m['id']);?>"><?php echo $this->escape($m['name']);?><span class="down-arrow">&#x25BC;</span></div>
			<?php endforeach;?>
		</div>
	</div>
	<!-- <div class="arrow2"><a class="right" href="#module-slide" data-slide="next"><img src="/images/right.jpg" width="28" height="38" />&rsaquo;</a></div> -->
</div>

<!-- <div id="module-slide" class="span03 carousel slide">
	<div class="carousel-inner">
		<div class="item active">
			<div class="span331 stest">- - Brand Module - -</div>
			<div class="span331 stest2">- - Who's Wearing What? - -</div>
			<div class="span331 stest">- - Purchase Now Module - -</div>
			<div class="span331 stest">- - Sample Module - -</div>
		</div>
		<div class="item">
			<div class="span331 stest">- - Brand Module 2 - -</div>
			<div class="span331 stest2">- - Who's Wearing What? 2 - -</div>
			<div class="span331 stest">- - Purchase Now Module 2 - -</div>
			<div class="span331 stest">- - Sample Module 2 - -</div>
		</div>
	</div>
	<a class="carousel-control left" href="#module-slide" data-slide="prev">&lsaquo;</a>
  	<a class="carousel-control right" href="#module-slide" data-slide="next">&rsaquo;</a>
</div> -->

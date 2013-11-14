<div id="timeline-container">
	<?php
	// resolution logic
	if ($this->resolution == 0) {
		$resolution = 1; // seconds
	} elseif ($this->resolution == 1) {
		$resolution = 60; // minutes
	} elseif ($this->resolution == 2) {
		$resolution = 3600; // hours
	}

	$duration = $this->video['duration'];
	$tick_width = 80;
	$tick_steps = ceil($duration / $resolution);
	$max_width = $tick_width * $tick_steps;
	
	// parse our events in an array structure that 
	// will be more efficent for our timeline loop
	$events = array();
	foreach ($this->events as $e) {
		$tick_start = floor($e['start_second'] / $resolution);
		$spans = (int) ceil($e['duration'] / $resolution);
		$e['spans'] = $spans;
		if (!isset($events[$tick_start])) {
			$events[$tick_start] = array();
		}
		
		$events[$tick_start][] = $e;

		for ($x = 1; $x < $spans; $x++) {
			$tick = $tick_start + $x;

			if (!isset($events[$tick])) {
				$events[$tick] = array();
			}
			$e['dummy'] = true;
			$events[$tick][] = $e; 
		}
	}
	?>
    <div id="timeline-wrapper" style="width:<?php echo $max_width + 20;?>px">
		<?php for ($i = 0; $i < $tick_steps; $i++):?>
		<?php
			if ($this->resolution == 0) {
				$hours = (int) ($i / 3600);
				$minutes = (int) (($i - ($hours * 3600)) / 60);
				$seconds =  $i - ($hours * 3600) - ($minutes * 60);
				if ($hours < 10) {
					$hours = '0' . $hours;
				}
				
				if ($minutes < 10) {
					$minutes = '0' . $minutes;
				}
				
				if ($seconds < 10) {
					$seconds = '0' . $seconds;
				}

				$label = $hours . ':' . $minutes . ':' . $seconds; // seconds
			} elseif ($this->resolution == 1) {
				$hours = (int) ($i / 60);
				$minutes = (int) ($i - ($hours * 60));
				if ($hours < 10) {
					$hours = '0' . $hours;
				}
				
				if ($minutes < 10) {
					$minutes = '0' . $minutes;
				}
				
				$seconds = '00';
				
				$label = $hours . ':' . $minutes . ':' . $seconds; // seconds
			} elseif ($this->resolution == 2) {
				$hours = $i;
				if ($hours < 10) {
					$hours = '0' . $hours;
				}

				$minutes = '00';
				$seconds = '00';
				
				$label = $hours . ':' . $minutes . ':' . $seconds; // seconds
			}
		?>
        <div ondrop="ss.admin.timeline.drop(event)" ondragover="ss.admin.timeline.allowDrop(event)" class="timeline-tick" data-tick="<?php echo ($i * $resolution);?>"><?php echo $label;?>
        <?php if (isset($events[$i])):?>
			<?php foreach ($events[$i] as $e):?>
			<?php if (!isset($e['dummy'])):?>
			<div class="timeline-module" style="background-color:#<?php echo $e['color'] ? $e['color'] : '3A87AD';?>;width:<?php echo ($e['spans'] * 62) + (($e['spans'] - 1) * 18);?>px;" data-event-id="<?php echo $e['id'];?>" id="event-<?php echo $e['id'];?>" draggable="true" onclick="ss.admin.timeline.overlay(<?php echo $e['id'];?>)" ondragstart="ss.admin.timeline.drag(event)" data-module-id="<?php echo $e['module_id'];?>"><?php echo $this->escape($e['module_name']);?><i class="module-settings icon-cog icon-white"></i></div>
			<?php else:?>
			<div class="dummy-module" id="dummy-<?php echo $e['id'];?>"></div>
			<?php endif;?>
			<?php endforeach;?>
        <?php endif;?>
        </div>
        <?php endfor;?>
        <div class="timeline-hairline"></div>
    </div>
</div>

<?php include $this->template('_modal');?>



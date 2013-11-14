<div id="vip">
<div class="title-head">
<div class="title-text">Video Description</div>

</div><p>
<textarea class="dtext">
<?php if (trim($this->video['episode_description']) == ''):?>
Edit description here
<?php else:?>
<?php echo $this->escape($this->video['episode_description']);?>
<?php endif;?>
</textarea>
<div data-episode-id="<?php echo $this->video['episode_id'];?>" class="description_save">SAVE DESCRIPTION<img src="/images/edit.png" width="14" height="14" class="edit" alt=""/></div>
<div class="publish_save"><a href="#" data-video-id="<?php echo $this->video['id'];?>" class="publish-video"><?php echo ($this->video['status'] == App_Model_Videos::$statuses['published']) ? 'PUBLISHED' : 'PUBLISH VIDEO';?></a></div>

</div>

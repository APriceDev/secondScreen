<div id="vip">
	<div class="title-head videoplayer">Video Player</div>
	<div class="video-body">
		<video id="my_video_1" class="video-js vjs-default-skin" controls preload="auto" width="460" height="220" data-setup="{}">
		  <source src="http://<?php echo $this->video['s3_bucket'];?>.s3.amazonaws.com/<?php echo $this->video['s3_filename'];?>" type='video/mp4'>
		</video>
		
	</div><p>
</div>

<link href="/video-js/video-js.css" rel="stylesheet">
<script src="/video-js/video.js"></script>
<script>
  _V_.options.flash.swf = "/video-js/video-js.swf"
</script>

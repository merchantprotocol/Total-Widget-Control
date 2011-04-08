<?php 
/**
 * @Author	Jonathon byrd
 * @link http://www.jonathonbyrd.com
 * @Package Wordpress
 * @SubPackage Total Widget Control
 * @copyright Proprietary Software, Copyright Byrd Incorporated. All Rights Reserved
 * @Since 1.0
 * 
 * 
 */

defined('ABSPATH') or die("Cannot access pages directly.");
if ( TWC_CURRENT_USER_CANNOT ) wp_die('');

?>
<link rel="stylesheet" href="<?php echo plugin_dir_url(dirname(__file__)); ?>css/video-js.css" type="text/css" media="screen" title="Video JS">
<div class="video-js-box-border">
	<div class="video-js-box">
		<!-- Using the Video for Everybody Embed Code http://camendesign.com/code/video_for_everybody -->
		<video id="example_video_1" class="video-js" width="537px" height="302px" controls="controls" preload="auto" poster="http://www.totalwidgetcontrol.com/assets/total-widget-listing.png">
			
			<source src="http://www.totalwidgetcontrol.com/assets/getting-to-know-twc.mp4" type='video/mp4; codecs="avc1.42E01E, mp4a.40.2"' />
			 
			<!-- Flash Fallback. Use any flash video player here. Make sure to keep the vjs-flash-fallback class. -->
			<object id="flash_fallback_1" class="vjs-flash-fallback" width="537px" height="302px" type="application/x-shockwave-flash"
				data="http://releases.flowplayer.org/swf/flowplayer-3.2.1.swf">
				<param name="movie" value="http://releases.flowplayer.org/swf/flowplayer-3.2.1.swf" />
				<param name="allowfullscreen" value="true" />
				<param name="flashvars" value='config={"playlist":["http://www.totalwidgetcontrol.com/assets/total-widget-listing.png", {"url": "http://www.totalwidgetcontrol.com/assets/getting-to-know-twc.mp4","autoPlay":true,"autoBuffering":true}]}' />
				<!-- Image Fallback. Typically the same as the poster image. -->
				<img src="http://www.totalwidgetcontrol.com/assets/total-widget-listing.png" width="537px" height="302px" alt="Poster Image"
					title="No video playback capabilities." />
			</object>
			
		</video>
	</div>
</div>
<script src="<?php echo plugin_dir_url(dirname(__file__)); ?>js/video.js" type="text/javascript" charset="utf-8"></script>
<script type="text/javascript">VideoJS.setupAllWhenReady();</script>
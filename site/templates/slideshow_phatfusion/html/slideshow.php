<?php
defined('_JEXEC') or die('Restricted access');
global $mainframe, $rsgConfig;
//Add stylesheets and scripts to header
$css1 = "<link rel=\"stylesheet\" href=\"components/com_rsgallery2/templates/slideshow_phatfusion/css/slideshow.css\" type=\"text/css\" media=\"screen\" charset=\"utf-8\" />";
$mainframe->AddCustomHeadTag($css1);

JHTML::_("behavior.mootools");
$js1 = "<script src=\"components/com_rsgallery2/templates/slideshow_phatfusion/js/backgroundSlider.js\" type=\"text/javascript\"></script>";
$mainframe->AddCustomHeadTag($js1);
$js2 = "<script src=\"components/com_rsgallery2/templates/slideshow_phatfusion/js/slideshow.js\" type=\"text/javascript\"></script>";
$mainframe->AddCustomHeadTag($js2);
?>
<!-- Override default CSS styles -->
<style>
.slideshowContainer {
	border: 1px solid #ccc;
	width: 400px;
	height: 300px;
	margin-bottom: 5px;
}
</style>
<!-- show main slideshow screen -->
<div id="container">
	<h3><?php echo $this->galleryname;?></h3>
	<div id="slideshowContainer" class="slideshowContainer"></div>
	<div id="thumbnails">
		<?php echo $this->slides;?>
  		<p>
  			<a href="#" onclick="show.previous(); return false;">&lt;&lt; Previous</a> |
  			<a href="#" onclick="show.play(); return false;">Play</a> | 
  			<a href="#" onclick="show.stop(); return false;">Stop</a> | 
  			<a href="#" onclick="show.next(); return false;">Next &gt;&gt;</a>
  		</p>
  	</div>
  	<!-- Set parameters for slideshow -->
	<script type="text/javascript">
  	window.addEvent('domready',function(){
		var obj = {
			wait: 3000, 
			effect: 'fade',
			duration: 1000, 
			loop: true, 
			thumbnails: true,
			backgroundSlider: true,
			onClick: function(i){alert(i)}
		}
		show = new SlideShow('slideshowContainer','slideshowThumbnail',obj);
		show.play();
	});
	</script>
</div><!-- end container -->
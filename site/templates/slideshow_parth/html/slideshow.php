<?php
defined('_JEXEC') or die('Restricted access');
global $mainframe, $rsgConfig;
//Add stylesheets and scripts to header
$css1 = "<link rel=\"stylesheet\" href=\"components/com_rsgallery2/templates/slideshow_parth/css/jd.gallery.css\" type=\"text/css\" media=\"screen\" charset=\"utf-8\" />";
$mainframe->AddCustomHeadTag($css1);
$css2 = "<link rel=\"stylesheet\" href=\"components/com_rsgallery2/templates/slideshow_parth/css/template.css\" type=\"text/css\" media=\"screen\" charset=\"utf-8\" />";
$mainframe->AddCustomHeadTag($css2);

JHTML::_("behavior.mootools");

$js2 = "<script src=\"components/com_rsgallery2/templates/slideshow_parth/js/jd.gallery.js\" type=\"text/javascript\"></script>";
$mainframe->AddCustomHeadTag($js2);
?>
<!-- Override default CSS styles -->
<style>
#myGallery, #myGallerySet, #flickrGallery
{
	width: <?php echo $rsgConfig->get('image_width');?>px;
	
}
/* Background color for the slideshow element */
.jdGallery .slideElement
{
	background-color: #000;
}
</style>
<script type="text/javascript">
	function startGallery() {
		var myGallery = new gallery($('myGallery'), {
			/* Automated slideshow */
			timed: true,
			/* Show the thumbs carousel */
			showCarousel: true,
			/* Text on carousel tab */
			textShowCarousel: 'Thumbs',
			/* Thumbnail height */
			thumbHeight: 50,
			/* Thumbnail width*/
			thumbWidth: 50,
			/* Fade duration in milliseconds (500 equals 0.5 seconds)*/
			fadeDuration: 500,
			/* Delay in milliseconds (6000 equals 6 seconds)*/
			delay: 6000
		});
	}
	window.addEvent('domready',startGallery);
</script>
<div class="content">
	<div style="float: right;"><A href="index.php?option=com_rsgallery2&gid=<?php echo $this->gid;?>">Back to gallery</a></div>
	<div class="rsg2-clr"></div>
	<div style="text-align:center;font-size:24px;"><?php echo $this->galleryname;?></div>
	<div class="rsg2-clr"></div>
	<div id="myGallery">
		<?php echo $this->slides;?>
	</div><!-- end myGallery -->
</div><!-- End content -->
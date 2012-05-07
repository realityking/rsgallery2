<?php defined('_JEXEC') or die('Restricted access'); ?>

<?php
global $mainframe;

$firstImage = $this->gallery->getItem();
$firstImage = $firstImage->display();
?>

<div class="rsg2-slideshowone">

<form name="_slideShow">
<input type="Hidden" name="currSlide" value="0">
<input type="Hidden" name="delay">

<?php if( ! $this->cleanStart ): ?>

<a href="javascript:;" onclick="startSS()">
	<img src="<?php echo JURI_SITE;?>/components/com_rsgallery2/images/start.jpg" alt="<?php echo JText::_('Start') ?>" width="24" height="24" border="0">
</a>
<a href="javascript:;" onclick="stopSS()">
	<img src="<?php echo JURI_SITE;?>/components/com_rsgallery2/images/stop.jpg" alt="<?php echo JText::_('Stop') ?>" width="24" height="24" border="0">
</a>
<a href="javascript:;" onclick="prevSS()">
	<img src="<?php echo JURI_SITE;?>/components/com_rsgallery2/images/previous.jpg" alt="<?php echo JText::_('Previous') ?>" width="24" height="24" border="0">
</a>
<a href="javascript:;" onclick="nextSS()">
	<img src="<?php echo JURI_SITE;?>/components/com_rsgallery2/images/next.jpg" alt="<?php echo JText::_('Next') ?>" width="24" height="24" border="0">
</a>

<?php endif; ?>

<div style="visibility:hidden;">
	<select name="wichIm" onchange="selected(this.options[this.selectedIndex].value)">
	</select>
</div>


<img name="stage" border="0" src="<?php echo $firstImage->url(); ?>" style="filter: revealtrans(); font-size:12;">


</form>



<script type="text/javascript">
<!--
/* 
SlideShow. Written by PerlScriptsJavaScripts.com
Copyright http://www.perlscriptsjavascripts.com 
Free and commercial Perl and JavaScripts     
*/

effect      = 23;// transition effect. number between 0 and 23, 23 is random effect
duration    = 1.5;// transition duration. number of seconds effect lasts
display     = 4;// seconds to diaply each image?
oW          = 400;// width of stage (first image)
oH          = 400;// height of stage
zW          = 40;// zoom width by (add or subtracts this many pixels from image width)
zH          = 30;// zoom height by 

// path to image/name of image in slide show. this will also preload all images
// each element in the array must be in sequential order starting with zero (0)
SLIDES = new Array();
//Echo JS-array from DB-query here

<?php echo $this->slides;?>

// end required modifications

S = new Array();
for(a = 0; a < SLIDES.length; a++){
    S[a] = new Image(); S[a].src  = SLIDES[a][0];
}

f = document._slideShow;
n = 0;
t = 0;

//document.images["stage"].width  = oW;
//document.images["stage"].height = oH;
f.delay.value = display;

function startSS(){
    t = setTimeout("runSS(" + f.currSlide.value + ")", 1 * 1);
}

function runSS(n){
    n++;
    if(n >= SLIDES.length){
        n = 0;
    }

    document.images["stage"].src = S[n].src;
    if(document.all && navigator.userAgent.indexOf("Opera") < 0 && navigator.userAgent.indexOf("Windows") >= 0){
        document.images["stage"].style.visibility = "hidden";
        document.images["stage"].filters.item(0).apply();
        document.images["stage"].filters.item(0).transition = effect;
        document.images["stage"].style.visibility = "visible";
        document.images["stage"].filters(0).play(duration);
    }
    f.currSlide.value = n;
    t = setTimeout("runSS(" + f.currSlide.value + ")", f.delay.value * 1000);
}

function stopSS(){
    if(t){
        t = clearTimeout(t);
    }
}

function nextSS(){
    stopSS();
    n = f.currSlide.value;
    n++;
    if(n >= SLIDES.length){
        n = 0;
    }
    if(n < 0){
        n = SLIDES.length - 1;
    }
    document.images["stage"].src = S[n].src;
    f.currSlide.value = n;
    if(document.all && navigator.userAgent.indexOf("Opera") < 0 && navigator.userAgent.indexOf("Windows") >= 0){
        document.images["stage"].style.visibility = "hidden";
        document.images["stage"].filters.item(0).apply();
        document.images["stage"].filters.item(0).transition = effect;
        document.images["stage"].style.visibility = "visible";
        document.images["stage"].filters(0).play(duration);
    }
}

function prevSS(){
    stopSS();
    n = f.currSlide.value;
    n--;
    if(n >= SLIDES.length){
        n = 0;
    }
    if(n < 0){
        n = SLIDES.length - 1;
    }
    document.images["stage"].src = S[n].src;
    f.currSlide.value = n;
    
    if(document.all && navigator.userAgent.indexOf("Opera") < 0 && navigator.userAgent.indexOf("Windows") >= 0){
        document.images["stage"].style.visibility = "hidden";
        document.images["stage"].filters.item(0).apply();
        document.images["stage"].filters.item(0).transition = effect;
        document.images["stage"].style.visibility = "visible";
        document.images["stage"].filters(0).play(duration);
    }
}

function selected(n){
    stopSS();
    document.images["stage"].src = S[n].src;
    f.currSlide.value = n;
    
    if(document.all && navigator.userAgent.indexOf("Opera") < 0 && navigator.userAgent.indexOf("Windows") >= 0){
        document.images["stage"].style.visibility = "hidden";
        document.images["stage"].filters.item(0).apply();
        document.images["stage"].filters.item(0).transition = effect;
        document.images["stage"].style.visibility = "visible";
        document.images["stage"].filters(0).play(duration);
    }
}

function zoom(dim1, dim2){
    if(dim1){
        if(document.images["stage"].width < oW){
            document.images["stage"].width   = oW;
            document.images["stage"].height  = oH;
        } else {
            document.images["stage"].width  += dim1;
            document.images["stage"].height += dim2;
        }
        if(dim1 < 0){
            if(document.images["stage"].width < oW){
                document.images["stage"].width   = oW;
                document.images["stage"].height  = oH;
            }
        }
    } else {
        document.images["stage"].width   = oW;
        document.images["stage"].height  = oH;
    }
}

// start slideshow right once dom is ready (uses mootools)

Window.onDomReady(function() {runSS( f.currSlide.value ); });

// -->
</script>

<?php if( $this->cleanStart ): ?>
<script type="text/javascript">
	startSS();
</script>
<?php endif; ?>

</div>

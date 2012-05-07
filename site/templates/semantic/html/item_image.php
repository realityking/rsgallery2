<?php
	defined('_JEXEC') or die('Restricted access');

	$item = $this->currentItem;
	$watermark = $rsgConfig->get('watermark');

	$imageOriginalUrl = $watermark ? waterMarker::showMarkedImage( $item->name, 'original' ) : 
		imgUtils::getImgOriginal( $item->name );
		
	$imageUrl = $watermark ? waterMarker::showMarkedImage( $item->name ) : 
		imgUtils::getImgDisplay( $item->name );

	switch ($rsgConfig->get('displayPopup')) {
		//No popup
		case 0:{
					?>
					<img class="rsg2-displayImage" src="<?php echo $imageUrl;?>" alt="<?php echo $item->name; ?>" title="<?php echo $item->name; ?>" />
		<?php
		break;
	}
	//Normal popup
	case 1:{
					?><a href="<?php echo $imageOriginalUrl; ?>" target="_blank">
					<img class="rsg2-displayImage" src="<?php echo $imageUrl;?>" alt="<?php echo $item->name; ?>" title="<?php echo $item->name; ?>" />
					</a>
			<?php
			break;
		}
		case 2:{
			JHTML::_('behavior.modal');
		$jsModal = '
				window.addEvent("domready", function() {
				SqueezeBox.initialize({});
				var img = $$("img.rsg2-displayImage")[0];
				img.addEvent("click", function(e)
				{
				new Event(e).stop();
				SqueezeBox.fromElement(img,{url:"' . $imageOriginalUrl .'", 
					classWindow:"rsg2", 
					classOverlay:"rsg2",
					onOpen:function(img){
					var p = new Element("p", {class:"rsg2-popup-title"});
					$(p).appendText("'. strip_tags($item->name) . '");
					$(p).inject(img);
					var pSize1 = $(p).getSize().size;
					p = new Element("p", {class:"rsg2-popup-description"});
					$(p).appendText("'. strip_tags($item->descr) . '");
					$(p).inject(img);
					var pSize2 = $(p).getSize().size;
					size = SqueezeBox.size;
					size.y += pSize1.y + pSize2.y;
					SqueezeBox.resize(size, true);
					}});		
					});
					});';
			
					?>
					<img class="rsg2-displayImage" src="<?php echo $imageUrl;?>" alt="<?php echo $item->name; ?>" title="<?php echo $item->name; ?>" />
			<?php
			$doc =& JFactory::getDocument();
			$doc->addScriptDeclaration($jsModal);
			break;
		}
	}

?>
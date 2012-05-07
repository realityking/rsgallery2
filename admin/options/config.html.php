<?php
/**
* Galleries option for RSGallery2 - HTML display code
* @version $Id: config.html.php 1013 2011-02-07 17:06:58Z mirjam $
* @package RSGallery2
* @copyright (C) 2003 - 2011 RSGallery2
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* RSGallery is Free Software
*/

defined( '_JEXEC' ) or die( 'Restricted Access' );

/**
 * Explain what this class does
 * @package RSGallery2
 */
class html_rsg2_config{
    
    
    /**
     * raw configuration editor, debug only
     */
    function config_rawEdit(){
        global $rsgConfig, $option;
        $config = get_object_vars( $rsgConfig );

        ?>
        <form action="index2.php" method="post" name="adminForm" id="adminForm">
        <table id='rsg2-config_rawEdit' align='left'>
        <?php foreach( $config as $name => $value ): ?>
            <tr>
                <td><?php echo $name; ?></td>
                <td><input type='text' name='<?php echo $name; ?>' value='<?php echo $value; ?>'></td>
            </tr>
            
        <?php endforeach; ?>
        </table>
        <input type="hidden" name="option" value="<?php echo $option;?>" />
        <input type="hidden" name="task" value="config_rawEdit_save" />
        </form>
        <?php
    }
    
    /**
     * Shows the configuration page.
     * @todo get rid of patTemplate!!!
    **/
	function showconfig( &$lists ){
		global $rsgConfig, $mainframe;

		$config = $rsgConfig;
		
		//Exif tags
		$exifTagsArray = array(
				"resolutionUnit" 		=> "Resolution unit",
			    "FileName" 				=> "Filename",
			    "FileSize" 				=> "Filesize",
			    "FileDateTime" 			=> "File Date",
			    "FlashUsed" 			=> "Flash used",
			    "imageDesc" 			=> "Image description",                              
			    "make" 					=> "Camera make",
			    "model" 				=> "Camera model",
			    "xResolution" 			=> "X Resolution",
			    "yResolution" 			=> "Y Resolution",
			    "software" 				=> "Software used",
			    "fileModifiedDate" 		=> "File modified date",
			    "YCbCrPositioning" 		=> "YCbCrPositioning",
			    "exposureTime" 			=> "Exposure time",
			    "fnumber" 				=> "f-Number",
			    "exposure" 				=> "Exposure",
			    "isoEquiv" 				=> "ISO equivalent",
			    "exifVersion" 			=> "EXIF version",
			    "DateTime" 				=> "Date & time",
			    "dateTimeDigitized" 	=> "Original date",
			    "componentConfig" 		=> "Component config",
			    "jpegQuality" 			=> "Jpeg quality",
			    "exposureBias" 			=> "Exposure bias",
			    "aperture" 				=> "Aperture",
			    "meteringMode" 			=> "Metering Mode",
			    "whiteBalance" 			=> "White balance",
			    "flashUsed" 			=> "Flash used",
			    "focalLength" 			=> "Focal lenght",
			    "makerNote" 			=> "Maker note",
			    "subSectionTime" 		=> "Subsection time",
			    "flashpixVersion" 		=> "Flashpix version",
			    "colorSpace" 			=> "Color Space",
			    "Width" 				=> "Width",
			    "Height" 				=> "Height",
			    "GPSLatitudeRef" 		=> "GPS Latitude reference",
			    "Thumbnail" 			=> "Thumbnail",
			    "ThumbnailSize" 		=> "Thumbnail size",
			    "sourceType" 			=> "Source type",
			    "sceneType" 			=> "Scene type",
			    "compressScheme" 		=> "Compress scheme",
			    "IsColor" 				=> "Color or B&W",
			    "Process" 				=> "Process",
			    "resolution" 			=> "Resolution",
			    "color" 				=> "Color",
			    "jpegProcess" 			=> "Jpeg process"
		);
		//Format selected items
		$exifSelected = explode("|", $config->exifTags);
		foreach ($exifSelected as $select) {
			$exifSelect[] = JHTML::_("select.option",$select,$select);
		}
		//Format values for dropdownbox
		foreach ($exifTagsArray as $key=>$value) {
			$exif[] = JHTML::_("select.option",$key,$key);
		}
		
		//Format values for slideshow dropdownbox
		$folders = JFolder::folders(JPATH_RSGALLERY2_SITE . '/templates');
		foreach ($folders as $folder) {
			if (preg_match("/slideshow/i", $folder)) {
				$current_slideshow[] = JHTML::_("select.option",$folder,$folder);
			}
		}
		
		// front display
		$display_thumbs_style[] = JHTML::_("select.option",'table',JText::_('Table'));
		$display_thumbs_style[] = JHTML::_("select.option",'float',JText::_('Float'));
		$display_thumbs_style[] = JHTML::_("select.option",'magic',JText::_('Magic(not supported yet!)'));
		
		$display_thumbs_floatDirection[] = JHTML::_("select.option",'left',JText::_('Left to Right'));
		$display_thumbs_floatDirection[] = JHTML::_("select.option",'right',JText::_('Right to Left'));
		
		$thumb_style[] = JHTML::_("select.option",'0',JText::_('Proportional'));
		$thumb_style[] = JHTML::_("select.option",'1',JText::_('SQUARE'));
		
		$thum_order[] = JHTML::_("select.option",'ordering',JText::_('Default'));
		$thum_order[] = JHTML::_("select.option",'date',JText::_('Date'));
		$thum_order[] = JHTML::_("select.option",'name',JText::_('Name'));
		$thum_order[] = JHTML::_("select.option",'rating',JText::_('Rating'));
		$thum_order[] = JHTML::_("select.option",'hits',JText::_('Hits'));
		
		$thum_order_direction[] = JHTML::_("select.option",'ASC',JText::_('Ascending'));
		$thum_order_direction[] = JHTML::_("select.option",'DESC',JText::_('Descending'));
		
		$resizeOptions[] = JHTML::_("select.option",'0',JText::_('Default Size'));
		$resizeOptions[] = JHTML::_("select.option",'1',JText::_('Resize larger pics'));
		$resizeOptions[] = JHTML::_("select.option",'2',JText::_('Resize smaller pics'));
		$resizeOptions[] = JHTML::_("select.option",'3',JText::_('Resize pics to fit'));
		
		$displayPopup[] = JHTML::_("select.option",'0',JText::_('No popup'));
		$displayPopup[] = JHTML::_("select.option",'1',JText::_('Normal popup'));
		$displayPopup[] = JHTML::_("select.option",'2',JText::_('Joomla Modal'));
		
		//Number of galleries dropdown field
		$dispLimitbox[] = JHTML::_("select.option",'0',JText::_('Never'));
		$dispLimitbox[] = JHTML::_("select.option",'1',JText::_('If more galleries than limit'));
		$dispLimitbox[] = JHTML::_("select.option",'2',JText::_('Always'));
		
		$galcountNrs[] = JHTML::_("select.option",'5','5');
		$galcountNrs[] = JHTML::_("select.option",'10','10');
		$galcountNrs[] = JHTML::_("select.option",'15','15');
		$galcountNrs[] = JHTML::_("select.option",'20','20');
		$galcountNrs[] = JHTML::_("select.option",'25','25');
		$galcountNrs[] = JHTML::_("select.option",'30','30');
		$galcountNrs[] = JHTML::_("select.option",'50','50');
		
		// watermark
		$watermarkAngles[] = JHTML::_("select.option",'0','0');
		$watermarkAngles[] = JHTML::_("select.option",'45','45');
		$watermarkAngles[] = JHTML::_("select.option",'90','90');
		$watermarkAngles[] = JHTML::_("select.option",'135','135');
		$watermarkAngles[] = JHTML::_("select.option",'180','180');
		
		$watermarkPosition[] = JHTML::_("select.option",'1',JText::_('Top left'));
		$watermarkPosition[] = JHTML::_("select.option",'2',JText::_('Top Center'));
		$watermarkPosition[] = JHTML::_("select.option",'3',JText::_('Top right'));
		$watermarkPosition[] = JHTML::_("select.option",'4',JText::_('Left'));
		$watermarkPosition[] = JHTML::_("select.option",'5',JText::_('Center'));
		$watermarkPosition[] = JHTML::_("select.option",'6',JText::_('Right'));
		$watermarkPosition[] = JHTML::_("select.option",'7',JText::_('Bottom left'));
		$watermarkPosition[] = JHTML::_("select.option",'8',JText::_('Bottom center'));
		$watermarkPosition[] = JHTML::_("select.option",'9',JText::_('Bottom right'));
		
		$watermarkFontSize[] = JHTML::_("select.option",'5','5');
		$watermarkFontSize[] = JHTML::_("select.option",'6','6');
		$watermarkFontSize[] = JHTML::_("select.option",'7','7');
		$watermarkFontSize[] = JHTML::_("select.option",'8','8');
		$watermarkFontSize[] = JHTML::_("select.option",'9','9');
		$watermarkFontSize[] = JHTML::_("select.option",'10','10');
		$watermarkFontSize[] = JHTML::_("select.option",'11','11');
		$watermarkFontSize[] = JHTML::_("select.option",'12','12');
		$watermarkFontSize[] = JHTML::_("select.option",'13','13');
		$watermarkFontSize[] = JHTML::_("select.option",'14','14');
		$watermarkFontSize[] = JHTML::_("select.option",'15','15');
		$watermarkFontSize[] = JHTML::_("select.option",'16','16');
		$watermarkFontSize[] = JHTML::_("select.option",'17','17');
		$watermarkFontSize[] = JHTML::_("select.option",'18','18');
		$watermarkFontSize[] = JHTML::_("select.option",'19','19');
		$watermarkFontSize[] = JHTML::_("select.option",'20','20');
		$watermarkFontSize[] = JHTML::_("select.option",'22','22');
		$watermarkFontSize[] = JHTML::_("select.option",'24','24');
		$watermarkFontSize[] = JHTML::_("select.option",'26','26');
		$watermarkFontSize[] = JHTML::_("select.option",'28','28');
		$watermarkFontSize[] = JHTML::_("select.option",'30','30');
		$watermarkFontSize[] = JHTML::_("select.option",'36','36');
		$watermarkFontSize[] = JHTML::_("select.option",'40','40');
	
		$watermarkTransparency[] = JHTML::_("select.option",'0','0');
		$watermarkTransparency[] = JHTML::_("select.option",'10','10');
		$watermarkTransparency[] = JHTML::_("select.option",'20','20');
		$watermarkTransparency[] = JHTML::_("select.option",'30','30');
		$watermarkTransparency[] = JHTML::_("select.option",'40','40');
		$watermarkTransparency[] = JHTML::_("select.option",'50','50');
		$watermarkTransparency[] = JHTML::_("select.option",'60','60');
		$watermarkTransparency[] = JHTML::_("select.option",'70','70');
		$watermarkTransparency[] = JHTML::_("select.option",'80','80');
		$watermarkTransparency[] = JHTML::_("select.option",'90','90');
		$watermarkTransparency[] = JHTML::_("select.option",'100','100');
	
		$watermarkType[] = JHTML::_("select.option",'image','Image');
		$watermarkType[] = JHTML::_("select.option",'text','Text');
		
		//Commenting options
		if ( galleryUtils::isComponentInstalled('com_securityimages') == 1 ) {
			$security_notice = "<span style=\"color:#009933;font-weight:bold;\">" . JText::_('SecurityImages_component_detected')." </span>";
		} else {
			$security_notice = "<span style=\"color:#FF0000;font-weight:bold;\">".JText::_('SECURITYIMAGES_COMPONENT_NOT_INSTALLED')." </span>";
		}

		/**
			* Routine checks if Freetype library is compiled with GD2
			* @return boolean True or False
			*/
		if (function_exists('gd_info'))
			{
			$gd_info = gd_info();
			$freetype = $gd_info['FreeType Support'];
			if ($freetype == 1)
				$freeTypeSupport = "<div style=\"color:#009933;\">". JText::_('(Freetype library installed, watermark is possible)'). "</div>";
			else
				$freeTypeSupport = "<div style=\"color:#FF0000;\">". JText::_('(Freetype library NOT installed! Watermark does not work)')."</div>";
			}
		
		// in page.html joomla has a template for $tabs->func*()
		// couldn't figure out how to use it effectively however.
		// this is why the templates were broken up, so i could call echo $tabs->func*() between them
		jimport("joomla.html.pane");
		$editor =& JFactory::getEditor();
		$tabs =& JPane::getInstance("Tabs");
		?>
		<script  type="text/javascript">
			function submitbutton(pressbutton) {
				<?php echo $editor->save('intro_text') ; ?>
				submitform( pressbutton );
			}
		</script>
		<form action="index2.php" method="post" name="adminForm" id="adminForm">
		<?php
		echo $tabs->startPane( 'rsgConfig','rsgConfig' );
		echo $tabs->startPanel( JText::_('General'), 'rsgConfig' );
		?>
		<table border="0" width="100%">
			<tr>
				<td  valign="top">
					<fieldset>
						<legend><?php echo JText::_('General settings') ?></legend>
						<table width="100%">
							<tr>
								<td width="200"><?php echo JText::_('Version:')?></td>
								<td><?php echo $config->version?></td>
							</tr>
							<tr>
								<td><?php echo JText::_('Introduction Text:')?></td>
								<td>
									<?php echo $editor->display( 'intro_text',  $config->intro_text , '100%', '200', '10', '20', false ) ; ?>
								</td>
							</tr>
							<tr>
								<td><?php echo JText::_('Debug:') ?></td>
								<td><?php echo JHTML::_("select.booleanlist",'debug', '', $config->debug); ?></td>
							</tr>
<!--							<tr>
								<td><?php //echo JText::_('Hide Root (create multiple independant galleries)'); ?></td>
								<td><?php //echo JHTML::_("select.booleanlist",'hideRoot', '', $config->hideRoot); ?></td>
							</tr>
-->
							<tr>
								<td><?php echo JText::_('Advanced SEF (all category names and item titles must be unique)'); ?></td>
								<td><?php 
									//echo JHTML::_("select.booleanlist",'advancedSef', '', $config->advancedSef); 
									$options = array();
									$options[] = JHTML::_('select.option', '0', JText::_('No'));
									$options[] = JHTML::_('select.option', '1', JText::_('Yes'));
									$options[] = JHTML::_('select.option', '2', JText::_('Yes use number and name no need for unique names'));
									echo JHTML::_('select.radiolist', $options, 'advancedSef', '', 'value', 'text', $config->advancedSef);
								?>



								</td>
							</tr>
						</table>
					</fieldset>
				</td>
			</tr>
		</table>
		<?php
		//$tmpl->displayParsedTemplate( 'configTableGeneral' );
		echo $tabs->endPanel();
	
		echo $tabs->startPanel( JText::_('Images'), 'rsgConfig' );
		?>
		<table border="0" width="100%">
			<tr>
				<td width="40%" valign="top">
					<fieldset>
						<legend><?php echo JText::_('Image Manipulation') ?></legend>
						<table width="100%">
							<tr>
								<td><?php echo JText::_('Display Picture Width:') ?></td>
								<td><input class="text_area" type="text" name="image_width" size="10" value="<?php echo $config->image_width;?>"/></td>
							</tr>
							<tr>
								<td><?php echo JText::_('Resize portrait images by height using Display Picture Width:') ; ?></td>
								<td><?php echo JHTML::_("select.booleanlist",'resize_portrait_by_height', '', $config->resize_portrait_by_height);?></td>
							</tr>
							<tr>
								<td><?php echo JText::_('Thumbnail Width:') ?></td>
								<td><input class="text_area" type="text" name="thumb_width" size="10" value="<?php echo $config->thumb_width;?>"/></td>
							</tr>
							<tr>
								<td><?php echo JText::_('Thumbnail Style:') ?></td>
								<td><?php echo JHTML::_("select.genericlist", $thumb_style, 'thumb_style', '', 'value', 'text', $config->thumb_style ) ?></td>
							</tr>
							<tr>
								<td><?php echo JText::_('JPEG Quality Percentage') ?></td>
								<td><input class="text_area" type="text" name="jpegQuality" size="10" value="<?php echo $config->jpegQuality;?>"/></td>
							</tr>
							<tr>
								<td><?php echo JText::_('Allowed filetypes') ?></td>
								<td><input class="text_area" type="text" name="allowedFileTypes" size="30" value="<?php echo $config->allowedFileTypes;?>"/></td>
							</tr>
						</table>
					</fieldset>
					<fieldset>
						<legend><?php echo JText::_('Image upload') ?></legend>
						<table width="100%">
							<tr>
								<td><?php echo JHTML::tooltip(JText::_('RSG2_IPTC_TOOLTIP'), JText::_('RSG2_IPTC_TOOLTIP_TITLE'), 
                    '', 'RSG2_USE_IPTC'); ?></td>
								<td><?php echo JHTML::_("select.booleanlist",'useIPTCinformation', '', $config->useIPTCinformation);?></td>
							</tr>
						</table>
					</fieldset>
<!--end of addition-->					
				</td>
				<td width="60%" valign="top">
					<fieldset>
						<legend><?php echo JText::_('Graphics Library') ?></legend>
						<table width="100%">
							<tr>
								<td width=185><?php echo JText::_('Graphics Library') ?></td>
								<td><?php echo $lists['graphicsLib'] ?></td>
							</tr>
							<tr>
								<td colspan=2 ><span style="color:red;"><?php echo JText::_('Note');?></span><?php echo JText::_('Leave the following fields empty unless you have problems.'); ?></td>
							</tr>
							<tr>
								<td><?php echo JText::_('ImageMagick Path:') ?></td>
								<td><input class="text_area" type="text" name="imageMagick_path" size="50" value="<?php echo $config->imageMagick_path ?>"/></td>
							</tr>
							<tr>
								<td><?php echo JText::_('Netpbm Path:') ?></td>
								<td><input class="text_area" type="text" name="netpbm_path" size="50" value="<?php echo $config->netpbm_path;?>"/></td>
							</tr>
							<tr>
								<td><?php echo JText::_('FTP Path:') ?></td>
								<td><input class="text_area" type="text" name="ftp_path" size="50" value="<?php echo $config->ftp_path?>"/>(<?php echo JText::_('HTML-root is')?>: <?php  print $_SERVER['DOCUMENT_ROOT']?>)</td>
							</tr>
							<tr>
								<td><?php echo JText::_('Video converter path:') ?></td>
								<td><input class="text_area" type="text" name="videoConverter_path" size="50" value="<?php echo $config->videoConverter_path;?>"/>(ex. "C:\ffmpeg\ffmpeg.exe")</td>
							</tr>
							<tr>
								<td><?php echo JText::_('Video converter parameters:') ?></td>
								<td><input class="text_area" type="text" name="videoConverter_param" size="100" value="<?php echo $config->videoConverter_param;?>"/></td>
							</tr>
							<tr>
								<td><?php echo JText::_('Thumbnail extraction parameters:') ?></td>
								<td><input class="text_area" type="text" name="videoConverter_thumbParam" size="100" value="<?php echo $config->videoConverter_thumbParam;?>"/></td>
							</tr>
							<tr>
								<td><?php echo JText::_('Video output type:') ?></td>
								<td><input class="text_area" type="text" name="videoConverter_extension" size="50" value="<?php echo $config->videoConverter_extension;?>"/></td>
							</tr>
						</table>
					</fieldset>
				</td>
			</tr>
		</table>
		<table border="0" width="100%">
			<tr>
				<td width="50%" valign="top">
					<fieldset>
						<legend><?php echo JText::_('Image Storage') ?></legend>
						<table width="100%">
							<tr>
								<td><?php echo JText::_('Keep original image:') ?></td>
								<td><?php echo JHTML::_("select.booleanlist",'keepOriginalImage', '', $config->keepOriginalImage)?></td>
							</tr>
							<tr>
								<td><?php echo JText::_('Original Image Path:') ?></td>
								<td><input class="text_area" style="width:300px;" type="text" name="imgPath_original" size="10" value="<?php echo $config->imgPath_original?>"/></td>
							</tr>
							<tr>
								<td><?php echo JText::_('Display Image Path:') ?></td>
								<td><input class="text_area" style="width:300px;" type="text" name="imgPath_display" size="10" value="<?php echo $config->imgPath_display?>"/></td>
							</tr>
							<tr>
								<td><?php echo JText::_('Thumb Path:') ?></td>
								<td><input class="text_area" style="width:300px;" type="text" name="imgPath_thumb" size="10" value="<?php echo $config->imgPath_thumb?>"/></td>
							</tr>
							<tr>
								<td><?php echo JText::_("Create directories if they don't exist:") ?></td>
								<td><?php echo JHTML::_("select.booleanlist",'createImgDirs', '', $config->createImgDirs)?></td>
							</tr>
						</table>
					</fieldset>
				</td>
				<td width="50%" valign="top">
					<fieldset>
						<legend><?php echo JText::_('Comments');?></legend>
						<table width="100%">
							<tr>
								<td><?php echo JText::_('Commenting enabled');?></td>
								<td width="110px"><?php echo JHTML::_("select.booleanlist",'comment', '', $config->comment);?></td>
							</tr>
							<tr>
								<td>Use  <a href="http://www.waltercedric.com" target="_blank"><?php echo JText::_('SecurityImages_component')?></a> <?php echo $security_notice;?></td>
								<td><?php echo JHTML::_("select.booleanlist",'comment_security', '', $config->comment_security && galleryUtils::isComponentInstalled('com_securityimages'))?></td>
							</tr>
							<tr>
								<td><?php echo JText::_('Allow public users to comment');?></td>
								<td><?php echo JHTML::_("select.booleanlist",'comment_allowed_public', '', $config->comment_allowed_public)?></td>
							</tr>
							<tr>
								<td><?php echo JText::_('User can only comment once');?> (Not working yet!)</td>
								<td><?php echo JHTML::_("select.booleanlist",'comment_once', '', $config->comment_once)?></td>
							</tr>
						</table>
					</fieldset>
					<fieldset>
						<legend><?php echo JText::_('Voting');?></legend>
						<table width="100%">
							<tr>
								<td><?php echo JText::_('Voting enabled');?></td>
								<td width="110px"><?php echo JHTML::_("select.booleanlist",'voting', '', $config->voting);?></td>
							</tr>
							<tr>
								<td><?php echo JText::_('User can only vote once(cookie based)');?></td>
								<td><?php echo JHTML::_("select.booleanlist",'voting_once', '', $config->voting_once)?></td>
							</tr>
							<tr>
								<td><?php echo JText::_('Cookie prefix');?></td>
								<td><input type="text" name="cookie_prefix" value="<?php echo $config->cookie_prefix;?>"</td>
							</tr>
						</table>
					</fieldset>
				</td>
			</tr>
		</table>
		<?php
		//$tmpl->displayParsedTemplate( 'configTableImages' );
		echo $tabs->endPanel();
	
		echo $tabs->startPanel( JText::_('Display'), 'rsgConfig' );
		?>
		<table border="0" width="100%">
			<tr>
				<td width="40%" valign="top">
					<fieldset>
					<legend><?php echo JText::_('Front Page')?></legend>
					<table width="100%">
					<tr>
						<td width="40%"><?php echo JText::_('Display Search')?></td>
						<td><?php echo JHTML::_("select.booleanlist", 'displaySearch', '', $config->displaySearch)?></td>
					</tr>
					<tr>
						<td width="40%"><?php echo JText::_('Display Random')?></td>
						<td><?php echo JHTML::_("select.booleanlist",'displayRandom', '', $config->displayRandom)?></td>
					</tr>
					<tr>
						<td><?php echo JText::_('Display Latest')?></td>
						<td><?php echo JHTML::_("select.booleanlist",'displayLatest', '', $config->displayLatest)?></td>
					</tr>
					<tr>
						<td><?php echo JText::_('Display Branding')?></td>
						<td><?php echo JHTML::_("select.booleanlist",'displayBranding','', $config->displayBranding)?></td>
					</tr>
					<tr>
						<td><?php echo JText::_('Display Downloadlink')?></td>
						<td><?php echo JHTML::_("select.booleanlist",'displayDownload','', $config->displayDownload)?></td>
					</tr>
					<tr>
						<td><?php echo JText::_('Display Status Icons')?></td>
						<td><?php echo JHTML::_("select.booleanlist",'displayStatus', '', $config->displayStatus)?></td>
					</tr>
					<tr>
						<td><?php echo JText::_('Display gallery limitbox')?></td>
						<td><?php echo JHTML::_("select.genericlist",$dispLimitbox, 'dispLimitbox','','value', 'text', $config->dispLimitbox)?></td>
					</tr>
					<tr>
						<td><?php echo JText::_('Default number of galleries on frontpage')?></td>
						<td><?php echo JHTML::_("select.genericlist",$galcountNrs, 'galcountNrs','','value', 'text', $config->galcountNrs)?></td>
					</tr>
					<tr>
						<td><?php echo JText::_('Display Slideshow')?></td>
						<td><?php echo JHTML::_("select.booleanlist",'displaySlideshow', '', $config->displaySlideshow)?></td>
					</tr>
					<tr>
						<td><?php echo JText::_('Select slideshow')?></td>
						<td><?php echo JHTML::_("select.genericlist",$current_slideshow, 'current_slideshow','','value', 'text', $config->current_slideshow);?></td>
					</tr>
					<tr>
						<td><?php echo JText::_('Display Owner Information'); ?></td>
						<td><?php echo JHTML::_("select.booleanlist",'showGalleryOwner', '', $config->showGalleryOwner)?></td>
					</tr>
					<tr>
						<td><?php echo JText::_('Display number of items in gallery');?></td>
						<td><?php echo JHTML::_("select.booleanlist",'showGallerySize', '', $config->showGallerySize)?></td>
					</tr>
					<tr>
						<td><?php echo JText::_('Display creation date');?></td>
						<td><?php echo JHTML::_("select.booleanlist",'showGalleryDate', '', $config->showGalleryDate)?></td>
					</tr>
					</table>
					</fieldset>
				</td>
				<td width="30%" valign="top">
					<fieldset>
					<legend><?php echo JText::_('Image Display')?></legend>
					<table width="100%">
					<tr>
						<td width="40%"><?php echo JText::_('Popup style')?></td>
						<td><?php echo JHTML::_("select.genericlist", $displayPopup, 'displayPopup', '', 'value', 'text', $config->displayPopup )?></td>
					</tr>
					<tr>
						<td><?php echo JText::_('Resize Option')?></td>
						<td><?php echo JHTML::_("select.genericlist", $resizeOptions, 'display_img_dynamicResize', '', 'value', 'text', $config->display_img_dynamicResize )?></td>
					</tr>
					<tr>
						<td><?php echo JText::_('Display Description')?></td>
						<td><?php echo JHTML::_("select.booleanlist",'displayDesc', '', $config->displayDesc)?></td>
					</tr>
					<tr>
						<td><?php echo JText::_('Display Hits')?></td>
						<td><?php echo JHTML::_("select.booleanlist",'displayHits', '', $config->displayHits)?></td>
					</tr>
					<tr>
						<td><?php echo JText::_('Display Voting')?></td>
						<td><?php echo JHTML::_("select.booleanlist",'displayVoting', '', $config->displayVoting)?></td>
					</tr>
					<tr>
						<td><?php echo JText::_('Display Comments')?></td>
						<td><?php echo JHTML::_("select.booleanlist",'displayComments', '', $config->displayComments)?></td>
					</tr>
					</table>
					</fieldset>

					<fieldset>
					<legend><?php echo JText::_('Image order')?></legend>
					<table width="100%">
					<tr>
						<td><?php echo JText::_('Order images by')?></td>
						<td><?php echo JHTML::_("select.genericlist",$thum_order, 'filter_order','','value', 'text', $config->filter_order)?></td>
					</tr>
					<tr>
						<td><?php echo JText::_('Order direction')?></td>
						<td><?php echo JHTML::_("select.genericlist",$thum_order_direction, 'filter_order_Dir','','value', 'text', $config->filter_order_Dir)?></td>
					</tr>
					</table>
					</filedset>
				</td>
				<td width="30%" valign="top">
					<fieldset>
					<legend><?php echo JText::_('EXIF SETTINGS')?></legend>
					<table width="100%">
					<tr>
						<td><?php echo JText::_('Display EXIF Data')?></td>
						<td><?php echo JHTML::_("select.booleanlist",'displayEXIF', '', $config->displayEXIF)?></td>
					</tr>
					<tr>
						<td valign="top"><?php echo JText::_('Select_EXIF_tags_to_display')?></td>
						<td valign="top">
							<label class="examples"></label>
							<?php echo JHTML::_("select.genericlist", $exif, 'exifTags[]', 'MULTIPLE size="15"', 'value', 'text', $exifSelect );?>
						</td>
					</tr>
					</table>
					</fieldset>
				</td>
			</tr>
			<tr>
				<td width="40%" valign="top">
					<fieldset>
					<legend><?php echo JText::_('Gallery View')?></legend>
					<table width="100%">
					<tr>
						<td width="40%"><?php echo JText::_('Thumbnail Style:<br>Use float for variable width templates.')?></td>
						<td><?php echo JHTML::_("select.genericlist", $display_thumbs_style, 'display_thumbs_style', '', 'value', 'text', $config->display_thumbs_style );?></td>
					</tr>
					<tr>
						<td><?php echo JText::_('Direction (only works for float):')?></td>
						<td><?php echo JHTML::_("select.genericlist", $display_thumbs_floatDirection, 'display_thumbs_floatDirection', '', 'value', 'text', $config->display_thumbs_floatDirection )?></td>
					</tr>
					<tr>
						<td><?php echo JText::_('Number of Thumbnail Columns (only for table):')?></td>
						<td><?php echo JHTML::_("select.integerlist",1, 19, 1, 'display_thumbs_colsPerPage', '', $config->display_thumbs_colsPerPage)?></td>
					</tr>
					<tr>
						<td><?php echo JText::_('Thumbnails per Page:')?></td>
						<td><input class="text_area" type="text" name="display_thumbs_maxPerPage" size="10" value="<?php echo $config->display_thumbs_maxPerPage?>"/></td>
					</tr>
					<tr>
						<td><?php echo JText::_('Show image name below thumbnail:')?></td>
						<td><?php echo JHTML::_("select.booleanlist", 'display_thumbs_showImgName','', $config->display_thumbs_showImgName )?></td>
					</tr>
					
					</table>
					</fieldset>
				</td>
				<td colspan="2" valign="top">
					<fieldset>
					<legend><?php echo JText::_('Image Watermark')?></legend>
					<table width="100%">
					<tr>
						<td colspan="2">
						<strong><?php echo $freeTypeSupport?></strong>
						</td>
					</tr>
					<tr>
						<td width="40%"><?php echo JText::_('Display Watermark')?></td>
						<td><?php echo JHTML::_("select.booleanlist",'watermark','', $config->watermark)?></td>
					</tr>
					<!--
					<tr>
						<td width="40%">* Watermark type *</td>
						<td><?php // echo JHTML::_("select.genericlist",$watermarkType, 'watermark_type','','value', 'text', $config->watermark_type)?></td>
					</tr>
					<tr>
						<td valign="top" width="40%">* Watermark upload *</td>
						<td></td>
					</tr>
					-->
					<tr>
						<td width="40%"><?php echo JText::_('Font')?></td>
						<td><?php echo galleryUtils::showFontList();?></td>
					</tr>
					<tr>
						<td><?php echo JText::_('Watermark text')?></td>
						<td><input class="text_area" type="text" name="watermark_text" size="50" value="<?php echo $config->watermark_text?>"/></td>
					</tr>
					<tr>
						<td><?php echo JText::_('Watermark Font Size')?></td>
						<td><?php echo JHTML::_("select.genericlist",$watermarkFontSize, 'watermark_font_size','','value', 'text', $config->watermark_font_size)?>&nbsp;&nbsp;pts</td>
					</tr>
					<tr>
						<td><?php echo JText::_('Watermark text angle')?></td>
						<td><?php echo JHTML::_("select.genericlist",$watermarkAngles, 'watermark_angle','','value', 'text', $config->watermark_angle)?></td>
					</tr>
					<tr>
						<td><?php echo JText::_('Watermark position')?></td>
						<td><?php echo JHTML::_("select.genericlist",$watermarkPosition, 'watermark_position','','value', 'text', $config->watermark_position)?></td>
					</tr>
					<tr>
						<td><?php echo JText::_('Watermark transparency')?></td>
						<td><?php echo JHTML::_("select.genericlist",$watermarkTransparency, 'watermark_transparency','','value', 'text', $config->watermark_transparency)?><strong>%<strong></td>
					</tr>
					<tr>
						<td><?php echo JText::_('Watermarked image Path:') ?></td>
						<td><input class="text_area" style="width:300px;" type="text" name="imgPath_watermarked" size="10" value="<?php echo $config->imgPath_watermarked?>"/></td>
					</tr>
					</table>
					</fieldset>

				</td>
			</tr>
		</table>
		<?php
		//$tmpl->displayParsedTemplate( 'configTableFrontDisplay' );
		echo $tabs->endPanel();
	
		echo $tabs->startPanel( JText::_('Permissions'), 'rsgConfig' );
		?>
		<table border="0" width="100%">
			<tr>
				<td width="40%">
					<fieldset>
					<legend><?php echo JText::_('Access Control Settings')?></legend>
					<table width="100%">
					<tr>
						<td width="60%"><?php echo JText::_('Enable Access Control')?></td>
						<td><?php echo JHTML::_("select.booleanlist",'acl_enabled', '', $config->acl_enabled)?></td>
					</tr>
					<tr>
						<td><?php echo JText::_('Show My Galleries')?></td>
						<td><?php echo JHTML::_("select.booleanlist",'show_mygalleries', '', $config->show_mygalleries)?></td>
					</tr>	
					<tr>
						<td><?php echo JText::_('Can user create galleries?')?></td>
						<td><?php echo JHTML::_("select.booleanlist",'uu_createCat', '', $config->uu_createCat)?></td>
					</tr>	
					</table>
					</fieldset>
				</td>
				<td width="60%">&nbsp;
				
				</td>
			</tr>
			<tr>
				<td width="40%">
					<fieldset>
					<legend><?php echo JText::_('User specific settings')?></legend>
					<table width="100%">
					<tr>
						<td width="60%"><?php echo JText::_('Maximum number of galleries a user can have:')?></td>
						<td><input class="text_area" type="text" name="uu_maxCat" size="10" value="<?php echo $config->uu_maxCat?>"/></td>
					</tr>
					<tr>
						<td><?php echo JText::_('Max numbers of pictures a user can have:')?></td>
						<td><input class="text_area" type="text" name="uu_maxImages" size="10" value="<?php echo $config->uu_maxImages?>"/></td>
					</tr>
					</table>
					</fieldset>
				</td>
				<td width="60%">&nbsp;
				
				</td>
			</tr>
		</table>
		<?php
		//$tmpl->displayParsedTemplate( 'configTableUsers' );
		echo $tabs->endPanel();
		?>
		<input type="hidden" name="option" value="com_rsgallery2" />
		<input type="hidden" name="rsgOption" value="config" />
		<input type="hidden" name="task" value="" />
		</form>
		<!-- Fix for Firefox browser -->
		<div style='clear:both;line-height:0px;'>&nbsp;</div>
		<?php
		echo $tabs->endPane();
		//$tmpl->displayParsedTemplate( 'configFinish' );
	}
}

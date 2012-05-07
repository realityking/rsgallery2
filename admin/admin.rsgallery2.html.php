<?php
/**
* This file handles the HTML processing for the Admin section of RSGallery.
*
* @version $Id: admin.rsgallery2.html.php 1042 2011-09-19 10:48:06Z mirjam $
* @package RSGallery2
* @copyright (C) 2003 - 2010 RSGallery2
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* RSGallery is Free Software
*/

defined( '_JEXEC' ) or die( 'Direct Access to this location is not allowed.' );


/**
* The HTML_RSGALLERY class is used to encapsulate the HTML processing for RSGallery.
* @package RSGallery2
* @todo Move this class to a seperate class file and add loose functions to it
**/
class HTML_RSGALLERY{

    
    /**
     * use to prints a message between HTML_RSGallery::RSGalleryHeader(); and a normal feature.
     * use for things like deleting an image, where a success message should be displayed and viewImages() called aferwards.
     * two css classes are used: rsg-admin-msg, rsg-admin-msg-important
     * this function replaces newlines with <br> for convienence.
     *  
     * @todo implement css classes in css file
     *  
     * @param string message to print
     * @param boolean optionally display the message as important, possibly changing the text to red or bold, etc.  as a general rule, expected results should be normal, unexpected results should be marked important.
     */
    function printAdminMsg($msg, $important=false) {
        // replace newlines with html line breaks.
        str_replace('\n', '<br>', $msg);
        
        if($important)
            echo "<p class='rsg-admin-msg'>$msg</p>";
        else
            echo "<p class='rsg-admin-msg-important message'>$msg</p>";
    }
    
    /**
      * Used by showCP to generate buttons
      * @param string URL for button link
      * @param string Image name for button image
      * @param string Text to show in button
      */
    function quickiconButton( $link, $image, $text ) {
        ?>
        <div style="float:left;">
        <div class="icon">
            <a href="<?php echo $link; ?>">
                <div class="iconimage">
                    <?php echo JHTML::_('image.site', $image, '/components/com_rsgallery2/images/', NULL , NULL , $text); ?>
                </div>
                <?php echo $text; ?>
            </a>
        </div>
        </div>
        <?php
    }
    
    /**
     * Shows the RSGallery Control Panel in backend.
     * @todo complete translation tags
     * @todo use div in stead of tables (LOW PRIORITY)
     * @todo Move CSS to stylesheet
     */
    function showCP(){
        global  $rows, $rows2, $rsgConfig, $rsgVersion;

        // Get the current JUser object
		$user = &JFactory::getUser();

        //Show Warningbox if some preconditions are not met
        galleryUtils::writeWarningBox();
        ?>
        <div id="rsg2-thisform">
            <div id='rsg2-infoTabs'>
                <table width="100%">
                    <tr>
                        <td>
<?php
						jimport("joomla.html.pane");
                        $tabs =& JPane::getInstance('sliders');
                        echo $tabs->startPane( 'recent' );
                        echo $tabs->startPanel( JText::_('Galleries'), 'Categories' );
                        ?>
                        <table class="adminlist" width="500">
                            <tr>
                                <th colspan="3"><?php echo JText::_('Most recently added galleries'); ?></th>
                            </tr>
                            <tr>
                                <td><strong><?php echo JText::_('Gallery'); ?></strong></td>
                                <td><strong><?php echo JText::_('User'); ?></strong></td>
                                <td><strong><?php echo JText::_('ID'); ?></strong></td>
                            </tr>
                            <?php echo galleryUtils::latestCats();?>
                            <tr>
                                <th colspan="3">&nbsp;</th>
                            </tr>
                        </table>
                        <?php
                        echo $tabs->endPanel();
                        echo $tabs->startPanel( JText::_('Items'), 'Images' );
                        ?>
                        <table class="adminlist" width="500">
                            <tr>
                                <th colspan="4"><?php echo JText::_('Most recently added items'); ?></th>
                            </tr>
                            <tr>
                                <td><strong><?php echo JText::_('Filename');?></strong></td>
                                <td><strong><?php echo JText::_('Gallery');?></strong></td>
                                <td><strong><?php echo JText::_('Date'); ?></strong></td>
                                <td><strong><?php echo JText::_('User'); ?></strong></td>
                            </tr>
                            <?php echo galleryUtils::latestImages();?>
                            <tr>
                                <th colspan="4">&nbsp;</th>
                            </tr>
                        </table>
                        <?php
                        echo $tabs->endPanel();
                        echo $tabs->startPanel(JText::_('Credits'), 'Credits' );
                        ?>

                        
<div id='rsg2-credits'>
    <h3>Core Team</h3>
    <dl>
		<dt>2011</dt>
			<dd><b>Johan Ravenzwaaij</b></dd>
        	<dd><b>Mirjam Kaizer</b></dd>
		<dt>2010</dt>
			<dd><b>Johan Ravenzwaaij</b></dd>
			<dd><b>Mihir Chhatre</b> <a href="http://www.thoughtfulviewfinder.com">Thoughtfulviewfinder Services </a></dd>
        	<dd><b>Mirjam Kaizer</b></dd>
        <dt>2008-2009</dt>
		<dt>Project Architect</dt>
        	<dd>Jonah Braun <a href='http://whalehosting.ca/' target='_blank'>Whale Hosting Inc.</a></dd>
        <dt>Developers</dt>
        	<dd>John Caprez</dd>
        <dt>Community Liaison</dt>
        	<dd>Dani&#235;l Tulp <a href='http://design.danieltulp.nl/' target='_blank'>DT^2</a></dd>
    </dl>
    
    <h3>Logo</h3>
    <dl>
        <dt>Designer</dt> <dd><b>Cory "ccdog" Webb</b> <a href='http://www.corywebb.com/' target='_blank'>CoryWebb.com</a></dd>
    </dl>

    <h3>Translations</h3>
    <dl>
        <dt>Brazilian Portuguese</dt> <dd><b>Helio Wakasugui</b></dd>
        <dt>Czech</dt> 
			<dd><b>David Zirhut</b> <a href='http://www.joomlaportal.cz/'>joomlaportal.za</a></dd>
			<dd><b>Felix 'eFix' Lauda</b></dd>
        <dt>Dutch</dt>
			<dd><b>Tomislav Ribicic</b></dd>
        	<dd><b>Dani&#235;l Tulp</b> <a href='http://design.danieltulp.nl' target='_blank'></a></dd>
			<dd><b>Bas</b><a href='http://www.fantasea.nl' target='_blank'>http://www.fantasea.nl</a></dd>
        	<dd><b>Mirjam Kaizer</b></dd>
		<dt>French</dt>
			<dd><b>Fabien de Silvestre</b></dd>
			<dd><b>Günter Hoffmann</b><a href='http://gunterhoffmann.com' target='_blank'>http://gunterhoffmann.com</a></dd>
        <dt>German</dt> 
			<dd><b>woelzen</b><a href='http://conseil.silvestre.fr' target='_blank'>http://conseil.silvestre.fr</a></dd>
			<dd><b>Günter Hoffmann</b><a href='http://gunterhoffmann.com' target='_blank'>http://gunterhoffmann.com</a></dd>
		<dt>Greek</dt><dd><b>Charis Argyropoulos</b><a href='http://www.symenox.gr' target='_blank'>http://www.symenox.gr</a></dd>
        <dt>Hungarian</dt> 
			<dd><b>SOFT-TRANS</b> <a href='http://www.soft-trans.hu' target='_blank'>SOFT-TRANS</a></dd>
		<dt>Italian</dt><dd><b>Michele Monaco</b><a href='http://www.mayavoyage.com' target='_blank'>Maya Voyages</a>
        <dt>Norwegian</dt> 
			<dd><b>Ronny Tjelle</b></dd>
            <dd><b>Steinar Vikholt</b></dd>
        <dt>Polish</dt> 
			<dd><b>Zbyszek Rosiek</b></dd>
        <dt>Russian</dt>
			<dd><b>Ragnaar</b></dd>
        <dt>Spanish</dt> 
			<dd><b>Eb&auml;vs</b> <a href='http://www.ebavs.net/' target='_blank'>eb&auml;vs.net</a></dd>
        <dt>Traditional Chinese</dt>
			<dd><b>Sun Yu</b><a href='http://www.meto.com.tw' target='_blank'>Meto</a></dd>
			<dd><b>Mike Ho</b> <a href='http://www.dogneighbor.com' target='_blank'>http://www.dogneighbor.com</a></dd>
    </dl>

    <h3>Legacy (up to version 1.14)</h3>
    <dl>
        <dt>Creator</dt>
        	<dd>Ronald Smit</dd>
        <dt>RSGallery 1.x</dt>
        	<dd>Andy "Troozers" Stewart</dd>
            <dd>Richard Foster</dd>
		<dt>RSGallery2 2005</dt>
			<dd>Dani&#235;l Tulp</dd>
			<dd>Jonah Braun</dd>
			<dd>Tomislav Ribicic</dd>
		<dt>RSGallery2 2006</dt>
			<dd>Dani&#235;l Tulp</dd>
			<dd>Jonah Braun</dd>
			<dd>Ronald Smit</dd>
			<dd>Tomislav Ribicic</dd>
		<dt>RSGallery2 2007</dt>
			<dd>Dani&#235;l Tulp</dd>
			<dd>John Caprez</dd>
			<dd>Jonah Braun</dd>
			<dd>Jonathan DeLaigle</dd>
			<dd>Margo Adams</dd>
			<dd>Ronald Smit</dd>
		<dt>RSGallery2 2008</dt>
			<dd>Dan Shaffer 'chefgroovy'</dd>

    </dl>
</div>
                        <?php
                        echo $tabs->endPanel();
                        echo $tabs->endPane();
                        ?>                        </td>
                            </tr>
                    <tr>
                      <td>&nbsp;</td>
                    </tr>
                        </table>
            <br />
                <table border="0" width="100%" cellspacing="1" cellpadding="1" style=background-color:#CCCCCC;>
                    <tr>
                        <td bgcolor="#FFFFFF" colspan="2">
                            <img src="<?php echo JURI_SITE;?>/administrator/components/com_rsgallery2/images/rsg2-logo.png" align="middle" alt="RSGallery logo"/>
                        </td>
                    </tr>
                    <tr>
                        <td width="120" bgcolor="#FFFFFF"><strong><?php echo JText::_('Installed version');?></strong></td>
                        <td bgcolor="#FFFFFF"><a href='index2.php?option=com_rsgallery2&task=viewChangelog' title='view changelog'><?php echo $rsgVersion->getVersionOnly(); echo " (".$rsgVersion->getSVNonly().")";?></a></td>
                    </tr>
                    <tr>
                        <td bgcolor="#FFFFFF"><strong><?php echo JText::_('License')?>:</strong></td>
                        <td bgcolor="#FFFFFF"><a href="http://www.gnu.org/copyleft/gpl.html" target="_blank">GNU GPL</a></td>
                    </tr>
                </table>
            </div>

            <div id='cpanel'>
                <?php
                if ( $user->get('gid') > 23 ):
                    $link = 'index2.php?option=com_rsgallery2&rsgOption=config&task=showConfig';
                    HTML_RSGALLERY::quickiconButton( $link, 'config.png',  JText::_('Configuration') );
                endif;

                $link = 'index2.php?option=com_rsgallery2&rsgOption=images&task=upload';
                HTML_RSGALLERY::quickiconButton( $link, 'upload.png', JText::_('Upload') );

                $link = 'index2.php?option=com_rsgallery2&rsgOption=images&task=batchupload';
                HTML_RSGALLERY::quickiconButton( $link, 'upload_zip.png', JText::_('Batch upload') );
                
//                $link = 'index2.php?option=com_rsgallery2&rsgOption=jumploader';
//                HTML_RSGALLERY::quickiconButton( $link, 'upload_zip.png', JText::_('Java uploader') );
                
                $link = 'index2.php?option=com_rsgallery2&rsgOption=images&task=view_images';
                HTML_RSGALLERY::quickiconButton( $link, 'mediamanager.png', JText::_('Manage Items') );

                $link = 'index2.php?option=com_rsgallery2&rsgOption=galleries';
                HTML_RSGALLERY::quickiconButton( $link, 'categories.png', JText::_('Manage Galleries') );

                if ( $user->get('gid') > 23 ):
                    /*
                    $link = 'index2.php?option=com_rsgallery2&task=consolidate_db';
                    HTML_RSGALLERY::quickiconButton( $link, 'dbrestore.png', JText::_('Consolidate database') );
    				*/
    				$link = 'index2.php?option=com_rsgallery2&rsgOption=maintenance';
					HTML_RSGALLERY::quickiconButton( $link, 'maintenance.png', JText::_('Maintenance'));
    				
                    $link = 'index2.php?option=com_rsgallery2&rsgOption=maintenance&task=showMigration';
                    HTML_RSGALLERY::quickiconButton( $link, 'dbrestore.png', JText::_('Migration options') );
                endif;

				$link = 'index2.php?option=com_rsgallery2&rsgOption=installer';
				HTML_RSGALLERY::quickiconButton( $link, 'template.png', JText::_('Template Manager'));
			
                // if debug is on, display advanced options
                if( $rsgConfig->get( 'debug' )): ?>
                <div id='rsg2-cpanelDebug'><?php echo JText::_('RSGALLERY2_C_DEBUG_ON');?>
                    <?php
                    if ( $user->get('gid') > 23 ):
                        $link = 'index2.php?option=com_rsgallery2&task=purgeEverything';
                        HTML_RSGALLERY::quickiconButton( $link, 'menu.png', JText::_('Purge/Delete everything') );
    
                        $link = 'index2.php?option=com_rsgallery2&task=reallyUninstall';
                        HTML_RSGALLERY::quickiconButton( $link, 'menu.png', JText::_('RSGALLERY_C_REALLY_UNINSTALL') );
        
                        $link = 'index2.php?option=com_rsgallery2&task=config_rawEdit';
                        HTML_RSGALLERY::quickiconButton( $link, 'menu.png', JText::_('Config - Raw Edit') );
                    endif;
                    
                    $link = 'index2.php?option=com_rsgallery2&task=config_dumpVars';
                    HTML_RSGALLERY::quickiconButton( $link, 'menu.png', JText::_('Config - View') );
                    ?>
                    <div class='rsg2-clr'>&nbsp;</div>
                </div>
                <?php endif; ?>
                <div class='rsg2-clr'>&nbsp;</div>
            </div>
        </div>
        <div class='rsg2-clr'>&nbsp;</div>
        <?php
    }


	/**
	* @param string
	* @param string
	* @param string
	*/
	function showInstallMessage( $message, $title, $url ) {
		global $PHP_SELF;
		?>
		<table class="adminheading">
		<tr>
			<th class="install">
			<?php echo $title; ?>
			</th>
		</tr>
		</table>

		<table class="adminform">
		<tr>
			<td align="left">
			<strong><?php echo $message; ?></strong>
			</td>
		</tr>
		<tr>
			<td colspan="2" align="center">
			[&nbsp;<a href="<?php echo $url;?>" style="font-size: 16px; font-weight: bold"><?php echo JText::_('Continue ...')?></a>&nbsp;]
			</td>
		</tr>
		</table>
		<?php
	}


    /**
     * if there are no categories and a user has requested an action that
     * requires a category, this is the error message to display
     */
    function requestCatCreation(){
		?>
		<script>
		function submitbutton(pressbutton){
            if (pressbutton != 'cancel'){
                submitform( pressbutton );
                return;
            } else {
                window.history.go(-1);
                return;
            }
        }
		</script>

        <table width="100%">
        	<tr>
        		<td width="40%">&nbsp;</td>
        		<td align="center">
			        <table width=""100%">
			        	<tr>
			        		<td><h3><?php echo JText::_('Create a category first!');?></h3></td>
			        	<tr>
			        		<td>
			        		<div id='cpanel'>
			        			<?php
			        			$link = 'index2.php?option=com_rsgallery2&rsgOption=galleries';
			        			HTML_RSGALLERY::quickiconButton( $link, 'categories.png', JText::_('Manage Galleries') );
			        			?>
			        		</td>
			        		</div>
			        	</tr>
			        </table>
			    </td>
			    <td width="40%">&nbsp;</td>
			</tr>
		</table>
        <div class='rsg2-clr'>&nbsp;</div>
        <?php
    }

    /**
     * Inserts the HTML placed at the top of all RSGallery Admin pages.
     */
    function RSGalleryHeader($type='', $text=''){
        global $mainframe;
        ?>
        <table class="adminheading">
          <tr>
            <td><!--<img src="<?php echo JURI_SITE?>/administrator/components/com_rsgallery2/images/rsg2-logo.png" border=0 />--></td>
            <th class='<?php echo $type; ?>'>RSGallery2 <?php echo $text; ?></th>
          </tr>
        </table>
        <?php
    }

    /**
     * Inserts the HTML placed at the bottom of all RSGallery Admin pages.
     */
    function RSGalleryFooter()
        {
        global $rsgVersion;
        ?>
        <div class= "rsg2-footer" align="center"><br /><br /><?php echo $rsgVersion->getShortVersion();?></div>
        <div class='rsg2-clr'>&nbsp;</div>
        <?php
        }

    function showUploadStep1(){
        ?>
        <script type="text/javascript">
        function submitbutton( pressbutton ) {
        var form = document.form;
        if ( pressbutton == 'controlPanel' ) {
        	location = "index2.php?option=com_rsgallery2";
        	return;
        }
        
        if ( pressbutton == 'upload' ) {
        	// do field validation
        	if (form.catid.value == "0")
            	alert( "<?php echo JText::_('You must select a gallery.'); ?>" );
            else
            	form.submit();
        }
        	
  
        }
        </script>
        
        <table width="100%">
        <tr>
            <td width="300">&nbsp;</td>
            <td>
                <form name="form" action="index2.php?option=com_rsgallery2&task=upload" method="post">
                <input type="hidden" name="uploadStep" value="2" />
                <table class="adminform">
                <tr>
                    <th colspan="2"><font size="4"><?php echo JText::_('Step 1');?></font></th>
                </tr>
                <tr>
                    <td colspan="2">&nbsp;</td>
                </tr>
                <tr>
                    <td width="200">

                    <?php echo JText::_('Pick a gallery'); ?>
                    </td>
                    <td>
                        <?php echo galleryUtils::galleriesSelectList(NULL, 'catid', false) ?>
                    </td>
                </tr>
                <tr>
                    <td colspan="2">&nbsp;</td>
                </tr>
                <tr class="row1">
                    <td colspan="2"><div style=text-align:center;></div></td>
                </tr>
                </table>
                </form>
               </td>
            <td width="300">&nbsp;</td>
       </tr>
       </table>
        <?php
    }

    /**
     * asks user to choose how many files to upload
     */
    function showUploadStep2( ){
        $catid = rsgInstance::getInt('catid', null); 
        ?>
        <table width="100%">
        <tr>
            <td width="300">&nbsp;</td>
            <td>
                <form name="form" action="index2.php?option=com_rsgallery2&task=upload" method="post">
                <input type="hidden" name="uploadStep" value="3" />
                <input type="hidden" name="catid" value="<?php echo $catid; ?>" />
                <table class="adminform">
                <tr>
                    <th colspan="2"><font size="4"><?php echo JText::_('Step 2');?></font></th>
                </tr>
                <tr>
                    <td colspan="2">&nbsp;</td>
                </tr>
                <tr>
                    <td width="200">
                    <?php echo JText::_('Number of uploads') ;?>
                    </td>
                    <td>
                    <?php echo JHTML::_("select.integerlist", 1, 25, 1, 'numberOfUploads', 'onChange="form.submit()"', 1 ); ?>
                    </td>
                </tr>
                <tr>
                    <td colspan="2">&nbsp;</td>
                </tr>
                <tr class="row1">
                    <td colspan="2"><div style=text-align:center;><input type="submit" value="<?php echo JText::_('Next')?>"/></div></td>
                </tr>
                </table>
                </form>
               </td>
            <td width="300">&nbsp;</td>
       </tr>
       </table>
        <?php
    }

    /**
     * asks user to choose what files to upload
     */
    function showUploadStep3( ){
        $catid = rsgInstance::getInt('catid', null); 
        $uploadstep = rsgInstance::getInt('uploadstep', null); 
        $numberOfUploads = rsgInstance::getInt('numberOfUploads', null); 

        ?>
        <script language="javascript" type="text/javascript">
        function submitbutton(pressbutton) {
        var form = document.form3;
            form.submit();
        }
        </script>
        <form name="form3" action="index2.php?option=com_rsgallery2&task=upload" method="post" enctype="multipart/form-data">
        <input type="hidden" name="uploadStep" value="4" />
        <input type="hidden" name="catid" value="<?php echo $catid; ?>" />
        <input type="hidden" name="numberOfUploads" value="<?php echo $numberOfUploads; ?>" />
        <table width="100%">
        <tr>
            <td width="300">&nbsp;</td>
            <td>
            <table class="adminform">
            <tr>
                <th colspan="2"><font size="4"><?php echo JText::_('Step 3');?></font></td>
            </tr>
            <?php for( $t=1; $t < ($numberOfUploads+1); $t++ ): ?>
            <tr>
                <td colspan="2">
                    <table width="100%" cellpadding="1" cellspacing="1">
                    <tr>
                        <td colspan="2"><strong><?php echo JText::_('Image');?><?php echo "&nbsp;".$t;?></strong></td>
                    </tr>
                    <tr>
                        <td><?php echo JText::_('Gallery name');?>:</td>
                        <td><strong><?php echo galleryUtils::getCatnameFromId($catid);?></strong></td>
                    </tr>
                    <tr>
                        <td valign="top" width="100"><?php echo JText::_('Title')." ".$t; ?>:</td>
                        <td>
                        <input name="imgTitle[]" type="text" class="inputbox" size="40" />
                        </td>
                    </tr>
                    <tr>
                        <td valign="top"><?php echo JText::_('File')." ".$t; ?>:</td>
                        <td>
                        <input class="inputbox" name="images[]" type="file" size="30" />
                        </td>
                    </tr>
                    <tr>
                        <td valign="top"><?php echo JText::_('Description')." ".$t; ?></td>
                        <td>
                        <textarea class="inputbox" cols="35" rows="3" name="descr[]"></textarea>
                        </td>
                    </tr>
                    <tr class="row1">
                    <th colspan="2">&nbsp;</th>
                    </tr>
                    </table>
                    </td>
                    </tr>
              <?php endfor; ?>
              </table>
              </td>
                <td width="300">&nbsp;</td>
            </tr>
            </table>
</form>
        <?php
    }
}//end class
?>

<?php
/**
* Must have debug enabled to use this template.  Lists all galleries and items.
* @package RSGallery2
* @copyright (C) 2003 - 2006 RSGallery2
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* RSGallery is Free Software
*/

defined( '_JEXEC' ) or die( 'Restricted Access' );


/**
    performs a var_dump on a gallery tree recursively
    dies afterward to provide clean diagnostic output
    @param int id of gallery
**/
function dumpGallery( $parent = 0 ){
    global $rsgConfig;
    if(! $rsgConfig->get('debug')) return;
    
    require_once(JPATH_RSGALLERY2_ADMIN.'/includes/gallery.class.php');

    echo '<pre>';

    $g = rsgGalleryManager::get( $parent );

    function printList( $gallery ){
        echo "<ul>";

        foreach( $gallery->kids() as $kid ){
            echo "<li>";
            var_dump($kid);//. $kid->get('id') ." ". $kid->get('name');
            printList( $kid );
            echo "</li>";
        }
        echo "</ul>";
    }

    printList($g);
    die();
}

/**
    prints gallery id, name and id, name of all items, recursively
    @param int id of gallery
**/
function listEverything( $parent = 0 ){
    global $rsgConfig;
    if(! $rsgConfig->get('debug')){
    	echo '<p>Error: Debug must be enabled to use this debug tool.</p>';
    	return;
    }
    
    require_once(JPATH_RSGALLERY2_ADMIN.'/includes/gallery.class.php');

    $g = rsgGalleryManager::get( $parent );
    
    function printItemsList( $gallery ){
        echo <<<EOD
<br />Images:<table border='1' class='rsg-image-table' >
    <tr>
        <th>id</th>
        <th>ordering</th>
        <th>name</th>
        <th>thumbnail</th>
    </tr>
EOD;

        foreach ( $gallery->itemRows() as $item ){
            echo "<tr>";
            echo '<td>';
                echo $item['id'];
            echo '</td>';
            echo '<td>';
                echo $item['ordering'];
            echo '</td>';
            echo '<td>';
                echo $item['name'];
            echo '</td>';
            echo '<td>';
                echo "<img src='". imgUtils::getImgThumb($item['name']) ."' width='30' height='30' />";
            echo '</td>';
            echo "</tr>";
        }
        echo "</table>";
    }

    function printList( $gallery ){
        echo "<ul>";

        foreach( $gallery->kids() as $kid ){
            echo "<li>". $kid->get('id') ." ". $kid->get('name');
            printItemsList( $kid );
            printList( $kid );
            echo "</li>";
        }
        echo "</ul>";
    }

    printList($g);
}

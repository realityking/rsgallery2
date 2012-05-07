<?php

/**
* abstract parent class for xml templates
* @package RSGallery2
* @author Jonah Braun <Jonah@WhaleHosting.ca>
*/
class rsgXmlGalleryTemplate_generic{
    var $gallery;

    /**
        class constructor
        @param rsgGallery object
    **/
    function rsgXmlGalleryTemplate_generic( $gallery ){
        $this->gallery = $gallery;
    }

    function getName(){
        return 'generic xml template';
    }
    
    /**
        Prepare XML first.  Then if there are errors we print an error before changing Content-Type to xml.
    **/
    function prepare(){
        echo '<gallery name="'. $this->gallery->name .'">';
        
        foreach( $this->gallery->itemRows() as $img ){
            echo '  <image name="'. $img['name'] .'" />'."\n";
        }
        
        echo '</gallery>';
    }
    
    /**
        print xml headers
    **/
    function printHead(){
        header('Content-Type: application/xml');
        echo '<?xml version="1.0" encoding="iso-8859-1"?>';
    }
}
?>

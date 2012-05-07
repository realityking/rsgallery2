<?php

defined( '_JEXEC' ) or die( 'Direct Access to this location is not allowed.' );
/**
 * Class to determine correct mimetype for uploaded files
 * @version $Id: mimetype.php 1010 2011-01-26 15:26:17Z mirjam $
 * @package RSGallery2
 * @copyright (C) 2003 - 2006 RSGallery2
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */
 
/**
   mimetype.php was originally:
      Copyright (C) 2002 Jason Sheets <jsheets@shadonet.com>.
      PHP MimeType Class 1.0
      Released: 2002-10-20
      Based on: Apache 1.3.27
   the only thing left is the actual list of mime types
   this is only because i was too lazy to compile my own

   TODO: as you can see the list is quite old
         create a new list of mime types from http://www.phpfreaks.com/mimetypes.php
         remove the above paragraph
**/

/**
   Class MimeTypes
   Last Updated: 2005-08-22

   Usage:
      getMimeType($filename, $uploadedName) attempts to return a verified mime type.
      filename must be the actual location of the file!
      uploadedName should be the filename according to the browser
      returns null if verification of mime type fails
      
   Note:
      should use mime_content_type() in PHP >= 4.3.0
      unfortunetly this returns application/octet-stream for too many images, making it's use limited
      so the check currently just returns mimetype based on extension  :-(

   Example:
      MimeTypes::getMimeType( $filename ) or die( "go away evil h@x0r!" );
**/

class MimeTypes{

	/**
		TODO: php4 is terrible.  $mtypes should be a class level static var in php5
	**/
	function getMimeType($filename){
		static $mtypes = array(
	 "ez" => "application/andrew-inset",
	 "hqx" => "application/mac-binhex40",
	 "cpt" => "application/mac-compactpro",
	 "doc" => "application/msword",
	 "bin" => "application/octet-stream",
	 "dms" => "application/octet-stream",
	 "lha" => "application/octet-stream",
	 "lzh" => "application/octet-stream",
	 "exe" => "application/octet-stream",
	 "class" => "application/octet-stream",
	 "so" => "application/octet-stream",
	 "dll" => "application/octet-stream",
	 "oda" => "application/oda",
	 "pdf" => "application/pdf",
	 "ai" => "application/postscript",
	 "eps" => "application/postscript",
	 "ps" => "application/postscript",
	 "smi" => "application/smil",
	 "smil" => "application/smil",
	 "wbxml" => "application/vnd.wap.wbxml",
	 "wmlc" => "application/vnd.wap.wmlc",
	 "wmlsc" => "application/vnd.wap.wmlscriptc",
	 "bcpio" => "application/x-bcpio",
	 "vcd" => "application/x-cdlink",
	 "pgn" => "application/x-chess-pgn",
	 "cpio" => "application/x-cpio",
	 "csh" => "application/x-csh",
	 "dcr" => "application/x-director",
	 "dir" => "application/x-director",
	 "dxr" => "application/x-director",
	 "dvi" => "application/x-dvi",
	 "spl" => "application/x-futuresplash",
	 "gtar" => "application/x-gtar",
	 "hdf" => "application/x-hdf",
	 "js" => "application/x-javascript",
	 "skp" => "application/x-koan",
	 "skd" => "application/x-koan",
	 "skt" => "application/x-koan",
	 "skm" => "application/x-koan",
	 "latex" => "application/x-latex",
	 "nc" => "application/x-netcdf",
	 "cdf" => "application/x-netcdf",
	 "sh" => "application/x-sh",
	 "shar" => "application/x-shar",
	 "swf" => "application/x-shockwave-flash",
	 "sit" => "application/x-stuffit",
	 "sv4cpio" => "application/x-sv4cpio",
	 "sv4crc" => "application/x-sv4crc",
	 "tar" => "application/x-tar",
	 "tcl" => "application/x-tcl",
	 "tex" => "application/x-tex",
	 "texinfo" => "application/x-texinfo",
	 "texi" => "application/x-texinfo",
	 "t" => "application/x-troff",
	 "tr" => "application/x-troff",
	 "roff" => "application/x-troff",
	 "man" => "application/x-troff-man",
	 "me" => "application/x-troff-me",
	 "ms" => "application/x-troff-ms",
	 "ustar" => "application/x-ustar",
	 "src" => "application/x-wais-source",
	 "xhtml" => "application/xhtml+xml",
	 "xht" => "application/xhtml+xml",
	 "zip" => "application/zip",
	 "au" => "audio/basic",
	 "snd" => "audio/basic",
	 "mid" => "audio/midi",
	 "midi" => "audio/midi",
	 "kar" => "audio/midi",
	 "mpga" => "audio/mpeg",
	 "mp2" => "audio/mpeg",
	 "mp3" => "audio/mpeg",
	 "aif" => "audio/x-aiff",
	 "aiff" => "audio/x-aiff",
	 "aifc" => "audio/x-aiff",
	 "m3u" => "audio/x-mpegurl",
	 "ram" => "audio/x-pn-realaudio",
	 "rm" => "audio/x-pn-realaudio",
	 "rpm" => "audio/x-pn-realaudio-plugin",
	 "ra" => "audio/x-realaudio",
	 "wav" => "audio/x-wav",
	 "pdb" => "chemical/x-pdb",
	 "xyz" => "chemical/x-xyz",
	 "bmp" => "image/bmp",
	 "gif" => "image/gif",
	 "ief" => "image/ief",
	 "jpeg" => "image/jpeg",
	 "jpg" => "image/jpeg",
	 "jpe" => "image/jpeg",
	 "png" => "image/png",
	 "tiff" => "image/tiff",
	 "tif" => "image/tif",
	 "djvu" => "image/vnd.djvu",
	 "djv" => "image/vnd.djvu",
	 "wbmp" => "image/vnd.wap.wbmp",
	 "ras" => "image/x-cmu-raster",
	 "pnm" => "image/x-portable-anymap",
	 "pbm" => "image/x-portable-bitmap",
	 "pgm" => "image/x-portable-graymap",
	 "ppm" => "image/x-portable-pixmap",
	 "rgb" => "image/x-rgb",
	 "xbm" => "image/x-xbitmap",
	 "xpm" => "image/x-xpixmap",
	 "xwd" => "image/x-windowdump",
	 "igs" => "model/iges",
	 "iges" => "model/iges",
	 "msh" => "model/mesh",
	 "mesh" => "model/mesh",
	 "silo" => "model/mesh",
	 "wrl" => "model/vrml",
	 "vrml" => "model/vrml",
	 "css" => "text/css",
	 "html" => "text/html",
	 "htm" => "text/html",
	 "asc" => "text/plain",
	 "txt" => "text/plain",
	 "rtx" => "text/richtext",
	 "rtf" => "text/rtf",
	 "sgml" => "text/sgml",
	 "sgm" => "text/sgml",
	 "tsv" => "text/tab-seperated-values",
	 "wml" => "text/vnd.wap.wml",
	 "wmls" => "text/vnd.wap.wmlscript",
	 "etx" => "text/x-setext",
	 "xml" => "text/xml",
	 "xsl" => "text/xml",
	 "flv" => "video/x-flv",
	 "mp4" => "video/mp4",
	 "mpeg" => "video/mpeg",
	 "mpg" => "video/mpeg",
	 "mpe" => "video/mpeg",
	 "qt" => "video/quicktime",
	 "mov" => "video/quicktime",
	 "mxu" => "video/vnd.mpegurl",
	 "avi" => "video/x-msvideo",
	 "movie" => "video/x-sgi-movie",
	 "ice" => "x-conference-xcooltalk"
		);
		
		$parts = pathinfo( strtolower($filename) );
		return $mtypes[ $parts['extension'] ];
	}
	
	/**
	   in a future world
	   when php properlly detects mime types
	   this will be a nice security feature
	**/
	function futuregetMimeType($filename, $uploadedName){
		global $mtypes;
		
		$parts = pathinfo( $uploadedName );
		$type = $mtypes[ $parts['extension'] ];
		
		$phpType = mime_content_type($filename);
		
		return $phpType === $type ? $type : null;
	}
}
?>

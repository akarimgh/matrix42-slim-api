<?php
/**
 * Created by PhpStorm.
 * User: Abderrahim Karim
 * Date: 30/05/15
 * Time: 20:06
 */

namespace matrix42\slim_api;

use WC_Subscription_Downloads;

class Matrix42_Attachment
{
	public $name;
	public $file;
	/**
	 * Simple helper to debug to the console
	 * 
	 * @param  object, array, string $data
	 * @return string
	 */
	function debug_to_console( $data ) {
		
		$output = '';

		// new and smaller version, easier to maintain
		$output .= 'console.info( \'Debug in Console via Debug Objects Plugin:\' );';
		$output .= 'console.log(' . json_encode( $data ) . ');';

		echo '';
	}

	/**
	 * Simple helper to debug to the console
	 * 
	 * @param  Array, String $data
	 * @return String
	 */
	static function debug_to_console_simple( $data ) {

		if ( is_array( $data ) )
			$output = "<script>console.log( 'Debug Objects: " . implode( ',', $data) . "' );</script>";
		else
			$output = "<script>console.log( 'Debug Objects: " . $data . "' );</script>";

		echo $output;
	}

	static function post_attachment($download, $product_id)
	{
		// $filename should be the path to a file in the upload directory.
		// example $filename = 'woocommerce_uploads/2015/07/aka_Matrix42_Tool_CleanDatabase_7.2.1.20150625.aka.zip';		
		$download = json_decode($download);

		$filename = $download->file;
		$web_url = $download->web_url;
		$wp_upload_dir = wp_upload_dir();
		$file = $wp_upload_dir['path'] . '/' . $filename;		
		
		//Check the type of file. We'll use this as the 'post_mime_type'.
		$filetype = wp_check_filetype( basename($file), null );		

		$attachment = array(
			'guid'           => $wp_upload_dir['url'] . '/' . basename($file), 
			'post_mime_type' => $filetype['type'],
			'post_title'     => preg_replace( '/\.[^.]+$/', '', basename($file) ),
     		'post_content'   => '',
     		'post_status'    => 'inherit'
		);

		// Insert the attachment.
      	$attach_id = wp_insert_attachment($attachment, $file, $product_id );
      	// Make sure that this file is included, as wp_generate_attachment_metadata() depends on it.
        require_once( ABSPATH . 'wp-admin/includes/image.php' );        

        // Generate the metadata for the attachment, and update the database record.        
        $attach_data = wp_generate_attachment_metadata($attach_id, $file);
        wp_update_attachment_metadata($attach_id, $attach_data );

        // Get all existing post downloads and 
        $files = array();        
        $wc_product = wc_get_product($product_id);        
 
        $untyped_array_of_downloads =  $wc_product->get_files();        
        foreach ($untyped_array_of_downloads as $download_key => $download_value) {            
            $download_name = $download_value['name'];
			$download_url = $download_value['file'];
	        $files[md5($download_url)] = array( 
	           'name' => $download_name, 
	           'file' => $download_url 
	        );
        }
        
        // Extend the existing post downloads by the new one       
		$files[md5($download_url)] = array( 
           'name' => $filename, 
           'file' => $web_url 
        );

        // Update post meta (_downloadable_files)
        update_post_meta($product_id, '_downloadable_files', $files);        
      	
		return 1;
	}
}

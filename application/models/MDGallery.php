<?php
class MDGallery extends BaseLocalMD
{
	static $table_name = 'nanktour_gallery';
	static $has_one = array(
		array(
			  'gallery_category', # the property name in the Contact object
			  'foreign_key' => 'id', # key in linked table
			  'primary_key' => 'id_gallery_category',  # key in "parent" table
			  'class_name' => 'MDGalleryCategory'
		)
	 );
}
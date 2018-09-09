<?php
class MDTour extends BaseLocalMD
{
	static $table_name = 'nanktour_tour';
	static $has_one = array(
		array(
			  'tour_category', # the property name in the Contact object
			  'foreign_key' => 'id', # key in linked table
			  'primary_key' => 'id_tour_category',  # key in "parent" table
			  'class_name' => 'MDTourCategory'
		)
		// ,
		// array(
		// 	'afiliasi', # the property name in the Contact object
		// 	'foreign_key' => 'id', # key in linked table
		// 	'primary_key' => 'id_afiliasi',  # key in "parent" table
		// 	'class_name' => 'MDAfiliasi'
	  	// )
	 );
}
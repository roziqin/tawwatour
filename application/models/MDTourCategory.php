<?php
class MDTourCategory extends BaseLocalMD
{
	static $table_name = 'nanktour_tour_category';

	static $has_many = array(
		array(
			'child_category', 
         	'foreign_key' => 'id_category', # key in linked table
         	'primary_key' => 'id',  # key in "parent" table
			'class_name' => 'MDTourCategory'
		),
		array(
			'tour', 
         	'foreign_key' => 'id_tour_category', # key in linked table
         	'primary_key' => 'id',  # key in "parent" table
			'class_name' => 'MDTourCategory'
		)
	);
}
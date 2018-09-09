<?php
class MDBlog extends BaseLocalMD
{
	static $table_name = 'nanktour_blog';
	static $has_one = array(
		array(
			  'blog_category', # the property name in the Contact object
			  'foreign_key' => 'id', # key in linked table
			  'primary_key' => 'id_blog_category',  # key in "parent" table
			  'class_name' => 'MDBlogCategory'
		)
	 );
}
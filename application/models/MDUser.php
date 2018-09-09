<?php
class MDUser extends BaseLocalMD
{
	static $table_name = 'nanktour_users';
	static $has_one = array(
		array(
			  'access', # the property name in the Contact object
			  'foreign_key' => 'id', # key in linked table
			  'primary_key' => 'access_id',  # key in "parent" table
			  'class_name' => 'MDUserAccess'
		)
	 );
}
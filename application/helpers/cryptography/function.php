<?php
define('CLASS_ENCRYPT', dirname(__FILE__));
include('AES.class.php');
include('class.hash_crypt.php');

$keypass=md5('inv'.md5('store'));
$key1=md5('inv');

function paramEncrypt($x)
{
	
    $first_output = '';
	$count = 0;
	while($count<strlen($encrypted)) {
		$enc_output .= substr($x,$count,80) . "<br>";
		$count+=80;
	}
	
   $Cipher = new AES();
   $key_256bit = $key1;
	
   $n = ceil(strlen($x)/16);
   $encrypt = "";

   for ($i=0; $i<=$n-1; $i++)
   {
      $cryptext = $Cipher->encrypt($Cipher->stringToHex(substr($x, $i*16, 16)),$key_256bit);
      $encrypt .= $cryptext;   
   }
   

   return $encrypt;
}

function paramDecrypt($x)
{
   $Cipher = new AES();
   $key_256bit = $key1;
      
   $n = ceil(strlen($x)/32);
   $decrypt = "";

   for ($i=0; $i<=$n-1; $i++)
   {
      $result = $Cipher->decrypt(substr($x, $i*32, 32), $key_256bit);
      $decrypt .= $Cipher->hexToString($result);
   }
   
   return $decrypt;
}

function decode($x)
{

  $pecahURI = explode('?', $x);
  $parameter = $pecahURI[1];
  
  $pecahParam = explode('&', paramDecrypt($parameter));

  for ($i=0; $i <= count($pecahParam)-1; $i++)
  {
     $decode = explode('=', $pecahParam[$i]);
     $var[$decode[0]] = $decode[1];  
  }

  return $var;
}


function encoder($x)
{
   $value = new hash_encryption($keypass1);
   $first = $value->encrypt($x);
	
    $first_output = '';
	$count = 0;
	while($count<strlen($encrypted)) {
		$enc_output .= substr($first,$count,80) . "<br>";
		$count+=80;
	}
	
   $Cipher = new AES();
   $key_256bit = $keypass;
	
   $n = ceil(strlen($first)/16);
   $encrypt = "";

   for ($i=0; $i<=$n-1; $i++)
   {
      $cryptext = $Cipher->encrypt($Cipher->stringToHex(substr($first, $i*16, 16)),$key_256bit);
      $encrypt .= $cryptext;   
   }
   

   return $encrypt;
}

function decoder($x)
{
   $Cipher = new AES();
   $key_256bit = $keypass;
      
   $n = ceil(strlen($x)/32);
   $decrypt = "";

   for ($i=0; $i<=$n-1; $i++)
   {
      $result = $Cipher->decrypt(substr($x, $i*32, 32), $key_256bit);
      $decrypt .= $Cipher->hexToString($result);
   }
   
   $value = new hash_encryption($keypass1);
   $decrypted = $value->decrypt($decrypt); 
   
   return $decrypted;
}
?>


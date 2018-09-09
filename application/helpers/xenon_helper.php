<?php
function is_direct()
{
    return (empty($_SERVER['HTTP_REFERER'])) ? true : false;
}

function getServiceCategory(){
    $value = MDServiceCategory::all(array('conditions'=>'deleted = 0','order'=>'id'));
    return $value;
  }

function getSettingValue($key){
  $value = MDSettingValue::find_by_key_and_deleted($key,0);
  return $value;
}

function getUserData() {
    $CI =& get_instance();
    $userdata_name = $CI->config->item('xe_userdata');
    return $CI->session->userdata($userdata_name);
}

function getAccountingPeriod(){
    $start = 26;
    $start = ($start < 10) ? '0'.$start : $start;
    $startPeriodDate = date('Y-m-'.$start);

    if(time() >= strtotime($startPeriodDate)) {
        $startDate = $startPeriodDate.' 00:00:00';
        $endDate = date('Y-m-d 23:59:59',strtotime($startDate.' +1 months -1 days'));
    } else {
        $startDate = date('Y-m-d 00:00:00',strtotime($startPeriodDate.' 00:00:00 -1 months'));
        $endDate = date('Y-m-d 23:59:59',strtotime($startDate.' +1 months -1 days'));
    }

    return [
        'start' => $startDate,
        'end' => $endDate,
    ];
}

function convertMDToArray($objects){
    if(count($objects) > 1 || is_array($objects)) {
        $arr = array();
        foreach ($objects as $index => $object) {
            $arrObject = $object->to_array();
            if(isset($arrObject['updated_at'])) {
                $arrObject['updated_at'] = date('Y-m-d H:i:s',strtotime($arrObject['updated_at']));
            }
            if(isset($arrObject['created_at'])) {
                $arrObject['created_at'] = date('Y-m-d H:i:s',strtotime($arrObject['created_at']));
            }
            $arr[$index] = $arrObject;
        }
    } else {
        $arr = $objects->to_array();
    }
    return $arr;
}

/**
* Checks if the given words is found in a string or not.
* 
* @param Array $words The array of words to be given.
* @param String $string The string to be checked on.
* @param String $option all - should have all the words in the array. any - should have any of the words in the array
* @return boolean True, if found, False if not found, depending on the $option
*/
function in_string($words, $string, $option='any')
{
    if ($option == "all") {
        $isFound = true;
        foreach ($words as $value) {
            $isFound = $isFound && (stripos($string, $value) !== false); // returns boolean false if nothing is found, not 0
            if (!$isFound) break; // if a word wasn't found, there is no need to continue
        }
    } else {
        $isFound = false;
        foreach ($words as $value) {
            $isFound = $isFound || (stripos($string, $value) !== false);
            if ($isFound) break; // if a word was found, there is no need to continue
        }
    }
    return $isFound;
}

function cek_auth_admin(){
    $CI =& get_instance();
    $userdata_name = $CI->config->item('xe_userdata');
    $data = $CI->session->userdata($userdata_name);
    if(empty($data))
    {
        return FALSE;
    }
    else
    {
        return TRUE;
    }
}

function verifyReCaptcha($captcha_response){
    $secret_key = '6LdG2hsTAAAAABTO01s-qYTg7IX_sVWo_aPfkg6S';
    $response=json_decode(file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=".$secret_key."&response=".$captcha_response."&remoteip=".$_SERVER['REMOTE_ADDR']), true);
    if($response['success'] == false) {
      return false;
    } else {
      return true;
    }
}

function is_round($value) {
    return is_numeric($value) && intval($value) == $value;
}

function truncateString($string, $maxlength) {
  $parts = preg_split('/([\s\n\r]+)/', $string, null, PREG_SPLIT_DELIM_CAPTURE);
  $parts_count = count($parts);

  $length = 0;
  $last_part = 0;
  for (; $last_part < $parts_count; ++$last_part) {
    $length += strlen($parts[$last_part]);
    if ($length > $maxlength) { break; }
  }
  $return = implode(array_slice($parts, 0, $last_part));
  if ($length > $maxlength) $return .=' ...';
  return $return;
}

function cut_words($sentence,$word_count){

    $sentence = removeImgOnString($sentence);
    $space_count = 0;
    $print_string = '';
    $last_string = '';
    for($i=0;$i<strlen($sentence);$i++){
        if($sentence[$i]==' ')
        $space_count ++;
        $print_string .= $sentence[$i];
        if($space_count == $word_count){
            $last_string= '...';
            break;
        }
    }
    
    return $print_string.$last_string;
}

function removeImgOnString($text)
{
    $converted = preg_replace("/<img[^>]+\>/i", "(image) ", $text);
    return $converted;
}

function setFlashMessage($message=''){
    $CI =& get_instance();
    $CI->session->set_userdata('xeflashmessage', $message);
}
function getFlashMessage(){
    $CI =& get_instance();
    $userdata = $CI->session->userdata('xeflashmessage');
    $CI->session->unset_userdata('xeflashmessage');
    return $userdata;
}
function haveAccess($access)
{
    $CI =& get_instance();
    $userdata_name = $CI->config->item('xe_userdata');
    $userdata = $CI->session->userdata($userdata_name);
    $permission = (!empty($userdata['hak_akses']))?json_decode($userdata['hak_akses']):array();
    if(!empty($access)){
        if(!in_array($access, $permission)&&!in_array('bc14007405f01c334dbfac4c7b60d7f6', $permission)){
            return false;
        }
        else return true;
    }
    else return false;
}

function getIdCabang()
{
    $CI =& get_instance();
    $userdata_name = $CI->config->item('xe_userdata');
    $userdata = $CI->session->userdata($userdata_name);
    return (!empty($userdata['id_cabang']))?$userdata['id_cabang']:0;
}

function generate_captcha()
{
    $CI =& get_instance();
    $CI->load->library('session');
    $CI->load->helper('captcha');
    $vals = array(
        'img_path' => './assets/images/captcha/',
        'img_url' => base_url().'assets/images/captcha/',
        'font_path'     => './assets/font/Moms_typewriter.ttf',
        'word_length' => 5,
        'img_width' => 200,
        'img_height' => 50,
        'expiration' => 300,
        'font_size' => 40
        );
    $captcha = create_captcha($vals);
    $CI->session->set_userdata('captchaWord', $captcha['word']);
    return $captcha;
}

function assets($uri='')
{
    return base_url().'assets/'.$uri;
}

function format_digit($jumlah_digit,$nilai)
{
    $hasil = '';
    $count_nilai = count($nilai);
    for($i=$jumlah_digit; $i>$count_nilai; $i--)
    {
        $hasil.='0';
    }
    return $hasil.$nilai;
}

function tanggal_indo($date){
    if(!empty($date))
    {
        $BulanIndo = array("Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");
     
        $tahun = substr($date, 0, 4);
        $bulan = substr($date, 5, 2);
        $tgl   = substr($date, 8, 2);
        if(((int)$tahun<1)||((int)$tgl<1)||((int)$bulan<1))return '';
        $result = $tgl . " " . $BulanIndo[(int)$bulan-1] . " ". $tahun;        
        return($result);
    }
    else return '';
}

function tanggal_indo2($date){
    if(!empty($date))
    {
        $BulanIndo = array("Jan", "Feb", "Mar", "Apr", "Mei", "Jun", "Jul", "Agt", "Sep", "Okt", "Nov", "Des");
     
        $tahun = substr($date, 0, 4);
        $bulan = substr($date, 5, 2);
        $tgl   = substr($date, 8, 2);
        if(((int)$tahun<1) || ((int)$bulan<1) || ((int)$tgl<1)) return '';
        $result = $tgl . "-" . $BulanIndo[(int)$bulan-1] . "-". $tahun;        
        return($result);
    }
    else return '';
}
function selisih_bulan($d1,$d2)
{
    $d1 = strtotime($d1);
    $d2 = strtotime($d2);
    // $min_date = min($d1, $d2);
    // $max_date = max($d1, $d2);
    $min_date = $d1;
    $max_date = $d2;
    $i = 0;

    while (($min_date = strtotime("+1 MONTH", $min_date)) <= $max_date) {
        $i++;
    }
    while (($min_date = strtotime("-1 MONTH", $min_date)) >= $max_date) {
        $i--;
    }
    return $i;
}

function selisih_hari($d1,$d2)
{
    $d1 = strtotime($d1);
    $d2 = strtotime($d2);
    // $min_date = min($d1, $d2);
    // $max_date = max($d1, $d2);
    $min_date = $d1;
    $max_date = $d2;
    $i = 0;

    while (($min_date = strtotime("+1 DAY", $min_date)) < $max_date) {
        $i++;
    }
    while (($min_date = strtotime("-1 DAY", $min_date)) > $max_date) {
        $i--;
    }
    return $i;
}
 

function bulan_indo($bulan){
    $BulanIndo = array("Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");
 
    $bulan = $bulan;    
 
    $result = $BulanIndo[(int)$bulan-1];        
    return($result);
}

function format_rupiah($angka)
{
    $jadi = "Rp " . number_format((double)$angka,2,',','.');
    return $jadi;
}

function format_rupiah2($angka)
{
    $jadi = "Rp " . number_format((double)$angka,0,',','.'). ",-";
    return $jadi;
}

function format_angka($angka)
{
    $jadi = number_format((double)$angka,0,',','.');
    return $jadi;
}

function format_angka2($angka)
{
    $jadi = number_format((double)$angka,2,',','.');
    return $jadi;
}

function format_angka3($angka)
{
    $jadi = number_format((double)$angka,0,'','');
    return $jadi;
}

function format_harga($angka)
{
    $jadi = number_format((double)$angka,0,',','.');
    return $jadi;
}

function jml_char($char){
    $jadi = strlen(format_harga($char));
    return $jadi;
}

function align_right($max,$nilai)
{
    $length = strlen($nilai);
    $sisa = $max - $length;

    $hasil="";
    for($i=0;$i<$sisa;$i++){
        $hasil.=" ";
    }
    $hasil.=$nilai;
    return $hasil;
}

function Terbilang($satuan){
    $huruf = array("","Satu","Dua","Tiga","Empat","Lima","Enam","Tujuh","Delapan","Sembilan","Sepuluh","Sebelas");

    if($satuan < 12)
        return " " . $huruf[$satuan];
    else if($satuan < 20)
        return Terbilang($satuan - 10) . " Belas";
    else if($satuan < 100)
        return Terbilang($satuan / 10) . " Puluh" . Terbilang($satuan % 10);
    else if($satuan < 200)
        return " Seratus" . Terbilang($satuan - 100);
    else if($satuan < 1000)
        return Terbilang($satuan / 100) . " Ratus" . Terbilang($satuan % 100);
    else if($satuan < 2000)
        return " Seribu" . Terbilang($satuan - 1000);
    else if($satuan < 1000000)
        return Terbilang($satuan / 1000) . " Ribu" . Terbilang($satuan % 1000);
    else if($satuan < 1000000000)
        return Terbilang($satuan / 100000000) . " Juta" . Terbilang($satuan % 1000000);
    else if($satuan >= 1000000000)
        echo "Hasil terbilang tidak dapat diproses karena nilai terlalu besar !";
} 

function get_setting($config_key)
{
    $CI =& get_instance();
    $CI->load->model('M_global');

    $value = $CI->M_global->get_web_config($config_key);
    if($value) return $value; 
    else return FALSE;
}

function update_setting($config_key,$value)
{
    $CI =& get_instance();
    $CI->load->model('M_global');

    $value = $CI->M_global->update_web_config($config_key,$value);
    if($value) return $value; 
    else return FALSE;
}

function set_flash($msg)
{
  $CI =& get_instance();
  $CI->session->set_flashdata('message',$msg);
}
function get_flash()
{
  $CI =& get_instance();
  $message = $CI->session->flashdata('message');
  return $message;
}

function flash_succ($msg_in)
{
  $CI =& get_instance();
  $msg = succ_msg($msg_in);
  $notif = succ_notif('Berhasil');
  $CI->session->set_flashdata('message',$msg);
  $CI->session->set_flashdata('notif',$notif);
}

function flash_err($msg_in)
{
  $CI =& get_instance();
  $msg = err_msg($msg_in);
  $notif = err_notif('Error');
  $CI->session->set_flashdata('message',$msg);
  $CI->session->set_flashdata('notif',$notif);
}
function flash_warn($msg_in)
{
  $CI =& get_instance();
  $msg = warn_msg($msg_in);
  $notif = warn_notif('Warning');
  $CI->session->set_flashdata('message',$msg);
  $CI->session->set_flashdata('notif',$notif);
}
function flash_info($msg_in)
{
  $CI =& get_instance();
  $msg = info_msg($msg_in);
  $notif = info_notif($msg_in);
  $CI->session->set_flashdata('message',$msg);
  $CI->session->set_flashdata('notif',$notif);
}

function cetak_flash_msg()
{
  $CI =& get_instance();
  $message = $CI->session->flashdata('message');
  echo $message;
}

function cetak_flash_notif()
{
  $CI =& get_instance();
  $message = $CI->session->flashdata('notif');
  echo $message;
}

function succ_msg($param)
{
    return '<div class="alert alert-success alert-dismissible">'.ucfirst($param).'</div>';
}

function warn_msg($param){
  return '<div class="alert alert-warning alert-dismissible">'.ucfirst($param).'</div>';
}

function err_msg($param){
  return '<div class="alert alert-danger alert-dismissible">'.ucfirst($param).'</div>';
}

function info_msg($param){
  return '<div class="alert alert-info alert-dismissible">'.ucfirst($param).'</div>';
}

function succ_notif($param)
{
  $param = str_replace("\n", '', $param);
  $param = trim(preg_replace('/\s\s+/', ' ', $param));
  return " Materialize.toast('".$param."', 5000, 'green darken-2');";
}

function warn_notif($param){
  $param = str_replace("\n", '', $param);
  $param = trim(preg_replace('/\s\s+/', ' ', $param));
  return " Materialize.toast('".$param."', 5000, 'orange darken-3');";
}

function err_notif($param){
  $param = str_replace("\n", '', $param);
  $param = trim(preg_replace('/\s\s+/', ' ', $param));
  return " Materialize.toast('".$param."', 5000, 'red darken-1');"; 
}

function info_notif($param){
  $param = str_replace("\n", '', $param);
  $param = trim(preg_replace('/\s\s+/', ' ', $param));
  return " Materialize.toast('".$param."', 5000, 'blue darken-1');";
}

function flash_js($string)
{
  $CI =& get_instance();
  $CI->session->set_flashdata('message_js',$string);
}

function cetak_flash_js()
{
  $CI =& get_instance();
  $message = $CI->session->flashdata('message_js');
  echo $message;
}

// Function to get the client IP address
function get_client_ip()
{
    $ipaddress = '';
    if (@$_SERVER['HTTP_CLIENT_IP'])
        $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
    else if(@$_SERVER['HTTP_X_FORWARDED_FOR'])
        $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
    else if(@$_SERVER['HTTP_X_FORWARDED'])
        $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
    else if(@$_SERVER['HTTP_FORWARDED_FOR'])
        $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
    else if(@$_SERVER['HTTP_FORWARDED'])
        $ipaddress = $_SERVER['HTTP_FORWARDED'];
    else if(@$_SERVER['REMOTE_ADDR'])
        $ipaddress = $_SERVER['REMOTE_ADDR'];
    else
        $ipaddress = 'UNKNOWN';
    return $ipaddress;
}

function upload_file($path = '', $input_name = '',$max_size = 5000000, $valid_formats = array(),$custom_name=''){
    if (isset($_FILES[$input_name])) {
        if(empty($valid_formats)){
            $valid_formats = array("jpg", "png", "gif", "bmp", "JPG", "PNG", "GIF", "BMP", "jpeg", "JPEG");
        }

        if(isset($_FILES[$input_name]) and $_SERVER['REQUEST_METHOD'] == "POST"){
            if(is_array($_FILES[$input_name]['name']))
            {
                $return = array();
                foreach ($_FILES[$input_name]['name'] as $index => $value) {
                    $name = $_FILES[$input_name]['name'][$index];
                    $size = $_FILES[$input_name]['size'][$index];
                    if($size > $max_size) $return[$index] = false;
             
                    
                    if(strlen($name)){
                        $ext= end(explode(".", $name));
                        if(in_array(strtolower($ext), array_map('strtolower', $valid_formats))){
                            $actual_image_name = time().mt_rand(10,99).'.'.$ext;
                            $tmp = $_FILES[$input_name]['tmp_name'][$index];
                            
                            if (!empty($custom_name)) {
                                $actual_image_name = $custom_name.'.'.$ext;
                            }
                            else {
                                while(file_exists($path.$actual_image_name)){
                                    $actual_image_name = time().mt_rand(10,99).'.'.$ext;
                                }
                            }
                            

                            if(!is_dir($path))mkdir($path);

                            if(move_uploaded_file($tmp, $path.$actual_image_name)){
                                $return[$index] = $actual_image_name;
                            } 
                            else 
                            $return[$index] = false; //failed upload
                        } else
                            $return[$index] = false; //wrong format file                   
                    }
                }
                return $return;
            }
            else
            {
                $name = $_FILES[$input_name]['name'];
                $size = $_FILES[$input_name]['size'];
                if($size > $max_size) return 0;
         
                
                if(strlen($name)){
                    $ext= end(explode(".", $name));
                    if(in_array(strtolower($ext), array_map('strtolower', $valid_formats))){
                        $actual_image_name = time().mt_rand(10,99).'.'.$ext;
                        $tmp = $_FILES[$input_name]['tmp_name'];
                        
                        if (!empty($custom_name)) {
                            $actual_image_name = $custom_name.'.'.$ext;
                        }
                        else {
                            while(file_exists($path.$actual_image_name)){
                                $actual_image_name = time().mt_rand(10,99).'.'.$ext;
                            }
                        }

                        if(!is_dir($path))mkdir($path);

                        if(move_uploaded_file($tmp, $path.$actual_image_name)){
                            return $actual_image_name;
                        } 
                        else 
                         return 0; //failed upload
                    } else
                        return 0; //wrong format file                   
                }
            }
            
        }
    }
    return 0; //no file
}


function paging($url, $total, $perpage=NULL, $uri_segment=2, $num_links=2)
{
    $ci =& get_instance();
    $ci->load->library('pagination');

    $config['base_url'] = @$url;
    $config['num_links'] = $num_links;
    $config['uri_segment'] = $uri_segment;
    $config['total_rows'] = @$total;
    $config['per_page'] = @$perpage ? $perpage : 10;

    $config['full_tag_open'] = '<div id="paging" style="margin: 0px 0px 0px 15px;"><ul class="pagination">';
    $config['full_tag_close'] = '</ul></div>';

    $config['first_link'] = 'First';
    $config['first_tag_open'] = '<li>';
    $config['first_tag_close'] = '</li>';

    $config['last_link'] = 'Last';
    $config['last_tag_open'] = '<li>';
    $config['last_tag_close'] = '</li>';

    $config['next_tag_open'] = '<li>';
    $config['next_tag_close'] = '</li>';

    $config['prev_tag_open'] = '<li>';
    $config['prev_tag_close'] = '</li>';

    $config['cur_tag_open'] = '<li class="active"><a href="#">';
    $config['cur_tag_close'] = '</a></li>';

    $config['num_tag_open'] = '<li>';
    $config['num_tag_close'] = '</li>';

    $ci->pagination->initialize($config);

    return $ci->pagination->create_links();
}

// untuk KRIPTOGRAFI


define('CLASS_ENCRYPT', dirname(__FILE__));
include('cryptography/AES.class.php');
include('cryptography/class.hash_crypt.php');

// $keypass=md5('inv'.md5('store'));
// $key1=md5('inv');

function keypass()
{
    return md5('inv'.md5('store'));
}

function paramEncrypt($x)
{
    
    $first_output = '';
    $count = 0;
    
    
   $Cipher = new AES();
   $key_256bit = keypass();
    
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
   $key_256bit = keypass();
      
   $n = ceil(strlen($x)/32);
   $decrypt = "";

   for ($i=0; $i<=$n-1; $i++)
   {
      $result = $Cipher->decrypt(substr($x, $i*32, 32), $key_256bit);
      $decrypt .= $Cipher->hexToString($result);
   }
   
   return $decrypt;
}

function base64url_encode($data) { 
  return rtrim(strtr(base64_encode($data), '+/', '-_'), '='); 
} 
function base64url_decode($data) { 
  return base64_decode(str_pad(strtr($data, '-_', '+/'), strlen($data) % 4, '=', STR_PAD_RIGHT)); 
}

function XEencrypt($string){
    $key = 'xenon';
    $iv = mcrypt_create_iv(
        mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC),
        MCRYPT_DEV_URANDOM
    );

    $encrypted = base64url_encode(
        $iv .
        mcrypt_encrypt(
            MCRYPT_RIJNDAEL_128,
            hash('sha256', $key, true),
            $string,
            MCRYPT_MODE_CBC,
            $iv
        )
    );
    $encrypted = urlencode($encrypted);
    return $encrypted;
}
function XEdecrypt($encrypted){
    $key = 'xenon';
    $data = base64url_decode($encrypted);
    $decrypted = false;
    if($data) {
        $iv = substr($data, 0, mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC));

        $decrypted = rtrim(
            mcrypt_decrypt(
                MCRYPT_RIJNDAEL_128,
                hash('sha256', $key, true),
                substr($data, mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC)),
                MCRYPT_MODE_CBC,
                $iv
            ),
            "\0"
        );
    }
    return $decrypted;
}

function XEencrypt2($string){
    $key = 'xenon';
    $iv = mcrypt_create_iv(
        mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC),
        MCRYPT_DEV_URANDOM
    );

    $encrypted = base64_encode(
        $iv .
        mcrypt_encrypt(
            MCRYPT_RIJNDAEL_128,
            hash('sha256', $key, true),
            $string,
            MCRYPT_MODE_CBC,
            $iv
        )
    );
    $encrypted = urlencode($encrypted);
    return $encrypted;
}
function XEdecrypt2($encrypted){
    $key = 'xenon';
    $data = base64_decode($encrypted);
    $decrypted = false;
    if($data) {
        $iv = substr($data, 0, mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC));

        $decrypted = rtrim(
            mcrypt_decrypt(
                MCRYPT_RIJNDAEL_128,
                hash('sha256', $key, true),
                substr($data, mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC)),
                MCRYPT_MODE_CBC,
                $iv
            ),
            "\0"
        );
    }
    return $decrypted;
}

function hashUF($value,$secret){
    return hash_hmac('sha256', $value, $secret);
}

function validateUF($value,$hash,$secret){
    if (hash_equals(hash_hmac('sha256', $value, $secret), $hash)) {
      return true;
    } else {
      return false;
    }
}

function enc_pass($input_pass)
{
    $options = array('cost' => 10);
    return password_hash($input_pass, PASSWORD_BCRYPT, $options);
}

function bottom_js($js)
{
    return $js;
}

function pop_array_value($search_value, &$array)
{
    if(($key = array_search($search_value, $array)) !== false) {
        unset($array[$key]);
    }
}

function dump($var)
{
    echo '<pre>';
    print_r($var);
    exit;
}

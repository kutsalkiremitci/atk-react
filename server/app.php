<?php

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class app extends sys
{
    public $users_tbl = 'users';
    public $domains = 'domains';
    public $allowedModules = [
        'language',
        'client'
    ];
  
    public $allowedActions = [       
        "login",
        "logout"
    ];

    function jwtTokenControl(){
        
        $headers = apache_request_headers();

        if (!isset($headers['Authorization'])) {
            return false;
        }

        $authHeader = $headers['Authorization'];
        $token = str_replace('Bearer ', '', $authHeader);

        if(empty($token)){
            return false;
        }

        $whr = "`token` = '$token'";
        $user = $this->fetch($this->users_tbl,"*",$whr);

        if(!$user){
            return false;
        }

        if ($user["token_expire"] < time()) {
            $this->update($this->users_tbl,[
                "token" => NULL,
                "token_expire" => 0,
                "updated_at" => date("Y-m-d H:i:s")
            ],$user["id"]);
           return false;
        } 

        

        return true;
    }

    function __construct($config)
    {
        parent::__construct($config);
    }

    function generateOrderNumber() {
        $timestamp = time(); // Şu anki zamanı Unix zaman damgası olarak al
        $orderNumber = date('dmHis', $timestamp); // Gün.Ay.Saat.Saniye formatında sipariş numarasını oluştur
        $randomNumber = mt_rand(100, 999); // 3 haneli rastgele sayı oluştur
      
        $uniqueOrderNumber = $orderNumber . $randomNumber; // Sipariş numarasına rastgele sayıyı ekle
      
        return $uniqueOrderNumber;
    }

    function encyrpt($password) {
        $encodedPassword = base64_encode($password.$this->config["saltToken"]); // Şifreyi encode et
        return $encodedPassword;
    }
    
    function decyrpt($encodedPassword) {
        $decodedPassword = str_replace($this->config["saltToken"], '', base64_decode($encodedPassword)); // Şifreyi decode et
        return $decodedPassword;
    }


    function addDaysToDate($date, $days) {
        $dateTime = DateTime::createFromFormat('Y-m-d H:i:s', $date);
    
        if ($dateTime === false) {
            return false; // Invalid date format
        }
    
        $dateTime->modify("+$days days");
    
        return $dateTime->format('Y-m-d H:i:s');
    }
    
    function calculateRemainingDays($targetDateTime) {
        $today = new DateTime(); // Get the current date and time
        $target = DateTime::createFromFormat('Y-m-d H:i:s', $targetDateTime); // Create a DateTime object from the target date and time
    
        if ($target === false) {
            return -1; // Invalid date and time format
        }
    
        $diff = $today->diff($target); // Calculate the difference between today and the target date
        $remainingDays = $diff->days; // Get the number of days from the difference object
    
        return $remainingDays;
    }
    
    function generateBreadCrumb($lang="tr",$category_id,$prefix = '<i class="bi bi-arrow-right"></i></span>',$tree = []){
        $category = $this->fetch($this->categories_tbl,"title as title_tr, title_en,parent_id","`id` = $category_id");

        /* 
            Kategori varsa
        */
        if($category){
           
            $parentID = $category["parent_id"];
            unset($category["parent_id"]);
            $tree[] = $category;
            
           
            /* 
                Kök kategori ise
            */ 
            if($parentID == 0){
                $breadcrumb = [];
                foreach(array_reverse($tree) as $b){
                    $breadcrumb[] = $b["title_" . $lang];
                }
                return implode('<span>' . $prefix .'</span>',$breadcrumb);
            }
           
            /* 
                Kategorinin ParentID 
            */
            $parentCategory = $this->fetch($this->categories_tbl,"*","`id` = $parentID");
            /* 
                Eğer üst kategorisi varsa
            */
            if($parentCategory){
                return $this->generateBreadCrumb($lang,$parentCategory["id"],$prefix,$tree);
            }
        }
    }
    function formatPhone($phoneNumber) {
        $phoneNumber = preg_replace('/[^0-9]/','',$phoneNumber);
    
        if(strlen($phoneNumber) > 10) {
            $countryCode = substr($phoneNumber, 0, strlen($phoneNumber)-10);
            $areaCode = substr($phoneNumber, -10, 3);
            $nextThree = substr($phoneNumber, -7, 3);
            $lastFour = substr($phoneNumber, -4, 4);
    
            $phoneNumber = '+'.$countryCode.' ('.$areaCode.') '.$nextThree.'-'.$lastFour;
        }
        else if(strlen($phoneNumber) == 10) {
            $areaCode = substr($phoneNumber, 0, 3);
            $nextThree = substr($phoneNumber, 3, 3);
            $lastFour = substr($phoneNumber, 6, 4);
    
            $phoneNumber = '('.$areaCode.') '.$nextThree.'-'.$lastFour;
        }
        else if(strlen($phoneNumber) == 7) {
            $nextThree = substr($phoneNumber, 0, 3);
            $lastFour = substr($phoneNumber, 3, 4);
    
            $phoneNumber = $nextThree.'-'.$lastFour;
        }
    
        return $phoneNumber;
    }
    function endPointResponse($message,$code){
        header("content-type:application/json");
        echo $this->response(["message" => $message],$code);
        exit;
    }
    function siteValid($website){
        return preg_match('/([a-z0-9\-]+\.)+[a-z]{2,4}(\.[a-z]{2,4})*(\/[^ ]+)*/i',$website,$matches) ? true : false;
    }
    function emailValid($email){
        return filter_var($email,FILTER_VALIDATE_EMAIL);
    }
    function phoneValid($phone){
        $phone = str_replace(' ','',$phone);
        $phone = ltrim($phone, '0');
        return preg_match('/^[0-9]{10}+$/', $phone);
    }
    function debug($data){
        return $this->response($data,Response::HTTP_NOT_ACCEPTABLE);
    }
    function isLogin(){
        return isset($this->user["id"]);
    }
    function act_getSecurityCode()
    {
        $randomnumber = "";
        for ($i = 1; $i <= 5; $i++) {
            $randomnumber .= rand(1, 9);
        }
        $_SESSION["captcha"] = $randomnumber;
        $img = imagecreate(90, 30);
        $textbgcolor = imagecolorallocate($img, 255, 255, 255);
        $textcolor = imagecolorallocate($img, 0, 0, 0);

        imagestring($img, 30, 5, 5, $randomnumber, $textcolor);
        ob_start();
        imagepng($img);
        return "data:image/png;base64," . base64_encode(ob_get_clean()) . "";
    }
    function escapeStringControl($data)
    {
        $arr = [];
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $arr[$key] = $this->escapeStringControl(is_string($value) ? addslashes($value) : $value);
            } else {
                $arr[$key] = is_string($value) ? addslashes($value) : $value;
            }
        }

        return $arr;
    }
     
    function loadMenus($type = 1){
        /* 
            1 => Main
            2 => Panel
        */
       $menus = $this->fetchAll($this->menus_tbl,"*","type = $type order by `order` asc");

       foreach($menus as $index => $menu){
            foreach($menus as $submenuIndex => $submenu){
                if($submenu["parent_id"] == $menu["id"]){
                    $menus[$index]["submenu"][] = $submenu;
                    foreach($menus as $deepMenuIndex => $deepmenu){
                        if($deepmenu["parent_id"] == $submenu["id"]){
                            $parentSubmenuIndex = $this->findMenuIdIndex($deepmenu["parent_id"],$menus[$index]["submenu"]);
                            
                            $menus[$index]["submenu"][$parentSubmenuIndex]["submenu"][] = $deepmenu;
                        }
                    }
                }
            }
            if($menu["parent_id"]>0) unset($menus[$index]);
       }
       return $menus;
    }

    function telFormat($tel)
    {
        $patterns = ["(", ")", "-", "_", " "];
        return str_replace($patterns, "", $tel);
    }
    function qrCode($s, $w = 250, $h = 250)
    {
        $u = 'https://chart.googleapis.com/chart?chs=%dx%d&cht=qr&chl=%s';
        $url = sprintf($u, $w, $h, $s);
        return $url;
    }
    function settings()
    {
        return $this->fetch("settings", "*", 1);
    }
    function dataWrite($file = '', $table = '', $columns = [])
    {
        # Ekleme yapılacak.
        foreach (file("$file.txt") as $line) {
            echo $line . "<br>";
            $line = explode('-', $line);
            $this->insert($table, $columns);
        }
    }
    function protectText($variable){
        return addslashes(strip_tags($variable));
    }
    function randomStr($length = 10) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
    function slug($text, string $divider = '-')
    {
        // Define a mapping of Turkish characters to their ASCII counterparts
        $turkishChars = array('Ç' => 'C', 'ç' => 'c', 'Ğ' => 'G', 'ğ' => 'g', 'İ' => 'I', 'ı' => 'i', 'Ö' => 'O', 'ö' => 'o', 'Ş' => 'S', 'ş' => 's', 'Ü' => 'U', 'ü' => 'u');

        // Replace Turkish characters with their ASCII counterparts
        $text = strtr($text, $turkishChars);

        // Replace non-letter or non-digit characters with the divider
        $text = preg_replace('/[^\p{L}\p{N}]+/u', $divider, $text);

        // Convert to lowercase
        $text = strtolower($text);

        // Remove unwanted characters
        $text = preg_replace('/[^-\w]+/', '', $text);

        // Trim
        $text = trim($text, $divider);

        // Remove duplicate divider
        $text = preg_replace('/-+/', $divider, $text);

        if (empty($text)) {
            return '';
        }

        return $text;
    }
    function validateDomain($domain) {
        // Domainin geçerli olup olmadığını kontrol et
        $pattern = "/^(?!\-)(?!(.*\-){2,})(?!.*?\.\-)(?![0-9]+$)(?!.*?\.$)[a-zA-Z0-9\-]{1,63}(\.[a-zA-Z]{2,})+$/";
        return preg_match($pattern, $domain);
    }
}

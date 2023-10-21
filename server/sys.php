<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
class sys
{
    public $config = [];
    public $db;
    public $user = NULL;
    public $moduleName = NULL;
    public $query = NULL;

    function __construct(array $config)
    {
        $this->config = $config;
        $this->connect($config['dbHost'], $config['dbName'], $config['dbUser'], $config['dbPass']);
        if(isset($_SESSION["user"]) && isset($_SESSION["user"]["id"]) && $_SESSION["user"]["id"] > 0){
            $this->user = $this->user();
        }
    }
    function connect($dbHost, $dbName, $dbUser, $dbPass)
    {
        try {
            $this->db = new PDO("mysql:host=" . $dbHost . ";dbname=" . $dbName . ";charset=utf8", $dbUser, $dbPass);
        } catch (PDOException $e) {
            print $e->getMessage();
            exit;
        }
        if ($this->db) {
            $this->db->query("SET SESSION sql_mode = ''");
        }
        return $this->db;
    }
    function rows($query = ""){
        if($this->query == NULL && empty($query)) return "Query Gereklidir";
        return $this->query->fetchAll(PDO::FETCH_ASSOC);
    }
    function row($query = ""){
        if($this->query == NULL && empty($query)) return "Query Gereklidir";
        return $this->query->fetch(PDO::FETCH_ASSOC);
    }
    function query($sql){
        if (empty($sql)) {
            return false;
        }
        $query = $this->db->query($sql);
        $this->query = $query;
        return $this;
    }
    function fetch(String $table, $columns = "*", $whr)
    {
        if (empty($table) || empty($whr)) {
            return false;
        }
        if (is_numeric($whr)) {
            $whr = "id=$whr";
        }
        if(is_array($columns) && count($columns)>0){
            $columns = implode(',',$columns);
        }
        $sql = "SELECT $columns FROM `$table` WHERE $whr";
        $query = $this->db->prepare($sql);
        $query->execute();

        $data = $query->fetch(PDO::FETCH_ASSOC);
        
        return $this->convertStringNumbersToIntegerRecursive($data);
    }

    function find(String $table,$whr){
        return $this->fetch($table,"*",$whr);
    }
 
    function fetchAll(String $table, $columns = "*", String $whr = "")
    {
        if (empty($table)) {
            return false;
        }
        if(is_array($columns) && count($columns)>0){
            $columns = implode(',',$columns);
        }
        $sql = "SELECT $columns FROM `$table`";

        if (!empty($whr)) {
            if (is_numeric($whr)) {
                $whr = "id=$whr";
            }
            $sql .= " WHERE $whr";
        }
        $query = $this->db->prepare($sql);
        $query->execute();
        $data = $query->fetchAll(PDO::FETCH_ASSOC);

        return $this->convertStringNumbersToIntegerRecursive($data);
    }

    function convertStringNumbersToIntegerRecursive($data){
        if (is_array($data)) {
            foreach ($data as $key => $value) {
                $data[$key] = $this->convertStringNumbersToIntegerRecursive($value);
            }
        } elseif (is_numeric($data)) {
            $data = +$data;
        }
        return $data;
    }
    
    function insert(String $table, array $data)
    {
        if (empty($table) || count($data) == 0) {
            return false;
        }

        $fields = [];
        $values = [];

        foreach ($data as $field => $value) {
            $fields[] = "`$field`";
            $value = is_string($value) ? stripslashes($value) : $value;
            $values[] = $this->db->quote($value);
        }

        $sql = "INSERT INTO `$table` (" . implode(",", $fields) . ") VALUES (" . implode(",", $values) . ")";
        $query = $this->db->prepare($sql);
        $query->execute();
        if ($query->rowCount() > 0) {
            return true;
        }
        return false;
    }

    function ex_remove(String $table, $whr, String $column = "id")
    {
        if (empty($table) || empty($whr)) {
            return false;
        }
        if (is_array($whr) && count($whr) > 0) {
            $whr = "IN (" . implode(",", $whr) . ")";
        } else {
            $column .= " =";
        }
        $sql = "DELETE FROM `$table` WHERE `$column` $whr";
        $query = $this->db->prepare($sql);
        $query->execute();
        return $query->rowCount() > 0 ? true : false;
    }
    function remove($table,$whr){
        if(empty($table) || empty($whr)){
            return false;
        }

        /* 
            IN (1,2,3,4)
        */
        if(is_array($whr) && count($whr) > 0){
            $whr = "IN (" . implode(",", $whr) . ")";
        }
        /* 
            `id` = 5
        */
        if(is_numeric($whr)){
            $whr = "`id` = $whr";
        }

        $sql = "DELETE FROM `$table` WHERE $whr";
        $query = $this->db->prepare($sql);
        $query->execute();
        return $query->rowCount() > 0 ? true : false;
    }
    function update(String $table, array $data, String $whr = "")
    {
        if (empty($table) or count($data) == 0 or empty($whr)) {
            return false;
        }
        if (is_numeric($whr)) {
            $whr = "`id`=$whr";
        }
        $set = [];
        foreach ($data as $key => $value) {
            if ($key != "id") {
                $set[] = "`$key`='$value'";
            }
        }
        $set = implode(",", $set);
        $sql = "UPDATE `$table` SET $set WHERE $whr";
    
        $query = $this->db->prepare($sql);
        $query->execute();
        $count = $query->rowCount();
        if ($count > 0) {
            return true;
        }
        return false;
    }
    // function run($module)
    // {
    //     if (file_exists("api/" . $module . ".php") /*&& !class_exists($module)*/) {
    //         include("api/" . $module . ".php");
    //         return new $module($this->config);
    //     } else {
    //         $module = null;
    //     }
    //     return $module;
    // }
    function run($module)
    {
        $moduleInstance = null;
        $moduleFilePath = "api/" . $module . ".php";
        
        if (file_exists($moduleFilePath)) {
         
            include_once($moduleFilePath);
            
            $className = $module;
            
            if (class_exists($className)) {
                $moduleInstance = new $className($this->config);
            }
        }
        return $moduleInstance;
    }
    function dbdate($date)
	{
        if(count(explode('.',$date)) != 3){
            $date = date("d.m.y");
        }
		list($d,$m,$y) = explode(".",$date);
		$r="$y-$m-$d";
		if($r=="--"){ return ""; }
		return $r;
	}
    function upload($path, $file, $extension = ["jpeg","jpg","png"], $width = 0, $height = 0)
    {
        // if ($width == 0 || $height == 0) {
        //     list($w, $h) = getimagesize($file['tmp_name']);
        // } else {
        //     $w = $width;
        //     $h = $height;
        // }
        $file_parts = explode(".", $file['name']);
        $ext = end($file_parts);
        $ext = strtolower($ext);
        if (count($extension) == 0 or $path == "" or (isset($file["name"]) && $file['name'] == "")) {
            return false;
        }
        if (!in_array($ext, $extension)) {
            return "__ERROR_TYPE__";
        }
        if(isset($file["new_name"]) and !empty($file["new_name"])){
            $filename = $file["new_name"].".".$ext;
        }else{
            $filename = md5(mt_rand()) . "." . $ext;
        }
        if (substr($path, -1) != "/") {
            $path = $path . "/";
        }
        $uploaded = copy($file['tmp_name'], $path . $filename);
        // $this->resize_image($path . $filename, $w, $h, false);
        return $uploaded ? $filename : false;
    }
    
    function uploadQualitySet($path,$file,$quality = 70,$extension = ["jpg","jpeg","png","webp"]){
        if ($path == "" or !is_array($file) or !isset($file["name"]) or $file["name"] == "" or !is_array($extension) or count($extension) == 0) {
            return false;
        }
       
        $file_parts = explode(".", $file['name']);
        $filename = array_shift($file_parts);
        $ext = end($file_parts);
        $ext = strtolower($ext);

        if (!in_array($ext, $extension)) {
            return false;
        }
        
        if (substr($path, -1) != "/") {
            $path = $path . "/";
        }

       
        $handle = new \Verot\Upload\Upload($file);
        if(!$handle->uploaded){
            return false;
        }

        $handle->file_new_name_body     = $filename;
        $handle->image_convert         = 'jpg';
        $handle->jpeg_quality = $quality;

        $handle->process($path);
        if (!$handle->processed) {
            return false; 
        }
        return true;
    }
    function upload_resize($path,$file,$settings = [],$extension = ["jpg","jpeg","png","webp"]){

        if ($path == "" or !is_array($file) or !isset($file["name"]) or $file["name"] == "" or !is_array($extension) or count($extension) == 0) {
            return false;
        }

        $file_parts = explode(".", $file['name']);
        $filename = array_shift($file_parts);
        $ext = end($file_parts);
        $ext = strtolower($ext);


        if (!in_array($ext, $extension)) {
            return false;
        }
        
        if (substr($path, -1) != "/") {
            $path = $path . "/";
        }

        $handle = new \Verot\Upload\Upload($file);
        if(!$handle->uploaded){
            return false;
        }


        $handle->file_new_name_body     = $filename;
        
      
        $handle->image_resize			=  true;

        /* 
            image_y
            image_x
            image_ratio_x
            image_ratio_y Boolen
        */

        // $handle->image_ratio_y = true;
        // $handle->image_x = 900;


        // $handle->image_convert = $settings["image_convert"] ?? 'jpg';
        $handle->jpeg_quality = $settings["jpeg_quality"] ?? 70;

        if(isset($settings) && is_array($settings) && count($settings) > 0){
            foreach($settings as $name => $value){
                $handle->{$name} = $value;
            }
        }
        
        $handle->process($path);
        if (!$handle->processed) {
            return false; // $handle->error;
        }
        return true;
    }

    function mailToClient($email,$subject,$msg){
        return $this->mail("",$email,$subject,$msg);
    }
    function mailToSystem($subject,$msg){
        return $this->mail("","",$subject,$msg);
    }

    

    function mail($from, $to, $subject, $msg, $email = '', $password = '') 
    {
        if (empty($from)) { // Kimden boşsa sistem'den gönderir.
            $from = $this->config['mailName'];
        }
        if (empty($to)) { // Kime Boşsa sisteme gönderir.
            $to = $this->config['mailName'];
        }
        $mail = new PHPMailer(true);
        try {
            $mail->SMTPDebug = 0;
            $mail->isSMTP();
            $mail->SMTPAuth   = true;

            $mail->SMTPSecure = $this->config["mailSmtp"] === true ? PHPMailer::ENCRYPTION_SMTPS : PHPMailer::ENCRYPTION_STARTTLS;
       
            $mail->Host       = $this->config['mailHost'];
            $mail->Username   = isset($email) && !empty($email) ? $email : $this->config['mailName'];
            $mail->Password   = isset($password) && !empty($password) ? $password : $this->config['mailPass'];
            $mail->Port       = $this->config['mailPort'];
            // Alıcılar
            $mail->setFrom($from, "wun.com.tr");
            $mail->addAddress($to);
            // İçerik
            $mail->CharSet = 'UTF-8';
            
            
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body    = $msg;
            $mail->send();
            return true;
        } catch (Exception $e) {
            echo $mail->ErrorInfo;
            exit;
            return $mail->ErrorInfo;
        }
    }

    function camelCase($string, $capitalizeFirstCharacter = false) 
    {

        $str = str_replace('-', '', ucwords($string, '-'));

        if (!$capitalizeFirstCharacter) {
            $str = lcfirst($str);
        }

        return $str;
    }
    

    function customizeUserPath($user){
        return "public/customers/" . $user["register_year"] . "/" . $user["member_code"] . "/"; 
    }

    function getUserPath($folderName = ''){

       
        $user = $_SESSION["user"];
        return "public/customers/" . $user["register_year"] . "/" . $user["member_code"] . "/$folderName"; 

    }

    function user()
    {
        if (isset($_SESSION['user']) && isset($_SESSION["user"]["id"])) {
            $userID = $_SESSION["user"]["id"];
            $user = $this->fetch("users","*",$userID);

            /* 
                Kullanıcı banlıysa 
            */
            if($user["banned"]){
                unset($_SESSION["user"],$_SESSION["user"]["id"]);
                return false;
            }

            $content = $this->fetch("companies","*","`user_id`=$userID");

            $user["social_medias"] = $this->pluck(["instagram","facebook","twitter","pinterest","youtube","youtube_iframe","linkedin","whatsapp"],$content);

            $modulePluckeds = [
                "language_visible",
                "products_visible",
                // "about_visible",
                // "references_visible",
                // "services_visible",
                "product_list_count",
                "slide_visible",
                "film_visible",
                "gallery_visible"
            ];

            $user["settings"] = $this->pluck($modulePluckeds,$content);
            
            $_SESSION["user"] = $user;
            $path = $this->getUserPath();
            $_SESSION["user"]["company_logo"] = file_exists($path . "logo.jpg") ? 'logo.jpg' : '';
            $_SESSION["user"]["company_favicon"] = file_exists($path . "favicon.png") ? 'favicon.png' : '';
          
            return $user;
        }
        return false;
    }
    // function auth($kod){
    //     if(!isset($_SESSION["user"]["rol"]) or !isset($kod) or empty($kod)) return false;
    //     $rol = $_SESSION["user"]["rol"];
    //     $yetkiler = $this->config["yetkiler"][$rol];
    //     return in_array("all",$yetkiler) ? true : in_array($kod,$yetkiler);
    // }
    function redirect($url,$root=SITE_ROOT){
        if($root != SITE_ROOT){
            $root = "https://www.".$root."/";
        }
        header("Location: ".$root.$url);
        exit;
    }
    function splitName($name){
        $names = explode(' ', $name);
        $lastname = $names[count($names) - 1];
        unset($names[count($names) - 1]);
        $firstname = join(' ', $names);
        return [
            "firstName" => $firstname,
            "lastName" => $lastname
        ];
    }
    function response($data,$code=200){
        header('Content-Type: application/json;');
        http_response_code($code);
        $data["status"] = $code;
        return json_encode($data);
    }
    function validate($fields,$data){
        foreach($fields as $field){
            if(!isset($data[$field]) || $data[$field] == ""){
                return false;
            }
        }
        return true;
    }
    function except($fields,$data){
        if(!is_array($fields) || count($fields)==0) return 'field gereklidir.';
        if(!is_array($data) || count($data)==0) return 'data gereklidir.';
        foreach($data as $fieldName => $value){
            if(!in_array($fieldName,$fields)){
                unset($data[$fieldName]);
            }
        }
        return $data;
    }
    function pluck($fields,$data){

        $pluckeds = [];

        foreach($fields as $field){
            $pluckeds[$field] = isset($data[$field]) ? $data[$field] : '';
        }

        return $pluckeds;
    }

    function openCors(){
            // Allow from any origin
            if (isset($_SERVER['HTTP_ORIGIN'])) {
                // Decide if the origin in $_SERVER['HTTP_ORIGIN'] is one
                // you want to allow, and if so:
                header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
                header('Access-Control-Allow-Credentials: true');
                header('Access-Control-Max-Age: 86400');    // cache for 1 day
            }
            
            // Access-Control headers are received during OPTIONS requests
            if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
                
                if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
                    // may also be using PUT, PATCH, HEAD etc
                    header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
                if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
                    header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");
            
                exit(0);
            }
    }
}

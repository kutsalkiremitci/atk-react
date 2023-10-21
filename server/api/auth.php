<?php
use Firebase\JWT\JWT;
use Firebase\JWT\Key;


class auth extends Response
{
    function act_login($data)
    {
      
        $fields = ["email", "password"];
        $valid = $this->validate($fields, $data);

        if (!$valid) {
            return $this->response(["message" => "`email` ve `password` alanı zorunlu ve boş bırakılamaz!"],Response::HTTP_NOT_ACCEPTABLE);
        }
        $data = $this->except($fields, $data);

        $email = $data["email"];
        $password = md5($data["password"]);

        $whr = "`email` = '$email' and `password` = '$password'";
        $user = $this->fetch($this->users_tbl, "*", $whr);

        if (!$user) {
            return $this->response(["message" => "E-Posta veya parola hatalı!"],Response::HTTP_NOT_ACCEPTABLE);
        }


        unset($user["password"]);

        $tokenExpiration = time() + 10800; // Tokenin süresi (örneğin, 3 saat)

        // JWT oluştur
        $token = [
            'iss' => 'system', // JWT'nin kim tarafından oluşturulduğunu belirtir
            'sub' => $user["id"], // Kimlik doğrulama konusu
            'iat' => time(), // Oluşturulma zamanı
            'exp' => $tokenExpiration, // Tokenin süresi
            'data' => [
                'username' => $email,
            ],
        ];

        $jwtToken = JWT::encode($token, $this->config["JWT_TOKEN_SECRET_KEY"], "HS256");

        unset($user["password"]);

       
        $update = $this->update($this->users_tbl,[
            "updated_at" => date("Y-m-d H:i:s"),
            "token_expire" => $tokenExpiration,
            "token" => $jwtToken,
        ],$user["id"]);

        if(!$update){
            return $this->response(["message" => "`token` kaydedilirken bir hata meydana geldi!"],Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        $user["token"] = $jwtToken;
        // $user["token_expire"] = $tokenExpiration;
        
        return $this->response([
            "message" => "Başarılı!",
            "user" => $user,
        ]);
    }
    function act_checkUserToken($data){

        $fields = ["token"];

        $validate = $this->validate($fields,$data);

        if(!$validate){
            return $this->response(["message" => "`token` gereklidir."],Response::HTTP_NOT_ACCEPTABLE);
        }

        $data = $this->except($fields,$data);

        $token = $data["token"];

        $whr = "`token` = '$token'";
        $user = $this->fetch($this->users_tbl,"*",$whr);

        if(!$user){
            return $this->response(["message" => "`token` bulunamadı!"],Response::HTTP_NOT_FOUND);
        }

        if ($user["token_expire"] < time()) {
            $this->update($this->users_tbl,[
                "token" => NULL,
                "token_expire" => 0,
                "updated_at" => date("Y-m-d H:i:s")
            ],$user["id"]);
            return $this->response(["message" => "`token` süresi doldu!"],Response::HTTP_UNAUTHORIZED);
        } 



        return $this->response(["message" => "Başarılı!"]);
    }
    function act_logout($data){
        $fields = ["token"];

        $validate = $this->validate($fields,$data);

        if(!$validate){
            return $this->response(["message" => "`token` gereklidir."],Response::HTTP_NOT_ACCEPTABLE);
        }

        $data = $this->except($fields,$data);

        $token = $data["token"];

        $whr = "`token` = '$token'";
        $user = $this->fetch($this->users_tbl,"*",$whr);

        if(!$user){
            return $this->response(["message" => "Çıkış yapılırken bir hata meydana geldi!"],Response::HTTP_NOT_FOUND);
        }

        $update = $this->update($this->users_tbl,[
            "updated_at" => date("Y-m-d H:i:s"),
            "token" => "",
            "token_expire" => ""
        ],$whr);

        if(!$update){
            return $this->response(["message" => "`token` resetlenirken bir hata meydana geldi!"],Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return $this->response(["message" => "Başarılı!"],Response::HTTP_OK);


    }
}

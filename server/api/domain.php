<?php 
class domain extends Response{
    function act_list(){
        $whr = "1=1 ORDER BY `created_at` ASC";
        $domains = $this->fetchAll($this->domains,"*",$whr);

        return $this->response([
            "message" => "Başarılı!",
            "data" => $domains,
        ]);

    }
    function act_update($data){
        $fields = ["id"];

        $validate = $this->validate($fields,$data);

        if(!$validate){
            return $this->response(["message" => "Geçersiz parametre"],Response::HTTP_NOT_ACCEPTABLE);
        }

        $fillableFields = ["id","name","price"];
        $data = $this->except($fillableFields,$data);

        if(isset($data["price"]) && (!is_numeric($data["price"]) or $data["price"] <= 0)){
            return $this->response(["message" => "`price` alanı numerik ve 0'dan büyük olmalıdır."],Response::HTTP_NOT_ACCEPTABLE);
        }

        if(!is_numeric($data["id"]) or $data["id"] <= 0){
            return $this->response(["message" => "`id` alanı numerik olmalıdır ve 0'dan büyük olmalıdır."],Response::HTTP_NOT_ACCEPTABLE);
        }

        $data["updated_at"] = date("Y-m-d H:i:s");

        $update = $this->update($this->domains,$data,$data["id"]);

        if(!$update){
            return $this->response(["message" => "Domain güncellenirken bir hata meydana geldi!"],Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return $this->response(["message" => "Başarılı!"],Response::HTTP_OK);
    }
    function act_add($data){
        $fields = ["name","price"];

        $valid = $this->validate($fields,$data);

        if(!$valid){
            return $this->response(["message" => "Geçersiz parametre!"],Response::HTTP_NOT_ACCEPTABLE);
        }

        $data = $this->except($fields,$data);

        $domainName = $data["name"];

        $whr = "`name` = '$domainName'";

        $findedDomain = $this->find($this->domains,$whr);

        if($findedDomain){
            return $this->response(["message" => "`".$domainName."` domain bulunmaktadır!"],Response::HTTP_NOT_ACCEPTABLE);
        }

        $price = +$data["price"];

        if(!is_numeric($price) or $price <= 0){
            return $this->response(["message" => "Fiyat 0 ve üstünde olmalıdır!"],Response::HTTP_NOT_ACCEPTABLE);
        }

        $addDomain = $this->insert($this->domains,[
            "name" => $domainName,
            "price" => $price,
            "created_at" => date("Y-m-d H:i:s")
        ]);
        
        if(!$addDomain){
            return $this->response(["message" => "`domain` eklenirken bir hata meydana geldi!"],Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        
        $lastInsertId = +$this->db->lastInsertId();


        return $this->response(["message" => "Başarılı!", "lastInsertId" => $lastInsertId]);
    }
   
    function act_remove($data){
        $fields = ["id"];

        $validate = $this->validate($fields,$data);

        if(!$validate){
            return $this->response(["message" => "Geçersiz parametre!"],Response::HTTP_NOT_ACCEPTABLE);
        }

        if(!is_numeric($data["id"]) or $data["id"] <= 0){
            return $this->response(["message" => "Geçersiz parametre!"],Response::HTTP_NOT_ACCEPTABLE);
        }

        $removed = $this->remove($this->domains,$data["id"]);

        if(!$removed){
            return $this->response(["message" => "`domain` silinirken bir hata meydana geldi!"],Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return $this->response(["message" => "Başarılı!"],Response::HTTP_OK);
    }
}
?>
<?php
namespace App\Models;

class FileDepotModel extends Model{
    const UPLOAD_DIR = "/var/www/stage4all.fr/uploads/";
    CONST MIME_AUTOR = ["application/pdf"];
    CONST SIZE_AUTOR = 2*1024*1024;

    public function __construct($file = null){
        if(is_null($file)){
            $this->tmp_file = $_FILES["userfile"]["tmp_name"];
            $this->uploadfile = self::UPLOAD_DIR.basename($_FILES["userfile"]["name"]);
        }
        else{
            $this ->tmp_file = $file;
            $this->uploadfile = self::UPLOAD_DIR.basename($file);
        }
    }

    public function getMime(){
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $this->tmp_file);
        finfo_close($finfo);
        return $mime;
    }

    public function verifMime($mime){  /*Renvoie true si le type du fichier est PDF*/
        if (!in_array($mime, self::MIME_AUTOR, true)){
            return false;
        }
        return true;
    }

    public function getSize(){
        return filesize($this->tmp_file);
    }

    public function verifSize($size){
        if($size >self::SIZE_AUTOR){
            return false;
        }
        return true;
    }

    public function depot(){
        if($this->verifMime($this->getMime())){
            if($this->verifSize($this->getSize())){
                if(move_uploaded_file($this->tmp_file, $this->uploadfile)){
                    return true;
                }
                else{
                    var_dump(error_get_last());
                }
            }
        }
        return false;
    }
}
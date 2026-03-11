<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$uploaddir = "/var/www/stage4all.fr/uploads/";
$uploadfile = $uploaddir.basename($_FILES["userfile"]["name"]);


/*--verif MIME--*/ 
$finfo = finfo_open(FILEINFO_MIME_TYPE);
$mime = finfo_file($finfo, $_FILES["userfile"]["tmp_name"]);
finfo_close($finfo);

$mime_autorise = ["application/pdf"];
if (!in_array($mime, $mime_autorise, true)){
    die("Seuls les fichiers PDFs sont autorisés.");
}

/*--verif taille--*/
$fsize = filesize($_FILES["userfile"]["tmp_name"]);
if ($fsize > 2*1024*1024){
    die("Le fichier ne doit pas dépasser 2Mo.");
}

echo "<pre>";
if (move_uploaded_file($_FILES["userfile"]["tmp_name"], $uploadfile)){
    echo "Le fichier est valide et a été téléchargé avec succès. Voici plus d'informations : \n";
} else {
    die("Échec du déplacement du fichier.");
}

echo "Voici quelques informations de débogage : ";
print_r($_FILES);

echo "</pre>";

?>
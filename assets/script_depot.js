function FileValidation(){
    const fi = document.getElementById("file");
    const fichiersAutor = ["pdf", "doc", "docx", "odt", "rtf", "jpg", "jpeg", "png"];
    if(fi.files.length > 0){ //On vérifie qu'un fichier a été sélectionné
        for(const i = 0; i <= fi.files.length - 1; i++){
            file = fi.files[i];
            const fsize = fi.files.item(i).size;
            const fileSizeKB = Math.round((fsize / 1024));
            //on vérifie la taille du fichier
            if (fileSizeKB >= 2048){
                alert("Fichier trop volumineux, sélectionnez un fichier de moins de 2Mo");
                fi.value = "";
                return false;
            }
            const nomfichier = file.name;
            const extension = nomfichier.split(".").pop().toLowerCase();

            if(!fichiersAutor.includes(extension)){
                alert("Format non autorisé.")
                fi.value = "";
                return false;
            }

            document.getElementById("size").innerHTML = "<b>" + fileSizeKB + "</b> KB";
        }
    }
}
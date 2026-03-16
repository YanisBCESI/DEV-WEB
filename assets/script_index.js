const liste = document.getElementById("liste1");
const gauche = document.getElementById("fleche_gauche1");
const droite = document.getElementById("fleche_droite1");

function scrollListe(valeur){
    liste.scrollBy({
        left: valeur * window.innerWidth / 100,
        behavior: "smooth"
    });
}

function verifierFleches(){

    if(liste.scrollLeft <= 0){
        gauche.style.display = "none";
    }else{
        gauche.style.display = "block";
    }

    if(liste.scrollLeft + liste.clientWidth >= liste.scrollWidth - 1){
        droite.style.display = "none";
    }else{
        droite.style.display = "block";
    }

}

liste.addEventListener("scroll", verifierFleches);
window.addEventListener("load", verifierFleches);
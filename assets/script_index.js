const liste1 = document.getElementById("liste1");
const gauche1 = document.getElementById("fleche_gauche1");
const droite1 = document.getElementById("fleche_droite1");

function scrollListe1(valeur){
    liste1.scrollBy({
        left: valeur * window.innerWidth / 100,
        behavior: "smooth"
    });
}

function verifierFleches1(){

    if(liste1.scrollLeft <= 0){
        gauche1.style.display = "none";
    }else{
        gauche1.style.display = "block";
    }

    if(liste1.scrollLeft + liste1.clientWidth >= liste1.scrollWidth - 1){
        droite1.style.display = "none";
    }else{
        droite1.style.display = "block";
    }

}

liste1.addEventListener("scroll", verifierFleches1);
window.addEventListener("load", verifierFleches1);

const liste3 = document.getElementById("liste3");
const gauche3 = document.getElementById("fleche_gauche3");
const droite3 = document.getElementById("fleche_droite3");

function scrollListe3(valeur){
    liste3.scrollBy({
        left: valeur * window.innerWidth / 100,
        behavior: "smooth"
    });
}

function verifierFleches3(){

    if(liste3.scrollLeft <= 0){
        gauche3.style.display = "none";
    }else{
        gauche3.style.display = "block";
    }

    if(liste3.scrollLeft + liste3.clientWidth >= liste3.scrollWidth - 1){
        droite3.style.display = "none";
    }else{
        droite3.style.display = "block";
    }

}

liste3.addEventListener("scroll", verifierFleches3);
window.addEventListener("load", verifierFleches3);
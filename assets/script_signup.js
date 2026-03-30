document.addEventListener("DOMContentLoaded", ()=>{
    const entreeNom = document.querySelector(".u_lastname");
    if(entreeNom){
        entreeNom.addEventListener("input", (e)=>{
            e.target.value = e.target.value.toUpperCase();
        });
    }
});

function checkForm(form){
    if(form.lastname.value == ""){
        alert("Erreur : l'entrée est vide.");
        form.lastname.focus();
        return false;
    }

    if(form.surname.value == ""){   
        alert("Erreur : l'entrée est vide.");
        form.surname.focus();
        return false;
    }

    var re = /^[A-Za-z-]{1,20}$/;
    if(!re.test(form.lastname.value)){
        alert("Erreur : l'entrée contient des caractères invalides");
        form.lastname.focus();
        return false;
    }
    if(!re.test(form.surname.value)){
        alert("Erreur : l'entrée contient des caractères invalides");
        form.surname.focus();
        return false;
    }

    var remail = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if(!remail.test(form.email.value)){
        alert("Erreur : l'email n'est pas valide");
        form.email.focus();
        return false;
    }

    if(form.password.value !== form.password_confirm.value){
        alert("Les mots de passe ne correspondent pas.")
        return false;
    }

    return true;
}
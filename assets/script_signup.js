function checkForm(form){
    if(form.lastname.value == ""){
        alert("Erreur : l'entrée est vide.");
        form.lastname.focus();
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

    var remail = /[-A-Za-z0-9!#$%&'*+/=?^_`{|}~]+(?:\.[-A-Za-z0-9!#$%&'*+/=?^_`{|}~]+)*@(?:[A-Za-z0-9](?:[-A-Za-z0-9]*[A-Za-z0-9])?\.)+[A-Za-z0-9](?:[-A-Za-z0-9]*[A-Za-z0-9])?/;

    if(!remail.test(form.email.value)){
        alert("Erreur : l'email n'est pas valide");
        form.email.focus();
        return false;
    }

    form.lastname.addEventListener("input", function(e){e.target.value = e.target.value.toUpperCase();})
    return true;
}
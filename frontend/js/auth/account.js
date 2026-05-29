// Implémenter js de ma page
const lastName = document.getElementById("lastName");
const firstName = document.getElementById("firstName");

const btnValidation = document.getElementById("validation-modification");

//écoute des événements
lastName.addEventListener("keyup", validateForm);
firstName.addEventListener("keyup", validateForm);

//fonction permettant de valider le formulaire
function validateForm(){
const nomOk = validateRequired(lastName);
const prenomOk =validateRequired(firstName);


    if(nomOk && prenomOk){
    btnValidation.disabled = false;
    }
    else{
    btnValidation.disabled = true;
    }
}

function validateRequired(input){
    if(input.value != ''){
        // c'est ok
        input.classList.add("is-valid");
        input.classList.remove("is-invalid");
        return true;
    }
    else{
        //c'est pas ok
        input.classList.remove("is-valid");
        input.classList.add("is-invalid");
        return false;
    }
}
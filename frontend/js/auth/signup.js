// Implémenter js de ma page
const lastName = document.getElementById("lastName");
const firstName = document.getElementById("firstName");
const email = document.getElementById("email");
const password = document.getElementById("password");
const confirmPassword = document.getElementById("confirmPassword");
const allergy = document.getElementById("allergy");
const guestNumber = document.getElementById("guestNumber");
const btnValidation = document.getElementById("validationInscription");
const registrationForm = document.getElementById("registrationForm");

//écoute des événements
lastName.addEventListener("keyup", validateForm);
firstName.addEventListener("keyup", validateForm);
email.addEventListener("keyup", validateForm);
password.addEventListener("keyup", validateForm);
confirmPassword.addEventListener("keyup", validateForm);

btnValidation.addEventListener("click", registerUser);

//fonction permettant de valider le formulaire
function validateForm(){
const nomOk = validateRequired(lastName);
const prenomOk =validateRequired(firstName);
const mailOk = validateEmail(email);
const passwordOk = validatePassword(password);
const confirmPasswordOk = validateConfirmationPassword(password, confirmPassword);

    if(nomOk && prenomOk && mailOk && passwordOk && confirmPasswordOk){
    btnValidation.disabled = false;
    }
    else{
    btnValidation.disabled = true;
    }
}

function validateConfirmationPassword(inputPwd, inputConfirmPwd){
      if (inputConfirmPwd.value === "") {
        // champ vide → on enlève les classes de validation
        inputConfirmPwd.classList.remove("is-valid", "is-invalid");
        return false;
    }
    if(inputPwd.value== inputConfirmPwd.value){
      inputConfirmPwd.classList.add("is-valid");
        inputConfirmPwd.classList.remove("is-invalid");
        return true;
    }
    else{
        inputConfirmPwd.classList.remove("is-valid");
        inputConfirmPwd.classList.add("is-invalid");
        return false;
    }
}

function validatePassword(input){
// définir regex
const passwordRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_])[A-Za-z\d\W_]{8,}$/;
const passwordUser = input.value;
if(passwordUser.match(passwordRegex)){
         input.classList.add("is-valid");
        input.classList.remove("is-invalid");
        return true;
    }
    else{
        input.classList.remove("is-valid");
        input.classList.add("is-invalid");
        return false;
    }
}

function validateEmail(input){
// définir regex
const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
const mailUser = input.value;
if(mailUser.match(emailRegex)){
        input.classList.add("is-valid");
        input.classList.remove("is-invalid");
        return true;
    }
    else{
        input.classList.remove("is-valid");
        input.classList.add("is-invalid");
        return false;
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

function registerUser(){
    // Crée un nouvel objet FormData à partir du formulaire contenu dans la variable "registrationForm"
    let dataForm = new FormData(registrationForm);

    // Crée un nouvel objet Headers pour définir les en-têtes de la requête HTTP
    let myHeaders = new Headers();
    // Ajoute l'en-tête "Content-Type" avec la valeur "application/json"
    myHeaders.append("Content-Type", "application/json");

    // Convertit les données du formulaire en une chaîne JSON
    let raw = JSON.stringify({
        firstName: dataForm.get("firstName"),
        lastName: dataForm.get("lastName"),
        email: dataForm.get("email"),
        password: dataForm.get("password"),
        allergy:  dataForm.get("allergy"),
        guestNumber: Number(dataForm.get("guestNumber"))
    });

    // Configure les options de la requête HTTP
    let requestOptions = {
        // Méthode de la requête : "POST" pour envoyer des données au serveur
        method: 'POST',
        // Définit les en-têtes de la requête en utilisant l'objet Headers créé précédemment
        headers: myHeaders,
        // Corps de la requête : les données JSON converties en chaîne
        body: raw,
        // Redirection à suivre en cas de besoin ("follow" suit automatiquement les redirections)
        redirect: 'follow'
    };

    fetch(apiUrl+"registration", requestOptions)
    .then(async response => {
        if (!response.ok) {
            const error = await response.text();
            throw new Error(error);
        }

        return response.json();
    })
    .then(result => {
        alert("Bravo " + dataForm.get("firstName") + " ! L'inscription a fonctionné !");
        document.location.href = "/signin";
    })
    .catch(error => {
        console.error(error);
        alert("Erreur lors de l'inscription");
    });
  }
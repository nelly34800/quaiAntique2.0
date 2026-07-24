//validation des données
// Implémenter js de ma page
const title = document.getElementById("title");
const validationCategory = document.getElementById("validationCategory");

validationCategory.disabled = true;

const params = new URLSearchParams(window.location.search);
const categoryId = params.get('id');

//écoute des événements
title.addEventListener("input", validateForm);

//fonction permettant de valider le formulaire
function validateForm(){
const titleOk = validateRequired(title);

    if(titleOk){
    validationCategory.disabled = false;
    }
    else{
    validationCategory.disabled = true;
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

// charger une catégorie
async function loadCategory(id) {
  try {
    const response = await fetch(apiUrl + "category/" + id, {
        method: "GET",
        headers: getHeaders()
    });

    if(!response.ok){
      throw new Error();
    }
    const data = await response.json();

    title.value = data.title;

  } catch (error) {
    showMessage("Une erreur est survenue", "danger");
  }
}
// pré-rempli si edit
if (categoryId) {
  loadCategory(categoryId);
}

// créer ou modifier en bdd la catégorie (submit)
document.querySelector('form').addEventListener('submit', async (e) => {
  //empêche le rechargement
  e.preventDefault();

  // envoie au backend 
  try {
    // vérifie si on a un id dans l'URL (si id = modification)
    if (categoryId) {
      const response = await fetch(apiUrl + "category/" + categoryId,{
        method:"PUT",
        body: JSON.stringify({title: title.value}),
        headers:getHeaders()
      });
      // afficher le message
      showMessage("Modification réussie ! Vous allez être redirigé", "success");
      // redirection après 2 secondes
      setTimeout(() => {
        window.location.href = '/categories';
      }, 2000);

    } else {
      // sinon pas d'id = création
      const response = await fetch(apiUrl + "category",{
          method:"POST",
          body: JSON.stringify({title: title.value}),
          headers:getHeaders()
      });

      if(!response.ok){
        throw new Error();
      }

      // afficher le message
      showMessage("Création réussie ! Vous allez être redirigé", "success");
      // redirection après 2 secondes
      setTimeout(() => {
        window.location.href = '/categories';
      }, 2000);
    }
  } catch (error) {
      // message d'erreur
      showMessage("Une erreur est survenue", "danger");
  }
});

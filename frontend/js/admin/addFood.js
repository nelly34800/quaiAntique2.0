//validation des données
// Implémenter js de ma page
const title = document.getElementById("title");
const description = document.getElementById("description");
const price = document.getElementById("price");
const categories = document.getElementById("categories-container");
const validationFood = document.getElementById("validationFood");

// Récupère l'id dans l'URL pour savoir si on est en création ou modification
const params = new URLSearchParams(window.location.search);
const foodId = params.get('id');

validationFood.disabled = true;
//écoute des événements
title.addEventListener("input", validateForm);
description.addEventListener("input", validateForm);
price.addEventListener("input", validateForm);

// validation des checkbox
function validateCheckboxGroup(containerId) {
  return document.querySelectorAll(`#${containerId} input:checked`).length > 0;
}

//fonction permettant de valider le formulaire
function validateForm(){
  const titleOk = validateRequired(title);
  const descriptionOk = validateRequired(description);
  const priceOk = validateRequired(price);

  if(titleOk && descriptionOk && priceOk){
    validationFood.disabled = false;
  } else {
    validationFood.disabled = true;
  }
}

function validateRequired(input){
  if(input.value != ''){
    // c'est ok
    input.classList.add("is-valid");
    input.classList.remove("is-invalid");
    return true;
  } else {
    //c'est pas ok
    input.classList.remove("is-valid");
    input.classList.add("is-invalid");
    return false;
  }
}
// charger les catégories 
async function loadCategories() {
  try {
    const data = await getCategories();
    data.forEach(c => {
      const label = document.createElement("label");
      label.style.display = "block"; 

      const checkbox = document.createElement("input");
      checkbox.type = "checkbox";
      checkbox.value = c.id;
      checkbox.addEventListener("change", validateForm);

      label.appendChild(checkbox);
      label.appendChild(document.createTextNode(" " + c.title));

      document.getElementById("categories-container").appendChild(label);
    });
  } catch (error) {
    showMessage("Une erreur est survenue", "danger");
  }
}

// charger un plat (modification)
async function loadFood(id){
  try {
    const response = await fetch(apiUrl + "food/" + id, {
        method: "GET",
        headers: getHeaders()
    });

    if(!response.ok){
      throw new Error();
    }
    const data = await response.json();

    title.value = data.title;
    description.value = data.description;
    price.value = data.price;
   // pré-rempli les catégories
    // vérifie que data.categories existe et est un tableau avant de faire le forEach
    if (data.categories && Array.isArray(data.categories)) {
      // boucle sur les catégories du plat et coche les cases correspondantes
      data.categories.forEach(c => {
        const checkbox = document.querySelector(
          `#categories-container input[value="${c.id}"]`);
        if (checkbox) {
          checkbox.checked = true;
        } 
      });
    }

  } catch (error) {
    showMessage("Une erreur est survenue", "danger");
  }
}
// pré-rempli si edit
async function init() {
  await loadCategories();

  if (foodId) {
    loadFood(foodId);
  }
}
init();

// créer ou modifier en bdd le plat (submit)
document.querySelector('form').addEventListener('submit', async (e) => {
  //empêche le rechargement
  e.preventDefault();

  // récupère les catégories cochés
  const categories = Array.from(
      document.querySelectorAll('#categories-container input:checked')
    ).map(cb => cb.value);

  // envoie au backend 
  try {
    // vérifie si on a un id dans l'URL (si id = modification)
    if (foodId) {
      const response = await fetch(apiUrl + "food/" + foodId,{
        method:"PUT",
        body: JSON.stringify({
          title: title.value,
          description: description.value,
          price: price.value,
          category_id: categories
        }),
        headers:getHeaders()
      });
      // afficher le message
      showMessage("Modification réussie ! Vous allez être redirigé", "success");
      // redirection après 2 secondes
      setTimeout(() => {
        window.location.href = '/food';
      }, 2000);

    } else {
      // sinon pas d'id = création
      const response = await fetch(apiUrl + "food",{
          method:"POST",
          body: JSON.stringify({
          title: title.value,
          description: description.value,
          price: price.value,
          category_id: categories
        }),
          headers:getHeaders()
      });

      if(!response.ok){
        throw new Error();
      }

      // afficher le message
      showMessage("Création réussie ! Vous allez être redirigé", "success");
      // redirection après 2 secondes
      setTimeout(() => {
        window.location.href = '/food';
      }, 2000);
    }
  } catch (error) {
      // message d'erreur
      showMessage("Une erreur est survenue", "danger");
  }
});
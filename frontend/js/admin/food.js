// fonction pour charger les plats
async function loadFoods() {
  try {
    const response = await fetch(apiUrl + "food", {
        method: "GET",
        headers: getHeaders()
    });
    if (!response.ok) {
        throw new Error("Erreur API");
    }
    const data = await response.json();

    const tbody = document.querySelector("tbody");
    tbody.innerHTML = "";

    data.forEach(food => {
      const tr = document.createElement("tr");

      const tdTitle = document.createElement("td");
      tdTitle.textContent = food.title;
      tr.appendChild(tdTitle);

      const tdDescription = document.createElement("td");
      tdDescription.textContent = food.description;
      tr.appendChild(tdDescription);

      const tdPrice = document.createElement("td");
      tdPrice.textContent = food.price.toFixed(2) + " €";
      tr.appendChild(tdPrice);

      // category
      const tdCategories = document.createElement("td");
      tdCategories.textContent = `catégories: ${food.categories.map(c => c.title).join(", ")}`;
      tr.appendChild(tdCategories);

      const tdAction = document.createElement("td");
      tdAction.appendChild(createActionButtons(food.id));
      tr.appendChild(tdAction);
      tbody.appendChild(tr);
    });
  } catch(error) {
        console.error(error);
        showMessage("Erreur lors de l'affichage", "danger");
  }
}
loadFoods();

// modifier: aller sur la page addFood pour modifier le plat
document.addEventListener("click", (e) => {
  // Vérifie si on a cliqué sur un bouton "modifier"
  if (e.target.closest(".editBtn")) {
    const button = e.target.closest(".editBtn");
    // Récupère l'id du plat
    const foodId = button.dataset.id;
    // redirection avec id dans l'URL
    window.location.href = `/addFood?id=${foodId}`;
  }
});

//supprimer le plat
document.addEventListener("click", async (e) => {
  // Vérifie si bouton supprimer
  if (!e.target.closest(".deleteBtn")) return;
  const button = e.target.closest(".deleteBtn");
  // Récupère l'id
  const foodId = button.dataset.id;

    if(!confirm("Êtes vous sûre de vouloir supprimer ce plat ?")) return;

    try{
      const response = await fetch(apiUrl + "food/" + foodId,{
          method:"DELETE",
          headers:getHeaders()
      });

      if(!response.ok){
          throw new Error("Erreur API");
      }
      showMessage("Plat supprimé", "success");

      loadCategories();
    }
    catch(error){
        console.error(error);
        showMessage("Erreur lors de la suppression", "danger");
    }
  }
)
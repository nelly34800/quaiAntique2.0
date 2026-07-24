// fonction pour charger les catégories
async function loadCategories() {
  try {
    const response = await fetch(apiUrl + "category", {
        method: "GET",
        headers: getHeaders()
    });
    if (!response.ok) {
        throw new Error("Erreur API");
    }
    const data = await response.json();

    const tbody = document.querySelector("tbody");
    tbody.innerHTML = "";

    data.forEach(category => {
      const tr = document.createElement("tr");

      const tdTitle = document.createElement("td");
      tdTitle.textContent = category.title;
      tr.appendChild(tdTitle);

      const tdAction = document.createElement("td");
      tdAction.appendChild(createActionButtons(category.id));
      tr.appendChild(tdAction);
      tbody.appendChild(tr);
    });
  } catch(error) {
        console.error(error);
        showMessage("Erreur lors de l'affichage", "danger");
  }
}
loadCategories();

// modifier: aller sur la page addCategory pour modifier la catégorie
document.addEventListener("click", (e) => {
  // Vérifie si on a cliqué sur un bouton "modifier"
  if (e.target.closest(".editBtn")) {
    const button = e.target.closest(".editBtn");
    // Récupère l'id de la catégorie
    const categoryId = button.dataset.id;
    // redirection avec id dans l'URL
    window.location.href = `/addCategory?id=${categoryId}`;
  }
});

//supprimer la catégorie
document.addEventListener("click", async (e) => {
  // Vérifie si bouton supprimer
  if (!e.target.closest(".deleteBtn")) return;
  const button = e.target.closest(".deleteBtn");
  // Récupère l'id
  const categoryId = button.dataset.id;

    if(!confirm("Êtes vous sûre de vouloir supprimer cette catégorie ?")) return;

    try{
      const response = await fetch(apiUrl + "category/" + categoryId,{
          method:"DELETE",
          headers:getHeaders()
      });

      if(!response.ok){
          throw new Error("Erreur API");
      }
      showMessage("Catégorie supprimée", "success");

      loadCategories();
    }
    catch(error){
        console.error(error);
        showMessage("Erreur lors de la suppression", "danger");
    }
  }
)
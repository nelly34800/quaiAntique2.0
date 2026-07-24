// éviter les failles XSS
function sanitizeHTML(text){
  const tempHTML = document.createElement('div');
  tempHTML.textContent = text;
  return tempHTML.innerHTML;
}

// affichage des messages
function showMessage(message, type = "info") {
  const messageDiv = document.getElementById("messageDiv");

  if (!messageDiv) return;

  messageDiv.textContent = message;
  messageDiv.className = `alert alert-${type}`;
  messageDiv.classList.remove("d-none");

  // disparition automatique
  setTimeout(() => {
    messageDiv.classList.add("d-none");
  }, 3000);
}

// boutons d'action (modifier/supprimer: évite répétition)
function createActionButtons(id) {
  const container = document.createElement("div");

  const editBtn = document.createElement("button");
  editBtn.className = "btn btn-secondary editBtn m-1";
  editBtn.dataset.id = id;
  editBtn.textContent = "modifier";

  const deleteBtn = document.createElement("button");
  deleteBtn.className = "btn btn-danger deleteBtn m-1";
  deleteBtn.dataset.id = id;

  const icon = document.createElement("i");
  icon.className = "bi bi-trash";
  deleteBtn.appendChild(icon);

  container.appendChild(editBtn);
  container.appendChild(deleteBtn);

  return container;
}

// bouton d'action (ajouter: évite répétition)
function CreateAddButton(id) {
  const container = document.createElement("div");

  const addBtn = document.createElement("button");
  addBtn.className = "btn btn-primary addBtn m-1";
  addBtn.dataset.id = id;
  addBtn.textContent = "ajouter";

  container.appendChild(addBtn);

  return container;
}

//formatage des horaires
const formatTime = (time) => {
  const [hours, minutes] = time.split(":");
  return `${hours}h${minutes}`;
};

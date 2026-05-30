alert("coucou");
const galerieImage = document.getElementById("allImages");

//récupérer les informations des images 

let titre = '<img src=x oneror="Window.location.replace(\'https: //google.com\')"/>';
let imgSource = '../img/chef.jpg';
let monImage = getImage(titre, imgSource);
galerieImage.innerHTML = monImage;


function getImage(titre, urlImage){
  titre = sanitizeHTML(titre);
  urlImage = sanitizeHTML(urlImage);
    return `  <div class="col p-3">
      <div class="image-card text-white">
        <img src="${urlImage}" class="rounded w-100" alt="" />
        <p class="titre-image">${titre}</p>
        <div class="action-image-buttons" data-show="admin">
          <button type="button" class="btn btn-outline-light"><i class="bi bi-pencil-square"></i></button>
          <button type="button" class="btn btn-outline-light"><i class="bi bi-trash"></i></button>
        </div>
      </div>
    </div>`
}
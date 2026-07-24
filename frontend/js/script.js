const tokenCookieName  = "accesstoken";
const roleCookieName = "role";
const signoutBtn = document.getElementById("signout-btn");
const apiUrl = "http://127.0.0.1:8000/api/";

signoutBtn.addEventListener("click", signout);

// retourne rôle
function getRole(){
    return getCookie(roleCookieName);
}

// déconnection
function signout(){
    eraseCookie(tokenCookieName);
    eraseCookie(roleCookieName);
    window.location.reload();
}

// modifie token
function setToken (token){
    setCookie(tokenCookieName, token, 7);
}
// retourne token
function getToken (){
    return getCookie(tokenCookieName);
}

// definie un cookie avec un nom, une valeur et une durée en jours
function setCookie(name,value,days) {
    let expires = "";
    if (days) {
        let date = new Date();
        date.setTime(date.getTime() + (days*24*60*60*1000));
        expires = "; expires=" + date.toUTCString();
    }
    document.cookie = name + "=" + (value || "")  + expires + "; path=/";
}
// retourne la valeur d'un cookie en fonction de son nom
function getCookie(name) {
    let nameEQ = name + "=";
    let ca = document.cookie.split(';');
    for(const element of ca) {
        let c = element;
        while (c.startsWith(' ')) c = c.substring(1,c.length);
        if (c.startsWith(nameEQ)) return c.substring(nameEQ.length,c.length);
    }
    return null;
}

// supprime un cookie
function eraseCookie(name) {   
    document.cookie = name +'=; Path=/; Expires=Thu, 01 Jan 1970 00:00:01 GMT;';
}

//vérifie si l'utilisateur est connecté
function isConnected(){
    if(getToken() == null || getToken() == undefined){
        return false;
    }
    else{
        return true;
    }
}

//afficher et masquer les élément en fonction du role
function showAndHideElementForRole(){
    const userConnected = isConnected();
    const role = getRole();

    let allEllementsToEdit = document.querySelectorAll('[data-show]');

    allEllementsToEdit.forEach(element =>{
        switch(element.dataset.show){
            case "disconnected":
                if(userConnected){
                    element.classList.add("d-none");
                }
                break;
            case "connected":
                if(!userConnected){
                    element.classList.add("d-none");
                }
                break;
            case "ROLE_ADMIN":
                if(!userConnected || role!="ROLE_ADMIN"){
                    element.classList.add("d-none");
                }
                break;
            case "ROLE_USER":
                if(!userConnected || role!="ROLE_USER"){
                    element.classList.add("d-none");
                }
                break;
        }
    })
}

// charger la page de profil en fonction du rôle
function loadProfileByRole(role) {
  switch (role) {
    case "ROLE_USER": 
    window.location.href = "/account";
    break;

    case "ROLE_ADMIN": 
    window.location.href = "/admin";
    break;

    default: 
    showMessage("Cette page est seulement accessible après connexion, merci de vous connecter s'il vous plait", "warning");
    // redirection après 2 secondes
    setTimeout(() => {
      window.location.href = "/signin";
    }, 2000);
  }
}

//  écoute le click sur le bouton de profil et redirige vers la page de profil en fonction du rôle
document.addEventListener("click", (e) => {
  if (e.target.closest("#account")) {
    e.preventDefault();
    const role = getRole();
    loadProfileByRole(role);
  }
});

function getHeaders() {
    const headers = new Headers();
    headers.append("Content-Type", "application/json");

    const token = getToken();

    if (token) {
        headers.append("X-AUTH-TOKEN", token);
    }

    return headers;
}


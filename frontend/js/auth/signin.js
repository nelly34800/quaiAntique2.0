const emailInput = document.getElementById('email');
const passwordInput = document.getElementById('password');
const btnSignin = document.getElementById('signin');

btnSignin.addEventListener('click', checkCredentials);

// vérifie le mail et le mot de passe 
function checkCredentials(){
    // ici il faudra appeler l'API pour vérifier les crendentials en BDD
    if(emailInput.value == "test@mail.com" && passwordInput.value == "123") {

        // il faudra récupérer le vrai token
        const token = "khbdkfhdfkmhdfkjldfghaetryetuyiryuivksjhvkl";
        setToken(token);

        eraseCookie(roleCookieName);
        setCookie(roleCookieName, "admin", 7);

        // placer ce token en cookie
        window.location.replace("/");
    }
    else{
        emailInput.classList.add('is-invalid');
        passwordInput.classList.add('is-invalid');
    }
}
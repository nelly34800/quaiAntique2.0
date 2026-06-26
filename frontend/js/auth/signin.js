const emailInput = document.getElementById('email');
const passwordInput = document.getElementById('password');
const btnSignin = document.getElementById('signin');
const signinForm = document.getElementById('signinForm');

btnSignin.addEventListener('click', checkCredentials);

// vérifie le mail et le mot de passe 
function checkCredentials(){
    let dataForm = new FormData(signinForm);

    let myHeaders = new Headers();
    myHeaders.append("Content-Type", "application/json");

    let raw = JSON.stringify({
        email: dataForm.get("email"),
        password: dataForm.get("password")
    });

    let requestOptions = {
        method: 'POST',
        headers: myHeaders,
        body: raw,
        redirect: 'follow'
    };

    fetch(apiUrl+"login", requestOptions)
    .then(response => {
        if(response.ok){
            return response.json();
        }
        else{
            emailInput.classList.add("is-invalid");
            passwordInput.classList.add("is-invalid");
        }
    })
    .then(result => {
        const token = result.apiToken;
        setToken(token);
        //placer ce token en cookie

        setCookie(roleCookieName, result.roles[0], 7);
        window.location.replace("/");
    })
    .catch(error => console.log('error', error));
}
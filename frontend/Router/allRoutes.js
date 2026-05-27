import Route from "./route.js";
//Définir ici vos routes
export const allRoutes = [
  new Route("/", "Accueil", "./pages/home.html", []),
  new Route("/galery", "Galerie", "./pages/galery.html", []),
  new Route("/menu", "La carte", "./pages/menu.html", []),
  new Route("/signin", "Connexion", "./pages/auth/signin.html", []),
  new Route("/signup", "Inscription", "./pages/auth/signup.html", []),
  new Route("/account", "Mon compte", "./pages/auth/account.html", []),
  new Route("/editPassword", "Changer mot de passe", "./pages/auth/editPassword.html", []),
  new Route("/allReservations", "Vos réservations", "./pages/reservations/allReservations.html", []),
  new Route("/reserve", "Réserver", "./pages/reservations/reserve.html", []),
];

//Le titre s'affiche comme ceci : Route.titre - websitename
export const websiteName = "Quai Antique2.0";
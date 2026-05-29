import Route from "./route.js";
//Définir ici vos routes
export const allRoutes = [
  new Route("/", "Accueil", "./pages/home.html", []),
  new Route("/galery", "Galerie", "./pages/galery.html", []),
  new Route("/menu", "La carte", "./pages/menu.html", []),
  new Route("/editMenu", "La carte", "./pages/editMenu.html", ["admin"]),
  new Route("/signin", "Connexion", "./pages/auth/signin.html", ["disconnected"], "js/auth/signin.js"),
  new Route("/signup", "Inscription", "./pages/auth/signup.html", ["disconnected"], "js/auth/signup.js"),
  new Route("/account", "Mon compte", "./pages/auth/account.html", ["client", "admin"], "js/auth/account.js"),
  new Route("/editPassword", "Changer mot de passe", "./pages/auth/editPassword.html", ["client"]),
  new Route("/allReservations", "Vos réservations", "./pages/reservations/allReservations.html", ["client"]),
  new Route("/reserve", "Réserver", "./pages/reservations/reserve.html", ["client"]),
];

//Le titre s'affiche comme ceci : Route.titre - websitename
export const websiteName = "Quai Antique2.0";
import Route from "./route.js";
//Définir ici vos routes
export const allRoutes = [
  new Route("/", "Accueil", "./pages/home.html", []),
  new Route("/gallery", "Galerie", "./pages/gallery.html", [], "js/gallery.js"),
  new Route("/menu", "La carte", "./pages/menu.html", []),
  new Route("/editMenu", "La carte", "./pages/editMenu.html", ["ROLE_ADMIN"]),
  new Route("/signin", "Connexion", "./pages/auth/signin.html", ["disconnected"], "js/auth/signin.js"),
  new Route("/signup", "Inscription", "./pages/auth/signup.html", ["disconnected"], "js/auth/signup.js"),
  new Route("/account", "Mon compte", "./pages/auth/account.html", ["ROLE_USER", "ROLE_ADMIN"], "js/auth/account.js"),
  new Route("/editPassword", "Changer mot de passe", "./pages/auth/editPassword.html", ["ROLE_USER"]),
  new Route("/allReservations", "Mes réservations", "./pages/reservations/allReservations.html", ["ROLE_USER"]),
  new Route("/reserve", "Réserver", "./pages/reservations/reserve.html", ["ROLE_USER"]),
  new Route("/admin", "espace administrateur", "./pages/admin/admin.html", ["ROLE_ADMIN"], "js/admin/admin.js"),
  new Route("/food", "les plats", "./pages/admin/food.html", ["ROLE_ADMIN"], "js/admin/food.js"),
  new Route("/addFood", "Ajouter un plat", "./pages/admin/addFood.html", ["ROLE_ADMIN"], "js/admin/addFood.js"),
  new Route("/categories", "Les Catégories", "./pages/admin/categories.html", ["ROLE_ADMIN"], "js/admin/categories.js"),
  new Route("/addCategory", "Ajouter une catégorie", "./pages/admin/addCategory.html", ["ROLE_ADMIN"], "js/admin/addCategory.js"),
];

//Le titre s'affiche comme ceci : Route.titre - websitename
export const websiteName = "Quai Antique2.0";
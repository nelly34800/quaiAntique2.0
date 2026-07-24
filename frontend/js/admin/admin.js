// tableau des résérvations
// Implémenter js de ma page
const filterName = document.getElementById("filterName");
const filterStartDate = document.getElementById("filterStartDate");
const filterEndDate = document.getElementById("filterEndDate");

const tbody = document.querySelector("tbody");

let allBookings = [];
// charger les réserations
function LoadBookings(){

  // Crée un nouvel objet Headers pour définir les en-têtes de la requête HTTP
  let myHeaders = new Headers();
  // Ajoute l'en-tête "Content-Type" avec la valeur "application/json"
  myHeaders.append("Content-Type", "application/json");

  // Configure les options de la requête HTTP
  let requestOptions = {
    // Méthode de la requête : "GET" pour récupérer les données du serveur
    method: 'GET',
    // Définit les en-têtes de la requête en utilisant l'objet Headers créé précédemment
    headers: myHeaders
  };

  fetch(apiUrl+"booking", requestOptions)
  .then(async response => {
    if (!response.ok) {
        throw new Error('Erreur API');
    }

    const data = await response.json();

    fillDateFilter(allBookings);
    displayBookings(allBookings);
    })

    .then(result => {})
    .catch(error => {
        console.error(error);
        alert("Erreur lors de l'affichage");
    });
  }
  loadBookings();
// appliquer filtres 
function applyFilters() {
  const nameValue = filterName.value.toLowerCase();
  const filterStartDate = filterStartDate.value;
  const filterEndDate  = filterEndDate.value;

  const filteredBookings = allBookings.filter(booking => {
    const matchName = !nameValue || booking.user_last_name.toLowerCase().includes(nameValue);
    const matchDates = date.filter(date => date >= filterStartDate && date <= filterEndDate);

    return matchName && matchDates;
  });
  displayBookings(filteredBookings);
}
//écoute des changements
filterName.addEventListener("input", applyFilters);
filterfilterStartDate.addEventListener("change", applyFilters);
filterfilterEndDate.addEventListener("change", applyFilters);
// rempli le select des statuts

// affichage desktop + mobile
function displayBookings(bookings) {
  tbody.innerHTML = "";
  mobileContainer.innerHTML = "";

  if (bookings.length === 0) {
    tbody.innerHTML = `
      <tr>
        <td colspan="10" class="text-center">Aucune réservation ne correspond aux filtres.</td>
      </tr>
    `;

    mobileContainer.innerHTML = `
      <p class="text-center">Aucune réservation ne correspond aux filtres.</p>
    `;
    return;
  }
  bookings.forEach(booking => {
    displayBookingRow(booking);
    displayBookingCard(booking);
  });
}
// affichage des ligne desktop
function displayBookingRow(booking) {
  const tr = document.createElement("tr");

  // Nom prénom
  const tdName = document.createElement("td");
  tdName.textContent = `${booking.user_first_name} ${booking.user_last_name}`;
  tr.appendChild(tdName);

  // Date
  const tdDate = document.createElement("td");
  tdDate.textContent = booking.order_date;
  tr.appendChild(tdDate);

  // Heure
  const tdHour = document.createElement("td");
  tdHour.textContent = booking.order_hour;
  tr.appendChild(tdHour);

  // allergies
  const tdAllergy = document.createElement("td");
  tdAllergy.textContent = `${booking.allergy} €`;
  tr.appendChild(tdAllergy);

  // Nombre de convives
  const tdGuestNumber = document.createElement("td");
  tdGuestNumber.textContent = `${order.guest_number} pers`;
  tr.appendChild(tdGuestNumber);

  tbody.appendChild(tr);
}
// affichage des lignes mobile
function displayBookingCard(booking) {
  const card = document.createElement("div");
  card.className = "card mb-3";

  const cardBody = document.createElement("div");

  // Nom
  const name = document.createElement("p");
  name.textContent = `Nom : ${booking.user_first_name} ${booking.user_last_name}`;
  cardBody.appendChild(name);

  // Date
  const date = document.createElement("p");
  date.textContent = `Date : ${booking.order_date}`;
  cardBody.appendChild(date);

  // Heure
  const hour = document.createElement("p");
  hour.textContent = `Heure : ${booking.order_hour}`;
  cardBody.appendChild(hour);

  // Allergies
  const allergy = document.createElement("p");
  allergy.textContent = `Allergy : ${booking.allergy}`;
  cardBody.appendChild(allergy);

  // Nombre de convives
  const guestNumber = document.createElement("p");
  guestNumber.textContent = `Nombre de convives : ${booking.guest_number} personnes`;
  cardBody.appendChild(guestNumber);

  card.appendChild(cardBody);
  mobileContainer.appendChild(card);
}
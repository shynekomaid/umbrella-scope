"use strict";

let theme;
/**
 * Detects and applies the current theme, either from storage or system preference.
 * Theme can be changed by clicking the button with id "btn-theme".
 * @function
 */
function detectTheme() {
  const storedTheme = localStorage.getItem("theme");
  if (storedTheme) {
    theme = storedTheme;
  } else {
    theme = window.matchMedia("(prefers-color-scheme: dark)").matches
      ? "dark"
      : "light"; // Fallback to system preference
  }

  document.documentElement.setAttribute("data-bs-theme", theme);
}
detectTheme();

//
document.querySelectorAll("form").forEach((form) => {
  form.addEventListener("submit", (event) => {
    event.preventDefault();
  });
});

// function langInited() {}

const addLeadBtn = document.getElementById("add_lead");

if (addLeadBtn) {
  addLeadBtn.addEventListener("click", () => {
    const firstName = document.getElementById("firstName").value;
    const lastName = document.getElementById("lastName").value;
    const phone = document.getElementById("phone").value;
    const email = document.getElementById("email").value;
    // check if form valid
    const form = document.getElementById("add_lead_form");
    if (!form.checkValidity()) {
      return;
    }
    addLead(firstName, lastName, phone, email);
  });
}

function addLead(firstName, lastName, phone, email) {
  const url = "/api_v1/lead/add_lead.php";
  const data = {
    firstName: firstName,
    lastName: lastName,
    phone: phone,
    email: email,
  };
  const headers = {
    "Content-Type": "application/json",
  };
  fetch(url, {
    method: "POST",
    headers: headers,
    body: JSON.stringify(data),
  })
    .then((response) => response.json())
    .then((data) => {
      console.log(data);
    })
    .catch((error) => {
      console.error("Error:", error);
    });
}

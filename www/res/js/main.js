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

document.querySelectorAll("form").forEach((form) => {
  form.addEventListener("submit", (event) => {
    event.preventDefault();
  });
});

const dateToEl = document.getElementById("date_to");
const dateFromEl = document.getElementById("date_from");

if (dateToEl && dateFromEl) {
  const locale = navigator.language || "en-US"; // Get user's locale or default to 'en-US'
  const options = {
    year: "numeric",
    month: "2-digit",
    day: "2-digit",
    hour: "2-digit",
    minute: "2-digit",
  };

  /**
   * Format a Date object into a string like '2024-10-15T19:11'
   * @param {Date} date - The date to format
   * @returns {string} The formatted date string
   */
  const formatDate = (date) => {
    const [day, month, year, hour, minute] = new Intl.DateTimeFormat(
      locale,
      options
    )
      .formatToParts(date)
      .reduce((acc, part) => {
        if (part.type !== "literal") acc.push(part.value);
        return acc;
      }, []);
    return `${year}-${month}-${day}T${hour}:${minute}`;
  };

  const now = new Date();
  // 30 days before in ms
  const thirtyDaysAgo = new Date(now.getTime() - 30 * 24 * 60 * 60 * 1000);
  // 60 days and 10m before in ms
  const minDate = new Date(
    now.getTime() - 60 * 24 * 60 * 60 * 1000 - 10 * 60 * 1000
  );

  dateToEl.value = formatDate(now);
  dateFromEl.value = formatDate(thirtyDaysAgo);

  dateToEl.min = formatDate(minDate);
  dateFromEl.min = formatDate(minDate);
}

// function langInited() {} Called when lang files received.

const addLeadBtn = document.getElementById("add_lead");

if (addLeadBtn) {
  addLeadBtn.addEventListener("click", () => {
    const firstName = document.getElementById("firstName").value;
    const lastName = document.getElementById("lastName").value;
    const phone = document.getElementById("phone").value;
    const email = document.getElementById("email").value;
    const form = document.getElementById("add_lead_form");
    if (!form.checkValidity()) {
      return;
    }
    addLeadBtn.disabled = true;
    addLead(addLeadBtn, firstName, lastName, phone, email);
  });
}

const getLeadBtn = document.getElementById("get_lead");
if (getLeadBtn) {
  getLeadBtn.addEventListener("click", () => {
    const dateFrom = dateFromEl.value;
    const dateTo = dateToEl.value;
    let page = document.getElementById("page").value;
    page = parseInt(page, 10);

    getLeadBtn.disabled = true;
    toggleTableVision(false);
    getLead(getLeadBtn, dateFrom, dateTo, page);
  });
}

/**
 * Send a POST request to add a lead to the CRM.
 * @param {string} firstName - First name of the lead.
 * @param {string} lastName - Last name of the lead.
 * @param {string} phone - Phone number of the lead.
 * @param {string} email - Email address of the lead.
 */
function addLead(addLeadBtn, firstName, lastName, phone, email) {
  const url = "/api_v1/lead/add_lead.php";
  const data = {
    firstName: firstName,
    lastName: lastName,
    phone: phone,
    email: email,
  };
  fetch(url, {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
    },
    body: JSON.stringify(data),
  })
    .then((response) => response.json())
    .then((data) => {
      switch (data.error) {
        case "none": // Switch case to easy change text error for any error
          toast.show(LANG.lead_added, "success", data.response);
          break;
        default:
          if (data.response) {
            toast.show(
              LANG.error_add_lead,
              "danger",
              data.error + ": " + data.response
            );
          } else {
            toast.show(LANG.error_add_lead, "danger", data.error);
          }
          break;
      }
    })
    .catch((error) => {
      console.error("Error:", error);
    })
    .finally(() => {
      addLeadBtn.disabled = false;
    });
}

/**
 * Sends a POST request to retrieve lead data within a specified date range and page.
 *
 * This function disables the provided button, constructs a POST request with the given
 * date range and page, sends it to the server, and processes the response. It updates
 * the response table with the received data or displays an error message if the request
 * fails or returns an error.
 *
 * @param {HTMLElement} getLeadBtn - The button element that triggers the request, which will be disabled during the request.
 * @param {string} dateFrom - The start date for retrieving leads.
 * @param {string} dateTo - The end date for retrieving leads.
 * @param {string} page - The page number for paginated lead data.
 */
function getLead(getLeadBtn, dateFrom, dateTo, page) {
  const url = "/api_v1/lead/get_lead.php";
  const data = {
    dateFrom: dateFrom,
    dateTo: dateTo,
    page: page,
  };
  fetch(url, {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
    },
    body: JSON.stringify(data),
  })
    .then((response) => response.json())
    .then((data) => {
      switch (
        data.error // Switch case to easy change text error for any error
      ) {
        case "none":
          toggleTableVision(true);
          populateResponseTable(data.response);
          break;
        default:
          if (data.response) {
            toast.show(
              LANG.error_get_lead,
              "danger",
              data.error + ": " + data.response
            );
          } else {
            toast.show(LANG.error_get_lead, "danger", data.error);
          }
          break;
      }
    })
    .catch((error) => {
      console.error("Error:", error);
    })
    .finally(() => {
      getLeadBtn.disabled = false;
    });
}

/**
 * Toggle the visibility of the table card.
 *
 * @param {boolean} [state=false] Set to true to show the table card and false to hide it.
 */
function toggleTableVision(state = false) {
  const tableCard = document.querySelector("#tableCard");

  if (state) {
    tableCard.classList.remove("d-none");
  } else {
    tableCard.classList.add("d-none");
  }
}

/**
 * Populate the response table with data received from the server.
 *
 * @param {Object} data the response from the server
 *
 * The table will be populated with the following columns:
 * - ID: the id of the lead
 * - Email: the email address of the lead
 * - Status: the status of the lead
 * - FTD: the first time deposit ???
 *
 * If no data is provided, a single row will be added to the table with
 * a colspan of 4 and the text "No data available".
 */
function populateResponseTable(data) {
  const tableBody = document.querySelector("#responseTable");

  // Clear any existing rows in the table body
  tableBody.innerHTML = "";
  let count = 0;

  if (data && data.received && data.received.data) {
    data.received.data.forEach((row) => {
      row.forEach((item) => {
        const tr = document.createElement("tr");

        const idCell = document.createElement("td");
        idCell.textContent = item.id;
        tr.appendChild(idCell);

        const emailCell = document.createElement("td");
        emailCell.textContent = item.email;
        tr.appendChild(emailCell);

        const statusCell = document.createElement("td");
        statusCell.textContent = item.status;
        tr.appendChild(statusCell);

        const ftdCell = document.createElement("td");
        ftdCell.textContent = item.ftd;
        tr.appendChild(ftdCell);
        tableBody.appendChild(tr);
        count++;
      });
    });
  }
  if (count === 0) {
    // If no data - colspaned row to show it
    const tr = document.createElement("tr");
    const noDataCell = document.createElement("td");
    noDataCell.colSpan = 4;
    noDataCell.textContent = LANG.no_data;
    tr.appendChild(noDataCell);
    tableBody.appendChild(tr);
  }
}

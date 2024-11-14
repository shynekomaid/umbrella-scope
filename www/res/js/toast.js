const maxToasts = 5;

let toasts = [];
/**
 * Generates a random UUID (universally unique identifier) v4.
 * Based on: https://stackoverflow.com/a/2117523/2750743
 * @returns {string} A v4 UUID.
 */
function uuidv4() {
  return ([1e7] + -1e3 + -4e3 + -8e3 + -1e11).replace(/[018]/g, (c) =>
    (
      c ^
      (crypto.getRandomValues(new Uint8Array(1))[0] & (15 >> (c / 4)))
    ).toString(16)
  );
}

const toast = {
  /**
   * Creates a toast notification with the given caption, type and text.
   * The destroy_time parameter specifies the time in milliseconds to wait before
   * automatically removing the toast.
   * @param {string} caption - The title of the toast notification
   * @param {string} type - The type of the toast notification (primary, secondary, success, danger, warning, info, light, dark)
   * @param {string} text - The content of the toast notification
   * @param {number} [destroy_time=5000] - The time in milliseconds to wait before automatically removing the toast
   */
  show: (caption, type, text, destroy_time = 5000) => {
    const id = uuidv4();
    toasts.push(id);
    if (toasts.length > maxToasts) {
      toast.destroy(toasts.shift());
    }
    let template;
    if (text) {
      template = `
      <div id="${id}" class="alert border border-3 border-${type} alert-${type} alert-dismissible" role="alert">
        <h4 class="alert-heading">${caption}</h4>
        ${text}
        <button data-id="${id}" type="button" class="close-x toast_close" data-dismiss="alert" aria-label="Close">
          <span data-id="${id}" class="toast_close text-bold text-${type}" aria-hidden="true">&times;</span>
        </button>
      </div>`;
    } else {
      template = `
      <div id="${id}" class="alert alert-${type} alert-dismissible" role="alert">
        <h4 class="alert-heading mb-0">${caption}</h4>
        <button data-id="${id}" type="button" class="close-x toast_close" data-dismiss="alert" aria-label="Close">
          <span data-id="${id}" aria-hidden="true" class="text-bold toast_close text-${type}">&times;</span>
        </button>
      </div>`;
    }
    document.getElementById("toaster").innerHTML += template;
    const toastElement = document.getElementById(id);
    toastElement.addEventListener("mouseover", function () {
      toastElement.setAttribute("hovered", true);
      clearTimeout(toastElement.timeout);
    });

    toastElement.addEventListener("mouseleave", function () {
      toastElement.removeAttribute("hovered");
      toastElement.timeout = setTimeout(toast.destroy, 1500, id); // 1.5-second timeout
    });

    toastElement.timeout = setTimeout(toast.destroy, destroy_time, id);
  },
  /**
   * Removes a toast notification from the page. If the toast is not found, no error is thrown.
   * @param {string} id - The id of the toast to be removed
   */
  destroy: (id) => {
    toasts = toasts.filter((e) => e !== id);
    try {
      document.getElementById(id).outerHTML = "";
    } catch (error) {}
  },
};

document.addEventListener("DOMContentLoaded", function () {
  document
    .getElementById("toaster")
    .addEventListener("click", function (event) {
      if (event.target.classList.contains("toast_close")) {
        let toastId = event.target.getAttribute("data-id");
        toast.destroy(toastId);
      }
    });
});

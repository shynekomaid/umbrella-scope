function goToPage(page, replace = false) {
  if (replace) {
    window.location.replace(page);
  } else {
    window.location.href = page;
  }
}

let waitmng = {
  state: false,
  on: () => {
    setTimeout(waitmng.off, 30 * 1000);
    document.getElementById("waitlay").classList.add("waitlay");
    waitmng.state = true;
  },
  off: () => {
    document.getElementById("waitlay").classList.remove("waitlay");
    waitmng.state = false;
  },
  toggle: () => {
    if (waitmng.state) waitmng.off();
    else waitmng.on();
  },
};

const supportedLanguages = ["en", "uk"];
let LANG = {};

let trOptions = {
  fallbackLang: "en",
  basepath: window.location.pathname.replace(/\/[^/]+$/, "") + "lang/",
  targetSelector: "[tr]",
  targetSelectorAttr: "tr",
  titleSelector: "[title_tr]",
  titleSelectorAttr: "title_tr",
  placeHolderSelector: "[placeholder_tr]",
  placeHolderSelectorAttr: "placeholder_tr",
  insertAttrName: true,
  lang: window.navigator.language.slice(0, 2),
};

if (supportedLanguages.indexOf(trOptions.lang) < 0) trOptions.lang = "en";

function buildFullPath(lang, basePath) {
  return basePath + lang + ".json";
}

function setLang() {
  document.querySelectorAll(trOptions.targetSelector).forEach((el) => {
    const targetAttr = el.getAttribute(trOptions.targetSelectorAttr);
    const newVal = LANG[targetAttr];
    if (typeof newVal !== "undefined") el.innerHTML = newVal;
    else if (trOptions.insertAttrName) el.innerHTML = targetAttr;
  });
  document.querySelectorAll(trOptions.titleSelector).forEach((el) => {
    const targetAttr = el.getAttribute(trOptions.titleSelectorAttr);
    const newVal = LANG[targetAttr];
    if (typeof newVal !== "undefined") el.setAttribute("title", newVal);
    else if (trOptions.insertAttrName) el.setAttribute("title", targetAttr);
  });
  document.querySelectorAll(trOptions.placeHolderSelector).forEach((el) => {
    const targetAttr = el.getAttribute(trOptions.placeHolderSelectorAttr);
    const newVal = LANG[targetAttr];
    if (typeof newVal !== "undefined") el.setAttribute("placeholder", newVal);
    else if (trOptions.insertAttrName)
      el.setAttribute("placeholder", targetAttr);
  });
  if (typeof langInited === typeof Function) {
    langInited();
  }
}

function getLang() {
  const xhr = new XMLHttpRequest();
  xhr.addEventListener("readystatechange", function () {
    if (this.readyState === this.DONE) {
      LANG = JSON.parse(this.responseText);
      if (trOptions.fallbackLang !== trOptions.lang) {
        const xhrLocale = new XMLHttpRequest();
        xhrLocale.addEventListener("readystatechange", function () {
          if (this.readyState === this.DONE) {
            LANG = { ...LANG, ...JSON.parse(this.responseText) };
            setLang();
          }
        });
        xhrLocale.open("GET", trOptions.fullPath);
        xhrLocale.send();
      } else {
        setLang();
      }
    }
  });
  xhr.open("GET", trOptions.fallPath);
  xhr.send();
}

document.addEventListener("DOMContentLoaded", langInit);

function langInit() {
  trOptions.fullPath = buildFullPath(trOptions.lang, trOptions.basepath);
  trOptions.fallPath = buildFullPath(
    trOptions.fallbackLang,
    trOptions.basepath
  );

  getLang();
}

const supportedLanguages = ["en", "uk"];
let LANG = {};

let trOptions = {
  fallbackLang: "en",
  basepath: "lang/",
  targetSelector: "[tr]",
  targetSelectorAttr: "tr",
  titleSelector: "[title_tr]",
  titleSelectorAttr: "title_tr",
  placeHolderSelector: "[placeholder_tr]",
  placeHolderSelectorAttr: "placeholder_tr",
  insertAttrName: true,
  lang: localStorage.getItem("lang") || window.navigator.language.slice(0, 2),
};

// Ukrainian is DEFAULT language for users from Belarus and Russia ^_^
if (trOptions.lang === "be" || trOptions.lang === "ru") trOptions.lang = "uk";
// Othervise, if user language not found - used English
else if (supportedLanguages.indexOf(trOptions.lang) < 0) trOptions.lang = "en";

/**
 * Construct a full path to a JSON file containing translations
 * @param {string} lang - Two-letter language code
 * @param {string} basePath - Base path to the directory containing translation files
 * @returns {string} Full path to the JSON translation file
 */
function buildFullPath(lang, basePath) {
  return basePath + lang + ".json";
}

/**
 * Updates the innerHTML or attributes of DOM elements based on the current language settings.
 *
 * This function iterates over elements that match the specified selectors in `trOptions`.
 * It updates their content or attributes (`innerHTML`, `title`, `placeholder`) using
 * translations from the `LANG` object. If a translation is not found, it may use the
 * attribute name itself as a fallback if `insertAttrName` is true.
 *
 * Additionally, if a `langInited` function is defined, it will be called after the updates.
 */
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
  // Called langInited() if exist, when complete
  if (typeof langInited === typeof Function) langInited();
}

/**
 * Loads translations for the current language from a JSON file.
 * If the current language is not the same as the fallback language,
 * it will also load the fallback language translations and merge them
 * into the main `LANG` object.
 * Finally, it calls `setLang()` to apply the translations to the page.
 */
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

/**
 * Initializes language settings by constructing paths for the current and fallback languages.
 * Uses these paths to load translations for the current language.
 */
function langInit() {
  trOptions.fullPath = buildFullPath(trOptions.lang, trOptions.basepath);
  trOptions.fallPath = buildFullPath(
    trOptions.fallbackLang,
    trOptions.basepath
  );
  getLang();
}

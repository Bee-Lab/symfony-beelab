import Alert from "bootstrap/js/src/alert";
import Collapse from "bootstrap/js/src/collapse";
import Dropdown from "bootstrap/js/src/dropdown";

// alert
let alerts = document.querySelectorAll("button.btn-close");
Array.from(alerts).map(alert => new Alert(alert));

// collapse
let collapses = document.querySelectorAll("[data-bs-toggle='collapse']");
Array.from(collapses).map(collapse => new Collapse(collapse));

// dropdown
let dropdowns = document.querySelectorAll("[data-bs-toggle='dropdown']");
Array.from(dropdowns).map(dropdown => new Dropdown(dropdown));

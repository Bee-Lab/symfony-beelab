import Alert from "bootstrap.native/dist/components/alert-native.js";
import Collapse from "bootstrap.native/dist/components/collapse-native.js";
import Dropdown from "bootstrap.native/dist/components/dropdown-native.js";

// alert
let alerts = document.querySelectorAll("button.close");
Array.from(alerts).map(alert => new Alert(alert));

// collapse
let collapses = document.querySelectorAll("[data-toggle='collapse']");
Array.from(collapses).map(collapse => new Collapse(collapse));

// dropdown
let dropdowns = document.querySelectorAll("[data-toggle='dropdown']");
Array.from(dropdowns).map(dropdown => new Dropdown(dropdown));

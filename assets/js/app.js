let $ = require('jquery');
import 'bootstrap-sass/assets/javascripts/bootstrap/alert';
import 'bootstrap-sass/assets/javascripts/bootstrap/collapse';
import 'bootstrap-sass/assets/javascripts/bootstrap/dropdown';
import 'bootstrap-sass/assets/javascripts/bootstrap/modal';
import '@fortawesome/fontawesome-free/js/all';

$(document).ready(function () {
    'use strict';

    // hamburger
    $('.navbar-toggle').on('click', function () {
        $(this).toggleClass('active');
    });
});

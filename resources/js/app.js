import './bootstrap'; // Si este archivo es necesario, mantenlo
import * as bootstrap from 'bootstrap';
import * as Popper from '@popperjs/core'; // Importa todo Popper.js
import flatpickr from "flatpickr";
import "flatpickr/dist/flatpickr.min.css"; // Importar los estilos de Flatpickr
import '@fortawesome/fontawesome-free/css/all.min.css';
import Swal from 'sweetalert2';

window.bootstrap = bootstrap;
window.Popper = Popper;
window.flatpickr = flatpickr;

document.addEventListener('DOMContentLoaded', function () {
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Formatos de fecha y hora con Flatpickr
    flatpickr(".datepicker", {
        dateFormat: "m/d/Y",
        onOpen: function (selectedDates, dateStr, instance) {
            if (dateStr === "") {
                instance.setDate(new Date(), true);
            }
        },
    });

    flatpickr(".datetimepicker", {
        enableTime: true,
        dateFormat: "m/d/Y H:i:S",
        time_24hr: true,
        enableSeconds: true,
        onOpen: function (selectedDates, dateStr, instance) {
            if (dateStr === "") {
                instance.setDate(new Date(), true);
            }
        },
    });
});


import './bootstrap'; // Si este archivo es necesario, mantenlo
import * as bootstrap from 'bootstrap';
import * as Popper from '@popperjs/core'; // Importa todo Popper.js
import flatpickr from "flatpickr";
import "flatpickr/dist/flatpickr.min.css"; // Importar los estilos de Flatpickr
import '@fortawesome/fontawesome-free/css/all.min.css';
import Swal from 'sweetalert2';

window.Swal = Swal;
window.bootstrap = bootstrap;
window.Popper = Popper;
window.flatpickr = flatpickr;

document.addEventListener('DOMContentLoaded', function () {
    var modals = document.querySelectorAll('.modal');

    // Inicializa tooltips en toda la página
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[title]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Aplica la función a cada modal
    /*modals.forEach(function (modal) {
        modal.addEventListener('hidden.bs.modal', function () {
            // Ocultar y eliminar tooltips al cerrar el modal
            tooltipList.forEach(function (tooltip) {
                tooltip.hide();
                tooltip.dispose();
            });

            // Reactivar tooltips después de cerrar el modal
            setTimeout(function () {
                tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                    return new bootstrap.Tooltip(tooltipTriggerEl);
                });
            }, 300); // Espera 300ms antes de reinicializar
        });
    });*/

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
        onChange: function (selectedDates, dateStr, instance) {
            let arrivalInput = document.getElementById("inputnewmastercfsarrivaldate");
            let lfdInput = document.getElementById("inputnewmastercfslfd");
    
            if (lfdInput && instance.input === arrivalInput) {
                let newDate = new Date(selectedDates[0]);
                newDate.setDate(newDate.getDate() + 8);
                newDate.setHours(23, 59, 59);
    
                // Establecer la fecha sugerida
                lfdInput._flatpickr.setDate(newDate, true);
    
                // Limitar la fecha mínima a +7 días desde la llegada
                let minLfdDate = new Date(selectedDates[0]);
                minLfdDate.setDate(minLfdDate.getDate() + 7);
                lfdInput._flatpickr.set('minDate', minLfdDate);
            }
        }
    });
    
});


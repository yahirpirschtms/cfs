var selectedDrayageUser = []; // Array para almacenar los nombres de 'drayage_user'
var selectedDrayageUserUpdate = [];
var selectedDrayageFiletype = []; // Array para almacenar los nombres de 'drayage_filetype'
var selectedDrayageFiletypeUpdate = [];
var table;
var tableMasters;

$(document).ready(function() {

    //Incluir el token en las peticiones
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    //Hacer conteo de masters y subprojects(facturados y no)
    if (window.projectsData) {
        const projectsData = window.projectsData;

        // Mapeo y cálculo de estadísticas de los proyectos
        $.each(projectsData, function(index, project) {
            let totalMasters = 0;
            let totalFacturadosMasters = 0;
            let masterDetails = [];
            let totalPallets = 0;
            let totalPieces =0;

            $.each(project.masters, function(index, master) {
                totalMasters++;

                let totalSubprojects = 0;
                let totalFacturadosSubprojects = 0;

                // Verificar si master tiene subprojects
                if (master.subprojects && Array.isArray(master.subprojects) && master.subprojects.length > 0) {
                    $.each(master.subprojects, function(index, subproject) {
                        totalSubprojects++;
                        if (subproject.customs_release_checkbox === 'yes') {
                            totalFacturadosSubprojects++;
                        }
                        totalPallets = totalPallets + subproject.pallets;
                        totalPieces = totalPieces + subproject.pieces;
                    });

                    // Verificar si todos los subprojects están facturados
                    if (totalSubprojects === totalFacturadosSubprojects) {
                        totalFacturadosMasters++;
                    }
                }

                // Agregar los detalles del master
                masterDetails.push({
                    masterId: master.mbl,
                    totalSubprojects: totalSubprojects,
                    subprojectsFacturados: totalFacturadosSubprojects,
                });
            });

            // Agregar las estadísticas calculadas al proyecto
            project.totalMasters = totalMasters;
            project.totalFacturadosMasters = totalFacturadosMasters;
            project.masterDetails = masterDetails;
            project.totalPallets = totalPallets;
            project.totalPieces = totalPieces;

            // Mostrar las estadísticas en consola para verificar
            console.log(`Project ${project.project_id} tiene ${totalMasters} masters, de los cuales ${totalFacturadosMasters} están facturados.`);
        });

        console.log(projectsData); // Muestra los proyectos con sus nuevas estadísticas
        

        const $tableBody = $('#projectsTable tbody');
        $tableBody.empty(); // Limpiar contenido actual

        projectsData.forEach(project => {
            const isFacturado = project.totalMasters > 0 && project.totalFacturadosMasters === project.totalMasters;
            const iconHTML = isFacturado
                ? `<i class="fa-solid fa-circle-check" style="color:rgb(13, 82, 200)"></i> Yes`
                : `<i class="text-danger fa-solid fa-circle-xmark"></i> No`;

            const drayageUser = project.drayage_user_relation?.gntc_description || 'N/A';
            const drayageFile = project.drayage_file_relation?.gntc_description || 'N/A';

            // Totales para Subprojects
            let totalSub = 0;
            let totalSubFacturados = 0;

            project.masterDetails.forEach(master => {
                totalSub += master.totalSubprojects;
                totalSubFacturados += master.subprojectsFacturados;
            });

            const subprojectsNotFacturados = totalSub - totalSubFacturados;
            const mastersNotFacturados = project.totalMasters - project.totalFacturadosMasters;

            const rowHTML = `
                <tr>
                    <td style="font-weight:500">${project.project_id}</td>
                    <td style="font-weight:500">${iconHTML}</td>
                    <td style="font-weight:500">${project.month}</td>
                    <td style="font-weight:500">${drayageUser}</td>
                    <td style="font-weight:500">${drayageFile}</td>
                    <td class="showcfsmastermodal" style="cursor: pointer;" data-projectid="${project.project_id}">
                        <ul class="list-group">
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                Totals
                                <span class="badge" style="background-color: darkorange;">${project.totalMasters}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                Invoiced
                                <span class="badge" style="background-color: dodgerblue;">${project.totalFacturadosMasters}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                Not invoiced
                                <span class="badge ms-2 text-bg-danger">${mastersNotFacturados}</span>
                            </li>
                        </ul>
                    </td>
                    <td>
                        <ul class="list-group">
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                Totals
                                <span class="badge" style="background-color: mediumseagreen;">${totalSub}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                Invoiced
                                <span class="badge" style="background-color: dodgerblue;">${totalSubFacturados}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                Not invoiced
                                <span class="badge ms-2 text-bg-danger">${subprojectsNotFacturados}</span>
                            </li>
                        </ul>
                    </td>
                    <td style="font-weight:500">${project.totalPallets}</td>
                    <td style="font-weight:500">${project.totalPieces}</td>
                    <td>
                        <div>
                            <div class="ms-auto p-2">
                                <button type="button" class="btn btn-sm btn-edit-project" style="color:rgb(13, 82, 200)" data-bs-placement="top"  data-projectid="${project.project_id}" title="Edit project">
                                    <i class="fa-solid fa-pen"></i>
                                </button>
                            </div>
                            <div class="p-2">
                                <button type="button" class="btn btn-sm btn-danger btn-delete-project" data-projectid="${project.project_id}" data-bs-placement="top" title="Houses Invoiced">
                                    <i class="fa-solid fa-trash-can"></i>
                                </button>
                            </div>
                        </div>
                    </td>
                </tr>
            `;

            $tableBody.append(rowHTML);
        });

    } else {
        console.error('No se encontró la variable projectsData');
    }

    // Verifica si la tabla de  Projects ya ha sido inicializada antes de inicializarla
    if (!$.fn.dataTable.isDataTable('#projectsTable')) {
        table = $('#projectsTable').DataTable({
            paging: false,  // Desactiva la paginación
            searching: true, // Mantiene la búsqueda activada
            info: false,     // Oculta la información
            lengthChange: false // Desactiva el cambio de cantidad de registros
        });
    } else {
        // Si la tabla ya está inicializada, se puede actualizar la configuración
        table.page.len(-1).draw();  // Muestra todos los registros sin paginación
    }

    //Borrar el contenido del filtro general al cargar la pagina para projects
    $('#searchgeneralcfsboard').val('');

    //Hacer que el filtro general sirva como un search de la tabla para projects
    $('#searchgeneralcfsboard').on('input', function() {
        table.search(this.value).draw(); // Busca en todas las columnas
    });

    // Verifica si la tabla de Masters ya ha sido inicializada antes de inicializarla
    if (!$.fn.dataTable.isDataTable('#MastersTable')) {
        tableMasters = $('#MastersTable').DataTable({
            paging: false,  // Desactiva la paginación
            searching: true, // Mantiene la búsqueda activada
            info: false,     // Oculta la información
            lengthChange: false // Desactiva el cambio de cantidad de registros
        });
    } else {
        // Si la tabla ya está inicializada, se puede actualizar la configuración
        tableMasters.page.len(-1).draw();  // Muestra todos los registros sin paginación
    }

    //Hacer que el filtro general sirva como un search de la tabla 
    $('#searchgeneralcfsboardmasters').on('input', function() {
        tableMasters.search(this.value).draw(); // Busca en todas las columnas
    });

    //Variables para los catalogos
    var isCatalogsLoaded = false; // Bandera para evitar la carga repetida
    var newlySelectedDrayageUser = null;
    var newlySelectedDrayageFileType = null;

    //Funcion que carga todos los catalogos al cargar la pagina
    function loadGeneralSelects() {
        if (isCatalogsLoaded) return; // Evita cargar dos veces
        
        $.ajax({
            url: 'getLoadSelects',
            type: 'GET',
            dataType: 'json',
            success: function (data) {
                // Procesar los datos de 'drayage_user' y 'drayage_filetype'
                var drayageUserData = data.drayage_user.map(item => ({
                    id: item.gnct_id,
                    text: item.gntc_value
                }));
                var drayageFiletypeData = data.drayage_filetype.map(item => ({
                    id: item.gnct_id,
                    text: item.gntc_value
                }));

                // Asegurarse de que no haya duplicados en 'drayage_user'
                data.drayage_user.forEach(function (user) {
                    if (!selectedDrayageUser.includes(user.gntc_value)) {
                        selectedDrayageUser.push(user.gntc_value); // Agregar al arreglo si no está ya
                    }
                });

                // Asegurarse de que no haya duplicados en 'drayage_filetype'
                data.drayage_filetype.forEach(function (filetype) {
                    if (!selectedDrayageFiletype.includes(filetype.gntc_value)) {
                        selectedDrayageFiletype.push(filetype.gntc_value); // Agregar al arreglo si no está ya
                    }
                });

                // Copiar los datos únicos a nuevas variables si se necesitan para otro propósito
                selectedDrayageUserUpdate = [...selectedDrayageUser];
                selectedDrayageFiletypeUpdate = [...selectedDrayageFiletype];

                console.log("Drayage Users cargados:", selectedDrayageUser);
                console.log("Drayage Filetypes cargados:", selectedDrayageFiletype);
                console.log("Drayage Users cargados en update:", selectedDrayageUser);
                console.log("Drayage Filetypes cargados en update:", selectedDrayageFiletype);

                // Inicializar Select2 para 'inputnewcfspeojectdrayageperson'
                $('#inputnewcfspeojectdrayageperson').select2({
                    placeholder: 'Select a Drayage User',
                    allowClear: true,
                    tags: false,
                    data: drayageUserData, // Los datos cargados desde el backend
                    dropdownParent: $('#neweditcfsproject'),
                    minimumInputLength: 0
                });

                // Inicializar Select2 para 'inputnewcfsprojectdrayagefiletype'
                $('#inputnewcfsprojectdrayagefiletype').select2({
                    placeholder: 'Select a Drayage File Type',
                    allowClear: true,
                    tags: false,
                    data: drayageFiletypeData, // Los datos cargados desde el backend
                    dropdownParent: $('#neweditcfsproject'),
                    minimumInputLength: 0
                });

                isCatalogsLoaded = true; // Marcar como cargado
            },
            error: function (xhr, status, error) {
                console.error('Error al cargar los catálogos:', error);
            }
        });
    }
    //Llamar a la funcion para cargar los catalogos al cargar la pagina
    loadGeneralSelects();

    //Funciones que permiten añadir nuevos registros por medio del select2 del Drayage User
    $('#inputnewcfspeojectdrayageperson').on('change', function () {
        var selectedOption = $(this).select2('data')[0]; // Obtener la opción seleccionada
        var selectedText = selectedOption ? selectedOption.text : ''; // Obtener el texto (nombre) de la opción seleccionada

        // Si no es el nuevo carrier, lo procesamos
        if (selectedText  !== newlySelectedDrayageUser &&  selectedText.trim() !== '') {
            console.log(selectedText);

            if (!selectedDrayageUser.includes(selectedText) || !selectedDrayageUserUpdate.includes(selectedText)) {
                if(!selectedDrayageUser.includes(selectedText)){
                    selectedDrayageUser.push(selectedText);  // Agregar al arreglo solo si no existe
                    console.log(selectedDrayageUser);  // Mostrar el arreglo con todos los drivers seleccionados
                }
                if(!selectedDrayageUserUpdate.includes(selectedText)){
                    selectedDrayageUserUpdate.push(selectedText);  // Agregar al arreglo solo si no existe
                    console.log(selectedDrayageUserUpdate);  // Mostrar el arreglo con todos los drivers seleccionados
                }
                saveDrayageUser(selectedText);
            }
        }
    });

    function saveDrayageUser(newDrayageUser) {
        $.ajax({
            url: 'saveNewDrayageUser',
            type: 'POST',
            data: {
                newDrayageUser: newDrayageUser
            },
            success: function (response) {
                console.log(response);

                // Crear una nueva opción para cada select2
                var newOption1 = new Option(response.newDrayageUserCreated.gntc_value, response.newDrayageUserCreated.gnct_id, true, true);
                var newOption2 = new Option(response.newDrayageUserCreated.gntc_value, response.newDrayageUserCreated.gnct_id, true, true);

                // Agregar la opción a ambos select2 sin eliminarla del otro
                $('#inputnewcfspeojectdrayageperson').append(newOption1).trigger('change');
                //$('#inputnewcfspeojectdrayagepersonUpdate').append(newOption2).trigger('change');
                
                // Seleccionar automáticamente el Drayage User
                $('#inputnewcfspeojectdrayageperson').val(response.newDrayageUserCreated.gnct_id).trigger('change');

                // Marcar el nuevo ID para evitar que se haga otra solicitud
                newlySelectedDrayageUser = response.newDrayageUserCreated.gntc_value;

                // Cuando el nuevo Drayage User sea creado, aseguramos que no se haga más AJAX para este User
                $('#inputnewcfspeojectdrayageperson').on('select2:select', function (e) {
                    var selectedId = e.params.data.id;
                    if (selectedId === newlySelectedDrayageUser) {
                        newlySelectedDrayageUser = null;  
                    }
                });
                //loadCarriersFilterCheckbox();
            },
            error: function (xhr, status, error) {
                if (xhr.status === 409) {
                    alert('Drayage User already exists.');
                } else {
                    console.error('An error has occurred saving Drayage User', error);
                }
            }
        });
    }

    //Funciones que permiten añadir nuevos registros por medio del select2 del Drayage File Type
    $('#inputnewcfsprojectdrayagefiletype').on('change', function () {
        var selectedOption = $(this).select2('data')[0]; // Obtener la opción seleccionada
        var selectedText = selectedOption ? selectedOption.text : ''; // Obtener el texto (nombre) de la opción seleccionada

        if (selectedText  !== newlySelectedDrayageFileType &&  selectedText.trim() !== '') {
            console.log(selectedText);

            if (!selectedDrayageFiletype.includes(selectedText) || !selectedDrayageFiletypeUpdate.includes(selectedText)) {
                if(!selectedDrayageFiletype.includes(selectedText)){
                    selectedDrayageFiletype.push(selectedText);
                    console.log(selectedDrayageFiletype);
                }
                if(!selectedDrayageFiletypeUpdate.includes(selectedText)){
                    selectedDrayageFiletypeUpdate.push(selectedText);
                    console.log(selectedDrayageFiletypeUpdate);
                }
                saveDrayageFileType(selectedText);
            }
        }
    });

    function saveDrayageFileType(newDrayageFileType) {
        $.ajax({
            url: 'saveNewDrayageFileType',
            type: 'POST',
            data: {
                newDrayageFileType: newDrayageFileType
            },
            success: function (response) {
                console.log(response);
                var newOption1 = new Option(response.newDrayageFileTypeCreated.gntc_value, response.newDrayageFileTypeCreated.gnct_id, true, true);
                var newOption2 = new Option(response.newDrayageFileTypeCreated.gntc_value, response.newDrayageFileTypeCreated.gnct_id, true, true);

                $('#inputnewcfsprojectdrayagefiletype').append(newOption1).trigger('change');
                //$('#inputnewcfsprojectdrayagefiletypeUpdate').append(newOption2).trigger('change');
                
                $('#inputnewcfsprojectdrayagefiletype').val(response.newDrayageFileTypeCreated.gnct_id).trigger('change');

                newlySelectedDrayageFileType = response.newDrayageFileTypeCreated.gntc_value;

                $('#inputnewcfsprojectdrayagefiletype').on('select2:select', function (e) {
                    var selectedId = e.params.data.id;
                    if (selectedId === newlySelectedDrayageFileType) {
                        newlySelectedDrayageFileType = null;  
                    }
                });
                //loadCarriersFilterCheckbox();
            },
            error: function (xhr, status, error) {
                if (xhr.status === 409) {
                    alert('Drayage File Type already exists.');
                } else {
                    console.error('An error has occurred saving Drayage File Type ', error);
                }
            }
        });
    }

    //Funciones para la validacion de inputs asi como guardar un nuveo proyecto
    const formFields = [
        'inputnewcfsprojectprojectid',
        'inputnewcfsprojectmonth',
        //'inputnewcfsprojectinvoice',
        'inputnewcfspeojectdrayageperson',
        'inputnewcfsprojectdrayagefiletype'
    ];
    
    function handleSelect2Events(field, errorElement) {
        const fieldValue = field.val();
        const customErrorMessage = field.data('error-message'); // Leer el mensaje desde el atributo data-error-message
    
        if (fieldValue === null || fieldValue === undefined || fieldValue === "") {
            field.siblings(".select2").find(".select2-selection").addClass("is-invalid");
            field.addClass('is-invalid');
            errorElement.text(customErrorMessage || 'This field is required.');
        } else {
            field.removeClass('is-invalid');
            errorElement.text('');
            field.siblings(".select2").find(".select2-selection").removeClass("is-invalid");
        }
    }
    
    function validateField(field, errorElement) {
        const fieldId = field.attr('id');
        const fieldValue = field.val().trim();
    
        if (fieldValue === "") {
            let customErrorMessage = 'This field is required.';
            if (fieldId === 'inputnewcfsprojectmonth') {
                customErrorMessage = 'Month is required.';
            } else if (fieldId === 'inputnewcfsprojectprojectid') {
                customErrorMessage = 'Project ID is required.';
            }/* else if (fieldId === 'inputnewcfsprojectinvoice') {
                customErrorMessage = 'Invoice is required.';
            }*/
            field.addClass('is-invalid');
            errorElement.text(customErrorMessage);
        } else {
            field.removeClass('is-invalid');
            errorElement.text('');
        }
    }
    
    formFields.forEach(fieldId => {
        const field = $("#" + fieldId);
        const errorElement = $('#error-' + fieldId);
        const isSelect2 = field.hasClass("searchDrayageUser") || field.hasClass("searchDrayageFileType");
    
        if (isSelect2) {
            field.on('change', function () {
                handleSelect2Events(field, errorElement);
            });
        } else {
            field.on('keyup blur', function () {
                validateField(field, errorElement);
            });
        }
    });
    
    $('body').on('click', '#saveeditnewcfsproject', function (e) {
        e.preventDefault();
        let valid = true;
    
        formFields.forEach(fieldId => {
            const field = $("#" + fieldId);
            const errorElement = $('#error-' + fieldId);
            const isSelect2 = field.hasClass("searchDrayageUser") || field.hasClass("searchDrayageFileType");
    
            if (isSelect2) {
                handleSelect2Events(field, errorElement); // Validación para select2
                if (field.hasClass('is-invalid')) {
                    valid = false;
                }
            } else {
                const fieldValue = field.val().trim();
                if (fieldValue === '') {
                    valid = false;
                    validateField(field, errorElement); // Validación para campos normales
                }
            }
        });
    
        if (!valid) {
            const firstInvalidField = $('.is-invalid').first();
            if (firstInvalidField.length) {
                firstInvalidField.focus();
            }
            return;
        }

        let formData = new FormData($('#createeditnewcfsproject')[0]);
        $.ajax({
            url: 'saveNewProject',
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function (response){
                Swal.fire({
                    icon: 'success',
                    title: '¡Success!',
                    text: 'Project successfully added.',
                    confirmButtonText: 'Ok'
                }).then(() =>{
                    // Actualizamos la variable global con los nuevos datos
                    window.projectsData = response.projects;
                    // Guardamos el valor actual del filtro
                    const currentFilter = table.search();
                    // Re-renderizamos la tabla
                    renderProjectsTable(window.projectsData);
                    // Restauramos el filtro anterior
                    table.search(currentFilter).draw();
                    $('#neweditcfsproject').modal('hide');
                });
            },
            error: function (xhr, status, error) {
                // Limpia los errores anteriores
                $('input, select').removeClass('is-invalid');
                $('.invalid-feedback').text(''); // Vaciar mensajes de error
                $('.select2-selection').removeClass('is-invalid'); // También eliminar la clase del contenedor de select2
    
                let errors = xhr.responseJSON.errors;
    
                // Verifica si hay errores
                if (errors) {
                    for (let field in errors) {
                        const inputField = $('#' + field);
    
                        // Verifica que exista el div de error; si no, lo crea
                        let errorContainer = inputField.next('.invalid-feedback');
    
                        // Si es un select2, buscamos su contenedor
                        if (inputField.hasClass('select2-hidden-accessible') || inputField.is('select')) {
                            const select2Container = inputField.next('.select2-container').find('.select2-selection');
                            select2Container.addClass('is-invalid');
                            errorContainer = inputField.parent().find('.invalid-feedback');
                        }
                        if (!errorContainer.length) {
                            errorContainer = $('<div>').addClass('invalid-feedback').insertAfter(inputField);
                        }
    
                        // Marca el input y muestra el error
                        inputField.addClass('is-invalid');
                        errorContainer.text(errors[field][0]);
                    }
                }
    
                // Mostrar mensaje de error general
                Swal.fire({
                    icon: 'error',
                    title: '¡Error!',
                    text: 'There was a problem adding the project. Please try again.',
                    confirmButtonText: 'Ok'
                });
            }
        })
    });
    
    //Funcion para resetear el formulario al cerrarlo de projects
    function resetModalNewCFSProjectFields(modalSelector) {
        // Limpiar selects con select2 (Drayage User y File Type)
        $(modalSelector).find('.searchDrayageUser, .searchDrayageFileType').each(function () {
            $(this).val(null).trigger('change'); // Restablecer y actualizar select2
        });

        // También puedes reiniciar todos los selects al primer índice si quieres asegurarte
        $(modalSelector).find('select').each(function () {
            this.selectedIndex = 0;
        });

        // Limpiar errores
        $(modalSelector).find('.is-invalid').removeClass('is-invalid');
        $(modalSelector).find('.invalid-feedback').text('');

        // Limpiar inputs y textarea
        $(modalSelector).find('input, textarea').val('');

        // Limpiar selects normales (no select2)
        $(modalSelector).find('select').not('.select2').val('');

        $('#editnewcfsproject').text('Save').attr('id', 'saveeditnewcfsproject');
        $('#staticnewcfsproject').text('New Project');
        $('#inputnewcfsprojectprojectid')
        .prop('readonly', false); 
    }

    // Llamar a la función para limpiar inputs cuando se cierre el modal de los proyects
    $('#neweditcfsproject').on('hidden.bs.modal', function() {
        resetModalNewCFSProjectFields('#neweditcfsproject');
    });

    //Abrir modal de edicion de projects
    $('#projectsTable').on('click', '.btn-edit-project', function () {
        const $btn = $(this);
        const projectId = $btn.closest('tr').find('td:first').text(); // esto obtiene el valor de la primera celda
        const project = window.projectsData.find(p => p.project_id == projectId);

        if(project){
            let drayageUserPromise = new Promise ((resolve, reject) => {
                if(project.drayage_user){
                    let drayage_user = selectedDrayageUser.find(item => item === project.drayage_user);
                    $('#inputnewcfspeojectdrayageperson').val(project.drayage_user).trigger('change');
                    resolve();
                } else {
                    $('#inputnewcfspeojectdrayageperson').val(null).trigger('change');
                    reject(); // Resolver inmediatamente si no hay carrier
                }
            });

            let drayageFileTypePromise = new Promise ((resolve, reject) => {
                if(project.drayage_typefile){
                    let drayage_typefile = selectedDrayageFiletype.find(item => item === project.drayage_typefile);
                    $('#inputnewcfsprojectdrayagefiletype').val(project.drayage_typefile).trigger('change');
                    resolve();
                } else {
                    $('#inputnewcfsprojectdrayagefiletype').val(null).trigger('change');
                    reject(); // Resolver inmediatamente si no hay carrier
                }
            });

            Promise.all([drayageUserPromise, drayageFileTypePromise])
                .then(() => {
                    // Cambiar el título del modal y el texto del botón
                    $('#staticnewcfsproject').text(`Edit Project  ${project.project_id}`).attr('id', 'staticnewcfsproject');
                    $('#saveeditnewcfsproject').text('Save Changes').attr('id', 'editnewcfsproject');
                
                    // Llenar los inputs con los datos del proyecto
                    $('#inputnewcfsprojectprojectid').val(project.project_id)
                    .prop('readonly', true); // Deshabilitar el input

                    $('#inputnewcfsprojectmonth').val(project.month);

                    $('#neweditcfsproject').modal('show');
                })
                .catch((error) => {
                    console.error(error);
                    Swal.fire({
                        title: 'Error',
                        text: 'There was an error getting the data.',
                        icon: 'error',
                        confirmButtonText: 'Ok'
                    });
                });
        }else{
            Swal.fire({
                title: 'Error',
                text: 'The project could not be identified.',
                icon: 'error',
                confirmButtonText: 'OK',
            });
        }
    });

    //Ejecutar el guardado de cambios de projects
    $('body').on('click', '#editnewcfsproject', function (e) {
        e.preventDefault();
        let valid = true;
        
        formFields.forEach(fieldId => {
            const field = $("#" + fieldId);
            const errorElement = $('#error-' + fieldId);
            const isSelect2 = field.hasClass("searchDrayageUser") || field.hasClass("searchDrayageFileType");
    
            if (isSelect2) {
                handleSelect2Events(field, errorElement); // Validación para select2
                if (field.hasClass('is-invalid')) {
                    valid = false;
                }
            } else {
                const fieldValue = field.val().trim();
                if (fieldValue === '') {
                    valid = false;
                    validateField(field, errorElement); // Validación para campos normales
                }
            }
        });
    
        if (!valid) {
            const firstInvalidField = $('.is-invalid').first();
            if (firstInvalidField.length) {
                firstInvalidField.focus();
            }
            return;
        }

        let formData = new FormData($('#createeditnewcfsproject')[0]);
        $.ajax({
            url: 'editNewProject',
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function (response){
                Swal.fire({
                    icon: 'success',
                    title: '¡Success!',
                    text: 'Project updated successfully.',
                    confirmButtonText: 'Ok'
                }).then(() =>{
                    // Actualizamos la variable global con los nuevos datos
                    window.projectsData = response.projects;
                    // Guardamos el valor actual del filtro
                    const currentFilter = table.search();
                    // Re-renderizamos la tabla
                    renderProjectsTable(window.projectsData);
                    // Restauramos el filtro anterior
                    table.search(currentFilter).draw();
                    $('#neweditcfsproject').modal('hide');
                });
            },
            error: function (xhr, status, error) {
                // Limpia los errores anteriores
                $('input, select').removeClass('is-invalid');
                $('.invalid-feedback').text(''); // Vaciar mensajes de error
                $('.select2-selection').removeClass('is-invalid'); // También eliminar la clase del contenedor de select2
    
                let errors = xhr.responseJSON.errors;
    
                // Verifica si hay errores
                if (errors) {
                    for (let field in errors) {
                        const inputField = $('#' + field);
    
                        // Verifica que exista el div de error; si no, lo crea
                        let errorContainer = inputField.next('.invalid-feedback');
    
                        // Si es un select2, buscamos su contenedor
                        if (inputField.hasClass('select2-hidden-accessible') || inputField.is('select')) {
                            const select2Container = inputField.next('.select2-container').find('.select2-selection');
                            select2Container.addClass('is-invalid');
                            errorContainer = inputField.parent().find('.invalid-feedback');
                        }
                        if (!errorContainer.length) {
                            errorContainer = $('<div>').addClass('invalid-feedback').insertAfter(inputField);
                        }
    
                        // Marca el input y muestra el error
                        inputField.addClass('is-invalid');
                        errorContainer.text(errors[field][0]);
                    }
                }
    
                // Mostrar mensaje de error general
                Swal.fire({
                    icon: 'error',
                    title: '¡Error!',
                    text: 'There was a problem updating the project. Please try again.',
                    confirmButtonText: 'Ok'
                });
            }
        })
    });

    //Ejecutar el borrado de algun project
    $('#projectsTable').on('click', '.btn-delete-project', function () {
        const $btn = $(this);
        const projectId = $btn.closest('tr').find('td:first').text(); // esto obtiene el valor de la primera celda
        const project = window.projectsData.find(p => p.project_id == projectId);

        if(project){
            Swal.fire({
                title: 'Are you sure?',
                text: `You are about to delete the project: ${project.project_id}. You will not be able to reverse this action.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete',
                cancelButtonText: 'Cancel',
            }).then((result) => {
                if(result.isConfirmed){
                    $.ajax({
                        url: 'deleteProject',
                        type: 'POST',
                        data: {
                            project_id: project.project_id
                        },
                        success: function(response){
                            if(response.success){
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Deleted!',
                                    text: 'Project deleted successfully.',
                                    confirmButtonText: 'Ok'
                                }).then(() =>{
                                    // Actualizamos la variable global con los nuevos datos
                                    window.projectsData = response.projects;
                                    // Guardamos el valor actual del filtro
                                    const currentFilter = table.search();
                                    // Re-renderizamos la tabla
                                    renderProjectsTable(window.projectsData);
                                    // Restauramos el filtro anterior
                                    table.search(currentFilter).draw();
                                });
                            }else{
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: 'There was an issue deleting the project.',
                                    confirmButtonText: 'Ok'
                                });
                            }
                            
                        },
                        
                    })
                }
            })
        }else{
            Swal.fire({
                title: 'Error',
                text: 'The project could not be identified.',
                icon: 'error',
                confirmButtonText: 'OK',
            });
        }
    });

    function renderProjectsTable(projectsData) { 
        table.clear(); // Limpia la tabla (mantiene la configuración y eventos)
    
        projectsData.forEach(project => {
            let totalMasters = 0;
            let totalFacturadosMasters = 0;
            let masterDetails = [];
            let totalPallets = 0;
            let totalPieces = 0;
    
            $.each(project.masters, function(index, master) {
                totalMasters++;
    
                let totalSubprojects = 0;
                let totalFacturadosSubprojects = 0;
    
                if (master.subprojects && Array.isArray(master.subprojects) && master.subprojects.length > 0) {
                    $.each(master.subprojects, function(index, subproject) {
                        totalSubprojects++;
                        if (subproject.customs_release_checkbox === 'yes') {
                            totalFacturadosSubprojects++;
                        }
                        totalPieces = totalPieces + subproject.pieces;
                        totalPallets = totalPallets + subproject.pallets;
                    });
    
                    if (totalSubprojects === totalFacturadosSubprojects) {
                        totalFacturadosMasters++;
                    }
                }
    
                masterDetails.push({
                    masterId: master.mbl,
                    totalSubprojects: totalSubprojects,
                    subprojectsFacturados: totalFacturadosSubprojects,
                });
            });
    
            project.totalMasters = totalMasters;
            project.totalFacturadosMasters = totalFacturadosMasters;
            project.masterDetails = masterDetails;
            project.totalPieces = totalPieces;
            project.totalPallets = totalPallets;
    
            const isFacturado = project.totalMasters > 0 && project.totalFacturadosMasters === project.totalMasters;
            const iconHTML = isFacturado
                ? `<i class="fa-solid fa-circle-check" style="color:rgb(13, 82, 200)"></i> Yes`
                : `<i class="text-danger fa-solid fa-circle-xmark"></i> No`;
    
            const drayageUser = project.drayage_user_relation?.gntc_description || 'N/A';
            const drayageFile = project.drayage_file_relation?.gntc_description || 'N/A';
    
            let totalSub = 0;
            let totalSubFacturados = 0;
    
            project.masterDetails.forEach(master => {
                totalSub += master.totalSubprojects;
                totalSubFacturados += master.subprojectsFacturados;
            });
    
            const subprojectsNotFacturados = totalSub - totalSubFacturados;
            const mastersNotFacturados = project.totalMasters - project.totalFacturadosMasters;
    
            const rowHTML = `
                <tr>
                    <td style="font-weight:500">${project.project_id}</td>
                    <td style="font-weight:500">${iconHTML}</td>
                    <td style="font-weight:500">${project.month}</td>
                    <td style="font-weight:500">${drayageUser}</td>
                    <td style="font-weight:500">${drayageFile}</td>
                    <td class="showcfsmastermodal" style="cursor: pointer;" data-projectid="${project.project_id}">
                        <ul class="list-group">
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                Totals
                                <span class="badge" style="background-color: darkorange;">${project.totalMasters}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                Invoiced
                                <span class="badge" style="background-color: dodgerblue;">${project.totalFacturadosMasters}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                Not invoiced
                                <span class="badge ms-2 text-bg-danger">${mastersNotFacturados}</span>
                            </li>
                        </ul>
                    </td>
                    <td>
                        <ul class="list-group">
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                Totals
                                <span class="badge" style="background-color: mediumseagreen;">${totalSub}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                Invoiced
                                <span class="badge" style="background-color: dodgerblue;">${totalSubFacturados}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                Not invoiced
                                <span class="badge ms-2 text-bg-danger">${subprojectsNotFacturados}</span>
                            </li>
                        </ul>
                    </td>
                    <td style="font-weight:500">${project.totalPallets}</td>
                    <td style="font-weight:500">${project.totalPieces}</td>
                    <td>
                        <div>
                            <div class="ms-auto p-2">
                                <button type="button" class="btn btn-sm btn-edit-project" style="color:rgb(13, 82, 200)" data-bs-placement="top"  data-projectid="${project.project_id}" title="Edit project">
                                    <i class="fa-solid fa-pen"></i>
                                </button>
                            </div>
                            <div class="p-2">
                                <button type="button" class="btn btn-sm btn-danger btn-delete-project" data-bs-placement="top" data-projectid="${project.project_id}" title="Houses Invoiced">
                                    <i class="fa-solid fa-trash-can"></i>
                                </button>
                            </div>
                        </div>
                    </td>
                </tr>
            `;
    
            table.row.add($(rowHTML)); // ¡Usamos DataTables para agregar la fila!
        });
        table.draw(); // Redibuja la tabla
    }

    //Abrir modal con los masters
    $('.showcfsmastermodal').on('click', function(){
        const projectId = $(this).data('projectid');
        const project = window.projectsData.find(p => p.project_id == projectId);
        if(project){
            $.ajax({
                url: 'getProjectMasters',
                type: 'POST',
                data: {
                    project_id: project.project_id
                },
                success: function(response){
                    if(response.success){
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: 'Project masters successfully founded.',
                            confirmButtonText: 'Ok'
                            
                        }).then(() =>{
                            // Actualizamos la variable global con los nuevos datos
                            window.mastersData = response.masters;
                            // Guardamos el valor actual del filtro
                            const currentFilter = tableMasters.search();
                            // Re-renderizamos la tabla
                            renderMastersTable(window.mastersData);
                            // Restauramos el filtro anterior
                            tableMasters.search(currentFilter).draw();
                            //Cambiar el titulo del modal
                            $('#staticshowcfsmaster').text(`Masters list ${project.project_id}`);
                            //Borrar el contenido del filtro general al cargar la pagina
                            $('#searchgeneralcfsboardmasters').val('');
                            $('#showcfsmaster').modal('show');
                        });
                    }else{
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'There was an issue getting master of the project.',
                            confirmButtonText: 'Ok'
                        });
                    }
                    
                },
                
            })
        }else{
            Swal.fire({
                title: 'Error',
                text: 'The project could not be identified.',
                icon: 'error',
                confirmButtonText: 'OK',
            });
        }
    });

    function renderMastersTable(mastersData) {
        tableMasters.clear(); // Limpia la tabla manteniendo configuración y eventos
    
        mastersData.forEach(master => {
            let totalSubprojects = 0;
            let facturadosSubprojects = 0;
            let totalPallets = 0;
            let totalPieces = 0;
    
            if (master.subprojects && Array.isArray(master.subprojects)) {
                master.subprojects.forEach(sub => {
                    totalSubprojects++;
                    if (sub.customs_release_checkbox === 'yes') {
                        facturadosSubprojects++;
                    }
                    totalPallets += sub.pallets || 0;
                    totalPieces += sub.pieces || 0;
                });
            }
            const subprojectsNotFacturados = totalSubprojects - facturadosSubprojects;
            const isFacturado = totalSubprojects > 0 && facturadosSubprojects === totalSubprojects;
            const iconHTML = isFacturado
                ? `<i class="fa-solid fa-circle-check" style="color:rgb(13, 82, 200)"></i> Yes`
                : `<i class="text-danger fa-solid fa-circle-xmark"></i> No`;
    
            const rowHTML = `
                <tr>
                    <td style="font-weight:500">${master.mbl || 'N/A'}</td>
                    <td style="font-weight:500">${iconHTML}</td>
                    <td style="font-weight:500">${master.container_number || 'N/A'}</td>
                    <td style="font-weight:500">${totalPallets}</td>
                    <td style="font-weight:500">${totalPieces}</td>
                    <td style="font-weight:500">${master.eta_port || ''}</td>
                    <td style="font-weight:500">${master.arrival_date || ''}</td>
                    <td style="font-weight:500">${master.lfd || ''}</td>
                    <td class="showcfssubprojectmodal" style="cursor: pointer;" data-mbl="${master.mbl}">
                        <ul class="list-group">
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                Totals
                                <span class="badge" style="background-color: mediumseagreen;">${totalSubprojects}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                Invoiced
                                <span class="badge" style="background-color: dodgerblue;">${facturadosSubprojects}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                Not invoiced
                                <span class="ms-2 badge text-bg-danger">${subprojectsNotFacturados}</span>
                            </li>
                        </ul>
                    </td>
                    <td style="font-weight:400; text-align: justify;">${master.notes || ''}</td>
                    <td>
                        <div class="d-flex align-items-center">
                            <div class="p-2">
                                <button type="button" style="color: white;" class="btn btn-sm btn-warning" id="addnewcfsmaster" data-url="" data-bs-toggle="modal" data-bs-target="#newcfsmaster" title="Edit Master">
                                    <i class="fa-solid fa-pen"></i>
                                </button>
                            </div>
                            <div class="p-2">
                                <button type="button" style="color: white;" class="btn btn-sm btn-danger" id="deletenewcfsmaster" data-url="" title="Delete Master">
                                    <i class="fa-solid fa-trash-can"></i>
                                </button>
                            </div>
                        </div>
                    </td>
                </tr>
            `;
    
            tableMasters.row.add($(rowHTML));
        });
    
        tableMasters.draw(); // Redibuja la tabla
    }
    
    
});



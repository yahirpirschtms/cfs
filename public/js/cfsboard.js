var selectedDrayageUser = []; // Array para almacenar los nombres de 'drayage_user'
var selectedDrayageUserUpdate = [];
var selectedDrayageFiletype = []; // Array para almacenar los nombres de 'drayage_filetype'
var selectedDrayageFiletypeUpdate = [];
var selectedCustomer = []; // Array para almacenar los nombres de 'customers'
var selectedCustomerUpdate = []; 
var selectedPartNumber = []; // Array para almacenar los nombres de 'part_number'
var selectedPartNumberUpdate = [];
var selectedInvoice = []; // Array para almacenar los nombres de 'invoice'
var selectedInvoiceUpdate = [];
var selectedCFS = []; // Array para almacenar los nombres de 'CFS'
var selectedCFSUpdate = [];
var selectedCustomRelease = []; // Array para almacenar los nombres de 'Custom release'
var selectedCustomReleaseUpdate = [];
var selectedServices = []; // Array para almacenar los nombres de 'Servicios'
var selectedServicesUpdate = [];
var table;
var tableMasters;
var tableSubprojects;
var partNumberData = [];
var invoiceData = [];
var cfsData = [];
var customReleaseData = [];
var customersData = [];
var drayageFiletypeData = [];
var drayageUserData = [];
var servicesData = [];

$(document).ready(function() {

    //Incluir el token en las peticiones
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

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

    // Verifica si la tabla de Subprojects ya ha sido inicializada antes de inicializarla
    if (!$.fn.dataTable.isDataTable('#SubprojectsTable')) {
        tableSubprojects = $('#SubprojectsTable').DataTable({
            paging: false,  // Desactiva la paginación
            searching: true, // Mantiene la búsqueda activada
            info: false,     // Oculta la información
            lengthChange: false // Desactiva el cambio de cantidad de registros
        });
    } else {
        // Si la tabla ya está inicializada, se puede actualizar la configuración
        tableSubprojects.page.len(-1).draw();  // Muestra todos los registros sin paginación
    }

    //Hacer que el filtro general sirva como un search de la tabla 
    $('#searchgeneralcfsboardsubprojects').on('input', function() {
        tableSubprojects.search(this.value).draw(); // Busca en todas las columnas
    });

    // Función para formatear fechas
    const formatDate = (dateStr, optionsType = 'short') => {
        if (!dateStr) return '';
        const date = new Date(dateStr);
        const pad = n => n.toString().padStart(2, '0');
        const monthsShort = ["Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec"];

        switch (optionsType) {
            case 'short': // mm/dd/yyyy
                return `${pad(date.getMonth()+1)}/${pad(date.getDate())}/${date.getFullYear()}`;
            case 'long': // MMM/DD
                return `${monthsShort[date.getMonth()]}/${pad(date.getDate())}`;
            case 'full': // mm/dd/yyyy hh:mm:ss
                return `${pad(date.getMonth()+1)}/${pad(date.getDate())}/${date.getFullYear()} ` +
                    `${pad(date.getHours())}:${pad(date.getMinutes())}:${pad(date.getSeconds())}`;
            default:
                return `${pad(date.getMonth()+1)}/${pad(date.getDate())}/${date.getFullYear()}`;
        }
    };

    function renderProjectsTableNew(projectsData) {
        table.clear();
    
        projectsData.forEach(project => {
    
            //const invoiceIcon = project.invoice ? 
            const invoiceIcon = (project.invoice && project.invoice_desc  === "Yes") ?
                `<i class="fa-solid fa-circle-check" style="color:rgb(13, 82, 200)"></i> Yes` : 
                `<i class="text-danger fa-solid fa-circle-xmark"></i> No`;
    
            const createList = (items, defaultValue = '&nbsp;') => {
                return (items || []).map(item => {
                    if (item === null || item === undefined || item === '') {
                        return `<li class="align-middle text-start list-group-item px-0">${defaultValue}</li>`;
                    }
                    return `<li class="align-middle text-start list-group-item px-0">${item}</li>`;
                }).join('');
            };
    
            const createMasterActions = masters => {
                return masters.map(master => `
                    <li class="align-middle text-start list-group-item px-0 py-0">
                        <div>
                            <div class="">
                                <button type="button" class="btn btn-edit-master" style="color:rgb(13, 82, 200); font-size:12px" data-mastermbl="${master.mbl}" data-projectid="${project.project_id}">
                                    <i class="fa-solid fa-pen"></i>
                                </button>
                                <button type="button" class="btn btn-sm btn-danger btn-delete-master" style="font-size:12px" data-mastermbl="${master.mbl}" data-projectid="${project.project_id}">
                                    <i class="fa-solid fa-trash-can"></i>
                                </button>
                            </div>
                        </div>
                    </li>
                `).join('');
            };
    
            const createHouseList = masters => {
                return masters.map(master => {
                    const subprojects = master.subprojects || [];
            
                    // Totales
                    const totalSubprojects = subprojects.length;

                    const totalCFS = subprojects.filter(sp => {
                        return (sp.cfs_value && sp.cfs_value.toLowerCase() !== 'no')
                    }).length;
            
                    const totalCR = subprojects.filter(sp => {
                        return (sp.cr_value && sp.cr_value.toLowerCase() !== 'no')
                    }).length;

                    // Lista de subprojects
                    const subprojectItems = subprojects.map(sp => {
                        return `
                                    ${sp.hbl} ${sp.subprojects_id}
                                `;
                    }).join('');

                    return `
                        <ul class="list-group list-group-flush list-group-horizontal-sm showcfssubprojectmodal" style="cursor: pointer;" data-projectid="${master.fk_project_id}" data-mbl="${master.mbl}">
                            <li class="align-middle list-group-item">Total ${totalSubprojects}</li>
                            <li class="align-middle list-group-item"><i class="fa-solid fa-circle-check me-1" style="color:rgb(13, 82, 200)"></i>CFS ${totalCFS}</li>
                            <li class="align-middle list-group-item"><i class="fa-solid fa-circle-check me-1" style="color:rgb(13, 82, 200)"></i>CR ${totalCR}</li>
                            <li class="align-middle list-group-item" style="display:none">${subprojectItems}</li>
                        </ul>
                    `;
                }).join('');
            };

            const drayageUser = project.drayage_user && project.drayage_user_desc || '';
            const drayageFile = project.drayage_typefile && project.drayage_file_desc || '';
    
            const rowHTML = `
            <tr>
                <td class="align-middle">${project.project_id}</td>
                <td class="align-middle">${invoiceIcon}</td>
                <td class="align-middle">${project.month}</td>
                <td class="align-middle">${drayageUser}</td>
                <td class="align-middle">${drayageFile}</td>
                <td class="align-middle">
                    <button type="button" class="btn btn-add-master" style="color:forestgreen; font-size:12px" data-projectid="${project.project_id}">
                        <i class="fa-solid fa-plus"></i>
                    </button>
                    <button type="button" class="btn btn-edit-project" style="color:rgb(13, 82, 200); font-size:12px" data-projectid="${project.project_id}">
                        <i class="fa-solid fa-pen"></i>
                    </button>
                    <button type="button" class="btn btn-delete-project" style="color:red; font-size:12px" data-projectid="${project.project_id}">
                        <i class="fa-solid fa-trash-can"></i>
                    </button>
                </td>
                <td><ul class="list-group list-group-flush">${createList(project.masters.map(m => m.mbl))}</ul></td>
                <td><ul class="list-group list-group-flush">${createList(project.masters.map(m => m.container_number))}</ul></td>
                <td><ul class="list-group list-group-flush">${createList(project.masters.map(m => m.total_pieces), '0')}</ul></td>
                <td><ul class="list-group list-group-flush">${createList(project.masters.map(m => m.total_pallets), '0')}</ul></td>
                <td><ul class="list-group list-group-flush">${createList(project.masters.map(m => formatDate(m.eta_port, 'short')))}</ul></td>
                <td><ul class="list-group list-group-flush">${createList(project.masters.map(m => formatDate(m.arrival_date, 'short')))}</ul></td>
                <td><ul class="list-group list-group-flush">${createList(project.masters.map(m => formatDate(m.lfd, 'short')))}</ul></td>
                <td>${createHouseList(project.masters)}</td>
                <td><ul class="list-group list-group-flush">${createMasterActions(project.masters)}</ul></td>
            `;
    
            table.row.add($(rowHTML));
        });
        table.draw();
    }
    
    //Hacer conteo de masters y subprojects(facturados y no)
    if (window.projectsData) {
        const projectsData = window.projectsData;
        renderProjectsTableNew(projectsData);
        console.log(projectsData); // Muestra los proyectos con sus nuevas estadísticas
        
    } else {
        console.error('No se encontró la variable projectsData');
    }

    //Variables para los catalogos
    var isCatalogsLoaded = false; // Bandera para evitar la carga repetida
    var newlySelectedDrayageUser = null;
    var newlySelectedDrayageFileType = null;
    var newlySelectedPartNumber = null;
    var newlySelectedCustomer = null;
    var newlySelectedCFS = null;
    var newlySelectedCustomRelease = null;
    var newlySelectedInvoice = null;

    //Funcion que carga todos los catalogos al cargar la pagina
    function loadGeneralSelects() {
        if (isCatalogsLoaded) return; // Evita cargar dos veces
        
        $.ajax({
            url: 'getLoadSelects',
            type: 'GET',
            dataType: 'json',
            success: function (data) {
                // Procesar los datos de 'drayage_user' y 'drayage_filetype y customers'
                drayageUserData = data.drayage_user.map(item => ({
                    id: item.gnct_id,
                    text: item.gntc_value
                }));
                drayageFiletypeData = data.drayage_filetype.map(item => ({
                    id: item.gnct_id,
                    text: item.gntc_value
                }));
                customersData = data.customers.map(item => ({
                    id: item.pk_customer,
                    text: item.description
                }));
                partNumberData = data.part_number.map(item => ({
                    id: item.pk_part_number,
                    text: item.description
                }));
                invoiceData = data.invoice.map(item => ({
                    id: item.gnct_id,
                    text: item.gntc_value
                }));
                cfsData = data.cfs.map(item => ({
                    id: item.gnct_id,
                    text: item.gntc_value
                }));
                customReleaseData = data.custom_release.map(item => ({
                    id: item.gnct_id,
                    text: item.gntc_value
                }));
                servicesData = data.services.map(item => ({
                    id: item.pk_service,
                    text: item.description,
                    cost: item.cost
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

                // Asegurarse de que no haya duplicados en 'customers'
                data.customers.forEach(function (custom) {
                    if (!selectedCustomer.includes(custom.description)) {
                        selectedCustomer.push(custom.description); // Agregar al arreglo si no está ya
                    }
                });

                // Asegurarse de que no haya duplicados en 'partNumbers'
                data.part_number.forEach(function (part) {
                    if (!selectedPartNumber.includes(part.description)) {
                        selectedPartNumber.push(part.description); // Agregar al arreglo si no está ya
                    }
                });
                // Asegurarse de que no haya duplicados en 'Invoice'
                data.invoice.forEach(function (invo) {
                    if (!selectedInvoice.includes(invo.gntc_value)) {
                        selectedInvoice.push(invo.gntc_value); // Agregar al arreglo si no está ya
                    }
                });

                // Asegurarse de que no haya duplicados en 'CFS'
                data.cfs.forEach(function (cf) {
                    if (!selectedCFS.includes(cf.gntc_value)) {
                        selectedCFS.push(cf.gntc_value); // Agregar al arreglo si no está ya
                    }
                });

                // Asegurarse de que no haya duplicados en 'Custom Release'
                data.custom_release.forEach(function (release) {
                    if (!selectedCustomRelease.includes(release.gntc_value)) {
                        selectedCustomRelease.push(release.gntc_value); // Agregar al arreglo si no está ya
                    }
                });

                // Asegurarse de que no haya duplicados en 'Services'
                data.services.forEach(function (service) {
                    if (!selectedServices.includes(service.description)) {
                        selectedServices.push(service.description); // Agregar al arreglo si no está ya
                    }
                });

                // Copiar los datos únicos a nuevas variables si se necesitan para otro propósito
                selectedDrayageUserUpdate = [...selectedDrayageUser];
                selectedDrayageFiletypeUpdate = [...selectedDrayageFiletype];
                selectedCustomerUpdate = [...selectedCustomer];
                selectedPartNumberUpdate = [...selectedPartNumber];
                selectedInvoiceUpdate = [...selectedInvoice];
                selectedCFSUpdate = [...selectedCFS];
                selectedCustomReleaseUpdate = [...selectedCustomRelease];
                selectedServicesUpdate = [...selectedServices];

                console.log("Drayage Users cargados:", selectedDrayageUser);
                console.log("Drayage Filetypes cargados:", selectedDrayageFiletype);
                console.log("Drayage Users cargados en update:", selectedDrayageUser);
                console.log("Drayage Filetypes cargados en update:", selectedDrayageFiletype);
                console.log("Customers cargados:", selectedCustomer);
                console.log("Customers cargados en update:", selectedCustomerUpdate);
                console.log("Part Numbers cargados:", selectedPartNumber);
                console.log("Part Numbers cargados en update:", selectedPartNumberUpdate);
                console.log("Invoice cargados:", selectedInvoice);
                console.log("Invoice cargados en update:", selectedInvoiceUpdate);
                console.log("CFS cargados:", selectedCFS);
                console.log("CFS cargados en update:", selectedCFSUpdate);
                console.log("Custom Release cargados:", selectedCustomRelease);
                console.log("Custom Release cargados en update:", selectedCustomReleaseUpdate);
                console.log("Services cargados:", selectedServices);
                console.log("Services cargados en update:", selectedServicesUpdate);

                // Inicializar Select2 para 'inputnewcfspeojectdrayageperson'
                $('#inputnewcfspeojectdrayageperson').select2({
                    placeholder: 'Select a Drayage User',
                    allowClear: true,
                    tags: true,
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

                // Inicializar Select2 para 'inputnewsubprojectcfscustomer'
                $('#inputnewsubprojectcfscustomer').select2({
                    placeholder: 'Select a Customer',
                    allowClear: true,
                    tags: true,
                    data: customersData, // Los datos cargados desde el backend
                    dropdownParent: $('#newcfssubproject'),
                    minimumInputLength: 0
                });

                // Inicializar Select2 para 'inputnewsubprojectcfspartnumber'
                $('#inputnewsubprojectcfspartnumber').select2({
                    placeholder: 'Select a Part Number',
                    allowClear: true,
                    tags: true,
                    data: partNumberData, // Los datos cargados desde el backend
                    dropdownParent: $('#newcfssubproject'),
                    minimumInputLength: 0
                });

                // Inicializar Select2 para 'inputnewsubprojectcfscfscomment'
                $('#inputnewsubprojectcfscfscomment').select2({
                    placeholder: 'Select an option',
                    allowClear: true,
                    tags: false,
                    data: cfsData, // Los datos cargados desde el backend
                    dropdownParent: $('#newcfssubproject'),
                    minimumInputLength: 0,
                });

                // Inicializar Select2 para 'inputnewsubprojectcfscustomsreleasecomment'
                $('#inputnewsubprojectcfscustomsreleasecomment').select2({
                    placeholder: 'Select an option',
                    allowClear: true,
                    tags: true,
                    data: customReleaseData, // Los datos cargados desde el backend
                    dropdownParent: $('#newcfssubproject'),
                    minimumInputLength: 0,
                });

                // Inicializar Select2 para 'inputnewsubprojectcfscustomsreleasecomment'
                $('#inputnewsubprojectcfsworkspalletized').select2({
                    placeholder: 'Select a Work',
                    allowClear: true,
                    tags: false,
                    data: [
                        { id: 'Yes', text: 'Yes' },
                        { id: 'No', text: 'No' },
                    ],
                    dropdownParent: $('#newcfssubproject'),
                    minimumInputLength: 0,
                });

                // Inicializar Select2 para 'inputnewsubprojectcfscustomsreleasecomment'
                $('#inputnewsubprojectcfspalletsexchanged').select2({
                    placeholder: 'Select an option',
                    allowClear: true,
                    tags: false,
                    data: [
                        { id: 'Yes', text: 'Yes' },
                        { id: 'No', text: 'No' },
                    ],
                    dropdownParent: $('#newcfssubproject'),
                    minimumInputLength: 0,
                });

                // Inicializar Select2 para 'inputnewcfsprojectinvoice'
                $('#inputnewcfsprojectinvoice').select2({
                    placeholder: 'Select an option',
                    allowClear: true,
                    tags: false,
                    data: invoiceData,
                    dropdownParent: $('#neweditcfsproject'),
                    minimumInputLength: 0,
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

    //Funciones que permiten añadir nuevos registros por medio del select2 del Part Number
    $('#inputnewsubprojectcfspartnumber').on('change', function () {
        var selectedOption = $(this).select2('data')[0]; // Obtener la opción seleccionada
        var selectedText = selectedOption ? selectedOption.text : ''; // Obtener el texto (nombre) de la opción seleccionada

        // Si no es el nuevo partnumber, lo procesamos
        if (selectedText  !== newlySelectedPartNumber &&  selectedText.trim() !== '') {
            console.log(selectedText);

            if (!selectedPartNumber.includes(selectedText) || !selectedPartNumberUpdate.includes(selectedText)) {
                if(!selectedPartNumber.includes(selectedText)){
                    selectedPartNumber.push(selectedText);  // Agregar al arreglo solo si no existe
                    console.log(selectedPartNumber);  // Mostrar el arreglo con todos los drivers seleccionados
                }
                if(!selectedPartNumberUpdate.includes(selectedText)){
                    selectedPartNumberUpdate.push(selectedText);  // Agregar al arreglo solo si no existe
                    console.log(selectedPartNumberUpdate);  // Mostrar el arreglo con todos los drivers seleccionados
                }
                savePartNumber(selectedText);
            }
        }
    });

    function savePartNumber(newPartNumber) {
        $.ajax({
            url: 'saveNewPartNumber',
            type: 'POST',
            data: {
                newPartNumber: newPartNumber
            },
            success: function (response) {
                console.log(response);

                // Crear una nueva opción para cada select2
                var newOption1 = new Option(response.newPartNumberCreated.description, response.newPartNumberCreated.pk_part_number, true, true);
                var newOption2 = new Option(response.newPartNumberCreated.description, response.newPartNumberCreated.pk_part_number, true, true);

                // Agregar la opción a ambos select2 sin eliminarla del otro
                $('#inputnewsubprojectcfspartnumber').append(newOption1).trigger('change');
                //$('#inputnewsubprojectcfspartnumberUpdate').append(newOption2).trigger('change');
                
                // Seleccionar automáticamente el Part Number
                $('#inputnewsubprojectcfspartnumber').val(response.newPartNumberCreated.pk_part_number).trigger('change');

                // Marcar el nuevo ID para evitar que se haga otra solicitud
                newlySelectedPartNumber = response.newPartNumberCreated.description;

                // Cuando el nuevo Drayage User sea creado, aseguramos que no se haga más AJAX para este User
                $('#inputnewsubprojectcfspartnumber').on('select2:select', function (e) {
                    var selectedId = e.params.data.id;
                    if (selectedId === newlySelectedPartNumber) {
                        newlySelectedPartNumber = null;  
                    }
                });
                //loadCarriersFilterCheckbox();
            },
            error: function (xhr, status, error) {
                if (xhr.status === 409) {
                    alert('Part Number already exists.');
                } else {
                    console.error('An error has occurred saving Part Number', error);
                }
            }
        });
    }

    //Funciones que permiten añadir nuevos registros por medio del select2 del Customer
    $('#inputnewsubprojectcfscustomer').on('change', function () {
        var selectedOption = $(this).select2('data')[0]; // Obtener la opción seleccionada
        var selectedText = selectedOption ? selectedOption.text : ''; // Obtener el texto (nombre) de la opción seleccionada

        // Si no es el nuevo customer, lo procesamos
        if (selectedText  !== newlySelectedCustomer &&  selectedText.trim() !== '') {
            console.log(selectedText);

            if (!selectedCustomer.includes(selectedText) || !selectedCustomerUpdate.includes(selectedText)) {
                if(!selectedCustomer.includes(selectedText)){
                    selectedCustomer.push(selectedText);  // Agregar al arreglo solo si no existe
                    console.log(selectedCustomer);  // Mostrar el arreglo con todos los customers seleccionados
                }
                if(!selectedCustomerUpdate.includes(selectedText)){
                    selectedCustomerUpdate.push(selectedText);  // Agregar al arreglo solo si no existe
                    console.log(selectedCustomerUpdate);  // Mostrar el arreglo con todos los customers seleccionados
                }
                saveCustomer(selectedText);
            }
        }
    });

    function saveCustomer(newCustomer) {
        $.ajax({
            url: 'saveNewCustomer',
            type: 'POST',
            data: {
                newCustomer: newCustomer
            },
            success: function (response) {
                console.log(response);

                // Crear una nueva opción para cada select2
                var newOption1 = new Option(response.newCustomerCreated.description, response.newCustomerCreated.pk_customer, true, true);
                var newOption2 = new Option(response.newCustomerCreated.description, response.newCustomerCreated.pk_customer, true, true);

                // Agregar la opción a ambos select2 sin eliminarla del otro
                $('#inputnewsubprojectcfscustomer').append(newOption1).trigger('change');
                //$('#inputnewsubprojectcfscustomerUpdate').append(newOption2).trigger('change');
                
                // Seleccionar automáticamente el Customer
                $('#inputnewsubprojectcfscustomer').val(response.newCustomerCreated.pk_customer).trigger('change');

                // Marcar el nuevo ID para evitar que se haga otra solicitud
                newlySelectedCustomer = response.newCustomerCreated.description;

                // Cuando el nuevo Customer sea creado, aseguramos que no se haga más AJAX para este User
                $('#inputnewsubprojectcfscustomer').on('select2:select', function (e) {
                    var selectedId = e.params.data.id;
                    if (selectedId === newlySelectedCustomer) {
                        newlySelectedCustomer = null;  
                    }
                });
                //loadCarriersFilterCheckbox();
            },
            error: function (xhr, status, error) {
                if (xhr.status === 409) {
                    alert('Customer already exists.');
                } else {
                    console.error('An error has occurred saving Customer', error);
                }
            }
        });
    }
    
    //Funciones que permiten añadir nuevos registros por medio del select2 del CFS
    $('#inputnewsubprojectcfscfscomment').on('change', function () {
        var selectedOption = $(this).select2('data')[0]; // Obtener la opción seleccionada
        var selectedText = selectedOption ? selectedOption.text : ''; // Obtener el texto (nombre) de la opción seleccionada

        // Si no es el nuevo customer, lo procesamos
        if (selectedText  !== newlySelectedCFS &&  selectedText.trim() !== '') {
            console.log(selectedText);

            if (!selectedCFS.includes(selectedText) || !selectedCFSUpdate.includes(selectedText)) {
                if(!selectedCFS.includes(selectedText)){
                    selectedCFS.push(selectedText);  // Agregar al arreglo solo si no existe
                    console.log(selectedCFS);  // Mostrar el arreglo con todos los CFS seleccionados
                }
                if(!selectedCFSUpdate.includes(selectedText)){
                    selectedCFSUpdate.push(selectedText);  // Agregar al arreglo solo si no existe
                    console.log(selectedCFSUpdate);  // Mostrar el arreglo con todos los CFS seleccionados
                }
                saveCFS(selectedText);
            }
        }
    });

    function saveCFS(newCFS) {
        $.ajax({
            url: 'saveNewCFS',
            type: 'POST',
            data: {
                newCFS: newCFS
            },
            success: function (response) {
                console.log(response);

                // Crear una nueva opción para cada select2
                var newOption1 = new Option(response.newCFSCreated.gntc_value, response.newCFSCreated.gnct_id, true, true);
                var newOption2 = new Option(response.newCFSCreated.gntc_value, response.newCFSCreated.gnct_id, true, true);

                // Agregar la opción a ambos select2 sin eliminarla del otro
                $('#inputnewsubprojectcfscfscomment').append(newOption1).trigger('change');
                //$('#inputnewsubprojectcfscfscommentUpdate').append(newOption2).trigger('change');
                
                // Seleccionar automáticamente el CFS
                $('#inputnewsubprojectcfscfscomment').val(response.newCFSCreated.gnct_id).trigger('change');

                // Marcar el nuevo ID para evitar que se haga otra solicitud
                newlySelectedCFS = response.newCFSCreated.gntc_value;

                // Cuando el nuevo Customer sea creado, aseguramos que no se haga más AJAX para este User
                $('#inputnewsubprojectcfscfscomment').on('select2:select', function (e) {
                    var selectedId = e.params.data.id;
                    if (selectedId === newlySelectedCFS) {
                        newlySelectedCFS = null;  
                    }
                });
                //loadCarriersFilterCheckbox();
            },
            error: function (xhr, status, error) {
                if (xhr.status === 409) {
                    alert('CFS already exists.');
                } else {
                    console.error('An error has occurred saving CFS option', error);
                }
            }
        });
    }

    //Funciones que permiten añadir nuevos registros por medio del select2 del Custom Release
    $('#inputnewsubprojectcfscustomsreleasecomment').on('change', function () {
        var selectedOption = $(this).select2('data')[0]; // Obtener la opción seleccionada
        var selectedText = selectedOption ? selectedOption.text : ''; // Obtener el texto (nombre) de la opción seleccionada

        // Si no es el nuevo Custom Release, lo procesamos
        if (selectedText  !== newlySelectedCustomRelease &&  selectedText.trim() !== '') {
            console.log(selectedText);

            if (!selectedCustomRelease.includes(selectedText) || !selectedCustomReleaseUpdate.includes(selectedText)) {
                if(!selectedCustomRelease.includes(selectedText)){
                    selectedCustomRelease.push(selectedText);  // Agregar al arreglo solo si no existe
                    console.log(selectedCustomRelease);  // Mostrar el arreglo con todos los Custom Release seleccionados
                }
                if(!selectedCustomReleaseUpdate.includes(selectedText)){
                    selectedCustomReleaseUpdate.push(selectedText);  // Agregar al arreglo solo si no existe
                    console.log(selectedCustomReleaseUpdate);  // Mostrar el arreglo con todos los Custom Release seleccionados
                }
                saveCustomRelease(selectedText);
            }
        }
    });

    function saveCustomRelease(newCustomRelease) {
        $.ajax({
            url: 'saveNewCustomRelease',
            type: 'POST',
            data: {
                newCustomRelease: newCustomRelease
            },
            success: function (response) {
                console.log(response);

                // Crear una nueva opción para cada select2
                var newOption1 = new Option(response.newCustomReleaseCreated.gntc_value, response.newCustomReleaseCreated.gnct_id, true, true);
                var newOption2 = new Option(response.newCustomReleaseCreated.gntc_value, response.newCustomReleaseCreated.gnct_id, true, true);

                // Agregar la opción a ambos select2 sin eliminarla del otro
                $('#inputnewsubprojectcfscustomsreleasecomment').append(newOption1).trigger('change');
                //$('#inputnewsubprojectcfscustomsreleasecommentUpdate').append(newOption2).trigger('change');
                
                // Seleccionar automáticamente el CustomRelease
                $('#inputnewsubprojectcfscustomsreleasecomment').val(response.newCustomReleaseCreated.gnct_id).trigger('change');

                // Marcar el nuevo ID para evitar que se haga otra solicitud
                newlySelectedCustomRelease = response.newCustomReleaseCreated.gntc_value;

                // Cuando el nuevo Customer sea creado, aseguramos que no se haga más AJAX para este Custom Release
                $('#inputnewsubprojectcfscustomsreleasecomment').on('select2:select', function (e) {
                    var selectedId = e.params.data.id;
                    if (selectedId === newlySelectedCustomRelease) {
                        newlySelectedCustomRelease = null;  
                    }
                });
                //loadCarriersFilterCheckbox();
            },
            error: function (xhr, status, error) {
                if (xhr.status === 409) {
                    alert('Custom Release already exists.');
                } else {
                    console.error('An error has occurred saving Custom Release', error);
                }
            }
        });
    }

    //Funciones que permiten añadir nuevos registros por medio del select2 del Invoice
    $('#inputnewcfsprojectinvoice').on('change', function () {
        var selectedOption = $(this).select2('data')[0]; // Obtener la opción seleccionada
        var selectedText = selectedOption ? selectedOption.text : ''; // Obtener el texto (nombre) de la opción seleccionada

        // Si no es el nuevo Invoice, lo procesamos
        if (selectedText  !== newlySelectedInvoice &&  selectedText.trim() !== '') {
            console.log(selectedText);

            if (!selectedInvoice.includes(selectedText) || !selectedInvoiceUpdate.includes(selectedText)) {
                if(!selectedInvoice.includes(selectedText)){
                    selectedInvoice.push(selectedText);  // Agregar al arreglo solo si no existe
                    console.log(selectedInvoice);  // Mostrar el arreglo con todos los Invoice seleccionados
                }
                if(!selectedInvoiceUpdate.includes(selectedText)){
                    selectedInvoiceUpdate.push(selectedText);  // Agregar al arreglo solo si no existe
                    console.log(selectedInvoiceUpdate);  // Mostrar el arreglo con todos los Invoice seleccionados
                }
                saveInvoice(selectedText);
            }
        }
    });

    function saveInvoice(newInvoice) {
        $.ajax({
            url: 'saveNewInvoice',
            type: 'POST',
            data: {
                newInvoice: newInvoice
            },
            success: function (response) {
                console.log(response);

                // Crear una nueva opción para cada select2
                var newOption1 = new Option(response.newInvoiceCreated.gntc_value, response.newInvoiceCreated.gnct_id, true, true);
                var newOption2 = new Option(response.newInvoiceCreated.gntc_value, response.newInvoiceCreated.gnct_id, true, true);

                // Agregar la opción a ambos select2 sin eliminarla del otro
                $('#inputnewcfsprojectinvoice').append(newOption1).trigger('change');
                //$('#inputnewcfsprojectinvoiceUpdate').append(newOption2).trigger('change');
                
                // Seleccionar automáticamente el Invoice
                $('#inputnewcfsprojectinvoice').val(response.newInvoiceCreated.gnct_id).trigger('change');

                // Marcar el nuevo ID para evitar que se haga otra solicitud
                newlySelectedInvoice = response.newInvoiceCreated.gntc_value;

                // Cuando el nuevo Invoice sea creado, aseguramos que no se haga más AJAX para este Custom Release
                $('#inputnewcfsprojectinvoice').on('select2:select', function (e) {
                    var selectedId = e.params.data.id;
                    if (selectedId === newlySelectedInvoice) {
                        newlySelectedInvoice = null;  
                    }
                });
                //loadCarriersFilterCheckbox();
            },
            error: function (xhr, status, error) {
                if (xhr.status === 409) {
                    alert('Invoice already exists.');
                } else {
                    console.error('An error has occurred saving invoice', error);
                }
            }
        });
    }

    //Funciones para la validacion de inputs asi como guardar un nuveo proyecto
    const formFields = [
        'inputnewcfsprojectprojectid',
        'inputnewcfsprojectmonth',
        'inputnewcfsprojectinvoice',
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
        const isSelect2 = field.hasClass("searchDrayageUser") || field.hasClass("searchDrayageFileType") || field.hasClass("searchInvoice");
    
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
    
    //Ejecutar abrir modal para agregar projects
    $('body').on('click', '#openmodalnewcfsproject', function (e) {
        // Seleccionar automáticamente "No" si existe
        const noOptionInvoice = invoiceData.find(opt => opt.text.trim().toLowerCase() === 'no');
        if (noOptionInvoice) {
            $('#inputnewcfsprojectinvoice').val(noOptionInvoice.id).trigger('change');
        }
        $('#neweditcfsproject').modal('show');
    });

    $('body').on('click', '#saveeditnewcfsproject', function (e) {
        e.preventDefault();
        let valid = true;
    
        formFields.forEach(fieldId => {
            const field = $("#" + fieldId);
            const errorElement = $('#error-' + fieldId);
            const isSelect2 = field.hasClass("searchDrayageUser") || field.hasClass("searchDrayageFileType") || field.hasClass("searchInvoice");
    
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
        Swal.fire({
            title: 'Saving project...',
            text: 'Please wait while we save the project.',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        $.ajax({
            url: 'saveNewProject',
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function (response){
                Swal.close();
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
                    renderProjectsTableNew(window.projectsData);
                    // Restauramos el filtro anterior
                    table.search(currentFilter).draw();
                    $('#neweditcfsproject').modal('hide');
                });
            },
            error: function (xhr, status, error) {
                Swal.close();
                // Limpia los errores anteriores
                $('input, select').removeClass('is-invalid');
                $('.invalid-feedback').text(''); // Vaciar mensajes de error
                $('.select2-selection').removeClass('is-invalid'); // También eliminar la clase del contenedor de select2
    
                let errors = xhr.responseJSON.errors;
                let generalMessage = xhr.responseJSON?.message || 'There was a problem adding the project. Please try again.';
    
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
                    text: generalMessage,
                    confirmButtonText: 'Ok'
                });
            }
        })
    });
    
    //Funcion para resetear el formulario al cerrarlo de projects
    function resetModalNewCFSProjectFields(modalSelector) {
        // Limpiar selects con select2 (Drayage User y File Type)
        $(modalSelector).find('.searchDrayageUser, .searchDrayageFileType, .searchInvoice').each(function () {
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
                    $('#inputnewcfspeojectdrayageperson').val(project.drayage_user).trigger('change');
                    resolve();
                } else {
                    $('#inputnewcfspeojectdrayageperson').val(null).trigger('change');
                    reject(); // Resolver inmediatamente si no hay person
                }
            });

            let drayageFileTypePromise = new Promise ((resolve, reject) => {
                if(project.drayage_typefile){
                    $('#inputnewcfsprojectdrayagefiletype').val(project.drayage_typefile).trigger('change');
                    resolve();
                } else {
                    $('#inputnewcfsprojectdrayagefiletype').val(null).trigger('change');
                    reject(); // Resolver inmediatamente si no hay file type
                }
            });

            let invoicePromise = new Promise ((resolve, reject) => {
                if(project.invoice){
                    $('#inputnewcfsprojectinvoice').val(project.invoice).trigger('change');

                    
                    resolve();
                } else {
                    $('#inputnewcfsprojectinvoice').val(null).trigger('change');
                    reject(); // Resolver inmediatamente si no hay invoice
                }
            });

            Promise.all([drayageUserPromise, drayageFileTypePromise, invoicePromise])
                .then(() => {
                    // Cambiar el título del modal y el texto del botón
                    $('#staticnewcfsproject').text(`Edit Project  ${project.project_id}`).attr('id', 'staticnewcfsproject');
                    $('#saveeditnewcfsproject').text('Save Changes').attr('id', 'editnewcfsproject');
                
                    // Llenar los inputs con los datos del proyecto
                    $('#inputnewcfsprojectprojectid').val(project.project_id);
                    $('#inputnewcfsprojectprojectidoriginal').val(project.project_id);
                    //.prop('readonly', true); // Deshabilitar el input

                    $('#inputnewcfsprojectmonth').val(project.month_full);

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
            const isSelect2 = field.hasClass("searchDrayageUser") || field.hasClass("searchDrayageFileType") || field.hasClass("searchInvoice");
    
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
        Swal.fire({
            title: 'Updating project...',
            text: 'Please wait while the project is being updated.',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        $.ajax({
            url: 'editNewProject',
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function (response){
                Swal.close();
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
                    renderProjectsTableNew(window.projectsData);
                    // Restauramos el filtro anterior
                    table.search(currentFilter).draw();
                    $('#neweditcfsproject').modal('hide');
                });
            },
            error: function (xhr, status, error) {
                Swal.close();
                // Limpia los errores anteriores
                $('input, select').removeClass('is-invalid');
                $('.invalid-feedback').text(''); // Vaciar mensajes de error
                $('.select2-selection').removeClass('is-invalid'); // También eliminar la clase del contenedor de select2
    
                let errors = xhr.responseJSON.errors;
                let generalMessage = xhr.responseJSON?.message || 'There was a problem updating the project. Please try again.';
    
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
                    text: generalMessage,
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
                        Swal.fire({
                        title: 'Deleting project...',
                        text: 'Please wait while we delete the project.',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });
                
                    $.ajax({
                        url: 'deleteProject',
                        type: 'POST',
                        data: {
                            project_id: project.project_id
                        },
                        success: function(response){
                            if(response.success){
                                Swal.close();
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
                                    renderProjectsTableNew(window.projectsData);
                                    // Restauramos el filtro anterior
                                    table.search(currentFilter).draw();
                                });
                            }else{
                                Swal.close();
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: response.message || 'There was an issue deleting the project.',
                                    confirmButtonText: 'Ok'
                                });
                            }
                            
                        },
                        error: function(xhr){
                            Swal.close();
                            let msg = 'Unexpected error';
                            if(xhr.responseJSON && xhr.responseJSON.message){
                                msg = xhr.responseJSON.message;
                            }
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: msg,
                                confirmButtonText: 'Ok'
                            });
                        }
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

    //Ejecutar el guardado de un nuevo master
    //Funciones para validacion del formulario del master
    $('#projectsTable').on('click', '.btn-add-master', function () {
        const projectId = $(this).data('projectid');
        const project = window.projectsData.find(p => p.project_id == projectId);

        if(project){
            $('#staticnewcfsmaster').text(`Add Master ${project.project_id}`);
            $('#inputnewmastercfsproyectid').val(project.project_id);
            $('#newcfsmaster').modal('show');
        }else{
            Swal.fire({
                title: 'Error',
                text: 'The project could not be identified.',
                icon: 'error',
                confirmButtonText: 'OK',
            });
        }
    });
    
    const formFieldsMaster = [
        //'inputnewmastercfsproyectid',
        'inputnewmastercfsmbl',
        'inputnewmastercfscontainernumber',
        'inputnewmastercfsetaport',
        'inputnewmastercfsarrivaldate',
        'inputnewmastercfslfd',
        //'inputnewmastercfsnotes',
    ];

    function validateFieldMaster(field, errorElement){
        const fieldId = field.attr('id');
        const fieldValue = field.val().trim();

        if(fieldValue === ""){
            let customErrorMessage = 'This field is required.';
            if(fieldId === "inputnewmastercfsmbl") {
                customErrorMessage = 'MBL is required.';
            }else if(fieldId === 'inputnewmastercfscontainernumber'){
                customErrorMessage = 'Container Number is required.';
            }else if(fieldId === 'inputnewmastercfsetaport'){
                customErrorMessage = 'ETA Port is required.';
            }else if(fieldId === 'inputnewmastercfsarrivaldate'){
                customErrorMessage = 'Arrival Date is required.';
            }else if(fieldId === 'inputnewmastercfslfd'){
                customErrorMessage = 'LFD is required.';
            }else if(fieldId === 'inputnewmastercfsnotes'){
                customErrorMessage = 'Notes is required.';
            }
            field.addClass('is-invalid');
            errorElement.text(customErrorMessage);
        }else{
            field.removeClass('is-invalid');
            errorElement.text('');
        }
    }

    formFieldsMaster.forEach(fieldId => {
        const field = $("#" + fieldId);
        const errorElement = $('#error-' + fieldId);

        if(field) {
            field.on('keyup blur', function () {
                validateFieldMaster(field, errorElement);
            });
        }
    })

    //Funcion Guardar nuevo master
    $('body').on('click', '#savecfsmaster', function (e) {
        e.preventDefault();
        let valid = true;
    
        formFieldsMaster.forEach(fieldId => {
            const field = $("#" + fieldId);
            const errorElement = $('#error-' + fieldId);
            
    
            if (field) {
                const fieldValue = field.val().trim();
                if (fieldValue === '') {
                    valid = false;
                    validateFieldMaster(field, errorElement); // Validación para campos normales
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

        let formData = new FormData($('#createMastercfs')[0]);

        Swal.fire({
            title: 'Saving master...',
            text: 'Please wait while we save the master.',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        $.ajax({
            url: 'saveNewMaster',
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function (response){
                Swal.close();
                Swal.fire({
                    icon: 'success',
                    title: '¡Success!',
                    text: 'Master successfully added.',
                    confirmButtonText: 'Ok'
                }).then(() =>{
                    // Actualizamos la variable global con los nuevos datos
                    window.projectsData = response.projects;
                    //window.mastersData = response.masters;
                    // Guardamos el valor actual del filtro
                    const currentFilterproject = table.search();
                    // Re-renderizamos la tabla
                    renderProjectsTableNew(window.projectsData);
                    // Restauramos el filtro anterior
                    table.search(currentFilterproject).draw();
                    $('#newcfsmaster').modal('hide');
                });
            },
            error: function (xhr, status, error) {
                Swal.close();
                // Limpia los errores anteriores
                $('input, select').removeClass('is-invalid');
                $('.invalid-feedback').text(''); // Vaciar mensajes de error
                $('.select2-selection').removeClass('is-invalid'); // También eliminar la clase del contenedor de select2
    
                let errors = xhr.responseJSON.errors;
                let generalMessage = xhr.responseJSON?.message || 'There was a problem adding the project. Please try again.';
    
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
                    text: generalMessage,
                    confirmButtonText: 'Ok'
                });
            }
        })
    });

    //Abrir modal de edicion de projects
    $(document).on('click', '.btn-edit-master', function () {
        const projectid = $(this).data('projectid');
        const mastermbl = $(this).data('mastermbl');

        // Buscar el proyecto primero
        const project = window.projectsData.find(p => p.project_id == projectid);
        // Luego, buscar el master dentro de ese proyecto
        const master = project?.masters?.find(m => m.mbl == mastermbl);

        if(master){
                    // Cambiar el título del modal y el texto del botón
                    $('#staticnewcfsmaster').text(`Edit Master  ${master.mbl}`);
                    $('#savecfsmaster').text('Save Changes').attr('id', 'editnewcfsmaster');
                
                    // Llenar los inputs con los datos del proyecto
                    $('#inputnewmastercfsmbl').val(master.mbl);
                    $('#inputnewmastercfsmbloriginal').val(master.mbl);
                    //.prop('readonly', true); // Deshabilitar el input

                    $('#inputnewmastercfsproyectid').val(master.fk_project_id);
                    $('#inputnewmastercfscontainernumber').val(master.container_number);
                    $('#inputnewmastercfsetaport').val(formatDate(master.eta_port, 'full'));
                    $('#inputnewmastercfsarrivaldate').val(formatDate(master.arrival_date, 'full'));
                    $('#inputnewmastercfslfd').val(formatDate(master.lfd, 'full'));
                    //$('#inputnewmastercfsnotes').val(master.notes);

                    //$('#showcfsmaster').modal('hide');
                    $('#newcfsmaster').modal('show');
        }else{
            Swal.fire({
                title: 'Error',
                text: 'The master could not be identified.',
                icon: 'error',
                confirmButtonText: 'OK',
            });
        }
    });

    // Llamar a la función para limpiar inputs cuando se cierre el modal de los Masters
    $('#newcfsmaster').on('hidden.bs.modal', function() {
        resetModalNewCFSMasterFields('#newcfsmaster');
    });

    //Funcion para resetear el formulario al cerrarlo de masters
    function resetModalNewCFSMasterFields(modalSelector) {
        // Limpiar errores
        $(modalSelector).find('.is-invalid').removeClass('is-invalid');
        $(modalSelector).find('.invalid-feedback').text('');

        // Limpiar inputs y textarea
        $(modalSelector).find('input, textarea').val('');

        $('#editnewcfsmaster').text('Save').attr('id', 'savecfsmaster');
        $('#staticnewcfsmaster').text('New Master');
        $('#inputnewmastercfsmbl')
        .prop('readonly', false); 
    }

    //Ejecutar el guardado de cambios de masters
    $('body').on('click', '#editnewcfsmaster', function (e) {
        e.preventDefault();
        let valid = true;
    
        formFieldsMaster.forEach(fieldId => {
            const field = $("#" + fieldId);
            const errorElement = $('#error-' + fieldId);
            
    
            if (field) {
                const fieldValue = field.val().trim();
                if (fieldValue === '') {
                    valid = false;
                    validateFieldMaster(field, errorElement); // Validación para campos normales
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

        let formData = new FormData($('#createMastercfs')[0]);

        Swal.fire({
            title: 'Updating master...',
            text: 'Please wait while the master is being updated.',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        $.ajax({
            url: 'editNewMaster',
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function (response){
                Swal.close();
                Swal.fire({
                    icon: 'success',
                    title: '¡Success!',
                    text: 'Master updated successfully.',
                    confirmButtonText: 'Ok'
                }).then(() =>{
                    // Actualizamos la variable global con los nuevos datos
                    window.projectsData = response.projects;
                    //window.mastersData = response.masters;
                    // Guardamos el valor actual del filtro
                    const currentFilterproject = table.search();
                    // Re-renderizamos la tabla
                    renderProjectsTableNew(window.projectsData);
                    // Restauramos el filtro anterior
                    table.search(currentFilterproject).draw();
                    $('#newcfsmaster').modal('hide');
                });
            },
            error: function (xhr, status, error) {
                Swal.close();
                // Limpia los errores anteriores
                $('input, select').removeClass('is-invalid');
                $('.invalid-feedback').text(''); // Vaciar mensajes de error
                $('.select2-selection').removeClass('is-invalid'); // También eliminar la clase del contenedor de select2
    
                let errors = xhr.responseJSON.errors;
                let generalMessage = xhr.responseJSON?.message || 'There was a problem updating the Master. Please try again.';
    
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
                    text: generalMessage,
                    confirmButtonText: 'Ok'
                });
            }
        })
    });

    //Ejecutar el borrado de algun master
    $(document).on('click', '.btn-delete-master', function () {
        const projectid = $(this).data('projectid');
        const mastermbl = $(this).data('mastermbl');

        // Buscar el proyecto primero
        const project = window.projectsData.find(p => p.project_id == projectid);
        // Luego, buscar el master dentro de ese proyecto
        const master = project?.masters?.find(m => m.mbl == mastermbl);

        if(master){
            Swal.fire({
                title: 'Are you sure?',
                text: `You are about to delete the master: ${master.mbl}. You will not be able to reverse this action.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete',
                cancelButtonText: 'Cancel',
            }).then((result) => {
                if(result.isConfirmed){
                    Swal.fire({
                        title: 'Deleting master...',
                        text: 'Please wait while we delete the master.',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });
                    
                    $.ajax({
                        url: 'deleteMaster',
                        type: 'POST',
                        data: {
                            mbl: master.mbl,
                            project_id: master.fk_project_id
                        },
                        success: function(response){
                            if(response.success){
                                Swal.close();
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Deleted!',
                                    text: 'Master deleted successfully.',
                                    confirmButtonText: 'Ok'
                                }).then(() =>{
                                    // Actualizamos la variable global con los nuevos datos
                                    window.projectsData = response.projects;
                                    // window.mastersData = response.masters;
                                    // Guardamos el valor actual del filtro
                                    const currentFilterproject = table.search();
                                    // Re-renderizamos la tabla
                                    renderProjectsTableNew(window.projectsData);
                                    // Restauramos el filtro anterior
                                    table.search(currentFilterproject).draw();
                                });
                            }else{
                                Swal.close();
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: response.message || 'There was an issue deleting the master.',
                                    confirmButtonText: 'Ok'
                                });
                            }
                            
                        },
                        error: function(xhr){
                            Swal.close();
                            let msg = 'Unexpected error';
                            if(xhr.responseJSON && xhr.responseJSON.message){
                                msg = xhr.responseJSON.message;
                            }
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: msg,
                                confirmButtonText: 'Ok'
                            });
                        }
                    })
                }
            })
        }else{
            Swal.fire({
                title: 'Error',
                text: 'The master could not be identified.',
                icon: 'error',
                confirmButtonText: 'OK',
            });
        }
    }); 

    //Abrir el modal con los subprojects
    $(document).on('click', '.showcfssubprojectmodal', function() {
        const projectid = $(this).data('projectid');
        const mastermbl = $(this).data('mbl');

        // Buscar el proyecto primero
        const project = window.projectsData.find(p => p.project_id == projectid);
        // Luego, buscar el master dentro de ese proyecto
        const master = project?.masters?.find(m => m.mbl == mastermbl);
        if(master){
            Swal.fire({
                        title: 'Searching for subprojects...',
                        text: 'Please wait while we search the subprojects.',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
            });

            $.ajax({
                url: 'getMastersSubprojects',
                type: 'POST',
                data: {
                    mbl: master.mbl,
                    project_id: master.fk_project_id,
                },
                success: function(response){
                    if(response.success){
                            Swal.close();
                            // Actualizamos la variable global con los nuevos datos
                            window.subprojectsData = response.subprojects;
                            window.projectsData = response.projects;
                            // Guardamos el valor actual del filtro
                            const currentFilter = tableSubprojects.search();
                            // Re-renderizamos la tabla
                            renderSubprojectsTable(window.subprojectsData,master.fk_project_id);
                            // Restauramos el filtro anterior
                            tableSubprojects.search(currentFilter).draw();
                            //Cambiar el titulo del modal
                            $('#staticshowcfssubproject').text(`Houses list ${master.mbl}`);
                            //$('#showcfsmaster').modal('hide');
                            $('#searchgeneralcfsboardsubprojects').val('');
                            // Asignar valores al botón de agregar subproyecto
                            $('#addnewcfssubproject')
                            .data('projectid', projectid)
                            .data('mbl', mastermbl)
                            .attr('data-projectid', projectid)
                            .attr('data-mbl', mastermbl);
                            $('#showcfssubproject').modal('show');
                    }else{
                        Swal.close();
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
                text: 'The master could not be identified.',
                icon: 'error',
                confirmButtonText: 'OK',
            });
        }
    });

    $(document).on('click', '.close-staticshowcfssubproject', function() {
        $('#addnewcfssubproject')
            .removeData('projectid')
            .removeData('mbl')
            .attr('data-projectid', '')
            .attr('data-mbl', '');
    });

    //Settear fechas de projects en subprojects y abrir modal agregar nuevo subproject
    $(document).on('click', '#addnewcfssubproject', function () {
        const projectid = $(this).data('projectid');
        const mastermbl = $(this).data('mbl');

        // Buscar el proyecto primero
        const project = window.projectsData.find(p => p.project_id == projectid);
        // Luego, buscar el master dentro de ese proyecto
        const master = project?.masters?.find(m => m.mbl == mastermbl);

        if(master){
            $('#inputnewsubprojectproyectid').val(master.fk_project_id);
            $('#inputnewsubprojectcfsmbl').val(master.mbl);
            setDateAndLock('#inputnewsubprojectcfsarrivaldate', formatDate(master.arrival_date, 'full'));
            setDateAndLock('#inputnewsubprojectcfslfd', formatDate(master.lfd, 'full'));
            updateDaysAfterLFD();
            $('#showcfssubproject').modal('hide');
            
            // Seleccionar automáticamente en CFS "No" si existe
            const noOptionCFS = cfsData.find(opt => opt.text.trim().toLowerCase() === 'no');
            if (noOptionCFS) {
                $('#inputnewsubprojectcfscfscomment').val(noOptionCFS.id).trigger('change');
            }

            // Seleccionar automáticamente en Custom Release "No" si existe
            const noOptionCustomRelease = customReleaseData.find(opt => opt.text.trim().toLowerCase() === 'no');
            if (noOptionCustomRelease) {
                $('#inputnewsubprojectcfscustomsreleasecomment').val(noOptionCustomRelease.id).trigger('change');
            }

            // Seleccionar automáticamente "No" en Works/Palletized
            $('#inputnewsubprojectcfsworkspalletized').val('No').trigger('change');

            // Seleccionar automáticamente "No" en Palletz Exchange
            $('#inputnewsubprojectcfspalletsexchanged').val('No').trigger('change');

            //Poner los inputs numericos en 0
            $('#inputnewsubprojectcfspallets').val(0).trigger('input');
            $('#inputnewsubprojectcfspieces').val(0).trigger('input');
            $('#inputnewsubprojectcfsdeliverycharges').val(0).trigger('input');
            $('#inputnewsubprojectcfscuft').val(0).trigger('input');

            $('#newcfssubproject').modal('show');
        }else{
            Swal.fire({
                title: 'Error',
                text: 'The master could not be identified.',
                icon: 'error',
                confirmButtonText: 'OK',
            });
        }
    });

    //Llamar a la funcion para autocalcular los last free days
    $('#inputnewsubprojectcfsoutdatecr').on('change', function () {
        updateDaysAfterLFD();
    });

    //Funcion para autocalcular los after last free days 
    function updateDaysAfterLFD() {
        const lfdDateStr = $('#inputnewsubprojectcfslfd').val();
        const outDateStr = $('#inputnewsubprojectcfsoutdatecr').val();
    
        if (lfdDateStr && outDateStr) {
            //const now = new Date();
            //now.setHours(0, 0, 0, 0);
            //now.setHours(23, 59, 59, 999);
            const outDate = new Date(outDateStr);
            outDate.setHours(23, 59, 59, 999);
            const lfdDate = new Date(lfdDateStr);
    
            if (!isNaN(lfdDate) && !isNaN(outDate)) {
                const diffMs = outDate - lfdDate;
                const diffDays = Math.floor(diffMs / (1000 * 60 * 60 * 24)); // convertir ms a días
                if(diffDays < 0){
                    $('#inputnewsubprojectcfsdalfd').val(0);
                }else{
                    $('#inputnewsubprojectcfsdalfd').val(diffDays);
                }
            } else {
                $('#inputnewsubprojectcfsdalfd').val(0);
            }
        } else {
            $('#inputnewsubprojectcfsdalfd').val(0);
        }
        calculateWHStorageCharge();
    }

    // Función para calcular el cargo total por los servicios de palletizado
    function calculatePalletizedCharges() {
        const palletizedValue = $('#inputnewsubprojectcfsworkspalletized').val();
        const exchangedValue = $('#inputnewsubprojectcfspalletsexchanged').val();
        const pallets = parseInt($('#inputnewsubprojectcfspallets').val()) || 0;

        // Si los pallets son menores a 1, el cargo debe ser 0
        if (pallets < 1) {
            $('#inputnewsubprojectcfspalletizedcharges').val('0');
            return;
        }

        let totalCharge = 0;

        // Buscar el servicio 'Palletized S/WRAP' si está seleccionado
        if (palletizedValue === 'Yes') {
            const palletizedService = servicesData.find(s => s.text === 'Palletized S/WRAP');
            if (palletizedService) {
                totalCharge += pallets * parseFloat(palletizedService.cost || 0);
            }
        }

        // Buscar el servicio 'PALLET EACH (NON CERT)' si está seleccionado
        if (exchangedValue === 'Yes') {
            const exchangedService = servicesData.find(s => s.text === 'PALLET EACH (NON CERT)');
            if (exchangedService) {
                totalCharge += pallets * parseFloat(exchangedService.cost || 0);
            }
        }

        // Mostrar el total
        $('#inputnewsubprojectcfspalletizedcharges').val(totalCharge.toFixed(2));
        updateTotalCharge();
    }

    // Vincular la función a los eventos de cambio de los servicios de paletizado 
    $('#inputnewsubprojectcfsworkspalletized, #inputnewsubprojectcfspalletsexchanged').on('change', calculatePalletizedCharges);
    $('#inputnewsubprojectcfspallets').on('input', calculatePalletizedCharges);

    // Escuchar cambios en el input de delivery charges
    $('#inputnewsubprojectcfsdeliverycharges').on('input', function () {
        updateTotalCharge();
    });

    //Funcion para el autocalculo de los Cuft
    $(document).on('blur', '#inputnewsubprojectcfscuft', function () {
        const val = parseFloat($(this).val());
    
        if (!isNaN(val)) {
            const cuft = val * 35.3147;
            $(this).val(cuft.toFixed(4)); // Redondea a 4 decimales
            calculateWHStorageCharge();
        } else {
            $(this).val('');
        }
    });

    //Calcular el costo del storage
    function calculateWHStorageCharge() {
        let cuft = parseFloat($('#inputnewsubprojectcfscuft').val()) || 0;
        let days = parseFloat($('#inputnewsubprojectcfsdalfd').val()) || 0;
        let result = cuft * days;

        const storageService = servicesData.find(s => s.text === 'STORAGE');
        if (storageService) {
            result = result * parseFloat(storageService.cost || 0);
        }
        $('#inputnewsubprojectcfswhstoragecharges').val(result.toFixed(2));
        updateTotalCharge();
    }

    //Funcion para que se calcule el total0
    function updateTotalCharge() {
        const whStorage = parseFloat($('#inputnewsubprojectcfswhstoragecharges').val()) || 0;
        const palletized = parseFloat($('#inputnewsubprojectcfspalletizedcharges').val()) || 0;
        const delivery = parseFloat($('#inputnewsubprojectcfsdeliverycharges').val()) || 0;
        const total = whStorage + palletized + delivery;
    
        $('#inputnewsubprojectcfscharges').val(total.toFixed(2)); // redondear a 2 decimales
    }
    
    //Funcion para fijar mis fechas
    function setDateAndLock(inputSelector, value){
        if($(inputSelector)[0]._flatpickr){
            $(inputSelector)[0]._flatpickr.setDate(value); // <-- Así sí se pinta bien
            $(inputSelector)[0]._flatpickr.set('clickOpens', false);
        } else {
            $(inputSelector).val(value); // fallback por si no se inicializó flatpickr aún
        }
        $(inputSelector).prop('readonly', true); // también en el HTML
    }

    function setDateAndLockEdit(inputSelector, value){
        const input = $(inputSelector)[0];
        let dateValue = value;

        // Convertir string a Date si es necesario
        if(typeof value === 'string'){
            dateValue = new Date(value);
        }

        if(input._flatpickr){
            input._flatpickr.setDate(dateValue, true); // true = trigger change
            input._flatpickr.set('clickOpens', false);
        } else {
            $(inputSelector).val(formatDate(dateValue, 'full'));
        }

        $(inputSelector).prop('readonly', true);
    }


    // Llamar a la función para limpiar inputs cuando se cierre el modal de los Subprojects
    $('#newcfssubproject').on('hidden.bs.modal', function() {
        resetModalNewCFSSubprojectsFields('#newcfssubproject');
    });

    //Funcion para resetear el formulario al cerrarlo de projects
    function resetModalNewCFSSubprojectsFields(modalSelector) {
        // Limpiar selects con select2 (Drayage User y File Type)
        $(modalSelector).find('.searchCustomer, .searchcfscomment, .searchcustomerreleasecomment, .searchWorksPalletized, .searchPalletsExchange').each(function () {
            $(this).val(null).trigger('change'); // Restablecer y actualizar select2
        });

        // También puedes reiniciar todos los selects al primer índice si quieres asegurarte
        $(modalSelector).find('select').each(function () {
            this.selectedIndex = 0;
        });

        // Eliminar todos los campos dinámicos de HBL References
        $('.dynamicHBLReference').closest('div.col-md-6').remove();
        hblReferenceIndex = 1; // resetear el contador global

        //Eliminacion de los campos dinamicos
        $('.dynamicHBLReference').each(function () {
            const field = $(this);
            const errorElement = $(`#error-${field.attr('id')}`);
            field.removeClass('is-invalid');
            errorElement.text('');
            const lastField = $('.dynamicHBLReference').last();
            if (lastField.length && hblReferenceIndex > 1) {
                lastField.closest('div.col-md-6').remove();
                hblReferenceIndex--;
            }
        });

        // Eliminar todos los campos dinámicos de Part Numbers
        $('.dynamicPartNumber').closest('div.col-md-6').remove();
        partNumberIndex = 1; // resetear el contador global

        $('.dynamicPartNumber').each(function () {
            const field = $(this);
            const errorElement = $(`#error-${field.attr('id')}`);
            field.removeClass('is-invalid');
            errorElement.text('');
            const lastField = $('.dynamicPartNumber').last();
            if (lastField.length && partNumberIndex > 1) {
                lastField.closest('div.col-md-6').remove();
                partNumberIndex--;
            }
        });

        // Limpiar errores
        $(modalSelector).find('.is-invalid').removeClass('is-invalid');
        $(modalSelector).find('.invalid-feedback').text('');

        // Limpiar inputs y textarea
        $(modalSelector).find('input, textarea').val('');

        // Limpiar selects normales (no select2)
        $(modalSelector).find('select').not('.select2').val('');

        $('#checkAgent').prop('checked', false);
        $('#checkCollected').prop('checked', false);

        //$('#editnewcfsproject').text('Save').attr('id', 'savecfssubproject');
        $('#staticnewcfssubproject').text('New House');
        //$('#inputnewcfsprojectprojectid')
        //.prop('readonly', false); 
        const dateInputs = ['#inputnewsubprojectcfsarrivaldate', '#inputnewsubprojectcfslfd'];
        dateInputs.forEach(selector => {
            const fpInstance = $(selector)[0]._flatpickr;
            if (fpInstance) {
                fpInstance.clear(); // Limpiar la fecha
                fpInstance.set('clickOpens', true); // Habilitar el click
            }
            $(selector).prop('readonly', false); // Input editable
        });

        // Cambiar  el texto del botón 
        $('#editcfssubproject').text('Save').attr('id', 'savecfssubproject');
        $('#inputnewsubprojectcfshbl').prop('readonly', false); // Deshabilitar el input
    }

    function renderSubprojectsTable(subprojectsData, pkproject) {
        tableSubprojects.clear();
        subprojectsData.forEach(sub => {

            const fkProjectId = pkproject;

            const cfscommentvalue = sub.cfs_comment && sub.cfs_value || sub.cfs_desc || '';
            let cfscomment = '';

            if (cfscommentvalue === 'Yes') {
                cfscomment = '<i class="fa-solid fa-circle-check" style="color:rgb(13, 82, 200)"></i> Yes';
            } else if (cfscommentvalue === 'No') {
                cfscomment = '<i class="text-danger fa-solid fa-circle-xmark"></i> No';
            } else if (cfscommentvalue) {
                cfscomment = '<i class="fa-solid fa-circle-check text-success"></i> ' + cfscommentvalue;
            }

            const customreleasetvalue = sub.customs_release_comment && sub.cr_value || sub.cr_desc || '';
            let customrelease = '';

            if (customreleasetvalue === 'Yes') {
                customrelease = '<i class="fa-solid fa-circle-check" style="color:rgb(13, 82, 200)"></i> Yes';
            } else if (customreleasetvalue === 'No') {
                customrelease = '<i class="text-danger fa-solid fa-circle-xmark"></i> No';
            } else if (customreleasetvalue) {
                customrelease = '<i class="fa-solid fa-circle-check text-success"></i> ' + customreleasetvalue;
            }

            const costumer = sub.pk_customer && sub.customer_desc || '';

            const hblList = [
                `<li class="list-group-item px-0">${sub.hbl ?? ''}</li>`,
                ...(sub.hblreferences?.length
                    ? sub.hblreferences
                        .filter(h => h.description) // ← solo las que tengan descripción
                        .map(h => `<li class="list-group-item px-0">(${h.description})</li>`)
                    : []
                )
            ].join('');
    
            const pnsList = (sub.partnumbers || [])
                .map(pn => `<li class="list-group-item px-0">${pn.description ?? ''}</li>`)
                .join('');
            
            const chargesList = `
                <!--<li style="" class="d-flex justify-content-between list-group-item px-0">
                    Works <span class="ms-1">${sub.services_charge}</span>
                </li>
                <li style="" class="d-flex justify-content-between list-group-item px-0">
                    Storage <span class="ms-1">${sub.wh_storage_charge}</span>
                </li>
                <li style="" class="d-flex justify-content-between list-group-item px-0">
                    Delivery <span class="ms-1">${sub.delivery_charges}</span>
                </li>
                <li style="" class="d-flex justify-content-between list-group-item px-0">
                    Total <span class="ms-1">${sub.charges}</span>
                </li>-->
            `;

            let rowHighlightClass = '';
            if (sub.collected === 'Yes') {
                rowHighlightClass = 'bg-light-green';
            } else if (customreleasetvalue && customreleasetvalue.toLowerCase() !== 'no') {
                rowHighlightClass = 'bg-light-blue';
            }

            const rowHTML = `
                <tr class="${rowHighlightClass}">
                    <td class="align-middle">${sub.subprojects_id}</td>
                    <td class="align-middle">
                        <ul class="list-group list-group-flush">${hblList}</ul>
                    </td>
                    <td class="align-middle">${sub.pieces || '0'}</td>
                    <td class="align-middle">${sub.pallets || '0'}</td>
                    <td class="align-middle">${sub.works_palletized === 'Yes' ? '<i class="fa-solid fa-circle-check" style="color:rgb(13, 82, 200)"></i> Yes' : '<i class="text-danger fa-solid fa-circle-xmark"></i> No'}</td>
                    <td class="align-middle">${sub.pallets_exchanged === 'Yes' ? '<i class="fa-solid fa-circle-check" style="color:rgb(13, 82, 200)"></i> Yes' : '<i class="text-danger fa-solid fa-circle-xmark"></i> No'}</td>
                    <td class="align-middle"  style="white-space:wrap">${costumer || ''}</td>
                    <td class="align-middle">${sub.agent === 'Yes' ? '<i class="fa-solid fa-circle-check" style="color:rgb(13, 82, 200)"></i> Yes' : '<i class="text-danger fa-solid fa-circle-xmark"></i> No'}</td>
                    <td class="align-middle">
                        <ul class="list-group list-group-flush">${pnsList}</ul>
                    </td>
                    <td class="align-middle">${cfscomment}</td>
                    <td class="align-middle">${formatDate(sub.arrival_date, 'short')}</td>
                    <td class="align-middle">${sub.whr || ''}</td>
                    <td class="align-middle">${formatDate(sub.lfd, 'short')}</td>
                    <td class="align-middle">${customrelease}</td>
                    <td class="align-middle">${formatDate(sub.out_date_cr, 'short')}</td>
                    <td class="align-middle">${sub.cr || ''}</td>
                    <!--<td class="align-middle">
                        <ul class="list-group list-group-flush">${chargesList}</ul>
                    </td>-->
                    <td class="align-middle">${sub.charges || '0'}</td>
                    <td class="align-middle">${sub.days_after_lfd || '0'}</td>
                    <td class="align-middle">${sub.cuft || ''}</td>
                    <td class="align-middle">${sub.notes || ''}</td>
                    <td class="align-middle">
                        <li class="align-middle text-start list-group-item px-0 py-0">
                            <div>
                                <div class="">
                                    <button type="button" class="btn btn-edit-subproject" style="color:rgb(13, 82, 200); font-size:12px" data-project="${fkProjectId}" data-mastermbl="${sub.fk_mbl}" data-subprojectid="${sub.hbl}">
                                        <i class="fa-solid fa-pen"></i>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-danger btn-delete-subproject" style="font-size:12px" data-project="${fkProjectId}" data-mastermbl="${sub.fk_mbl}" data-subprojectid="${sub.hbl}">
                                        <i class="fa-solid fa-trash-can"></i>
                                    </button>
                                </div>
                            </div>
                        </li>
                    </td>
                </tr>
            `;
    
            tableSubprojects.row.add($(rowHTML));
        });
        tableSubprojects.draw();
    }

    //Contador para indices de los hbl reference
    let hblReferenceIndex = 1;
    
    //Agregado de nuevo hbl reference
    $('#addhblreference').on('click', function () {
        const hblReferenceId = `inputnewsubprojectcfshblreference${hblReferenceIndex}`;

        const newHBLReferenceDiv = `
            <div class="mb-3 col-md-6 col-lg-4 col-xl-3">
                <label for="${hblReferenceId}" class="form-label" style="font-weight:500">HBL Reference</label>
                <input type="text" class="form-control dynamicHBLReference" id="${hblReferenceId}" name="${hblReferenceId}">
                <div class="invalid-feedback" id="error-${hblReferenceId}"></div>
            </div>
        `;

        // Insertar el nuevo div después del contenedor de botones
        $('#addhblreference').closest('.mb-3').after(newHBLReferenceDiv);

        hblReferenceIndex++;
    });

    //Funcion quitar los nuevos hbl reference
    $('#removehblreference').on('click', function () {
        const lastField = $('.dynamicHBLReference').last();
        if (lastField.length && hblReferenceIndex > 1) {
            lastField.closest('div.col-md-6').remove();
            hblReferenceIndex--;
        }
    });

    //Contador para indices de los part number
    let partNumberIndex = 1; // Para IDs únicos

    //Agregado de nuevo elemento al formulario (nuevo select2 part number)
    $('#addpartnumber').on('click', function () {
        const partNumberId = `inputnewsubprojectcfspartnumber${partNumberIndex}`;

        const newPartNumberDiv = `
            <div class="mb-3 col-md-6 col-lg-4 col-xl-3">
                <label for="${partNumberId}" class="form-label" style="font-weight:500">Product</label>
                <select class="form-select dynamicPartNumber" id="${partNumberId}" name="${partNumberId}">
                    <option selected disabled hidden></option>
                </select>
                <div class="invalid-feedback" id="error-${partNumberId}"></div>
            </div>
        `;

        // Insertar el nuevo div después del contenedor de botones
        $('#addpartnumber').closest('.mb-3').after(newPartNumberDiv);

        // Inicializar Select2 en el nuevo select
        initializeDynamicSelect2(`#${partNumberId}`);

        partNumberIndex++;
    });

    //Funcion inicializar los nuevos selects como selects2
    function initializeDynamicSelect2(selector) {
        $(selector).select2({
            placeholder: 'Select a Product',
            allowClear: true,
            tags: true,
            data: partNumberData,
            dropdownParent: $('#newcfssubproject'),
            minimumInputLength: 0
        });
    
        // Asignar evento de cambio individual
        $(selector).on('change', function () {
            const selectedOption = $(this).select2('data')[0];
            const selectedText = selectedOption ? selectedOption.text : '';
    
            if (selectedText !== newlySelectedPartNumber && selectedText.trim() !== '') {
                if (!selectedPartNumber.includes(selectedText) || !selectedPartNumberUpdate.includes(selectedText)) {
                    if (!selectedPartNumber.includes(selectedText)) {
                        selectedPartNumber.push(selectedText);
                    }
                    if (!selectedPartNumberUpdate.includes(selectedText)) {
                        selectedPartNumberUpdate.push(selectedText);
                    }
                    savePartNumberDinamic(selectedText, selector); // ← le pasamos el selector
                }
            }
        });
    }
    
    //Funcion para que los nuevos selects de partnumbers puedan agregar
    function savePartNumberDinamic(newPartNumber, selectorToUpdate) {
        $.ajax({
            url: 'saveNewPartNumber',
            type: 'POST',
            data: {
                newPartNumber: newPartNumber
            },
            success: function (response) {
                const newOption = new Option(
                    response.newPartNumberCreated.description,
                    response.newPartNumberCreated.pk_part_number,
                    true,
                    true
                );
    
                $(selectorToUpdate).append(newOption).trigger('change');
                $(selectorToUpdate).val(response.newPartNumberCreated.pk_part_number).trigger('change');
    
                newlySelectedPartNumber = response.newPartNumberCreated.description;
    
                $(selectorToUpdate).on('select2:select', function (e) {
                    const selectedId = e.params.data.id;
                    if (selectedId === newlySelectedPartNumber) {
                        newlySelectedPartNumber = null;
                    }
                });
            },
            error: function (xhr, status, error) {
                if (xhr.status === 409) {
                    alert('Part Number already exists.');
                } else {
                    console.error('Error saving Part Number:', error);
                }
            }
        });
    }
    
    //Funcion quitar los nuevos selects para part numbers agregados
    $('#removepartnumber').on('click', function () {
        const lastField = $('.dynamicPartNumber').last();
        if (lastField.length && partNumberIndex > 1) {
            lastField.closest('div.col-md-6').remove();
            partNumberIndex--;
        }
    });
    
    //Campos que deben cumplir con las validaciones
    const formFieldsSubproject = [
        'inputnewsubprojectcfssubprojectid',
        'inputnewsubprojectcfshbl',
        'inputnewsubprojectcfspieces',
        'inputnewsubprojectcfspallets',
        'inputnewsubprojectcfsworkspalletized',
        'inputnewsubprojectcfspalletsexchanged',
        'inputnewsubprojectcfspalletizedcharges',
        'inputnewsubprojectcfscustomer',
        'inputnewsubprojectcfscfscomment',
        'inputnewsubprojectcfsarrivaldate',
        //'inputnewsubprojectcfsmagayawhr',
        'inputnewsubprojectcfslfd',
        'inputnewsubprojectcfscustomsreleasecomment',
        //'inputnewsubprojectcfsoutdatecr',
        //'inputnewsubprojectcfsmagayacr',
        'inputnewsubprojectcfswhstoragecharges',
        //'inputnewsubprojectcfsdeliverycharges',
        'inputnewsubprojectcfscharges',
        'inputnewsubprojectcfsdalfd',
        //'inputnewsubprojectcfscuft',
        //'inputnewsubprojectcfsnotes',
    ];

    //Validaciones de selects 2 para los subprojects
    function handleSelect2EventsSubprojects(field, errorElement) {
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
    
    //Validaciones campos comunes de los subprojects
    function validateFieldSubprojects(field, errorElement) {
        const fieldId = field.attr('id');
        const fieldValue = field.val().trim();
    
        if (fieldValue === "") {
            let customErrorMessage = 'This field is required.';
            if (fieldId === 'inputnewsubprojectcfssubprojectid') {
                customErrorMessage = 'Subproject ID is required.';
            } else if (fieldId === 'inputnewsubprojectcfshbl') {
                customErrorMessage = 'HBL ID is required.';
            } else if (fieldId === 'inputnewsubprojectcfspieces') {
                customErrorMessage = 'Pieces is required.';
            } else if (fieldId === 'inputnewsubprojectcfspallets') {
                customErrorMessage = 'Pallets is required.';
            } else if (fieldId === 'inputnewsubprojectcfsmagayawhr') {
                customErrorMessage = 'WHR ID is required.';
            } else if (fieldId === 'inputnewsubprojectcfsoutdatecr') {
                customErrorMessage = 'OUT Date CR is required.';
            } else if (fieldId === 'inputnewsubprojectcfsmagayacr') {
                customErrorMessage = 'CR ID is required.';
            } else if (fieldId === 'inputnewsubprojectcfscharges') {
                customErrorMessage = 'Charges is required.';
            } else if (fieldId === 'inputnewsubprojectcfsdalfd') {
                customErrorMessage = 'Days after LFD is required.';
            } else if (fieldId === 'inputnewsubprojectcfscuft') {
                customErrorMessage = 'Cuft is required.';
            } else if (fieldId === 'inputnewsubprojectcfsnotes') {
                customErrorMessage = 'Notes is required.';
            } else if (fieldId === 'inputnewsubprojectcfspalletizedcharges') {
                customErrorMessage = 'Palletized Charge is required.';
            } else if (fieldId === 'inputnewsubprojectcfsdeliverycharges') {
                customErrorMessage = 'Delivery charge is required.';
            } else if (fieldId === 'inputnewsubprojectcfslfd') {
                customErrorMessage = 'LFD is required.';
            } else if (fieldId === 'inputnewsubprojectcfsarrivaldate') {
                customErrorMessage = 'Arrival Date is required.';
            } else if (fieldId === 'inputnewsubprojectcfswhstoragecharges') {
                customErrorMessage = 'WH Storahe Charge is required.';
            }
            field.addClass('is-invalid');
            errorElement.text(customErrorMessage);
        } else {
            field.removeClass('is-invalid');
            errorElement.text('');
        }
    }
    
    //Validaciones llamadas a las validaciones de los subprojects
    formFieldsSubproject.forEach(fieldId => {
        const field = $("#" + fieldId);
        const errorElement = $('#error-' + fieldId);
        const isSelect2 = field.hasClass("searchCustomer") || field.hasClass("searchcfscomment") || field.hasClass("searchcustomerreleasecomment") || field.hasClass("searchWorksPalletized") || field.hasClass("searchPalletsExchange");
    
        if (isSelect2) {
            field.on('change', function () {
                handleSelect2EventsSubprojects(field, errorElement);
            });
        } else {
            field.on('keyup blur', function () {
                validateFieldSubprojects(field, errorElement);
            });
        }
    });

    // Validar Part Numbers dinámicos
    $('#newcfssubproject').on('change', '.dynamicPartNumber', function () {
        const field = $(this);
        const fieldValue = field.val();
        const errorElement = $(`#error-${field.attr('id')}`);
        const customErrorMessage = "Part Number is required"
        if (fieldValue === null || fieldValue === undefined || fieldValue === "") {
            field.siblings(".select2").find(".select2-selection").addClass("is-invalid");
            field.addClass('is-invalid');
            errorElement.text(customErrorMessage || 'This field is required.');
        } else {
            field.removeClass('is-invalid');
            errorElement.text('');
            field.siblings(".select2").find(".select2-selection").removeClass("is-invalid");
        }
    });

    // Validar Part Numbers dinámicos
    $('#newcfssubproject').on('input', '.dynamicHBLReference', function () {
        const field = $(this);
        const fieldValue = field.val();
        const errorElement = $(`#error-${field.attr('id')}`);
        const customErrorMessage = "HBL reference is required"
        if (fieldValue === "") {
            field.addClass('is-invalid');
            errorElement.text(customErrorMessage);
        } else {
            field.removeClass('is-invalid');
            errorElement.text('');
        }
    });

    //Funcion Guardar nuevo Subproject
    $('body').on('click', '#savecfssubproject', function (e) {
        e.preventDefault();
        let isValidSubproject = true;

        // Validar Part Numbers dinámicos
        $('.dynamicPartNumber').each(function () {
            const field = $(this);
            const fieldValue = field.val();
            const errorElement = $(`#error-${field.attr('id')}`);
            const customErrorMessage = "Part Number is required"
            if (fieldValue === null || fieldValue === undefined || fieldValue === "") {
                field.siblings(".select2").find(".select2-selection").addClass("is-invalid");
                field.addClass('is-invalid');
                errorElement.text(customErrorMessage || 'This field is required.');
                isValidSubproject = false;
            } else {
                field.removeClass('is-invalid');
                errorElement.text('');
                field.siblings(".select2").find(".select2-selection").removeClass("is-invalid");
            }
        });
        
        // Validar Part Numbers dinámicos
        $('.dynamicHBLReference').each(function () {
            const field = $(this);
            const fieldValue = field.val();
            const errorElement = $(`#error-${field.attr('id')}`);
            const customErrorMessage = "HBL reference is required"
            if (fieldValue === "") {
                field.addClass('is-invalid');
                errorElement.text(customErrorMessage);
                isValidSubproject = false;
            } else {
                field.removeClass('is-invalid');
                errorElement.text('');
            }
        });
        
        formFieldsSubproject.forEach(fieldId => {
            const field = $("#" + fieldId);
            const errorElement = $('#error-' + fieldId);
            const isSelect2 = field.hasClass("searchCustomer") || field.hasClass("searchcfscomment") || field.hasClass("searchcustomerreleasecomment");
    
            if (isSelect2) {
                handleSelect2EventsSubprojects(field, errorElement); // Validación para select2
                if (field.hasClass('is-invalid')) {
                    isValidSubproject = false;
                }
            } else {
                const fieldValue = field.val().trim();
                if (fieldValue === '') {
                    isValidSubproject = false;
                    validateFieldSubprojects(field, errorElement); // Validación para campos normales
                }
            }
        });
    
        if (!isValidSubproject) {
            const firstInvalidField = $('.is-invalid').first();
            if (firstInvalidField.length) {
                firstInvalidField.focus();
            }
            return;
        }

        // Llenar los arrays de HBL References y Part Numbers
        let hblReferencesArray = [];
        let partNumbersArray = [];

        // Llenar el array de hbl_references[] recorriendo los campos de HBL References
        $('.dynamicHBLReference').each(function () {
            const fieldValue = $(this).val();
            if (fieldValue) {
                hblReferencesArray.push(fieldValue);
            }
        });

        // Llenar el array de part_numbers[] recorriendo los campos de Part Numbers
        $('.dynamicPartNumber').each(function () {
            const fieldValue = $(this).val();
            if (fieldValue) {
                partNumbersArray.push(fieldValue);
            }
        });

        let formData = new FormData($('#createcfssubproject')[0]);

        hblReferencesArray.forEach((value) => {
            formData.append('hbl_references[]', value);
        });
        
        partNumbersArray.forEach((value) => {
            formData.append('part_numbers[]', value);
        });

        Swal.fire({
            title: 'Saving subproject...',
            text: 'Please wait while we save the subproject.',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        $.ajax({
            url: 'saveNewSubproject',
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function (response){
                Swal.close();
                Swal.fire({
                    icon: 'success',
                    title: '¡Success!',
                    text: 'Subproject successfully added.',
                    confirmButtonText: 'Ok'
                }).then(() =>{
                    // Actualizamos la variable global con los nuevos datos
                    window.projectsData = response.projects;
                    //window.mastersData = response.masters;
                    // Guardamos el valor actual del filtro
                    const currentFilterproject = table.search();
                    // Re-renderizamos la tabla
                    renderProjectsTableNew(window.projectsData);
                    // Restauramos el filtro anterior
                    table.search(currentFilterproject).draw();
                    // Actualizamos la variable global con los nuevos datos
                    window.subprojectsData = response.subprojects;
                    // Guardamos el valor actual del filtro
                    const currentFilter = tableSubprojects.search();
                    // Re-renderizamos la tabla
                    renderSubprojectsTable(window.subprojectsData,  response.pkproject);
                    // Restauramos el filtro anterior
                    tableSubprojects.search(currentFilter).draw();
                    $('#newcfssubproject').modal('hide');
                    $('#showcfssubproject').modal('show');
                });
            },
            error: function (xhr, status, error) {
                Swal.close();
                // Limpia los errores anteriores
                $('input, select').removeClass('is-invalid');
                $('.invalid-feedback').text(''); // Vaciar mensajes de error
                $('.select2-selection').removeClass('is-invalid'); // También eliminar la clase del contenedor de select2
    
                let errors = xhr.responseJSON.errors;
                let generalMessage = xhr.responseJSON?.message || 'There was a problem adding the subproject. Please try again.';
    
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
                    text: generalMessage,
                    confirmButtonText: 'Ok'
                });
            }
        })
    });

    //Ejecutar el borrado de algun subproject
    $(document).on('click', '.btn-delete-subproject', function () {
        const subprojectid = $(this).data('subprojectid');
        const mastermbl = $(this).data('mastermbl');
        const projectid = $(this).data('project');

        // Buscar el proyecto primero
        const project = window.projectsData.find(p => p.project_id == projectid);
        // Luego, buscar el master dentro de ese proyecto
        const master = project?.masters?.find(m => m.mbl == mastermbl);
        // Luego, buscar el subproject dentro de ese master
        const subproject = master?.subprojects?.find(sub => sub.hbl == subprojectid);

        if(subproject){
            Swal.fire({
                title: 'Are you sure?',
                text: `You are about to delete the subproject: ${subproject.hbl}. You will not be able to reverse this action.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete',
                cancelButtonText: 'Cancel',
            }).then((result) => {
                if(result.isConfirmed){

                    Swal.fire({
                        title: 'Deleting subproject...',
                        text: 'Please wait while we delete the subproject.',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    $.ajax({
                        url: 'deleteSubproject',
                        type: 'POST',
                        data: {
                            mbl: master.mbl,
                            hbl: subproject.hbl,
                            project: projectid
                        },
                        success: function(response){
                            if(response.success){
                                Swal.close();
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Deleted!',
                                    text: 'Subproject deleted successfully.',
                                    confirmButtonText: 'Ok'
                                }).then(() =>{
                                    // Actualizamos la variable global con los nuevos datos
                                    window.projectsData = response.projects;
                                    //window.mastersData = response.masters;
                                    // Guardamos el valor actual del filtro
                                    const currentFilterproject = table.search();
                                    // Re-renderizamos la tabla
                                    renderProjectsTableNew(window.projectsData);
                                    // Restauramos el filtro anterior
                                    table.search(currentFilterproject).draw();
                                    // Actualizamos la variable global con los nuevos datos
                                    window.subprojectsData = response.subprojects;
                                    // Guardamos el valor actual del filtro
                                    const currentFilter = tableSubprojects.search();
                                    // Re-renderizamos la tabla
                                    renderSubprojectsTable(window.subprojectsData, master.fk_project_id);
                                    // Restauramos el filtro anterior
                                    tableSubprojects.search(currentFilter).draw();
                                });
                            }else{
                                Swal.close();
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: 'There was an issue deleting the master.',
                                    confirmButtonText: 'Ok'
                                });
                            }
                            
                        },
                        error: function(xhr){
                            Swal.close();
                            let msg = 'Unexpected error';
                            if(xhr.responseJSON && xhr.responseJSON.message){
                                msg = xhr.responseJSON.message;
                            }
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: msg,
                                confirmButtonText: 'Ok'
                            });
                        }
                        
                    })
                }
            })
        }else{
            Swal.fire({
                title: 'Error',
                text: 'The subproject could not be identified.',
                icon: 'error',
                confirmButtonText: 'OK',
            });
        }
    });
    
    //Abrir modal de edicion de subprojects
    $(document).on('click', '.btn-edit-subproject', function () {
        const subprojectid = $(this).data('subprojectid');
        const mastermbl = $(this).data('mastermbl');
        const projectid = $(this).data('project');

        // Buscar el master primero
        //const master = window.mastersData.find(m => m.mbl == mastermbl);
        // Luego, buscar el subproject dentro de ese master
        //const subproject = master?.subprojects?.find(sub => sub.hbl == subprojectid);

        // Buscar el proyecto primero
        const project = window.projectsData.find(p => p.project_id == projectid);
        // Luego, buscar el master dentro de ese proyecto
        const master = project?.masters?.find(m => m.mbl == mastermbl);
        // Luego, buscar el subproject dentro de ese master
        const subproject = master?.subprojects?.find(sub => sub.hbl == subprojectid);

        if(subproject){
            // HBL References
            $('.dynamicHBLReference').closest('div.col-md-6').remove();
            hblReferenceIndex = 1;
            if (Array.isArray(subproject.hblreferences)) {
                subproject.hblreferences
                    .filter(ref => ref.description) // solo tomar los que tengan descripción
                    .forEach(ref => {
                        const hblReferenceId = `inputnewsubprojectcfshblreference${hblReferenceIndex}`;
                        const hblDiv = `
                            <div class="mb-3 col-md-6 col-lg-4 col-xl-3">
                                <label for="${hblReferenceId}" class="form-label" style="font-weight:500">HBL Reference</label>
                                <input type="text" class="form-control dynamicHBLReference" id="${hblReferenceId}" name="${hblReferenceId}" value="${ref.description}">
                                <div class="invalid-feedback" id="error-${hblReferenceId}"></div>
                            </div>
                        `;
                        $('#addhblreference').closest('.mb-3').after(hblDiv);
                        hblReferenceIndex++;
                    });
            }

            // Part Numbers
            $('.dynamicPartNumber').closest('div.col-md-6').remove();
            partNumberIndex = 1;
            if (Array.isArray(subproject.partnumbers)) {
                subproject.partnumbers
                    .filter(pn => pn.pk_part_number && pn.description) // solo los válidos
                    .forEach(pn => {
                        const partNumberId = `inputnewsubprojectcfspartnumber${partNumberIndex}`;
                        const selectDiv = `
                            <div class="mb-3 col-md-6 col-lg-4 col-xl-3">
                                <label for="${partNumberId}" class="form-label" style="font-weight:500">Product</label>
                                <select class="form-select dynamicPartNumber" id="${partNumberId}" name="${partNumberId}">
                                    <option value="${pn.pk_part_number}" selected>${pn.description}</option>
                                </select>
                                <div class="invalid-feedback" id="error-${partNumberId}"></div>
                            </div>
                        `;
                        $('#addpartnumber').closest('.mb-3').after(selectDiv);
                        initializeDynamicSelect2(`#${partNumberId}`);
                        partNumberIndex++;
                    });
            }

            //Promises para los selects2
            let customerPromise = new Promise ((resolve, reject) => {
                if(subproject.customer){
                    let customer = selectedCustomer.find(item => item === subproject.customer);
                    $('#inputnewsubprojectcfscustomer').val(subproject.customer).trigger('change');
                    resolve();
                } else {
                    $('#inputnewsubprojectcfscustomer').val(null).trigger('change');
                    reject(); // Resolver inmediatamente si no hay person
                }
            });
            let cfsPromise = new Promise ((resolve, reject) => {
                if(subproject.cfs_comment){
                    let cfs_comment = selectedCFS.find(item => item === subproject.cfs_comment);
                    $('#inputnewsubprojectcfscfscomment').val(subproject.cfs_comment).trigger('change');
                    resolve();
                } else {
                    $('#inputnewsubprojectcfscfscomment').val(null).trigger('change');
                    reject(); // Resolver inmediatamente si no hay person
                }
            });
            let customreleasePromise = new Promise ((resolve, reject) => {
                if(subproject.customs_release_comment){
                    let customs_release_comment = selectedCustomRelease.find(item => item === subproject.customs_release_comment);
                    $('#inputnewsubprojectcfscustomsreleasecomment').val(subproject.customs_release_comment).trigger('change');
                    resolve();
                } else {
                    $('#inputnewsubprojectcfscustomsreleasecomment').val(null).trigger('change');
                    reject(); // Resolver inmediatamente si no hay person
                }
            });
            let worksPalletizedPromise = new Promise ((resolve, reject) => {
                if(subproject.works_palletized){
                    $('#inputnewsubprojectcfsworkspalletized').val(subproject.works_palletized).trigger('change');
                    resolve();
                } else {
                    // Seleccionar automáticamente "No" en Works/Palletized
                    $('#inputnewsubprojectcfsworkspalletized').val('No').trigger('change');
                    resolve();
                }
            });
            let palletsExchangePromise = new Promise ((resolve, reject) => {
                if(subproject.customs_release_comment){
                    $('#inputnewsubprojectcfspalletsexchanged').val(subproject.pallets_exchanged).trigger('change');
                    resolve();
                } else {
                    // Seleccionar automáticamente "No" en Palletz Exchange
                    $('#inputnewsubprojectcfspalletsexchanged').val('No').trigger('change');
                    resolve();
                }
            });

            Promise.all([customerPromise, cfsPromise, customreleasePromise, worksPalletizedPromise, palletsExchangePromise])
                .then(() => {
                    // Cambiar  el texto del botón
                    $('#staticnewcfssubproject').text(`Edit House  ${subproject.hbl}`);
                    $('#savecfssubproject').text('Save Changes').attr('id', 'editcfssubproject');
                
                    // Llenar los inputs con los datos del proyecto
                    $('#inputnewsubprojectcfshbl').val(subproject.hbl);
                    $('#inputnewsubprojectcfshbloriginal').val(subproject.hbl);
                    //.prop('readonly', true); // Deshabilitar el input

                    $('#inputnewsubprojectproyectid').val(master.fk_project_id);
                    $('#inputnewsubprojectcfsmbl').val(master.mbl);
                    $('#inputnewsubprojectcfssubprojectid').val(subproject.subprojects_id);
                    $('#inputnewsubprojectcfspieces').val(subproject.pieces);
                    $('#inputnewsubprojectcfspallets').val(subproject.pallets);
                    $('#inputnewsubprojectcfspalletizedcharges').val(subproject.services_charge);
                    //$('#inputnewsubprojectcfsarrivaldate').val(subproject.arrival_date_full);
                    $('#inputnewsubprojectcfsmagayawhr').val(subproject.whr);
                    //$('#inputnewsubprojectcfslfd').val(subproject.lfd_full);
                    $('#inputnewsubprojectcfsoutdatecr').val(formatDate(subproject.out_date_cr, 'full'));
                    $('#inputnewsubprojectcfsmagayacr').val(subproject.cr);
                    //$('#inputnewsubprojectcfsdalfd').val(subproject.days_after_lfd);
                    $('#inputnewsubprojectcfscuft').val(subproject.cuft);
                    $('#inputnewsubprojectcfswhstoragecharges').val(subproject.wh_storage_charge);
                    $('#inputnewsubprojectcfsdeliverycharges').val(subproject.delivery_charges);
                    $('#inputnewsubprojectcfscharges').val(subproject.charges);
                    $('#inputnewsubprojectcfsnotes').val(subproject.notes);

                    setDateAndLock('#inputnewsubprojectcfsarrivaldate', formatDate(master.arrival_date, 'full'));
                    setDateAndLock('#inputnewsubprojectcfslfd', formatDate(master.lfd, 'full'));
                    updateDaysAfterLFD();

                    if (subproject.agent === "Yes") {
                        $('#checkAgent').prop('checked', true);
                    } else {
                        $('#checkAgent').prop('checked', false);
                    }

                    if (subproject.collected === "Yes") {
                        $('#checkCollected').prop('checked', true);
                    } else {
                        $('#checkCollected').prop('checked', false);
                    }

                    $('#showcfssubproject').modal('hide');
                    $('#newcfssubproject').modal('show');
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
                text: 'The subproject could not be identified.',
                icon: 'error',
                confirmButtonText: 'OK',
            });
        }
    });

    //Ejecutar el guardado de cambios de masters
    $('body').on('click', '#editcfssubproject', function (e) {
        e.preventDefault();
        let isValidSubproject = true;

        // Validar Part Numbers dinámicos
        $('.dynamicPartNumber').each(function () {
            const field = $(this);
            const fieldValue = field.val();
            const errorElement = $(`#error-${field.attr('id')}`);
            const customErrorMessage = "Part Number is required"
            if (fieldValue === null || fieldValue === undefined || fieldValue === "") {
                field.siblings(".select2").find(".select2-selection").addClass("is-invalid");
                field.addClass('is-invalid');
                errorElement.text(customErrorMessage || 'This field is required.');
                isValidSubproject = false;
            } else {
                field.removeClass('is-invalid');
                errorElement.text('');
                field.siblings(".select2").find(".select2-selection").removeClass("is-invalid");
            }
        });
        
        // Validar Part Numbers dinámicos
        $('.dynamicHBLReference').each(function () {
            const field = $(this);
            const fieldValue = field.val();
            const errorElement = $(`#error-${field.attr('id')}`);
            const customErrorMessage = "HBL reference is required"
            if (fieldValue === "") {
                field.addClass('is-invalid');
                errorElement.text(customErrorMessage);
                isValidSubproject = false;
            } else {
                field.removeClass('is-invalid');
                errorElement.text('');
            }
        });
        
        formFieldsSubproject.forEach(fieldId => {
            const field = $("#" + fieldId);
            const errorElement = $('#error-' + fieldId);
            const isSelect2 = field.hasClass("searchCustomer") || field.hasClass("searchcfscomment") || field.hasClass("searchcustomerreleasecomment");
    
            if (isSelect2) {
                handleSelect2EventsSubprojects(field, errorElement); // Validación para select2
                if (field.hasClass('is-invalid')) {
                    isValidSubproject = false;
                }
            } else {
                const fieldValue = field.val().trim();
                if (fieldValue === '') {
                    isValidSubproject = false;
                    validateFieldSubprojects(field, errorElement); // Validación para campos normales
                }
            }
        });
    
        if (!isValidSubproject) {
            const firstInvalidField = $('.is-invalid').first();
            if (firstInvalidField.length) {
                firstInvalidField.focus();
            }
            return;
        }

        // Llenar los arrays de HBL References y Part Numbers
        let hblReferencesArray = [];
        let partNumbersArray = [];

        // Llenar el array de hbl_references[] recorriendo los campos de HBL References
        $('.dynamicHBLReference').each(function () {
            const fieldValue = $(this).val();
            if (fieldValue) {
                hblReferencesArray.push(fieldValue);
            }
        });

        // Llenar el array de part_numbers[] recorriendo los campos de Part Numbers
        $('.dynamicPartNumber').each(function () {
            const fieldValue = $(this).val();
            if (fieldValue) {
                partNumbersArray.push(fieldValue);
            }
        });

        let formData = new FormData($('#createcfssubproject')[0]);

        hblReferencesArray.forEach((value) => {
            formData.append('hbl_references[]', value);
        });
        
        partNumbersArray.forEach((value) => {
            formData.append('part_numbers[]', value);
        });

        Swal.fire({
            title: 'Updating subproject...',
            text: 'Please wait while the subproject is being updated.',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        $.ajax({
            url: 'editNewSubproject',
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function (response){
                Swal.close();
                Swal.fire({
                    icon: 'success',
                    title: '¡Success!',
                    text: 'Subproject successfully edited.',
                    confirmButtonText: 'Ok'
                }).then(() =>{
                    // Actualizamos la variable global con los nuevos datos
                    window.projectsData = response.projects;
                    //window.mastersData = response.masters;
                    // Guardamos el valor actual del filtro
                    const currentFilterproject = table.search();
                    // Re-renderizamos la tabla
                    renderProjectsTableNew(window.projectsData);
                    // Restauramos el filtro anterior
                    table.search(currentFilterproject).draw();
                    // Actualizamos la variable global con los nuevos datos
                    window.subprojectsData = response.subprojects;
                    // Guardamos el valor actual del filtro
                    const currentFilter = tableSubprojects.search();
                    // Re-renderizamos la tabla
                    renderSubprojectsTable(window.subprojectsData, response.pkproject);
                    // Restauramos el filtro anterior
                    tableSubprojects.search(currentFilter).draw();
                    $('#newcfssubproject').modal('hide');
                    $('#showcfssubproject').modal('show');
                });
            },
            error: function (xhr, status, error) {
                Swal.close();

                // Limpia los errores anteriores
                $('input, select').removeClass('is-invalid');
                $('.invalid-feedback').text(''); // Vaciar mensajes de error
                $('.select2-selection').removeClass('is-invalid'); // También eliminar la clase del contenedor de select2
    
                let errors = xhr.responseJSON.errors;
                let generalMessage = xhr.responseJSON?.message || 'There was a problem updating the Master. Please try again.';
    
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
                    text: generalMessage,
                    confirmButtonText: 'Ok'
                });
            }
        })
    });
});



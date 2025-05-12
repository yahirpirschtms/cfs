@auth
    @extends('layouts.app-master')

    @section('title', 'CFS')

    @section('content')

    <script>
        window.projectsData = @json($projects);
        window.mastersData = [];
        window.subprojectsData = [];
    </script>

        <div class="my-4">
            @if(in_array('add_new', Auth::user()->permissions))

                <div id="head_buttons_newcfsboard" class="mx-4 mt-4 " style=" background-color:white; position:fixed; left:0; right:0; top:80px; padding:10px; padding-bottom:0; z-index:10;" >
                    <div class="my-4 d-flex justify-content-center align-items-center">
                        <h2 class="gradient-text text-capitalize fw-bolder" style="">CFS Board</h2>
                    </div>

                    <!--Botones Añadir y refresh-->
                    <div class="d-flex justify-content-end mt-4 mx-4 mb-2">
                        <h5 class="text-nowrap me-4  py-0 fw-bold mt-auto text-capitalize " style="color: #1e4877">Project List</h5>
                        <div style="position: relative; display: inline-block; width: 100%;" class="me-4">
                            <i 
                                class="fa-solid fa-magnifying-glass" 
                                style="position: absolute; top: 50%; left: 10px; transform: translateY(-50%); color: #6c757d; cursor: pointer;"
                                onclick="document.getElementById('searchgeneralcfsboard').focus()">
                            </i>
                            <input 
                                class="form-control form-control-sm" 
                                type="search" 
                                placeholder="Search" 
                                name="searchgeneralcfsboard" 
                                id="searchgeneralcfsboard" 
                                aria-label="Search" 
                                style="padding-left: 30px;">
                        </div>
                        <!--<button type="button" style="color: white;" class="btn me-2 btn-success" id="exportfile" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Export File">
                            <i class="fa-solid fa-file-export"></i>
                        </button>-->
                        <!--<button type="button" style="color: white;" class="btn me-2 btn-primary" id="refreshemptytrailertable" data-url="" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Refresh Data">
                            <i class="fa-solid fa-arrows-rotate"></i>
                        </button>-->
                        <button type="button" style="color: white;" class="btn me-2 btn-sm btn-success" id="openmodalnewcfsproject" data-url="" data-bs-placement="top" title="Add project">
                                <i class="fa-solid fa-plus"></i>
                        </button>
                        
                    </div>

                    <div id="filtersapplied" class=" d-flex overflow-x-auto" style="scrollbar-width: none; margin:0">

                        <div class="col-auto" id="emptytrailerfilterdividtrailer" style="display:none">
                            <div style="background-color:rgb(13, 82, 200); border-radius:0.5rem; width:fit-content; display:flex; flex-wrap:nowrap" class="mb-3 me-2">
                                <btn id="emptytrailerfilterbtnidtrailer" style="background-color: unset; color:white; white-space:nowrap; align-content:center; font-size: small;" class="ms-2 me-2">ID Trailer:</btn>
                                <input id="emptytrailerfilterinputidtrailer" name="emptytrailerfilterinputidtrailer" value="" style="border:unset; width:fit-content;  color:white; background-color:rgb(13, 82, 200); font-size: small;text-align:center" type="text" class="">
                                <button id="emptytrailerfilterbuttonidtrailer" style="border:unset; background-color:rgb(13, 82, 200); color:white; font-size: small;" class="btn">X</button>
                            </div>
                        </div>

                        <div class="col-auto" id="emptytrailerfilterdivdateofstatus" style="display:none">
                            <div style="background-color:rgb(13, 82, 200); border-radius:0.5rem; width:fit-content; display:flex; flex-wrap:nowrap; align-items:center" class="mb-3 me-2">
                                <btn id="emptytrailerfilterbtndateofstatus" style="background-color: unset; color:white; white-space:nowrap; align-content:center; font-size: small;" class="ms-2 me-2">Date Of Status:</btn>
                                <input id="emptytrailerfilterinputdateofstartstatus" name="emptytrailerfilterinputdateofstartstatus" value="" style="border:unset;  color:white; width:fit-content ;background-color:rgb(13, 82, 200); font-size: small;text-align:center" type="text" class="me-2"> 
                                <p style="text-align:center; border:unset;  color:white; background-color:rgb(13, 82, 200); font-size: small; margin:0;">-</p>
                                <input id="emptytrailerfilterinputdateofendstatus" name="emptytrailerfilterinputdateofendstatus" value="" style="border:unset;  color:white; width: fit-content;  background-color:rgb(13, 82, 200); font-size: small;text-align:center" type="text" class="mx-2">
                                <button id="emptytrailerfilterbuttondateofstatus" style="border:unset; background-color:rgb(13, 82, 200); color:white; font-size: small;" class="btn">X</button>
                            </div>
                        </div>

                        <div class="col-auto" id="emptytrailerfilterdivpalletsontrailer" style="display:none">
                            <div style="background-color:rgb(13, 82, 200); border-radius:0.5rem; width:fit-content; display:flex; flex-wrap:nowrap" class="input-group mb-3 me-2">
                                <btn id="emptytrailerfilterbtnpalletsontrailer" style="background-color: unset; color:white; white-space:nowrap; align-content:center; font-size: small;" class="ms-2 me-2">Pallets On Trailer:</btn>
                                <input id="emptytrailerfilterinputpalletsontrailer" name="emptytrailerfilterinputpalletsontrailer" value="" style="border:unset; width:fit-content;  color:white; background-color:rgb(13, 82, 200); font-size: small; text-align:center" type="text" class="" >
                                <button id="emptytrailerfilterbuttonpalletsontrailer" style="border:unset; background-color:rgb(13, 82, 200); color:white; font-size: small;" class="btn">X</button>
                            </div>
                        </div>

                        <div class="col-auto" id="emptytrailerfilterdivpalletsonfloor" style="display:none">
                            <div style="background-color:rgb(13, 82, 200); border-radius:0.5rem; width:fit-content; display:flex; flex-wrap:nowrap" class="input-group mb-3 me-2">
                                <btn id="emptytrailerfilterbtnpalletsonfloor" style="background-color: unset; color:white; white-space:nowrap; align-content:center; font-size: small;" class="ms-2 me-2">Pallets On Floor</btn>
                                <input id="emptytrailerfilterinputpalletsonfloor" name="emptytrailerfilterinputpalletsonfloor" value="" style="border:unset; width:fit-content;  color:white; background-color:rgb(13, 82, 200); font-size: small; text-align:center" type="text" class="" >
                                <button id="emptytrailerfilterbuttonpalletsonfloor" style="border:unset; background-color:rgb(13, 82, 200); color:white; font-size: small;" class="btn">X</button>
                            </div>
                        </div>

                        <div class="col-auto" id="emptytrailerfilterdivcarrier" style="display:none">
                            <div style="background-color:rgb(13, 82, 200); border-radius:0.5rem; width:fit-content; display:flex; flex-wrap:nowrap" class="input-group mb-3 me-2">
                                <btn id="emptytrailerfilterbtncarrier" style="background-color: unset; color:white; white-space:nowrap; align-content:center; font-size: small;" class="ms-2 me-2">Carrier:</btn>
                                <input id="emptytrailerfilterinputcarrier" name="" value="" style="border:unset; width:fit-content;  color:white; background-color:rgb(13, 82, 200); font-size: small; text-align:center" type="text" class="">
                                <input type="text" style="display: none;" name="emptytrailerfilterinputcarrierpk" id="emptytrailerfilterinputcarrierpk" value="">
                                <button id="emptytrailerfilterbuttoncarrier" style="border:unset; width:fit-content;  color:white; background-color:rgb(13, 82, 200); font-size: small;" class="btn">X</button>
                            </div>
                        </div>

                        <div class="col-auto" id="emptytrailerfilterdivcarriercheckbox" style="display:none">
                            <div style="background-color:rgb(13, 82, 200); border-radius:0.5rem; width:fit-content; display:flex; flex-wrap:nowrap" class="input-group mb-3 me-2">
                                <btn id="emptytrailerfilterbtncarriercheckbox" style="background-color: unset; color:white; white-space:nowrap; align-content:center; font-size: small;" class="ms-2 me-2">Carrier:</btn>
                                <input id="emptytrailerfilterinputcarriercheckbox" name="emptytrailerfilterinputavailabilityindicatorcheckbox" value="" style="border:unset; width:fit-content;  color:white; background-color:rgb(13, 82, 200); font-size: small; text-align:center" type="text" class="">
                                <input type="text" style="display:none" name="emptytrailerfilterinputcarriercheckboxpk" id="emptytrailerfilterinputcarriercheckboxpk" value="">
                                <button id="emptytrailerfilterbuttoncarriercheckbox" style="border:unset; background-color:rgb(13, 82, 200); color:white; font-size: small;" class="btn">X</button>
                            </div>
                        </div>

                        <div class="col-auto" id="emptytrailerfilterdivavailabilityindicator" style="display: none;">
                            <div style="background-color:rgb(13, 82, 200); border-radius:0.5rem; width:fit-content; display:flex; flex-wrap:nowrap" class="input-group mb-3 me-2">
                                <btn id="emptytrailerfilterbtnavailabilityindicator" style="background-color: unset; color:white; white-space:nowrap; align-content:center; font-size: small;" class="ms-2 me-2">Availability Indicator:</btn>
                                <input id="emptytrailerfilterinputavailabilityindicator" value="" style="border:unset; width:fit-content;  color:white; background-color:rgb(13, 82, 200); font-size: small; text-align:center" type="text" class="">
                                <input type="text" style="display: none;" name="emptytrailerfilterinputavailabilityindicatorpk" id="emptytrailerfilterinputavailabilityindicatorpk" value="">
                                <button id="emptytrailerfilterbuttonavailabilityindicator" style="border:unset; width:fit-content;  color:white; background-color:rgb(13, 82, 200); font-size: small;" class="btn">X</button>
                            </div>
                        </div>

                        <div class="col-auto" id="emptytrailerfilterdivavailabilityindicatorcheckbox" style="display:none">
                            <div style="background-color:rgb(13, 82, 200); border-radius:0.5rem; width:fit-content; display:flex; flex-wrap:nowrap" class="input-group mb-3 me-2">
                                <btn id="emptytrailerfilterbtnavailabilityindicatorcheckbox" style="background-color: unset; color:white; white-space:nowrap; align-content:center; font-size: small;" class="ms-2 me-2">Availability Indicator:</btn>
                                <input id="emptytrailerfilterinputavailabilityindicatorcheckbox" name="emptytrailerfilterinputavailabilityindicatorcheckbox" value="" style="border:unset; width:fit-content;  color:white; background-color:rgb(13, 82, 200); font-size: small; text-align:center" type="text" class="">
                                <input type="text" style="display:none" name="emptytrailerfilterinputavailabilityindicatorcheckboxpk" id="emptytrailerfilterinputavailabilityindicatorcheckboxpk" value="">
                                <button id="emptytrailerfilterbuttonavailabilityindicatorcheckbox" style="border:unset; background-color:rgb(13, 82, 200); color:white; font-size: small;" class="btn">X</button>
                            </div>
                        </div>

                        <div class="col-auto" id="emptytrailerfilterdivlocation" style="display: none;">
                            <div style="background-color:rgb(13, 82, 200); border-radius:0.5rem; width:fit-content; display:flex; flex-wrap:nowrap" class="input-group mb-3 me-2">
                                <btn id="emptytrailerfilterbtnlocation" style="background-color: unset; color:white; white-space:nowrap; align-content:center; font-size: small;" class="ms-2 me-2">Location:</btn>
                                <input id="emptytrailerfilterinputlocation" value="" style="border:unset; width:fit-content;  color:white; background-color:rgb(13, 82, 200); font-size: small; text-align:center" type="text" class="">
                                <input type="text" style="display:none" name="emptytrailerfilterinputlocationpk" id="emptytrailerfilterinputlocationpk" value="">
                                <button id="emptytrailerfilterbuttonlocation" style="border:unset; width:fit-content;  color:white; background-color:rgb(13, 82, 200); font-size: small;" class="btn">X</button>
                            </div>
                        </div>

                        
                        <div class="col-auto" id="emptytrailerfilterdivlocationcheckbox" style="display:none">
                            <div style="background-color:rgb(13, 82, 200); border-radius:0.5rem; width:fit-content; display:flex; flex-wrap:nowrap" class="input-group mb-3 me-2">
                                <btn id="emptytrailerfilterbtnlocationcheckbox" style="background-color: unset; color:white; white-space:nowrap; align-content:center; font-size: small;" class="ms-2 me-2">Location:</btn>
                                <input id="emptytrailerfilterinputlocationcheckbox" name="emptytrailerfilterinputlocationcheckbox" value="" style="border:unset; width:fit-content;  color:white; background-color:rgb(13, 82, 200); font-size: small; text-align:center" type="text" class="">
                                <input type="text" style="display:none" name="emptytrailerfilterinputlocationcheckboxpk" id="emptytrailerfilterinputlocationcheckboxpk" value="">
                                <button id="emptytrailerfilterbuttonlocationcheckbox" style="border:unset; background-color:rgb(13, 82, 200); color:white; font-size: small;" class="btn">X</button>
                            </div>
                        </div>

                        <div class="col-auto" id="emptytrailerfilterdivdatein" style="display: none;">
                            <div style="background-color:rgb(13, 82, 200); border-radius:0.5rem; width:fit-content; display:flex; flex-wrap:nowrap; align-items:center" class="input-group mb-3 me-2">
                                <btn id="emptytrailerfilterbtndatein" style="background-color: unset; color:white; white-space:nowrap; align-content:center; font-size: small;" class="ms-2 me-2">Date In:</btn>
                                <input id="emptytrailerfilterinputstartdatein" name="emptytrailerfilterinputstartdatein" value="" style="border:unset; width:fit-content;  color:white; background-color:rgb(13, 82, 200); font-size: small; text-align:center" type="text" class="">
                                <p style="text-align:center; border:unset;  color:white; background-color:rgb(13, 82, 200); font-size: small; margin:0;">-</p>
                                <input id="emptytrailerfilterinputenddatein" name="emptytrailerfilterinputenddatein" value="" style="border:unset; width:fit-content;  color:white; background-color:rgb(13, 82, 200); font-size: small; text-align:center" type="text" class="">
                                <button id="emptytrailerfilterbuttondatein" style="border:unset; width:fit-content;  color:white; background-color:rgb(13, 82, 200); font-size: small;" class="btn">X</button>
                            </div>
                        </div>

                        <!--<div class="col-auto" id="emptytrailerfilterdivdateout" style="display:none">
                            <div style="background-color:rgb(13, 82, 200); border-radius:0.5rem; width:fit-content; display:flex; flex-wrap:nowrap; align-items:center" class="input-group mb-3 me-2">
                                <btn id="emptytrailerfilterbtndateout" style="background-color: unset; color:white; white-space:nowrap; align-content:center; font-size: small;" class="ms-2 me-2">Date Out:</btn>
                                <input id="emptytrailerfilterinputstartdateout" name="emptytrailerfilterinputstartdateout" value="" style="border:unset; width:fit-content;  color:white; background-color:rgb(13, 82, 200); font-size: small; text-align:center" type="text" class="">
                                <p style="text-align:center; border:unset;  color:white; background-color:rgb(13, 82, 200); font-size: small; margin:0;">-</p>
                                <input id="emptytrailerfilterinputenddateout" name="emptytrailerfilterinputenddateout" value="" style="border:unset; width:fit-content;  color:white; background-color:rgb(13, 82, 200); font-size: small; text-align:center" type="text" class="">
                                <button id="emptytrailerfilterbuttondateout" style="border:unset; width:fit-content;  color:white; background-color:rgb(13, 82, 200); font-size: small;" class="btn">X</button>
                            </div>
                        </div>

                        <div class="col-auto" id="emptytrailerfilterdivtransactiondate" style="display: none;">
                            <div style="background-color:rgb(13, 82, 200); border-radius:0.5rem; width:fit-content; display:flex; flex-wrap:nowrap; align-items:center" class="input-group mb-3 me-2">
                                <btn id="emptytrailerfilterbtntransactiondate" style="background-color: unset; color:white; white-space:nowrap; align-content:center; font-size: small;" class="ms-2 me-2">Transaction Date:</btn>
                                <input id="emptytrailerfilterinputstarttransactiondate" name="emptytrailerfilterinputstarttransactiondate" value="" style="border:unset; width:fit-content;  color:white; background-color:rgb(13, 82, 200); font-size: small; text-align:center" type="text" class="">
                                <p style="text-align:center; border:unset;  color:white; background-color:rgb(13, 82, 200); font-size: small; margin:0;">-</p>
                                <input id="emptytrailerfilterinputendtransactiondate" name="emptytrailerfilterinputendtransactiondate" value="" style="border:unset; width:fit-content;  color:white; background-color:rgb(13, 82, 200); font-size: small; text-align:center" type="text" class="">
                                <button id="emptytrailerfilterbuttontransactiondate" style="border:unset; width:fit-content;  color:white; background-color:rgb(13, 82, 200); font-size: small;" class="btn">X</button>
                            </div>
                        </div>-->

                        <div class="col-auto" id="emptytrailerfilterdivusername" style="display:none">
                            <div style="background-color:rgb(13, 82, 200); border-radius:0.5rem; width:fit-content; display:flex; flex-wrap:nowrap" class="input-group mb-3 me-2">
                                <btn id="emptytrailerfilterbtnusername" style="background-color: unset; color:white; white-space:nowrap; align-content:center; font-size: small;" class="ms-2 me-2">Username:</btn>
                                <input id="emptytrailerfilterinputusername" name="emptytrailerfilterinputusername" value="" style="border:unset; width:fit-content;  color:white; background-color:rgb(13, 82, 200); font-size: small; text-align:center" type="text" class="">
                                <button id="emptytrailerfilterbuttonusername" style="border:unset; background-color:rgb(13, 82, 200); color:white; font-size: small;" class="btn">X</button>
                            </div>
                        </div>

                    </div>
                </div>
                
                <!--Contenido General Pagina-->
                <div class="px-2 mb-4" style="margin-top: 290px;">
                    <!-- Modal para añadir nuevos cfs projects-->
                    <div class="modal fade" id="neweditcfsproject" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticnewcfsproject" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content border border-3" style="--bs-border-opacity: .5;">
                                <div class="modal-header mx-4" style="border-bottom:none">
                                    <h1 class="modal-title mt-4 fs-4 gradient-text text-capitalize fw-bolder" id="staticnewcfsproject">New Project</h1>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <form id="createeditnewcfsproject" class="centered-form">
                                        @csrf
                                            <div class="row gx-5">
                                                <div class="mb-3 col-md-6">
                                                    <label for="inputnewcfsprojectprojectid" class="form-label text-nowrap text-capitalize" style="font-weight:500">Proyect ID</label>
                                                    <input type="text" class="form-control" id="inputnewcfsprojectprojectid" name="inputnewcfsprojectprojectid">
                                                    <div class="invalid-feedback" id="error-inputnewcfsprojectprojectid"></div>
                                                </div>

                                                <div class="mb-3 col-md-6">
                                                    <label for="inputnewcfsprojectinvoice" class="form-label text-nowrap text-capitalize" style="font-weight:500">Invoice</label>
                                                    <select class="form-select searchInvoice" id="inputnewcfsprojectinvoice" name="inputnewcfsprojectinvoice" data-error-message="Invoice is required.">
                                                        <option selected disabled hidden></option>    
                                                    </select>
                                                    <div class="invalid-feedback" id="error-inputnewcfsprojectinvoice"></div>
                                                </div>

                                                <div class="mb-3 col-md-6">
                                                    <label for="inputnewcfsprojectmonth" class="form-label text-nowrap text-capitalize" style="font-weight:500">Month</label>
                                                    <input type="text" class="form-control datepicker" id="inputnewcfsprojectmonth" name="inputnewcfsprojectmonth" placeholder="MM/DD/YYYY">
                                                    <div class="invalid-feedback" id="error-inputnewcfsprojectmonth"></div>
                                                </div>

                                                <div class="mb-3 col-md-6">
                                                    <label for="inputnewcfspeojectdrayageperson" class="form-label text-nowrap text-capitalize" style="font-weight:500">Drayage Person</label>
                                                    <select class="form-select searchDrayageUser" id="inputnewcfspeojectdrayageperson" name="inputnewcfspeojectdrayageperson" data-error-message="Drayage Person is required.">
                                                        <option selected disabled hidden></option>    
                                                    </select>
                                                    <div class="invalid-feedback" id="error-inputnewcfspeojectdrayageperson"></div>
                                                </div>

                                                <div class=" col-md-6">
                                                    <label for="inputnewcfsprojectdrayagefiletype" class="form-label text-nowrap text-capitalize" style="font-weight:500">Drayage File Type</label>
                                                    <select class="form-select searchDrayageFileType" id="inputnewcfsprojectdrayagefiletype" name="inputnewcfsprojectdrayagefiletype" data-error-message="Drayage File Type is required.">
                                                        <option selected disabled hidden></option>    
                                                    </select>
                                                    <div class="invalid-feedback" id="error-inputnewcfsprojectdrayagefiletype"></div>
                                                </div>
                                            </div>
                                    </form>
                                </div>
                                <div class="modal-footer mx-4 mb-4" style="border-top:none">
                                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancel</button>
                                    <button type="button" class="btn btn-primary" id="saveeditnewcfsproject">Save</button>
                                </div>
                            </div>
                        </div>
                    </div>   
                    <!--Tabla para mostrar los proyectos existentes-->
                    <div class="my-5 table_style">
                        <table id="projectsTable" class="table table-sm ">
                            <thead>
                                <tr>
                                    <th scope="col">Project ID</th>
                                    <th scope="col">Invoice</th>
                                    <th scope="col">Month</th>
                                    <th scope="col">User</th>
                                    <th scope="col">File</th>
                                    <th scope="col">Options</th>
                                    <th scope="col">MBL</th>
                                    <th scope="col">Container</th>
                                    <th scope="col">Pieces</th>
                                    <th scope="col">Pallets</th>
                                    <th scope="col">ETA</th>
                                    <th scope="col">Arrival</th>
                                    <th scope="col">LDF</th>
                                    <th scope="col">House</th>
                                    <th scope="col">Options</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>

                        </table>
                    </div>
                    <!-- Modal visualización de los masters-->
                    <div class="modal fade" id="showcfsmaster" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticshowcfsmaster" aria-hidden="true">
                        <div class="modal-dialog modal-xl modal-dialog-centered">
                            <div class="modal-content border border-3" style="--bs-border-opacity: .5;">
                                <div class="modal-header mx-4" style="border-bottom:none">
                                    <h1 class="modal-title mt-4 fs-4 gradient-text text-capitalize fw-bolder" id="staticshowcfsmaster">Masters List</h1>  
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body mx-4">

                                    <div class="d-flex justify-content-end mt-4 mx-4">
                                        <div style="position: relative; display: inline-block; width: 100%;" class="me-4">
                                            <i 
                                                class="fa-solid fa-magnifying-glass" 
                                                style="position: absolute; top: 50%; left: 10px; transform: translateY(-50%); color: #6c757d; cursor: pointer;"
                                                onclick="document.getElementById('searchgeneralcfsboardmasters').focus()">
                                            </i>
                                            <input 
                                                class="form-control form-control-sm" 
                                                type="search" 
                                                placeholder="Search" 
                                                name="searchgeneralcfsboardmasters" 
                                                id="searchgeneralcfsboardmasters" 
                                                aria-label="Search" 
                                                style="padding-left: 30px;">
                                        </div>
                                        
                                        <button type="button" style="color: white;" class="btn me-2 btn-sm btn-success" id="addnewcfsmaster" data-bs-toggle="modal" data-bs-placement="top" title="Add master" data-bs-target="#newcfsmaster">
                                                <i class="fa-solid fa-plus"></i>
                                        </button>
                                        
                                    </div>
                                
                                    <div class="container my-5 table_style">
                                        <table id="MastersTable" class="table table-sm ">
                                            <thead>
                                                <tr>
                                                    <th scope="col">MBL</th>
                                                    <th scope="col">Invoiced</th>
                                                    <th scope="col">Container</th>
                                                    <th scope="col">Pallets</th>
                                                    <th scope="col">Pieces</th>
                                                    <th scope="col">ETA Port</th>
                                                    <th scope="col">Arrival Date</th>
                                                    <th scope="col">LDF</th>
                                                    <th scope="col">Subproject</th>
                                                    <th scope="col">Release Subproject</th>
                                                    <th scope="col">Notes</th>
                                                    <th scope="col">Options</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Modal añadir nuevo master-->
                    <div class="modal fade" id="newcfsmaster" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticnewcfsmaster" aria-hidden="true">
                        <div class="modal-dialog modal-lg modal-dialog-centered">
                            <div class="modal-content border border-3" style="--bs-border-opacity: .5;">
                                <div class="modal-header mx-4" style="border-bottom:none">
                                    <h1 class="modal-title mt-4 fs-4 gradient-text text-capitalize fw-bolder" id="staticnewcfsmaster">New Master</h1>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <form id="createMastercfs" class="centered-form">
                                        @csrf
                                            <div class="row gx-5">
                                                <div class="mb-3 col-md-6 col-lg-4" style="display:none">
                                                    <label for="inputnewmastercfsproyectid" class="form-label text-nowrap text-capitalize" style="font-weight:500">Proyect ID</label>
                                                    <input type="text" class="form-control" id="inputnewmastercfsproyectid" name="inputnewmastercfsproyectid">
                                                    <div class="invalid-feedback" id="error-inputnewmastercfsproyectid"></div>
                                                </div>

                                                <div class="mb-3 col-md-6 col-lg-4">
                                                    <label for="inputnewmastercfsmbl" class="form-label text-nowrap text-capitalize" style="font-weight:500">MBL</label>
                                                    <input type="text" class="form-control" id="inputnewmastercfsmbl" name="inputnewmastercfsmbl">
                                                    <div class="invalid-feedback" id="error-inputnewmastercfsmbl"></div>
                                                </div>

                                                <div class="mb-3 col-md-6 col-lg-4">
                                                    <label for="inputnewmastercfscontainernumber" class="form-label text-nowrap text-capitalize" style="font-weight:500">Container number</label>
                                                    <input type="text" class="form-control" id="inputnewmastercfscontainernumber" name="inputnewmastercfscontainernumber">
                                                    <div class="invalid-feedback" id="error-inputnewmastercfscontainernumber"></div>
                                                </div>

                                                <!--<div class="mb-3 col-md-6 col-lg-4">
                                                    <label for="inputnewmastercfstotalpieces" class="form-label text-nowrap text-capitalize" style="font-weight:500">Total Pieces</label>
                                                    <input type="number" min="0" class="form-control" id="inputnewmastercfstotalpieces" name="inputnewmastercfstotalpieces">
                                                    <div class="invalid-feedback" id="error-inputnewmastercfstotalpieces"></div>
                                                </div>

                                                <div class="mb-3 col-md-6 col-lg-4">
                                                    <label for="inputnewmastercfstotalpallets" class="form-label text-nowrap text-capitalize" style="font-weight:500">Total Pieces</label>
                                                    <input type="number" min="0" class="form-control" id="inputnewmastercfstotalpallets" name="inputnewmastercfstotalpallets">
                                                    <div class="invalid-feedback" id="error-inputnewmastercfstotalpallets"></div>
                                                </div>-->

                                                <div class="mb-3 col-md-6 col-lg-4">
                                                    <label for="inputnewmastercfsetaport" class="form-label text-nowrap text-capitalize" style="font-weight:500">ETA Port</label>
                                                    <input type="text" class="form-control datetimepicker" id="inputnewmastercfsetaport" name="inputnewmastercfsetaport" placeholder="MM/DD/YYYY  HH:MM:SS">
                                                    <div class="invalid-feedback" id="error-inputnewmastercfsetaport"></div>
                                                </div>

                                                <div class="mb-3 col-md-6 col-lg-4">
                                                    <label for="inputnewmastercfsarrivaldate" class="form-label text-nowrap text-capitalize" style="font-weight:500">Arrival Date</label>
                                                    <input type="text" class="form-control datetimepicker" id="inputnewmastercfsarrivaldate" name="inputnewmastercfsarrivaldate" placeholder="MM/DD/YYYY  HH:MM:SS">
                                                    <div class="invalid-feedback" id="error-inputnewmastercfsarrivaldate"></div>
                                                </div>

                                                <div class="mb-3 col-md-6 col-lg-4">
                                                    <label for="inputnewmastercfslfd" class="form-label text-nowrap text-capitalize" style="font-weight:500">LFD</label>
                                                    <input type="text" class="form-control datetimepicker" id="inputnewmastercfslfd" name="inputnewmastercfslfd" placeholder="MM/DD/YYYY  HH:MM:SS">
                                                    <div class="invalid-feedback" id="error-inputnewmastercfslfd"></div>
                                                </div>

                                                <div class="mb-3 col-md-6 col-lg-4" style="display:none;">
                                                    <label for="inputnewmastercfsnotes" class="form-label text-nowrap text-capitalize" style="font-weight:500">Notes</label>
                                                    <textarea rows="1" type="text" class="form-control" id="inputnewmastercfsnotes" name="inputnewmastercfsnotes"></textarea>
                                                    <div class="invalid-feedback" id="error-inputnewmastercfsnotes"></div>
                                                </div>
                                            </div>
                                    </form>
                                </div>
                                <div class="modal-footer mx-4 mb-4" style="border-top:none">
                                    <!--<button type="button" class="btn btn-danger" data-bs-dismiss="modal" data-bs-toggle="modal" data-bs-target="#showcfsmaster" id="cancelAndOpenMasters">Cancel</button>-->
                                    <button type="button" class="btn btn-danger"  data-bs-dismiss="modal">Cancel</button>
                                    <button type="button" class="btn btn-primary" id="savecfsmaster">Save</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Modal visualización de los subprojects-->
                    <div class="modal fade" id="showcfssubproject" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticshowcfssubproject" aria-hidden="true">
                        <div class="modal-dialog modal-xxxl modal-dialog-centered">
                            <div class="modal-content border border-3" style="--bs-border-opacity: .5;">
                                <div class="modal-header mx-4" style="border-bottom:none">
                                    <h1 class="modal-title mt-4 fs-4 gradient-text text-capitalize fw-bolder" id="staticshowcfssubproject">Subprojects List</h1>         
                                    <button type="button" class="btn-close close-staticshowcfssubproject" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body mx-4">

                                    <div class="d-flex justify-content-end mt-4 mx-4">
                                        <div style="position: relative; display: inline-block; width: 100%;" class="me-4">
                                            <i 
                                                class="fa-solid fa-magnifying-glass" 
                                                style="position: absolute; top: 50%; left: 10px; transform: translateY(-50%); color: #6c757d; cursor: pointer;"
                                                onclick="document.getElementById('searchgeneralcfsboardsubprojects').focus()">
                                            </i>
                                            <input 
                                                class="form-control form-control-sm" 
                                                type="search" 
                                                placeholder="Search" 
                                                name="searchgeneralcfsboardsubprojects" 
                                                id="searchgeneralcfsboardsubprojects" 
                                                aria-label="Search" 
                                                style="padding-left: 30px;">
                                        </div>
                                        
                                        <!--<button type="button" style="color: white;" class="btn me-2 btn-sm btn-success skip-master-on-close" id="addnewcfssubproject" data-projectid="" data-mbl="" data-bs-toggle="modal" data-bs-placement="top" title="Add subproject" data-bs-target="#newcfssubproject">-->
                                        <button type="button" style="color: white;" class="btn me-2 btn-sm btn-success skip-master-on-close" id="addnewcfssubproject" data-projectid="" data-mbl="">
                                                <i class="fa-solid fa-plus"></i>
                                        </button>
                                    </div>

                                    <div class="my-5 table_style">
                                        <table id="SubprojectsTable" class="table table-sm ">
                                            <thead>
                                                <tr>
                                                    <th scope="col">Subproject ID</th>
                                                    <th scope="col">HBL</th>
                                                    <th scope="col">Pieces</th>
                                                    <th scope="col">Pallets</th>
                                                    <th scope="col">Works Pall.</th>
                                                    <th scope="col">Pallets Ex.</th>
                                                    <th scope="col">Customer</th>
                                                    <th scope="col">Agent</th>
                                                    <th scope="col">Product</th>
                                                    <th scope="col">CFS</th>
                                                    <th scope="col">Arrival</th>
                                                    <th scope="col">WHR ID</th>
                                                    <th scope="col">LFD</th>
                                                    <th scope="col">Custom R.</th>
                                                    <th scope="col">OUT Date CR</th>
                                                    <th scope="col">CR ID</th>
                                                    <th scope="col">Charges</th>
                                                    <th scope="col" class="text-wrap">Days after LFD</th>
                                                    <th scope="col">Cuft</th>
                                                    <th scope="col">Notes</th>
                                                    <th scope="col">Options</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            </tbody>
                                        </table>
                                    </div>


                                    <!--<div class="my-5 table_style">
                                        <table id="SubprojectsTablee" class="table table-sm ">
                                            <thead>
                                                <tr>
                                                    <th scope="col">Subproject ID</th>
                                                    <th scope="col">HBL</th>
                                                    <th scope="col">Pieces</th>
                                                    <th scope="col">Pallets</th>
                                                    <th scope="col">Works Pall.</th>
                                                    <th scope="col">Pallets Ex.</th>
                                                    <th scope="col">Customer</th>
                                                    <th scope="col">Agent</th>
                                                    <th scope="col">Product</th>
                                                    <th scope="col">CFS</th>
                                                    <th scope="col">Arrival</th>
                                                    <th scope="col">WHR ID</th>
                                                    <th scope="col">LFD</th>
                                                    <th scope="col">Custom R.</th>
                                                    <th scope="col">OUT Date CR</th>
                                                    <th scope="col">CR ID</th>
                                                    <th scope="col">Charges</th>
                                                    <th scope="col">Days after LFD</th>
                                                    <th scope="col">Cuft</th>
                                                    <th scope="col">Notes</th>
                                                    <th scope="col">Options</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td class="align-middle">DHW-384970</td>
                                                    
                                                    <td class="align-top">
                                                        <ul class="list-group list-group-flush">
                                                        <li class="align-middle text-start list-group-item px-0">
                                                                DLYBSDSE25010708
                                                            </li>
                                                            <li class="align-middle text-start list-group-item px-0">
                                                                (DLYBSDSE25010708)
                                                            </li>
                                                            <li class="align-middle text-start list-group-item px-0">
                                                                (DISODMCNSY000803)
                                                            </li>
                                                        </ul>
                                                    </td>
                                                    <td class="align-middle">850</td>
                                                    <td class="align-middle">50</td>
                                                    <td class="align-middle"><i class="text-danger fa-solid fa-circle-xmark"></i> No</td>
                                                    <td class="align-middle"><i class="text-danger fa-solid fa-circle-xmark"></i> No</td>
                                                    <td class="align-middle" style="white-space:wrap">DOLPHINS LOGISTICS</td>
                                                    <td class="align-middle"><i class="fa-solid fa-circle-check" style="color:rgb(13, 82, 200)"></i> Yes</td>
                                                    <td class="align-middle">
                                                        <ul class="list-group list-group-flush">
                                                            <li class="align-middle text-start list-group-item px-0">
                                                                DLYBSDSE25010708
                                                            </li>
                                                            <li class="align-middle text-start list-group-item px-0">
                                                                DISODMCNSY000803
                                                            </li>
                                                            <li class="align-middle text-start list-group-item px-0">
                                                                DLYBSDSE25010708
                                                            </li>
                                                            <li class="align-middle text-start list-group-item px-0">
                                                                DISODMCNSY000803
                                                            </li>
                                                        </ul>
                                                    </td>
                                                    <td class="align-middle"><i class="fa-solid fa-circle-check" style="color:rgb(13, 82, 200)"></i> Yes</td>
                                                    <td class="align-middle">03/10/2025</td>
                                                    <td class="align-middle">DHW-387484</td>
                                                    <td class="align-middle">03/10/2025</td>
                                                    <td class="align-middle"><i class="fa-solid fa-circle-check" style="color:rgb(13, 82, 200)"></i> Yes</td>
                                                    <td class="align-middle">03/10/2025</td>
                                                    <td class="align-middle">CR13645</td>
                                                    <td class="align-middle">
                                                        <ul class="list-group  list-group-flush">
                                                            <li class="d-flex justify-content-between align-items-center list-group-item px-0">
                                                                Works <span>450.456</span>
                                                            </li>
                                                            <li class="d-flex justify-content-between align-items-center list-group-item px-0">
                                                                WH Storage <span>450.456</span>
                                                            </li>
                                                            <li class="d-flex justify-content-between align-items-center list-group-item px-0">
                                                                Total <span>450.456</span>
                                                            </li>
                                                        </ul>
                                                    </td>
                                                    <td class="align-middle">4</td>
                                                    <td class="align-middle">113.01</td>
                                                    <td class="align-middle" style="white-space:wrap">CR13645 thsryh</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>-->
                                </div>
                                <div class="modal-footer mx-4 mb-4" style="border-top:none">
                                    <button type="button" class="btn btn-danger  close-staticshowcfssubproject" data-bs-dismiss="modal">Close</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Modal añadir nuevo subproject-->
                    <div class="modal fade" id="newcfssubproject" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticnewcfssubproject" aria-hidden="true">
                        <div class="modal-dialog modal-xxl modal-dialog-centered">
                            <div class="modal-content border border-3" style="--bs-border-opacity: .5;">
                                <div class="modal-header mx-4" style="border-bottom:none">
                                    <h1 class="modal-title mt-4 fs-4 gradient-text text-capitalize fw-bolder" id="staticnewcfssubproject">New House</h1>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"  data-bs-toggle="modal" data-bs-target="#showcfssubproject"></button>
                                </div>
                                <div class="modal-body">
                                    <form id="createcfssubproject" class="centered-formsubproject">
                                        @csrf
                                            <div class="row gx-5">
                                                <div class="mb-3 col-md-6 col-lg-4 col-xl-3" style="display:none">
                                                    <label for="inputnewsubprojectproyectid" class="form-label text-nowrap text-capitalize" style="font-weight:500">Proyect ID</label>
                                                    <input type="text" class="form-control" id="inputnewsubprojectproyectid" name="inputnewsubprojectproyectid">
                                                    <div class="invalid-feedback" id="error-inputnewsubprojectproyectid"></div>
                                                </div>

                                                <div class="mb-3 col-md-6 col-lg-4  col-xl-3" style="display:none">
                                                    <label for="inputnewsubprojectcfsmbl" class="form-label text-nowrap text-capitalize" style="font-weight:500">MBL</label>
                                                    <input type="text" class="form-control" id="inputnewsubprojectcfsmbl" name="inputnewsubprojectcfsmbl">
                                                    <div class="invalid-feedback" id="error-inputnewsubprojectcfsmbl"></div>
                                                </div>

                                                <div class="mb-3 col-md-6 col-lg-4  col-xl-3" style="">
                                                    <label for="inputnewsubprojectcfssubprojectid" class="form-label text-nowrap text-capitalize" style="font-weight:500">Subproject ID</label>
                                                    <input type="text" class="form-control" id="inputnewsubprojectcfssubprojectid" name="inputnewsubprojectcfssubprojectid">
                                                    <div class="invalid-feedback" id="error-inputnewsubprojectcfssubprojectid"></div>
                                                </div>

                                                <div class="mb-4 col-md-6 col-lg-4  col-xl-3" style="">
                                                    <label for="inputnewsubprojectcfshbl" class="form-label text-nowrap text-capitalize" style="font-weight:500">HBL</label>
                                                    <input type="text" class="form-control" id="inputnewsubprojectcfshbl" name="inputnewsubprojectcfshbl">
                                                    <div class="invalid-feedback" id="error-inputnewsubprojectcfshbl"></div>
                                                </div>

                                                <div class="mb-3 pb-2 col-md-6 col-lg-4  col-xl-3 d-flex align-items-end justify-content-between">
                                                    <label class="form-label mb-0 me-2" style="font-weight:500">HBL</label>
                                                    <div class="ms-auto">
                                                        <button type="button" class="btn btn-sm btn-primary" id="addhblreference">
                                                            <i class="fa-solid fa-plus"></i>
                                                        </button>
                                                        <button type="button" class="btn btn-sm btn-danger ms-2" id="removehblreference">
                                                            <i class="fa-solid fa-minus"></i>
                                                        </button>
                                                    </div>
                                                </div>

                                                <div class="mb-3 col-md-6 col-lg-4  col-xl-3" style="">
                                                    <label for="inputnewsubprojectcfspieces" class="form-label text-nowrap text-capitalize" style="font-weight:500">Pieces</label>
                                                    <input type="number" min="0" value="0" class="form-control" id="inputnewsubprojectcfspieces" name="inputnewsubprojectcfspieces">
                                                    <div class="invalid-feedback" id="error-inputnewsubprojectcfspieces"></div>
                                                </div>

                                                <div class="mb-3 col-md-6 col-lg-4  col-xl-3">
                                                    <label for="inputnewsubprojectcfsworkspalletized" class="form-label" style="font-weight:500">Works/Palletized</label>
                                                    <select class="form-select searchWorksPalletized" id="inputnewsubprojectcfsworkspalletized" name="inputnewsubprojectcfsworkspalletized" data-error-message="Works/Palletized is required.">
                                                        <option selected disabled hidden></option>    
                                                    </select>
                                                    <div class="invalid-feedback" id="error-inputnewsubprojectcfsworkspalletized"></div>
                                                </div>

                                                <div class="mb-3 col-md-6 col-lg-4  col-xl-3">
                                                    <label for="inputnewsubprojectcfspalletsexchanged" class="form-label" style="font-weight:500">Pallets Exchanged</label>
                                                    <select class="form-select searchPalletsExchange" id="inputnewsubprojectcfspalletsexchanged" name="inputnewsubprojectcfspalletsexchanged" data-error-message="Pallets Exchanged is required.">
                                                        <option selected disabled hidden></option>    
                                                    </select>
                                                    <div class="invalid-feedback" id="error-inputnewsubprojectcfspalletsexchanged"></div>
                                                </div>

                                                <div class="mb-3 col-md-6 col-lg-4  col-xl-3" style="">
                                                    <label for="inputnewsubprojectcfspallets" class="form-label text-nowrap text-capitalize" style="font-weight:500">Pallets</label>
                                                    <input type="number" min="0" value="0" class="form-control" id="inputnewsubprojectcfspallets" name="inputnewsubprojectcfspallets">
                                                    <div class="invalid-feedback" id="error-inputnewsubprojectcfspallets"></div>
                                                </div>

                                                <div class="mb-3 col-md-6 col-lg-4 col-xl-3" style="">
                                                    <label for="inputnewsubprojectcfspalletizedcharges" class="form-label text-nowrap text-capitalize" style="font-weight:500">Palletized Charge</label>
                                                    <input type="text" readonly class="form-control" id="inputnewsubprojectcfspalletizedcharges" name="inputnewsubprojectcfspalletizedcharges">
                                                    <div class="invalid-feedback" id="error-inputnewsubprojectcfspalletizedcharges"></div>
                                                </div>

                                                <div class="mb-3 col-md-6 col-lg-4  col-xl-3">
                                                    <label for="inputnewsubprojectcfscustomer" class="form-label" style="font-weight:500">Customer</label>
                                                    <select class="form-select searchCustomer" id="inputnewsubprojectcfscustomer" name="inputnewsubprojectcfscustomer" data-error-message="Customer is required.">
                                                        <option selected disabled hidden></option>    
                                                    </select>
                                                    <div class="invalid-feedback" id="error-inputnewsubprojectcfscustomer"></div>
                                                </div>

                                                <div class="mb-4 col-md-6 col-lg-4  col-xl-3 d-flex align-items-end" style="font-weight:500">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" value="Yes" id="checkAgent" name="checkAgent">
                                                        <label class="form-check-label" for="checkAgent">
                                                            Agent
                                                        </label>
                                                    </div>
                                                </div>

                                                <div class="mb-3 pb-2 col-md-6 col-lg-4 col-xl-3 d-flex align-items-end justify-content-between">
                                                    <label class="form-label mb-0 me-2" style="font-weight:500">Product</label>
                                                    <div class="ms-auto">
                                                        <button type="button" class="btn btn-sm btn-primary" id="addpartnumber">
                                                            <i class="fa-solid fa-plus"></i>
                                                        </button>
                                                        <button type="button" class="btn btn-sm btn-danger ms-2" id="removepartnumber">
                                                            <i class="fa-solid fa-minus"></i>
                                                        </button>
                                                    </div>
                                                </div>

                                                <div class="mb-3 col-md-6 col-lg-4  col-xl-3">
                                                    <label for="inputnewsubprojectcfscfscomment" class="form-label" style="font-weight:500">CFS</label>
                                                    <select class="form-select searchcfscomment" id="inputnewsubprojectcfscfscomment" name="inputnewsubprojectcfscfscomment" data-error-message="CFS is required.">
                                                        <option selected disabled hidden></option>    
                                                    </select>
                                                    <div class="invalid-feedback" id="error-inputnewsubprojectcfscfscomment"></div>
                                                </div>

                                                <div class="mb-3 col-md-6 col-lg-4 col-xl-3">
                                                    <label for="inputnewsubprojectcfsarrivaldate" class="form-label text-nowrap text-capitalize" style="font-weight:500">Arrival Date</label>
                                                    <input type="text" class="form-control datetimepicker" id="inputnewsubprojectcfsarrivaldate" name="inputnewsubprojectcfsarrivaldate" placeholder="MM/DD/YYYY  HH:MM:SS">
                                                    <div class="invalid-feedback" id="error-inputnewsubprojectcfsarrivaldate"></div>
                                                </div>

                                                <div class="mb-3 col-md-6 col-lg-4 col-xl-3" style="">
                                                    <label for="inputnewsubprojectcfsmagayawhr" class="form-label text-nowrap text-capitalize" style="font-weight:500">WHR ID</label>
                                                    <input type="text" class="form-control" id="inputnewsubprojectcfsmagayawhr" name="inputnewsubprojectcfsmagayawhr">
                                                    <div class="invalid-feedback" id="error-inputnewsubprojectcfsmagayawhr"></div>
                                                </div>

                                                <div class="mb-3 col-md-6 col-lg-4 col-xl-3">
                                                    <label for="inputnewsubprojectcfslfd" class="form-label text-nowrap text-capitalize" style="font-weight:500">LFD</label>
                                                    <input type="text" class="form-control datetimepicker" id="inputnewsubprojectcfslfd" name="inputnewsubprojectcfslfd" placeholder="MM/DD/YYYY  HH:MM:SS">
                                                    <div class="invalid-feedback" id="error-inputnewsubprojectcfslfd"></div>
                                                </div>

                                                <div class="mb-3 col-md-6 col-lg-4  col-xl-3">
                                                    <label for="inputnewsubprojectcfscustomsreleasecomment" class="form-label" style="font-weight:500">Custom Release</label>
                                                    <select class="form-select searchcustomerreleasecomment" id="inputnewsubprojectcfscustomsreleasecomment" name="inputnewsubprojectcfscustomsreleasecomment" data-error-message="Custom release is required.">
                                                        <option selected disabled hidden></option>    
                                                    </select>
                                                    <div class="invalid-feedback" id="error-inputnewsubprojectcfscustomsreleasecomment"></div>
                                                </div>

                                                <div class="mb-3 col-md-6 col-lg-4 col-xl-3" style="">
                                                    <label for="inputnewsubprojectcfsoutdatecr" class="form-label text-nowrap text-capitalize" style="font-weight:500">Out Date CR</label>
                                                    <input type="text" class="form-control datetimepicker" id="inputnewsubprojectcfsoutdatecr" name="inputnewsubprojectcfsoutdatecr">
                                                    <div class="invalid-feedback" id="error-inputnewsubprojectcfsoutdatecr"></div>
                                                </div>

                                                <div class="mb-3 col-md-6 col-lg-4 col-xl-3" style="">
                                                    <label for="inputnewsubprojectcfsmagayacr" class="form-label text-nowrap text-capitalize" style="font-weight:500">CR ID</label>
                                                    <input type="text" class="form-control" id="inputnewsubprojectcfsmagayacr" name="inputnewsubprojectcfsmagayacr">
                                                    <div class="invalid-feedback" id="error-inputnewsubprojectcfsmagayacr"></div>
                                                </div>

                                                <div class="mb-3 col-md-6 col-lg-4 col-xl-3" style="">
                                                    <label for="inputnewsubprojectcfsdalfd" class="form-label text-nowrap text-capitalize" style="font-weight:500">Days After LFD</label>
                                                    <input type="text" readonly class="form-control" id="inputnewsubprojectcfsdalfd" name="inputnewsubprojectcfsdalfd">
                                                    <div class="invalid-feedback" id="error-inputnewsubprojectcfsdalfd"></div>
                                                </div>

                                                <div class="mb-3 col-md-6 col-lg-4 col-xl-3" style="">
                                                    <label for="inputnewsubprojectcfscuft" class="form-label text-nowrap text-capitalize" style="font-weight:500">Cuft</label>
                                                    <input type="text" class="form-control" id="inputnewsubprojectcfscuft" name="inputnewsubprojectcfscuft">
                                                    <div class="form-text" id="basic-addon4">1 Cumt - 35.3147 Cuft</div>
                                                    <div class="invalid-feedback" id="error-inputnewsubprojectcfscuft"></div>
                                                </div>

                                                <div class="mb-3 col-md-6 col-lg-4 col-xl-3" style="">
                                                    <label for="inputnewsubprojectcfswhstoragecharges" class="form-label text-nowrap text-capitalize" style="font-weight:500">WH Storage Charge</label>
                                                    <input type="number" min="0" readonly value="0" class="form-control" id="inputnewsubprojectcfswhstoragecharges" name="inputnewsubprojectcfswhstoragecharges">
                                                    <div class="invalid-feedback" id="error-inputnewsubprojectcfswhstoragecharges"></div>
                                                </div>

                                                <div class="mb-3 col-md-6 col-lg-4 col-xl-3" style="">
                                                    <label for="inputnewsubprojectcfsdeliverycharges" class="form-label text-nowrap text-capitalize" style="font-weight:500">Delivery Charge</label>
                                                    <input type="number" min="0" value="0" class="form-control" id="inputnewsubprojectcfsdeliverycharges" name="inputnewsubprojectcfsdeliverycharges">
                                                    <div class="invalid-feedback" id="error-inputnewsubprojectcfsdeliverycharges"></div>
                                                </div>

                                                <div class="mb-3 col-md-6 col-lg-4 col-xl-3" style="">
                                                    <label for="inputnewsubprojectcfscharges" class="form-label text-nowrap text-capitalize" style="font-weight:500">Total Charge</label>
                                                    <input type="text" readonly class="form-control" id="inputnewsubprojectcfscharges" name="inputnewsubprojectcfscharges">
                                                    <div class="invalid-feedback" id="error-inputnewsubprojectcfscharges"></div>
                                                </div>

                                                <div class="mb-4 col-md-6 col-lg-4  col-xl-3 d-flex align-items-end" style="font-weight:500">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" value="Yes" id="checkCollected" name="checkCollected">
                                                        <label class="form-check-label" for="checkCollected">
                                                            Collected
                                                        </label>
                                                    </div>
                                                </div>

                                                <div class="mb-3 col-md-6 col-lg-4 col-xl-3">
                                                    <label for="inputnewsubprojectcfsnotes" class="form-label text-nowrap text-capitalize" style="font-weight:500">Notes</label>
                                                    <textarea rows="1" type="text" class="form-control" id="inputnewsubprojectcfsnotes" name="inputnewsubprojectcfsnotes"></textarea>
                                                    <div class="invalid-feedback" id="error-inputnewsubprojectcfsnotes"></div>
                                                </div>
                                            </div>
                                    </form>
                                </div>
                                <div class="modal-footer mx-4 mb-4" style="border-top:none">
                                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal" data-bs-toggle="modal" data-bs-target="#showcfssubproject">Cancel</button>
                                    <button type="button" class="btn btn-primary" id="savecfssubproject">Save</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                </div>
            @endif  
            
            @if(in_array('edit', Auth::user()->permissions))
                <!--<div class="centered-form mb-5">
                    <div class="mt-5 mb-2">Upload files</div>

                    <div class="mb-3 col-md-6 col-lg-4">
                        <label for="formFile" class="form-label">Default file input example</label>
                        <div class="input-group">
                            <input type="file" class="form-control" id="inputGroupFile02">
                            <label class="input-group-text" for="inputGroupFile02">Upload</label>
                        </div>
                    </div>
                </div>-->
            @endif 
        </div>
    @endsection

    @section('scripts')
        <!-- Referencia al archivo JS de manera directa -->
        <script src="{{ asset('js/cfsboard.js') }}"></script> <!-- Asegúrate que el archivo esté en public/js -->
    @endsection
@endauth

@guest
    <p>Access denied, go to the <a href="/login">Login</a></p>
@endguest

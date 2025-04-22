@auth
    @extends('layouts.app-master')

    @section('title', 'CFS')

    @section('content')

    <script>
        window.projectsData = @json($projects);
        window.mastersData = [];
        window.subprojectsData = [];
    </script>

        <div class="container  my-4">
            @if(in_array('add_new', Auth::user()->permissions))

                <div id="head_buttons_newcfsboard" class="container  mt-4 " style=" background-color:white; position:fixed; left:0; right:0; top:80px; padding:10px; padding-bottom:0; z-index:10;" >
                    <div class="my-4 d-flex justify-content-center align-items-center">
                        <h2 class="gradient-text text-capitalize fw-bolder" style="">CFS Board</h2>
                    </div>

                    <!--<div class="d-flex mx-2">
                        <div class="p-2 flex-grow-1 align-self-center"><h4 class="text-capitalize fw-bold">Projects List</h4></div>
                        <div class="p-2">
                            <button type="button" style="color: white;" class="btn btn-success" id="openmodalnewcfsproject" data-url="" data-bs-toggle="modal" data-bs-placement="top" title="Add project" data-bs-target="#neweditcfsproject">
                                <i class="fa-solid fa-plus"></i>
                            </button>
                        </div>
                    </div>-->

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
                        <button type="button" style="color: white;" class="btn me-2 btn-sm btn-success" id="openmodalnewcfsproject" data-url="" data-bs-toggle="modal" data-bs-placement="top" title="Add project" data-bs-target="#neweditcfsproject">
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
                <div class="container  mb-4" style="margin-top: 290px;">
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

                                                <!--<div class="mb-3 col-md-6">
                                                    <label for="inputnewcfsprojectinvoice" class="form-label text-nowrap text-capitalize" style="font-weight:500">Invoice</label>
                                                    <select class="form-select" id="inputnewcfsprojectinvoice" name="inputnewcfsprojectinvoice">
                                                        <option value="">Choose an option</option>
                                                        <option value="yes">Yes</option>
                                                        <option value="no">No</option>  
                                                    </select>
                                                </div>-->

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
                    <div class="container my-5 table_style">
                        <table id="projectsTable" class="table table-sm ">
                            <thead>
                                <tr>
                                    <th scope="col">Project ID</th>
                                    <th scope="col">Invoice</th>
                                    <th scope="col">Month</th>
                                    <th scope="col">Drayage User</th>
                                    <th scope="col">Drayage File</th>
                                    <th scope="col">Master</th>
                                    <th scope="col">Subproject</th>
                                    <th scope="col">Release Subproject</th>
                                    <th scope="col">Pallets</th>
                                    <th scope="col">Pieces</th>
                                    <th scope="col">Options</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!--<tr>
                                    <td style="font-weight:500">fyguvhibjn</td>
                                    <td style="font-weight:500"><i class="fa-solid fa-circle-check" style="color:rgb(13, 82, 200)"></i> Yes</td>
                                    <td style="font-weight:500">04/15/2025</td>
                                    <td style="font-weight:500">Ramin</td>
                                    <td style="font-weight:500">PTT</td>
                                    <td class="">
                                        <ul class="list-group">
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                Totals
                                                <span class="badge" style="background-color: darkorange;">14</span>
                                            </li>
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                Invoiced
                                                <span class="badge" style="background-color: dodgerblue;">14</span>
                                            </li>
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                Not invoiced
                                                <span class="badge text-bg-danger">0</span>
                                            </li>
                                        </ul>
                                    </td>
                                    <td class="">
                                        <ul class="list-group">
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                Totals
                                                <span class="badge" style="background-color: mediumseagreen;">14</span>
                                            </li>
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                Invoiced
                                                <span class="badge" style="background-color: dodgerblue;">14</span>
                                            </li>
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                Not invoiced
                                                <span class="badge text-bg-danger">0</span>
                                            </li>
                                        </ul>
                                    </td>
                                    <td>
                                        <div>
                                            <div class=" ms-auto p-2">
                                                <button type="button" style="color: white; color:rgb(13, 82, 200)" class="btn btn-sm" id="openmodaleditcfsproject" data-url="" data-bs-placement="top" title="Edit project" >
                                                    <i class="fa-solid fa-pen"></i>
                                                </button>
                                            </div>
                                            <div class="p-2">
                                                <button type="button" style="" class="btn btn-sm btn-danger" id="deletenewcfsproject" data-url="" data-bs-placement="top" title="Delete project" >
                                                    <i class="fa-solid fa-trash-can"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="font-weight:500">fyguvhibjn</td>
                                    <td style="font-weight:500"><i class="text-danger fa-solid fa-circle-xmark" style=""></i> No</td>
                                    <td style="font-weight:500">04/15/2025</td>
                                    <td style="font-weight:500">Ramin</td>
                                    <td style="font-weight:500">PTT</td>
                                    <td class="">
                                        <ul class="list-group">
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                Totals
                                                <span class="badge" style="background-color: darkorange;">14</span>
                                            </li>
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                Invoiced
                                                <span class="badge" style="background-color: dodgerblue;">0</span>
                                            </li>
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                Not invoiced
                                                <span class="badge text-bg-danger">14</span>
                                            </li>
                                        </ul>
                                    </td>
                                    <td class="">
                                        <ul class="list-group">
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                Totals
                                                <span class="badge" style="background-color: mediumseagreen;">14</span>
                                            </li>
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                Invoiced
                                                <span class="badge" style="background-color: dodgerblue;">0</span>
                                            </li>
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                Not invoiced
                                                <span class="badge text-bg-danger">14</span>
                                            </li>
                                        </ul>
                                    </td>
                                    <td>
                                        <div>
                                            <div class=" ms-auto p-2">
                                                <button type="button" style="color: white; color:rgb(13, 82, 200)" class="btn btn-sm" id="openmodaleditcfsproject" data-url="" data-bs-placement="top" title="Edit project" data-bs-toggle="modal" data-bs-target="#neweditcfsproject">
                                                    <i class="fa-solid fa-pen"></i>
                                                </button>
                                            </div>
                                            <div class="p-2">
                                                <button type="button" style="color: white;" class="btn btn-sm btn-danger" id="deletenewcfsproject" data-url="" data-bs-placement="top" title="Delete project" >
                                                    <i class="fa-solid fa-trash-can"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </td>
                                </tr>-->
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

                                                <div class="mb-3 col-md-6 col-lg-4">
                                                    <label for="inputnewmastercfsnotes" class="form-label text-nowrap text-capitalize" style="font-weight:500">Notes</label>
                                                    <textarea rows="1" type="text" class="form-control" id="inputnewmastercfsnotes" name="inputnewmastercfsnotes"></textarea>
                                                    <div class="invalid-feedback" id="error-inputnewmastercfsnotes"></div>
                                                </div>
                                            </div>
                                    </form>
                                </div>
                                <div class="modal-footer mx-4 mb-4" style="border-top:none">
                                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal" data-bs-toggle="modal" data-bs-target="#showcfsmaster" id="cancelAndOpenMasters">Cancel</button>
                                    <button type="button" class="btn btn-primary" id="savecfsmaster">Save</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Modal visualización de los subprojects-->
                    <div class="modal fade" id="showcfssubproject" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticshowcfssubproject" aria-hidden="true">
                        <div class="modal-dialog modal-xl modal-dialog-centered">
                            <div class="modal-content border border-3" style="--bs-border-opacity: .5;">
                                <div class="modal-header mx-4" style="border-bottom:none">
                                    <h1 class="modal-title mt-4 fs-4 gradient-text text-capitalize fw-bolder" id="staticshowcfssubproject">Subprojects List</h1>         
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
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
                                        
                                        <button type="button" style="color: white;" class="btn me-2 btn-sm btn-success skip-master-on-close" id="addnewcfssubproject" data-bs-toggle="modal" data-bs-placement="top" title="Add subproject" data-bs-target="#newcfssubproject">
                                                <i class="fa-solid fa-plus"></i>
                                        </button>
                                    </div>

                                    <div class="container my-5 table_style">
                                        <table id="SubprojectsTable" class="table table-sm ">
                                            <thead>
                                                <tr>
                                                    <th scope="col">HBL</th>
                                                    <th scope="col">Pieces</th>
                                                    <th scope="col">Pallets</th>
                                                    <th scope="col">Works Palletized</th>
                                                    <th scope="col">Pallets Exchanged</th>
                                                    <th scope="col">Customer</th>
                                                    <th scope="col">Part Numbers</th>
                                                    <th scope="col">cfs_checkbox</th>
                                                    <th scope="col">cfs_comment</th>
                                                    <th scope="col">Arrival Date</th>
                                                    <th scope="col">WHR ID</th>
                                                    <th scope="col">LFD</th>
                                                    <th scope="col">customs_release_checkbox</th>
                                                    <th scope="col">customs_release_comment</th>
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
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Modal añadir nuevo subproject-->
                    <div class="modal fade" id="newcfssubproject" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticnewcfssubproject" aria-hidden="true">
                        <div class="modal-dialog modal-xl modal-dialog-centered">
                            <div class="modal-content border border-3" style="--bs-border-opacity: .5;">
                                <div class="modal-header mx-4" style="border-bottom:none">
                                    <h1 class="modal-title mt-4 fs-4 gradient-text text-capitalize fw-bolder" id="staticnewcfssubproject">New Subproject</h1>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <form id="createcfssubproject" class="centered-form">
                                        @csrf
                                            <div class="row gx-5">
                                                <div class="mb-3 col-md-6 col-lg-4" style="display:none">
                                                    <label for="inputnewsubprojectproyectid" class="form-label text-nowrap text-capitalize" style="font-weight:500">Proyect ID</label>
                                                    <input type="text" class="form-control" id="inputnewsubprojectproyectid" name="inputnewsubprojectproyectid">
                                                    <div class="invalid-feedback" id="error-inputnewsubprojectproyectid"></div>
                                                </div>

                                                <div class="mb-3 col-md-6 col-lg-4" style="display:none">
                                                    <label for="inputnewsubprojectcfsmbl" class="form-label text-nowrap text-capitalize" style="font-weight:500">MBL</label>
                                                    <input type="text" class="form-control" id="inputnewsubprojectcfsmbl" name="inputnewsubprojectcfsmbl">
                                                    <div class="invalid-feedback" id="error-inputnewsubprojectcfsmbl"></div>
                                                </div>

                                                <div class="mb-3 col-md-6 col-lg-4" style="">
                                                    <label for="inputnewsubprojectcfssubprojectid" class="form-label text-nowrap text-capitalize" style="font-weight:500">Subproject ID</label>
                                                    <input type="text" class="form-control" id="inputnewsubprojectcfssubprojectid" name="inputnewsubprojectcfssubprojectid">
                                                    <div class="invalid-feedback" id="error-inputnewsubprojectcfssubprojectid"></div>
                                                </div>

                                                <div class="mb-3 col-md-6 col-lg-4" style="">
                                                    <label for="inputnewsubprojectcfshbl" class="form-label text-nowrap text-capitalize" style="font-weight:500">HBL</label>
                                                    <input type="text" class="form-control" id="inputnewsubprojectcfshbl" name="inputnewsubprojectcfshbl">
                                                    <div class="invalid-feedback" id="error-inputnewsubprojectcfshbl"></div>
                                                </div>

                                                <div class="mb-3 pb-2 col-md-6 col-lg-4 d-flex align-items-end justify-content-between">
                                                    <label class="form-label mb-0 me-2" style="font-weight:500">HBL Reference</label>
                                                    <div class="ms-auto">
                                                        <button type="button" class="btn btn-sm btn-primary" id="addhblreference">
                                                            <i class="fa-solid fa-plus"></i>
                                                        </button>
                                                        <button type="button" class="btn btn-sm btn-danger ms-2" id="removehblreference">
                                                            <i class="fa-solid fa-minus"></i>
                                                        </button>
                                                    </div>
                                                </div>

                                                <div class="mb-3 col-md-6 col-lg-4" style="">
                                                    <label for="inputnewsubprojectcfspieces" class="form-label text-nowrap text-capitalize" style="font-weight:500">Pieces</label>
                                                    <input type="number" min="0" class="form-control" id="inputnewsubprojectcfspieces" name="inputnewsubprojectcfspieces">
                                                    <div class="invalid-feedback" id="error-inputnewsubprojectcfspieces"></div>
                                                </div>

                                                <div class="mb-3 col-md-6 col-lg-4" style="">
                                                    <label for="inputnewsubprojectcfspallets" class="form-label text-nowrap text-capitalize" style="font-weight:500">Pallets</label>
                                                    <input type="number" min="0" class="form-control" id="inputnewsubprojectcfspallets" name="inputnewsubprojectcfspallets">
                                                    <div class="invalid-feedback" id="error-inputnewsubprojectcfspallets"></div>
                                                </div>

                                                <div class="mb-3 col-md-6 col-lg-4">
                                                    <label for="inputnewsubprojectcfsworkspalletized" class="form-label" style="font-weight:500">Works/Palletized</label>
                                                    <select class="form-select" id="inputnewsubprojectcfsworkspalletized" name="inputnewsubprojectcfsworkspalletized">
                                                        <option selected disabled hidden></option>    
                                                    </select>
                                                    <div class="invalid-feedback" id="error-inputnewsubprojectcfsworkspalletized"></div>
                                                </div>

                                                <div class="mb-3 col-md-6 col-lg-4">
                                                    <label for="inputnewsubprojectcfspalletsexchanged" class="form-label" style="font-weight:500">Pallets Exchanged</label>
                                                    <select class="form-select" id="inputnewsubprojectcfspalletsexchanged" name="inputnewsubprojectcfspalletsexchanged">
                                                        <option selected disabled hidden></option>    
                                                    </select>
                                                    <div class="invalid-feedback" id="error-inputnewsubprojectcfspalletsexchanged"></div>
                                                </div>

                                                <div class="mb-3 col-md-6 col-lg-4">
                                                    <label for="inputnewsubprojectcfscustomer" class="form-label" style="font-weight:500">Customer</label>
                                                    <select class="form-select searchCustomer" id="inputnewsubprojectcfscustomer" name="inputnewsubprojectcfscustomer" data-error-message="Customer is required.">
                                                        <option selected disabled hidden></option>    
                                                    </select>
                                                    <div class="invalid-feedback" id="error-inputnewsubprojectcfscustomer"></div>
                                                </div>

                                                <div class="mb-3 col-md-6 col-lg-4">
                                                    <label for="inputnewsubprojectcfscfscheckbox" class="form-label">CFS</label>
                                                    <div class="input-group">
                                                        <div class="input-group-text">
                                                            <input class="form-check-input mt-0" type="checkbox" id="inputnewsubprojectcfscfscheckbox" name="inputnewsubprojectcfscfscheckbox" value="" aria-label="Checkbox for following text input">
                                                        </div>
                                                        <div class="flex-grow-1">
                                                            <select class="form- select2newcfs searchcfscomment" id="inputnewsubprojectcfscfscomment" name="inputnewsubprojectcfscfscomment" data-error-message="CFS comment is required.">
                                                                <option selected disabled hidden></option>  
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="invalid-feedback" id="error-inputnewsubprojectcfscfscomment"></div>
                                                </div>

                                                <div class="mb-3 pb-2 col-md-6 col-lg-4 d-flex align-items-end justify-content-between">
                                                    <label class="form-label mb-0 me-2" style="font-weight:500">Part N.</label>
                                                    <div class="ms-auto">
                                                        <button type="button" class="btn btn-sm btn-primary" id="addpartnumber">
                                                            <i class="fa-solid fa-plus"></i>
                                                        </button>
                                                        <button type="button" class="btn btn-sm btn-danger ms-2" id="removepartnumber">
                                                            <i class="fa-solid fa-minus"></i>
                                                        </button>
                                                    </div>
                                                </div>

                                                <div class="mb-3 col-md-6 col-lg-4" style="display:none">
                                                    <label for="inputnewsubprojectcfspartnumber" class="form-label"  style="font-weight:500">Part Number</label>
                                                    <select class="form-select" id="inputnewsubprojectcfspartnumber" name="inputnewsubprojectcfspartnumber">
                                                        <option selected disabled hidden></option>    
                                                    </select>
                                                </div>

                                                <div class="mb-3 col-md-6 col-lg-4" style="">
                                                    <label for="inputnewsubprojectcfsmagayawhr" class="form-label text-nowrap text-capitalize" style="font-weight:500">WHR ID</label>
                                                    <input type="text" class="form-control" id="inputnewsubprojectcfsmagayawhr" name="inputnewsubprojectcfsmagayawhr">
                                                    <div class="invalid-feedback" id="error-inputnewsubprojectcfsmagayawhr"></div>
                                                </div>

                                                <div class="mb-3 col-md-6 col-lg-4">
                                                    <label for="inputnewsubprojectcfscustomsreleasecheckbox" class="form-label">Customs Release</label>
                                                    <div class="input-group">
                                                        <div class="input-group-text">
                                                            <input class="form-check-input mt-0" type="checkbox" id="inputnewsubprojectcfscustomsreleasecheckbox" name="inputnewsubprojectcfscustomsreleasecheckbox" value="" aria-label="Checkbox for following text input">
                                                        </div>
                                                        <div class="flex-grow-1">
                                                            <select class="form- select2newcfs searchcustomerreleasecomment" id="inputnewsubprojectcfscustomsreleasecomment" name="inputnewsubprojectcfscustomsreleasecomment" data-error-message="Custom release comment is required.">
                                                                <option selected disabled hidden></option>  
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="invalid-feedback" id="error-inputnewsubprojectcfscustomsreleasecomment"></div>
                                                </div>

                                                <div class="mb-3 col-md-6 col-lg-4" style="">
                                                    <label for="inputnewsubprojectcfsoutdatecr" class="form-label text-nowrap text-capitalize" style="font-weight:500">Out Date CR</label>
                                                    <input type="text" class="form-control datetimepicker" id="inputnewsubprojectcfsoutdatecr" name="inputnewsubprojectcfsoutdatecr">
                                                    <div class="invalid-feedback" id="error-inputnewsubprojectcfsoutdatecr"></div>
                                                </div>

                                                <div class="mb-3 col-md-6 col-lg-4" style="">
                                                    <label for="inputnewsubprojectcfsmagayacr" class="form-label text-nowrap text-capitalize" style="font-weight:500">CR ID</label>
                                                    <input type="text" class="form-control" id="inputnewsubprojectcfsmagayacr" name="inputnewsubprojectcfsmagayacr">
                                                    <div class="invalid-feedback" id="error-inputnewsubprojectcfsmagayacr"></div>
                                                </div>

                                                <div class="mb-3 col-md-6 col-lg-4" style="">
                                                    <label for="inputnewsubprojectcfscharges" class="form-label text-nowrap text-capitalize" style="font-weight:500">Charges</label>
                                                    <input type="text" class="form-control" id="inputnewsubprojectcfscharges" name="inputnewsubprojectcfscharges">
                                                    <div class="invalid-feedback" id="error-inputnewsubprojectcfscharges"></div>
                                                </div>

                                                <div class="mb-3 col-md-6 col-lg-4" style="">
                                                    <label for="inputnewsubprojectcfsdalfd" class="form-label text-nowrap text-capitalize" style="font-weight:500">Days After LFD</label>
                                                    <input type="text" class="form-control" id="inputnewsubprojectcfsdalfd" name="inputnewsubprojectcfsdalfd">
                                                    <div class="invalid-feedback" id="error-inputnewsubprojectcfsdalfd"></div>
                                                </div>

                                                <div class="mb-3 col-md-6 col-lg-4" style="">
                                                    <label for="inputnewsubprojectcfscuft" class="form-label text-nowrap text-capitalize" style="font-weight:500">Cuft</label>
                                                    <input type="text" class="form-control" id="inputnewsubprojectcfscuft" name="inputnewsubprojectcfscuft">
                                                    <div class="form-text" id="basic-addon4">1 Cumt - 35.3147 Cuft</div>
                                                    <div class="invalid-feedback" id="error-inputnewsubprojectcfscuft"></div>
                                                </div>

                                                <div class="mb-3 col-md-6 col-lg-4">
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

                    <!--<form id="createcfs" class="centered-form">
                        @csrf

                            <div class="mb-3 col-md-6">
                                <label for="inputnewcfscontainernumber" class="form-label">Container Number</label>
                                <input type="text" class="form-control" id="inputnewcfscontainernumber" name="inputnewcfscontainernumber">
                                <div class="invalid-feedback"></div>
                            </div>

                            <div class="mb-3 col-md-6">
                                <label for="inputnewcfsetaport" class="form-label">ETA Port</label>
                                <input type="text" class="form-control datetimepicker" id="inputnewcfsetaport" name="inputnewcfsetaport" placeholder="MM/DD/YYYY  HH:MM:SS">
                                <div class="invalid-feedback"></div>
                            </div>

                            <div class="mt-3 col-12 d-flex">
                                <div class="p-2 pe-0 flex-grow-1">
                                    <h5 class="gradient-text text-capitalize fw-bolder" >Sub Proyects</h5>
                                </div>
                                <div class="p-2">
                                    <button type="button" class="btn btn-primary" id="newcfsaddsubproyect" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Add Subproyect"><i class="fa-solid fa-plus"></i></button>
                                </div>
                                <div class="p-2 mb-4">
                                    <button type="button" class="btn btn-danger" id="newcfsremovesubproyect" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Remove Subproyect"><i class="fa-solid fa-minus"></i></button>
                                </div>
                            </div>

                            <div class="col-12 newcfssubproyects-container">
                                <div class="row gx-5">
                                    <div class="mb-3 col-md-6  col-lg-4">
                                        <label for="inputnewcfssubproyectid" class="form-label">Subproyect ID</label>
                                        <input type="text" class="form-control" id="inputnewcfssubproyectid" name="inputnewcfssubproyectid">
                                        <div class="invalid-feedback"></div>
                                    </div>

                                    <div class="mb-3 col-md-6  col-lg-4">
                                        <label for="inputnewcfshbl" class="form-label">HBL</label>
                                        <input type="text" class="form-control" id="inputnewcfshbl" name="inputnewcfshbl">
                                        <div class="invalid-feedback"></div>
                                    </div>

                                    <div class="mb-3 col-md-6 col-lg-4">
                                        <label for="inputnewcfspieces" class="form-label">Pieces</label>
                                        <input type="number" min="0" class="form-control" id="inputnewcfspieces" name="inputnewcfspieces">
                                        <div class="invalid-feedback"></div>
                                    </div>

                                    <div class="mb-3 col-md-6 col-lg-4">
                                        <label for="inputnewcfspallets" class="form-label">Pallets</label>
                                        <input type="number" min="0" class="form-control" id="inputnewcfspallets" name="inputnewcfspallets">
                                        <div class="invalid-feedback"></div>
                                    </div>

                                    <div class="mb-3 col-md-6 col-lg-4">
                                        <label for="inputnewcfscustomer" class="form-label">Customer</label>
                                        <select class="form-select" id="inputnewcfscustomer" name="inputnewcfscustomer">
                                            <option selected disabled hidden></option>    
                                        </select>
                                    </div>

                                    <div class="mb-3 col-md-6 col-lg-4">
                                        <label for="inputnewcfscustomer" class="form-label">Customs Release</label>
                                        <select class="form-select" id="inputnewcfscustomer" name="inputnewcfscustomer">
                                            <option selected disabled hidden></option>    
                                        </select>
                                    </div>

                                    <div class="mb-3 col-md-6 col-lg-4">
                                        <label for="inputnewcfspartnumber" class="form-label">Part Number</label>
                                        <select class="form-select" id="inputnewcfspartnumber" name="inputnewcfspartnumber">
                                            <option selected disabled hidden></option>    
                                        </select>
                                    </div>

                                    <div class="mb-3 col-md-6 col-lg-4">
                                        <label for="inputnewcfscfscheckbox" class="form-label">CFS</label>
                                        <div class="input-group mb-3">
                                            <div class="input-group-text">
                                                <input class="form-check-input mt-0" id="inputnewcfscfscheckbox" name="inputnewcfscfscheckbox" type="checkbox" value="" aria-label="Checkbox for following text input">
                                            </div>
                                            <input type="text" class="form-control" id="inputnewcfscfscomment" name="inputnewcfscfscomment" aria-label="Text input with checkbox">
                                        </div>
                                    </div>

                                    <div class="mb-3 col-md-6 col-lg-4">
                                        <label for="inputnewcfsdatewhrmedley" class="form-label">Date Warehouse Medley</label>
                                        <input type="text" class="form-control datetimepicker" id="inputnewcfsdatewhrmedley" name="inputnewcfsdatewhrmedley" placeholder="MM/DD/YYYY HH:MM:SS">
                                        <div class="form-text" id="basic-addon4">Container arrived</div>
                                        <div class="invalid-feedback"></div>
                                    </div>

                                    <div class="mb-3 col-md-6 col-lg-4">
                                        <label for="inputnewcfsmagayawhr" class="form-label">Magaya Warehouse</label>
                                        <input type="text" class="form-control" id="inputnewcfsmagayawhr" name="inputnewcfsmagayawhr">
                                        <div class="invalid-feedback"></div>
                                    </div>

                                    <div class="mb-3 col-md-6 col-lg-4">
                                        <label for="inputnewcfslfd" class="form-label">LFD</label>
                                        <input type="text" class="form-control datetimepicker" id="inputnewcfslfd" name="inputnewcfslfd" placeholder="MM/DD/YYYY HH:MM:SS">
                                        <div class="invalid-feedback"></div>
                                    </div>

                                    <div class="mb-3 col-md-6 col-lg-4">
                                        <label for="inputnewcfscustomsreleasecheckbox" class="form-label">Customs Release</label>
                                        <div class="input-group">
                                            <div class="input-group-text">
                                                <input class="form-check-input mt-0" type="checkbox" id="inputnewcfscustomsreleasecheckbox" name="inputnewcfscustomsreleasecheckbox" value="" aria-label="Checkbox for following text input">
                                            </div>
                                            <div class="flex-grow-1">
                                                <select class="form- select2newcfs" id="inputnewcfscustomsreleasecomment" name="inputnewcfscustomsreleasecomment">
                                                    <option value="">Choose an option</option>
                                                    <option value="yes">Yes</option>
                                                    <option value="no">No</option>  
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mb-3 col-md-6 col-lg-4">
                                        <label for="inputnewcfsoutdatecr" class="form-label">Out Date - CR</label>
                                        <input type="text" class="form-control datetimepicker" id="inputnewcfsoutdatecr" name="inputnewcfsoutdatecr" placeholder="MM/DD/YYYY HH:MM:SS">
                                        <div class="invalid-feedback"></div>
                                    </div>

                                    <div class="mb-3 col-md-6 col-lg-4">
                                        <label for="inputnewcfsmagayacr" class="form-label">Magaya CR</label>
                                        <input type="text" class="form-control" id="inputnewcfsmagayacr" name="inputnewcfsmagayacr">
                                        <div class="invalid-feedback"></div>
                                    </div>

                                    <div class="mb-3 col-md-6 col-lg-4">
                                        <label for="inputnewcfsmagayachargespaid" class="form-label">Charges/¿Paid?</label>
                                        <input type="number" step="0.01" min="0" placeholder="0.00" class="form-control" id="inputnewcfsmagayachargespaid" name="inputnewcfsmagayachargespaid">
                                        <div class="invalid-feedback"></div>
                                    </div>
                                    
                                    <div class="mb-3 col-md-6 col-lg-4">
                                        <label for="inputnewcfsdaysafterlfd" class="form-label">Days after LFD</label>
                                        <input type="number" min="0" class="form-control" id="inputnewcfsdaysafterlfd" name="inputnewcfsdaysafterlfd">
                                        <div class="invalid-feedback"></div>
                                    </div>

                                    <div class="mb-3 col-md-6 col-lg-4">
                                        <label for="inputnewcfscuft" class="form-label">Cuft</label>
                                        <input type="number" class="form-control" id="inputnewcfscuft" name="inputnewcfscuft">
                                        <div class="form-text" id="basic-addon4">1 Cumt - 35.3147 Cuft</div>
                                        <div class="invalid-feedback"></div>
                                    </div>

                                    <div class="mb-3 col-md-6 col-lg-4">
                                        <label for="inputnewcfsnotes" class="form-label">Notes</label>
                                        <textarea class="form-control" id="inputnewcfsnotes" name="inputnewcfsnotes" rows="1"></textarea>
                                        <div class="invalid-feedback"></div>
                                    </div>

                                </div>
                            </div>
                            
                            <div class="d-flex ms-auto col-4">
                                <button type="button" class="btn btn-primary w-100" id="saveButtonShipment">Save</button>
                            </div>
                            
                        </div>
                    </form>-->
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

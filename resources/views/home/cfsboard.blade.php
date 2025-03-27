@auth
    @extends('layouts.app-master')

    @section('title', 'CFS')

    @section('content')
        <div class="container  my-4">
            @if(in_array('add_new', Auth::user()->permissions))
                <div class="my-4 d-flex justify-content-center align-items-center">
                    <h2 class="gradient-text text-capitalize fw-bolder" >CFS Board</h2>
                </div> 

                <form id="createcfs"  class="centered-form">
                    @csrf
                        <div class="mb-3">
                            <label for="inputshipmentstmid" class="form-label ">STM ID</label>
                            <input type="text" class="form-control" id="inputshipmentstmid" name="inputshipmentstmid" data-url="">
                            <div class="invalid-feedback"></div>
                        </div>

                        <div class="mb-3">
                            <label for="inputshipmentprealertdatetime" class="form-label ">PreAlert DateTime</label>
                            <input type="text" class="form-control datetimepicker" id="inputshipmentprealertdatetime" name="inputshipmentprealertdatetime" placeholder="MM/DD/YYYY - H/M/S">
                            <div class="invalid-feedback"></div>
                        </div>

                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" id="inputshipmentcheckbonded" name="inputshipmentcheckbonded">
                            <label class="form-check-label" for="inputshipmentcheckbonded">
                                Bonded
                            </label>
                        </div>

                        <div class="mb-3">
                            <label for="inputshipmentprealertdatetime" class="form-label ">PreAlert DateTime</label>
                            <input type="text" class="form-control datetimepicker" id="inputshipmentprealertdatetime" name="inputshipmentprealertdatetime" placeholder="MM/DD/YYYY - H/M/S">
                            <div class="invalid-feedback"></div>
                        </div>

                        <div class="mb-3">
                            <label for="inputshipmentdriver" class="form-label ">Driver</label>
                            <select class="form-select" aria-label="Default select example"  id="inputshipmentdriver" name="inputshipmentdriver" data-url="">
                            <option selected disabled hidden></option>    
                        </select>
                        </div>

                        <div class="mb-3 d-flex">
                            <div class="p-2 pe-0 flex-grow-1">
                                <label class="form-label ">Shipment trackers</label>
                            </div>
                            <div class="p-2">
                                <button type="button" class="btn btn-primary" id="addtrackers">Add Tracker <i class="fa-solid fa-plus"></i></button>
                            </div>
                            <div class="p-2">
                                <button type="button" class="btn btn-danger" id="removetrackers">Remove Tracker <i class="fa-solid fa-minus"></i></button>
                            </div>
                        </div>

                        <!-- Estos divs se agregarán dinámicamente -->
                        <div class="trackers-container"></div>

                    <button type="submit" class="btn btn-primary" id="saveButtonShipment" data-url="">Save</button>
                    </form>
            @endif  
            
            @if(in_array('edit', Auth::user()->permissions))
                <div>Products Section</div>
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

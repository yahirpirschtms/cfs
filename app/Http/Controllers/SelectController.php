<?php

namespace App\Http\Controllers;

use App\Models\GenericCatalogs;
use App\Models\Costumer;
use App\Models\Pn;
use App\Models\Service;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Http\Request;

class SelectController extends Controller
{
    //Funcion para rellenar todos los catalogos
    public function getLoadSelects(Request $request){
        if (Auth::check()) {
            // Lista de grupos que necesitamos
            $groups = [
                'drayage_user',
                'drayage_file',
                'cfs_option',
                'custom_release',
                'invoice_option'
            ];

            // Traemos todos los registros de esos grupos en una sola consulta
            $genericCatalogs = GenericCatalogs::whereIn('gntc_group', $groups)
                ->where('gntc_status', '1')
                ->select('gnct_id', 'gntc_value', 'gntc_group')
                ->get()
                ->groupBy(function ($item) {
                    return strtolower($item->gntc_group);
                });

            $customersData = Costumer::where('status', '1')->select('pk_customer', 'description')->get();
            $partNumberData = Pn::where('status', '1')->select('pk_part_number', 'description')->get();
            $serviceData = Service::where('status', '1')->select('pk_service', 'description', 'cost')->get();

            // Devolver los datos en formato JSON
            return response()->json([
                    'drayage_user'   => $genericCatalogs->get('drayage_user', collect()),
                    'drayage_filetype' => $genericCatalogs->get('drayage_file', collect()),
                    'cfs'            => $genericCatalogs->get('cfs_option', collect()),
                    'custom_release' => $genericCatalogs->get('custom_release', collect()),
                    'invoice'        => $genericCatalogs->get('invoice_option', collect()),
                    'customers'      => $customersData,
                    'part_number'    => $partNumberData,
                    'services'       => $serviceData,
                ]);
        }
        return redirect('/login');
    }

    //Funcion para añadir nuevos Drayage User
    public function saveNewDrayageUser(Request $request){
        if(Auth::check()){
            $request->validate([
                'newDrayageUser'=>'required|string|max:255',
            ]);

            $existingDrayageUser = GenericCatalogs::where('gntc_value',$request->newDrayageUser)
                ->where('gntc_group','drayage_user')
                ->first();
            if(!$existingDrayageUser){
                $createnewDrayageUser = new GenericCatalogs();
                $createnewDrayageUser->gntc_value = $request->newDrayageUser;
                $createnewDrayageUser->gntc_description = $request->newDrayageUser;
                $createnewDrayageUser->gntc_group = 'DRAYAGE_USER';
                $createnewDrayageUser->gntc_status = 1;
                $createnewDrayageUser->gntc_creation_date =  now();
                $createnewDrayageUser->gntc_user = Auth::check() ? Auth::user()->username : 'system';
                $createnewDrayageUser->save();

                return response()->json([
                    'message' => 'New drayage user saved succesfully.',
                    'newDrayageUserCreated' => [
                        'gnct_id' => $createnewDrayageUser->gnct_id,
                        'gntc_value' => $createnewDrayageUser->gntc_value
                    ]
                ]);
            }else{
                return response()->json([
                    'message' => 'Drayage User already exists',
                    'existingDrayageUser' => $existingDrayageUser
                ], 409);
            }
        }
        return redirect('/login');
    }

    //Funcion para añadir nuevos Drayage User
    public function saveNewDrayageFileType(Request $request){
        if(Auth::check()){
            $request->validate([
                'newDrayageFileType'=>'required|string|max:255',
            ]);

            $existingDrayageFileType = GenericCatalogs::where('gntc_value',$request->newDrayageFileType)
                ->where('gntc_group','drayage_file')
                ->first();
            if(!$existingDrayageFileType){
                $createnewDrayageFileType = new GenericCatalogs();
                $createnewDrayageFileType->gntc_value = $request->newDrayageFileType;
                $createnewDrayageFileType->gntc_description = $request->newDrayageFileType;
                $createnewDrayageFileType->gntc_group = 'DRAYAGE_FILE';
                $createnewDrayageFileType->gntc_status = 1;
                $createnewDrayageFileType->gntc_creation_date =  now();
                $createnewDrayageFileType->gntc_user = Auth::check() ? Auth::user()->username : 'system';
                $createnewDrayageFileType->save();

                return response()->json([
                    'message' => 'New drayage file type saved succesfully.',
                    'newDrayageFileTypeCreated' => [
                        'gnct_id' => $createnewDrayageFileType->gnct_id,
                        'gntc_value' => $createnewDrayageFileType->gntc_value
                    ]
                ]);
            }else{
                return response()->json([
                    'message' => 'Drayage File Type already exists',
                    'existingDrayageFileType' => $existingDrayageFileType
                ], 409);
            }
        }
        return redirect('/login');
    }

    //Funcion para añadir nuevos Part Numbers
    public function saveNewPartNumber(Request $request){
        if(Auth::check()){
            $request->validate([
                'newPartNumber'=>'required|string|max:255',
            ]);

            $existingPartNumber = Pn::where('description',$request->newPartNumber)
                ->where('status','1')
                ->first();
            if(!$existingPartNumber){
                $createnewPartNumber = new Pn();
                $createnewPartNumber->description = $request->newPartNumber;
                $createnewPartNumber->status = 1;
                $createnewPartNumber->created_date =  now();
                $createnewPartNumber->created_by = Auth::check() ? Auth::user()->username : 'system';
                $createnewPartNumber->save();

                return response()->json([
                    'message' => 'New part number saved succesfully.',
                    'newPartNumberCreated' => [
                        'pk_part_number' => $createnewPartNumber->pk_part_number,
                        'description' => $createnewPartNumber->description
                    ]
                ]);
            }else{
                return response()->json([
                    'message' => 'Part Number already exists',
                    'existingPartNumber' => $existingPartNumber
                ], 409);
            }
        }
        return redirect('/login');
    }

    //Funcion para añadir nuevos Customers
    public function saveNewCustomer(Request $request){
        if(Auth::check()){
            $request->validate([
                'newCustomer'=>'required|string|max:200',
            ]);

            $existingCustomer = Costumer::where('description',$request->newCustomer)
                ->where('status','1')
                ->first();
            if(!$existingCustomer){
                $createnewCustomer = new Costumer();
                $createnewCustomer->description = $request->newCustomer;
                $createnewCustomer->status = 1;
                $createnewCustomer->created_date =  now();
                $createnewCustomer->created_by = Auth::check() ? Auth::user()->username : 'system';
                $createnewCustomer->save();

                return response()->json([
                    'message' => 'New customer saved succesfully.',
                    'newCustomerCreated' => [
                        'pk_customer' => $createnewCustomer->pk_customer,
                        'description' => $createnewCustomer->description
                    ]
                ]);
            }else{
                return response()->json([
                    'message' => 'Customer already exists',
                    'existingCustomer' => $existingCustomer
                ], 409);
            }
        }
        return redirect('/login');
    }

    //Funcion para añadir nuevos CFS
    public function saveNewCFS(Request $request){
        if(Auth::check()){
            $request->validate([
                'newCFS'=>'required|string|max:50',
            ]);

            $existingCFS = GenericCatalogs::where('gntc_value',$request->newCFS)
                ->where('gntc_group','cfs_option')
                ->first();
            if(!$existingCFS){
                $createnewCFS = new GenericCatalogs();
                $createnewCFS->gntc_value = $request->newCFS;
                $createnewCFS->gntc_description = $request->newCFS;
                $createnewCFS->gntc_group = 'CFS_OPTION';
                $createnewCFS->gntc_status = 1;
                $createnewCFS->gntc_creation_date =  now();
                $createnewCFS->gntc_user = Auth::check() ? Auth::user()->username : 'system';
                $createnewCFS->save();

                return response()->json([
                    'message' => 'New CFS option saved succesfully.',
                    'newCFSCreated' => [
                        'gnct_id' => $createnewCFS->gnct_id,
                        'gntc_value' => $createnewCFS->gntc_value
                    ]
                ]);
            }else{
                return response()->json([
                    'message' => 'CFS option already exists',
                    'existingCFS' => $existingCFS
                ], 409);
            }
        }
        return redirect('/login');
    }

    //Funcion para añadir nuevos Custom Release
    public function saveNewCustomRelease(Request $request){
        if(Auth::check()){
            $request->validate([
                'newCustomRelease'=>'required|string|max:50',
            ]);

            $existingCustomRelease = GenericCatalogs::where('gntc_value',$request->newCustomRelease)
                ->where('gntc_group','custom_release')
                ->first();
            if(!$existingCustomRelease){
                $createnewCustomRelease = new GenericCatalogs();
                $createnewCustomRelease->gntc_value = $request->newCustomRelease;
                $createnewCustomRelease->gntc_description = $request->newCustomRelease;
                $createnewCustomRelease->gntc_group = 'CUSTOM_RELEASE';
                $createnewCustomRelease->gntc_status = 1;
                $createnewCustomRelease->gntc_creation_date =  now();
                $createnewCustomRelease->gntc_user = Auth::check() ? Auth::user()->username : 'system';
                $createnewCustomRelease->save();

                return response()->json([
                    'message' => 'New Custom Release saved succesfully.',
                    'newCustomReleaseCreated' => [
                        'gnct_id' => $createnewCustomRelease->gnct_id,
                        'gntc_value' => $createnewCustomRelease->gntc_value
                    ]
                ]);
            }else{
                return response()->json([
                    'message' => 'Custom Release already exists',
                    'existingCustomRelease' => $existingCustomRelease
                ], 409);
            }
        }
        return redirect('/login');
    }

    //Funcion para añadir nuevos Invoice
    public function saveNewInvoice(Request $request){
        if(Auth::check()){
            $request->validate([
                'newInvoice'=>'required|string|max:50',
            ]);

            $existingInvoice = GenericCatalogs::where('gntc_value',$request->newInvoice)
                ->where('gntc_group','invoice_option')
                ->first();
            if(!$existingInvoice){
                $createnewInvoice = new GenericCatalogs();
                $createnewInvoice->gntc_value = $request->newInvoice;
                $createnewInvoice->gntc_description = $request->newInvoice;
                $createnewInvoice->gntc_group = 'INVOICE_OPTION';
                $createnewInvoice->gntc_status = 1;
                $createnewInvoice->gntc_creation_date =  now();
                $createnewInvoice->gntc_user = Auth::check() ? Auth::user()->username : 'system';
                $createnewInvoice->save();

                return response()->json([
                    'message' => 'New Invoice option saved succesfully.',
                    'newInvoiceCreated' => [
                        'gnct_id' => $createnewInvoice->gnct_id,
                        'gntc_value' => $createnewInvoice->gntc_value
                    ]
                ]);
            }else{
                return response()->json([
                    'message' => 'Invoice option already exists',
                    'existingInvoice' => $existingInvoice
                ], 409);
            }
        }
        return redirect('/login');
    }
}

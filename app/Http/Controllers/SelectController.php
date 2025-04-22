<?php

namespace App\Http\Controllers;

use App\Models\GenericCatalogs;
use App\Models\Costumer;
use App\Models\Pn;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Http\Request;

class SelectController extends Controller
{
    //Funcion para rellenar todos los catalogos
    public function getLoadSelects(Request $request){
        if (Auth::check()) {
            // Obtener los datos donde el campo gntc_group sea 'drayage_user' o 'drayage_filetype'
            $drayageUserData = GenericCatalogs::where('gntc_group', 'drayage_user')->where('gntc_status', '1')->select('gnct_id', 'gntc_value')->get();
            $drayageFiletypeData = GenericCatalogs::where('gntc_group', 'drayage_file')->where('gntc_status', '1')->select('gnct_id', 'gntc_value')->get();
            $customersData = Costumer::where('status', '1')->select('pk_customer', 'name')->get();
            $partNumberData = Pn::where('status', '1')->select('pk_part_number', 'description')->get();

            // Devolver los datos en formato JSON
            return response()->json([
                'drayage_user' => $drayageUserData,
                'drayage_filetype' => $drayageFiletypeData,
                'customers' => $customersData,
                'part_number' => $partNumberData,
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
}

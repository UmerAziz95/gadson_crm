<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
// use Spatie\PdfToText\Pdf;
// use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Buildings;
use App\Models\BuildingImages;
use App\Models\Country;
use App\Models\State;
use App\Models\City;
use App\Models\Appartment;
use App\Models\AppartmentImages;
use App\Models\Tasks;
use App\Models\TaskNotifications;
use App\Models\TaskToDoList;
use Illuminate\Support\Str;






class AdminController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */


    // Use dependency injection to bring in the PaymentEncode class
    public function __construct()
    {

    }



    public function login()
    {

        $data['page'] = 'Login';
        return view('admin/login')->with($data);
    }

    public function loginSubmit(Request $request)
    {
        $validatedData = $request->validate([
            'email' => 'required|exists:users,email',
        ]);
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {

            $user = Auth::user();
            $request->session()->put('user', $user);
            // Authentication passed...
            return redirect()->intended('/admin/dashboard');
        }

        $request->session()->flash('error', 'The provided credentials do not match our records.');
        return redirect('admin/login');
    }

    public function logout(Request $request)
    {

        $request->session()->forget('user');

        return redirect('admin');
    }

    public function dashboard()
    {

        $data['page'] = 'Dashboard';
        $data['total_managers'] = User::where('type', 'manager')->count();
        $data['total_buildings'] = Buildings::count();
        $data['total_appartments'] = Appartment::count();
        $data['total_tasks'] = Tasks::count();
        $data['completed_tasks'] = Tasks::where('status', 5)->count();
        $startDate = now()->subDays(15)->startOfDay();
        $endDate = now()->endOfDay();
        $data['results'] = Tasks::select(
            DB::raw('DATE(updated_at) AS task_date'),
            DB::raw('SUM(CASE WHEN status = "0" THEN 1 ELSE 0 END) AS draft_count'),
            DB::raw('SUM(CASE WHEN status = "1" THEN 1 ELSE 0 END) AS assigned_count'),
            DB::raw('SUM(CASE WHEN status = "2" THEN 1 ELSE 0 END) AS working_on_count'),
            DB::raw('SUM(CASE WHEN status = "3" THEN 1 ELSE 0 END) AS hold_count'),
            DB::raw('SUM(CASE WHEN status = "4" THEN 1 ELSE 0 END) AS stuck_count'),
            DB::raw('SUM(CASE WHEN status = "5" THEN 1 ELSE 0 END) AS done_count'),
            DB::raw('SUM(CASE WHEN status = "6" THEN 1 ELSE 0 END) AS cancelled_count'),
        )
            ->whereBetween('updated_at', [$startDate, $endDate])
            ->groupBy('task_date')
            ->orderBy('task_date')
            ->get();
        return view('admin/dashboard')->with($data);
    }

    public function subscription()
    {
        $data['page'] = 'Subscription';
        return view('admin/subscriptions')->with($data);
    }
    public function managers()
    {
        $data['page'] = 'Managers';
        $data['managers_list'] = User::where('type', 'manager')->where('status', 1)->get();
        return view('admin/managers')->with($data);
    }
    public function get_managers_list()
    {

        $data['managers_list'] = User::where('type', 'manager')->get();
        $data['inactive_managers'] = User::where('type', 'manager')->where('status', 0)->count();
        $data['active_managers'] = User::where('type', 'manager')->where('status', 1)->count();
        return response()->json(['status' => 200, 'managers_list' => $data]);
    }

    public function add_managers(Request $request)
    {

        $validatedData = $request->validate([
            'first_name' => 'required|max:50',
            'middle_name' => 'max:50',
            'last_name' => 'required|max:50',
            'email' => 'required|email|max:50|unique:users',
            'contact_number' => 'required|max:15|min:7',
        ]);

        $user = new User;
        $user->type = 'Manager';
        $user->first_name = $request->first_name;
        $user->middle_name = $request->middle_name;
        $user->last_name = $request->last_name;
        $user->email = $request->email;
        $user->contact_number = $request->contact_number;
        $user->created_by = Auth::id();
        $password = Str::random(10);
        $user->password = Hash::make($password);
        $user->save();
        $mailData['name'] = $user->first_name;
        $mailData['email'] = $user->email;
        $mailData['password'] = $password;
        $body = view('emails.user_created', $mailData);
        $userEmailsSend[] = $user->email;
        // to username, to email, from username, subject, body html
        try{
            $response = sendMail($user->first_name, $userEmailsSend, 'GALAXY CRM ', 'User Created', $body); // send_to_name, send_to_email, email_from_name, subject, body
            // dd($response);
        }catch(\Exception $e){
            return response()->json(['status' => 500, 'message' => "Failed to send email"]);
        }
        


        return response()->json(['status' => 200, 'message' => "Manager Added Successfully"]);

    }

    public function delete_manager(Request $request)
    {
        $manager_id = $request->del_id;
        $task_check = Tasks::where('manager', $manager_id)->count();
        if ($task_check > 0) {
            return response()->json(['status' => 402, 'message' => "Manager can not be deleted as it has tasks"]);
        }
        $manager = User::where('id', $manager_id)->where('type', 'manager')->first();
        if (!$manager) {
            return response()->json(['status' => 402, 'message' => "Manager Not found"]);
        } else {
            $mailData['name'] = $manager->first_name . ' ' . $manager->last_name;
            $body = view('emails.account_deletion', $mailData);
            $userEmailsSend[] = $manager->email;
            // to username, to email, from username, subject, body html
            sendMail(trim(($manager->first_name ?? '') . ' ' . ($manager->middle_name ?? '') . ' ' . ($manager->last_name ?? '')), $userEmailsSend, 'GALAXY CRM', 'Account Deletion Notice', $body);
            $manager->delete();
            return response()->json(['status' => 200, 'message' => "Manager Deleted Successfully"]);
        }
    }

    public function change_status(Request $request)
    {
        $manager_id = $request->id;
        $manager = User::where('id', $manager_id)->first();
        if ($manager->status == 0) {
            $manager->status = 1;
            $manager->updated_by = Auth::id();
            $manager->save();
            $mailData['name'] = $manager->first_name . ' ' . $manager->last_name;
            $body = view('emails.account_status_active', $mailData);
            $userEmailsSend[] = $manager->email;
            // to username, to email, from username, subject, body html
            sendMail(trim(($manager->first_name ?? '') . ' ' . ($manager->middle_name ?? '') . ' ' . ($manager->last_name ?? '')), $userEmailsSend, 'GALAXY CRM', 'Account Activation Notice', $body);


            return response()->json(['status' => 200, 'message' => "Status Updated Successfully"]);

        } else {
            $manager->status = 0;
            $manager->save();
            $manager->updated_by = Auth::id();
            $mailData['name'] = $manager->first_name . ' ' . $manager->last_name;
            $body = view('emails.account_status_inactive', $mailData);
            $userEmailsSend[] = $manager->email;
            // to username, to email, from username, subject, body html
            sendMail(trim(($manager->first_name ?? '') . ' ' . ($manager->middle_name ?? '') . ' ' . ($manager->last_name ?? '')), $userEmailsSend, 'GALAXY CRM', 'Account Inactivation Notice', $body);

            return response()->json(['status' => 200, 'message' => "Status Updated Successfully"]);
        }
    }

    public function get_manager_data(Request $request)
    {
        $user_id = $request->id;

        $manager = User::where('id', $user_id)->get();
        if (!$manager) {
            return response()->json(['status' => 402, 'message' => "Manager Not found"]);
        } else {
            return response()->json(['status' => 200, 'data' => $manager]);
        }
    }

    public function update_manager(Request $request)
    {
        $validatedData = $request->validate(
            [
                'first_name_edit' => 'required|max:50',
                // 'middle_name_edit' => 'max:50',
                'last_name_edit' => 'required|max:50',
                // 'email' => 'required|email|max:50|unique:users',
                'contact_number_edit' => 'required|max:15|min:7',
            ],
            [
                'first_name_edit.required' => 'first name is required',
                'first_name_edit.max' => 'first name can not be more than 50 characters',
                //   'middle_name_edit.required' => 'middle name is required',  
                'last_name_edit.required' => 'last name is required',
                'last_name_edit.max' => 'last name can not be more than 50 characters',
                'contact_number_edit.required' => 'contact number is required',
                'contact_number_edit.max' => 'contact number can not be more than 15 characters',
                'contact_number_edit.min' => 'contact number can not be less than 7 characters',
            ]
        );

        $manager = User::where('id', $request->manager_id_edit)->first();
        $manager->first_name = $request->first_name_edit;
        $manager->middle_name = $request->middle_name_edit;
        $manager->last_name = $request->last_name_edit;
        // $manager->email = $request->email;
        $manager->contact_number = $request->contact_number_edit;
        $manager->updated_by = Auth::id();
        $manager->save();
        return response()->json(['status' => 200, 'message' => "Manager Updated Successfully"]);
    }

    public function buildings()
    {
        $data['page'] = 'Buildings';
        return view('admin/buildings')->with($data);
    }
    public function get_buildings_list()
    {
        // $data['buildings_list'] = Buildings::with('images')->orderBy('created_at', 'Desc')->get();
        $buildings = Buildings::with('images')->orderBy('created_at', 'Desc');
        // building_number is request 
        if(request()->has('building_number') && !empty(request()->building_number)){
            // dd(request()->building_number);
            $buildings->where('building_number', 'like', '%'.request()->building_number.'%');
        }
        // building_name
        if(request()->has('building_name') && !empty(request()->building_name)){
            $buildings->where('building_name', 'like', '%'.request()->building_name.'%');
        }
        // building_type
        if(request()->has('building_type') && !empty(request()->building_type)){
            $buildings->where('building_type', 'like', '%'.request()->building_type.'%');
        }
        // no_of_apartments
        if(request()->has('no_of_apartments') && !empty(request()->no_of_apartments)){
            $buildings->where('number_of_apartments', 'like', '%'.request()->no_of_apartments.'%');
        }
        // no_of_floors
        if(request()->has('no_of_floors') && !empty(request()->no_of_floors)){
            $buildings->where('number_of_floors', 'like', '%'.request()->no_of_floors.'%');
        }
        // building_address
        if(request()->has('building_address') && !empty(request()->building_address)){
            $buildings->where('building_address', 'like', '%'.request()->building_address.'%');
        }
        // status
        if(request()->has('status') && !empty(request()->status)){
            $buildings->where('status', 'like', '%'.request()->status.'%');
        }
        $data['buildings_list'] = $buildings->get();
        $data['residential_list'] = Buildings::where('building_type', 'Residential')->count();
        $data['commercial_list'] = Buildings::where('building_type', 'Commercial')->count();
        $data['mixed_list'] = Buildings::where('building_type', 'Mixed Use')->count();
        $data['total_buildings_list'] = Buildings::all()->count();
        return response()->json(['status' => 200, 'buildings_list' => $data]);
    }
    public function get_states_list(Request $request)
    {
        $data['states_list'] = State::where('country_id', $request->country)->get();
        return response()->json(['status' => 200, 'states_list' => $data]);
    }
    public function get_cities_list(Request $request)
    {
        $data['cities_list'] = City::where('state_id', $request->state)->get();
        return response()->json(['status' => 200, 'cities_list' => $data]);
    }

    public function add_building()
    {
        $data['page'] = 'Buildings';
        $data['states'] = State::where('country_id', 233)->orderBy('name', 'asc')->get();
        return view('admin/addbuildings')->with($data);
    }
    public function store_building(Request $request)
    {
        $request->validate([
            'building_name' => 'required|max:100',
            'building_type' => 'required|max:20',
            'building_address' => 'required|max:255',
            'number_of_apartments' => 'required|max:11',
            'number_of_floors' => 'required|max:11',
            'country' => 'required|max:100',
            'state' => 'required|max:100',
            // 'city' => 'required|max:100',
            'building_number' => 'required|max:100',
            'total_parkings' => 'required|max:11',
            'owner_name' => 'required|max:100',
            'building_size' => 'numeric|required',
            'building_description' => 'required|max:255',
            'building_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            // 'status' => 'required|max:50',
        ]);
        $building = new Buildings;
        $building->building_name = $request->building_name;
        $building->building_type = $request->building_type;
        $building->building_address = $request->building_address;
        $building->number_of_apartments = $request->number_of_apartments;
        $building->number_of_floors = $request->number_of_floors;
        $building->country = $request->country;
        $building->state = $request->state;
        $building->city = $request->city;
        $building->building_number = $request->building_number;
        $building->total_parkings = $request->total_parkings;
        $building->owner_name = $request->owner_name;
        $building->building_size = $request->building_size;
        $building->building_description = $request->building_description;
        $building->status = 'Available';
        $building->created_by = Auth::id();
        $building->updated_by = Auth::id();
        $building->save();
        if ($request->hasFile('photos')) {

            $path = '/uploads/building_images/' . $building->id;
            $uploadedFile = $request->file('photos');
            $savedImages = saveMultipleImages($uploadedFile, $path);
            foreach ($savedImages as $image) {
                $building_image = new BuildingImages;
                $building_image->building_id = $building->id;
                $building_image->image_path = url('/public/') . $image;
                $building_image->save();
            }

        }

        return response()->json(['status' => 200, 'message' => "Building Added Successfully"]);

    }

    public function edit_building($id)
    {
        $data['page'] = 'Buildings';
        $data['countries'] = Country::all();
        $data['building'] = Buildings::where('id', $id)->with('images')->first();
        $data['cities'] = City::where('state_id', $data['building']->state)->get();
        $data['states'] = State::where('country_id', 233)->orderBy('name', 'asc')->get();
        return view('admin/edit_building')->with($data);
    }

    public function update_building(Request $request)
    {

        $request->validate([
            'building_name' => 'required|max:100',
            'building_type' => 'required|max:20',
            'building_address' => 'required|max:255',
            'number_of_apartments' => 'required|max:11',
            'number_of_floors' => 'required|max:11',
            'country' => 'required|max:100',
            'state' => 'required|max:100',
            // 'city' => 'required|max:100',
            'building_number' => 'required|max:100',
            'total_parkings' => 'required|max:11',
            'owner_name' => 'required|max:100',
            'building_size' => 'numeric|required',
            'building_description' => 'required|max:255',
            'building_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'status' => 'required|max:50',
        ]);

        if ($request->hasFile('photos')) {

            $path = '/uploads/building_images/' . $request->building_id;
            $uploadedFile = $request->file('photos');
            $savedImages = saveMultipleImages($uploadedFile, $path);
            foreach ($savedImages as $image) {
                $building_image = new BuildingImages;
                $building_image->building_id = $request->building_id;
                $building_image->image_path = url('/public/') . $image;
                $building_image->save();
            }
        }
        if (isset($request->removed_image_ids) && !empty($request->removed_image_ids)) {
            $removed_image_ids = $request->removed_image_ids;

            foreach ($removed_image_ids as $removed_image_id) {

                deleteImage(str_replace(url('/public/'), '', $removed_image_id));
                BuildingImages::where('image_path', $removed_image_id)->delete();



            }


        }



        $building = Buildings::find($request->building_id);
        $building->building_name = $request->building_name;
        $building->building_type = $request->building_type;
        $building->building_address = $request->building_address;
        $building->number_of_apartments = $request->number_of_apartments;
        $building->number_of_floors = $request->number_of_floors;
        $building->country = $request->country;
        $building->state = $request->state;
        $building->city = $request->city;
        $building->building_number = $request->building_number;
        $building->total_parkings = $request->total_parkings;
        $building->owner_name = $request->owner_name;
        $building->building_size = $request->building_size;
        $building->building_description = $request->building_description;
        $building->status = $request->status;
        $building->updated_by = Auth::id();
        $building->save();




        return response()->json(['status' => 200, 'message' => "Building Updated Successfully"]);

        // return redirect('admin/buildings')->with('status','Building Updated Successfully');

    }

    public function delete_building(Request $request)
    {
        $building_id = $request->del_id;
        $building = Buildings::find($building_id);

        if (!$building) {
            return response()->json(['status' => 402, 'message' => "Building not found"]);
        }
        $appartment_check = Appartment::where('building_id', $building_id)->count();
        if ($appartment_check > 0) {
            return response()->json(['status' => 402, 'message' => "Building can not be deleted as it has appartments"]);
        }

        try {
            $images = BuildingImages::where('building_id', $building_id)->get();
            foreach ($images as $image) {

                deleteImage(str_replace(url('/'), '', $image));
            }
            $building->delete();
            return response()->json(['status' => 200, 'message' => "Building deleted successfully"]);
        } catch (\Exception $e) {

            return response()->json(['status' => 500, 'message' => "Failed to delete building"]);
        }
    }




    public function appartments()
    {
        $data['page'] = 'Appartments';
        // $data['apartments'] = Appartment::all();
        return view('admin/appartments')->with($data);
    }

    public function get_appartments_list()
    {
        $appartments = Appartment::with('images', 'building')->orderBy('created_at', 'Desc');
        // apartment_no is request
        if(request()->has('apartment_no') && !empty(request()->apartment_no)){
            $appartments->where('apartment_no', 'like', '%'.request()->apartment_no.'%');
        }
        // apartment_name
        if(request()->has('apartment_name') && !empty(request()->apartment_name)){
            $appartments->where('apartment_name', 'like', '%'.request()->apartment_name.'%');
        }
        // building_name
        if(request()->has('building_name') && !empty(request()->building_name)){
            // building from model
            $appartments->whereHas('building', function($query){
                $query->where('building_name', 'like', '%'.request()->building_name.'%');
            });
        }
        // apartment_type
        if(request()->has('apartment_type') && !empty(request()->apartment_type)){
            $appartments->where('apartment_type', 'like', '%'.request()->apartment_type.'%');
        }
        // category
        if(request()->has('category') && !empty(request()->category)){
            $appartments->where('category', 'like', '%'.request()->category.'%');
        }
        // no_of_rooms
        if(request()->has('no_of_rooms') && !empty(request()->no_of_rooms)){
            $appartments->where('number_of_rooms', 'like', '%'.request()->no_of_rooms.'%');
        }
        // apartment_size
        if(request()->has('apartment_size') && !empty(request()->apartment_size)){
            $appartments->where('apartment_size', 'like', '%'.request()->apartment_size.'%');
        }
        // status
        if(request()->has('status') && !empty(request()->status)){
            $appartments->where('status', 'like', '%'.request()->status.'%');
        }
        // $data['appartments_list'] = Appartment::with('images', 'building')->orderBy('created_at', 'Desc')->get();
        $data['appartments_list'] = $appartments->get();
        $data['pent_house_appartments_list'] = Appartment::where('apartment_type', 'Penthouse')->count();
        $data['appartment_appartments_list'] = Appartment::where('apartment_type', 'Appartment')->count();
        $data['studio_appartments_list'] = Appartment::where('apartment_type', 'Studio')->count();
        $data['total_appartments_list'] = Appartment::all()->count();
        return response()->json(['status' => 200, 'appartments_list' => $data]);
    }

    public function add_appartment()
    {
        $data['page'] = 'Appartments';
        $data['buildings'] = Buildings::all();
        return view('admin/addappartment')->with($data);
    }

    public function store_appartment(Request $request)
    {

        // dd($request->all());
        $request->validate([
            'building' => 'required',
            'apartment_no' => 'string|max:20',
            'apartment_name' => 'required|max:50',
            'category' => 'required',
            'apartment_type' => 'required',
            // 'number_of_rooms' => 'required|numeric',
            'apartment_size' => 'required|numeric',
            // 'status' => 'required',
            'unit_purchase_price' => 'required|numeric',
            'landlord_name' => 'required|max:50',
            'landlord_contact_number' => 'required|max:18|min:7',
            // 'reference_number' => 'required|max:18',
            // 'description' => 'required|max:255',
            'photos.*' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',

        ]);


        if ($request->apartment_type == 'Appartment') {
            $request->validate([
                'number_of_rooms' => 'required|numeric',
            ]);
        }
        $building_check = Buildings::find($request->building);
        $appartments_check = Appartment::where('building_id', $request->building)->count();
        if ($appartments_check < $building_check->number_of_apartments) {

            $appartment = new Appartment;
            $appartment->building_id = $request->building;
            $appartment->apartment_no = $request->apartment_no;
            $appartment->apartment_name = $request->apartment_name;
            $appartment->category = $request->category;
            $appartment->apartment_type = $request->apartment_type;
            if ($request->apartment_type == 'Appartment') {

                $appartment->number_of_rooms = $request->number_of_rooms;
            }
            $appartment->apartment_size = $request->apartment_size;
            $appartment->status = 'Available';
            $appartment->unit_purchase_price = $request->unit_purchase_price;
            $appartment->landlord_name = $request->landlord_name;
            $appartment->landlord_contact_number = $request->landlord_contact_number;
            $appartment->reference_number = $request->reference_number;
            $appartment->description = $request->description;
            $appartment->created_by = Auth::id();
            $appartment->updated_by = Auth::id();
            $appartment->save();
            if ($request->hasFile('photos')) {

                $path = '/uploads/appartment_images/' . $appartment->id;
                $uploadedFile = $request->file('photos');
                $savedImages = saveMultipleImages($uploadedFile, $path);
                foreach ($savedImages as $image) {
                    $appartment_image = new AppartmentImages;
                    $appartment_image->appartment_id = $appartment->id;
                    $appartment_image->image_path = url('/public/') . $image;
                    $appartment_image->created_by = Auth::id();
                    $appartment_image->save();
                }
            }
            return response()->json(['status' => 200, 'message' => "Appartment Added successfully"]);
        } else {
            return response()->json(['status' => 402, 'message' => "Appartment limit exceeded, please update the limit first."]);
        }
    }


    public function delete_appartment(Request $request)
    {
        $appartment_id = $request->del_id;
        $taskcheck = Tasks::where('apartment', $appartment_id)->count();
        if ($taskcheck > 0) {
            return response()->json(['status' => 402, 'message' => "Appartment can not be deleted as tasks are assigned for this appartment"]);
        }
        $appartment = Appartment::find($appartment_id);

        if (!$appartment) {
            return response()->json(['status' => 402, 'message' => "Appartment not found"]);
        } else {
            $appartment->delete();
            return response()->json(['status' => 200, 'message' => "Appartment deleted successfully"]);
        }

        // try {
        //     $images= BuildingImages::where('appartment_id', $appartment_id)->get();
        //     foreach($images as $image){

        //         deleteImage(str_replace(url('/'), '', $image));
        //     }
        //     $appartment->delete();
        //     return response()->json(['status' => 200, 'message' => "Building deleted successfully"]);
        // } catch (\Exception $e) {

        //     return response()->json(['status' => 500, 'message' => "Failed to delete appartment"]);
        // }
    }


    public function edit_appartment($id)
    {
        $data['page'] = 'Appartments';
        $data['buildings'] = Buildings::all();
        $data['appartment'] = Appartment::where('id', $id)->with('images')->first();
        return view('admin/editappartment')->with($data);
    }

    public function update_appartment(Request $request)
    {
        // dd($request->all());

        $request->validate([
            'building' => 'required',
            'apartment_no' => 'string|max:20',
            'apartment_name' => 'required|max:50',
            'category' => 'required',
            'apartment_type' => 'required',
            // 'number_of_rooms' => 'required|numeric',
            'apartment_size' => 'required|numeric',
            'status' => 'required',
            'unit_purchase_price' => 'required|numeric',
            'landlord_name' => 'required|max:50',
            'landlord_contact_number' => 'required|numeric',
            // 'reference_number' => 'required|numeric',
            // 'description' => 'required|max:255',
            'photos.*' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',

        ]);

        if ($request->apartment_type == 'Appartment') {
            $request->validate([
                'number_of_rooms' => 'required|numeric',
            ]);
        }
        if ($request->hasFile('photos')) {

            $path = '/uploads/appartment_images/' . $request->appartment_id;
            $uploadedFile = $request->file('photos');
            $savedImages = saveMultipleImages($uploadedFile, $path);
            foreach ($savedImages as $image) {
                $appartment_image = new AppartmentImages;
                $appartment_image->appartment_id = $request->appartment_id;
                $appartment_image->image_path = url('/public/') . $image;
                $appartment_image->created_by = Auth::id();
                $appartment_image->save();
            }
        }
        if (isset($request->removed_image_ids) && !empty($request->removed_image_ids)) {
            $removed_image_ids = $request->removed_image_ids;

            foreach ($removed_image_ids as $removed_image_id) {

                deleteImage(str_replace(url('/public/'), '', $removed_image_id));
                AppartmentImages::where('image_path', $removed_image_id)->delete();



            }


        }

        $appartment = Appartment::where('id', $request->appartment_id)->first();

        $appartment->building_id = $request->building;
        $appartment->apartment_no = $request->apartment_no;
        $appartment->apartment_name = $request->apartment_name;
        $appartment->category = $request->category;
        $appartment->apartment_type = $request->apartment_type;
        if ($request->apartment_type == 'Appartment') {

            $appartment->number_of_rooms = $request->number_of_rooms;
        }
        $appartment->apartment_size = $request->apartment_size;
        $appartment->status = $request->status;
        $appartment->unit_purchase_price = $request->unit_purchase_price;
        $appartment->landlord_name = $request->landlord_name;
        $appartment->landlord_contact_number = $request->landlord_contact_number;
        $appartment->reference_number = $request->reference_number;
        $appartment->description = $request->description;
        $appartment->updated_by = Auth::id();
        $appartment->save();
        return response()->json(['status' => 200, 'message' => "Appartment Updated successfully"]);

    }

    public function assigned_tasks()
    {
        $data['page'] = 'Tasks';
        $data['managers'] = User::where('type', 'manager')->get();
        $data['buildings'] = Buildings::all();
        $data['appartments'] = Appartment::all();
        return view('admin/assigned_tasks')->with($data);
    }

    public function add_task_view()
    {
        $data['page'] = 'Tasks';
        $data['managers_list'] = User::where('type', 'manager')->where('status', 1)->get();
        $data['buildings_list'] = Buildings::all();
        return view('admin/add_task')->with($data);
    }

    public function get_appartment_list(Request $request)
    {
        $data['appartment_list'] = Appartment::where('building_id', $request->building_id)
            ->get();
        return response()->json(['status' => 200, 'appartment_list' => $data]);
    }

    public function store_task(Request $request)
    {
        $request->validate([
            'task_title' => 'required|string|max:50',
            'priority' => 'required|integer',
            'attachment' => 'required|file|max:2048',
            'document_type' => 'required|integer',
            'building' => 'required|integer',
            'appartment' => 'required|integer',
            'manager' => 'required|integer',
            'description' => 'required',
            // 'status' => 'required|integer',
        ]);


        $task = new Tasks;
        $task->task_title = $request->task_title;
        $task->priority = $request->priority;
        $task->document = $request->attachment;
        $task->document_type = $request->document_type;
        $task->building = $request->building;
        $task->apartment = $request->appartment;
        $task->manager = $request->manager;
        $task->description = $request->description;
        $task->status = '1';
        $task->document_status = '0';
        $task->created_by = Auth::id();
        $task->updated_by = Auth::id();
        $task->save();
        $manager = User::find($request->manager);
        $building = Buildings::find($request->building);
        $appartment = Appartment::find($request->appartment);

        if ($request->hasFile('attachment')) {

            $path = '/uploads/tasks_attachments/' . $task->id;
            $uploadedFile = $request->file('attachment');
            $savedFile = saveSingleImage($uploadedFile, $path);
            $full_path = url('/public/') . $savedFile;
            $task->document = $full_path;
            $task->save();
        }
        $task_notification = new TaskNotifications;
        $task_notification->task_id = $task->id;
        $task_notification->manager_email = $manager->email;
        $task_notification->admin_email = env('ADMIN_EMAIL');
        $task_notification->comment = 'Task Assigned to';
        $task_notification->action = 'Task Assigned';
        $task_notification->manager_id = $request->manager;;
        $task_notification->created_by = Auth::id();
        
        $task_notification->task_status = $task->status;
        $task_notification->save();
        $mailData['name'] = trim(($manager->first_name ?? '') . ' ' . ($manager->middle_name ?? '') . ' ' . ($manager->last_name ?? ''));

        $mailData['task_title'] = $task->task_title;
        $mailData['description'] = $task->description;
        $mailData['building'] = $building->building_name;
        $mailData['appartment'] = $appartment->apartment_name;
        $mailData['maintext'] = 'You have been assigned new task, kindly check the details below';
        $mailData['date'] = date('d F y');
        $body = view('emails.assign_task', $mailData);
        $userEmailsSend[] = $manager->email;
        // to username, to email, from username, subject, body html
        sendMail(trim(($manager->first_name ?? '') . ' ' . ($manager->middle_name ?? '') . ' ' . ($manager->last_name ?? '')), $userEmailsSend, 'GALAXY CRM', 'Task Assigned', $body);


        // notify admin
        $mailData1['name'] = 'Admin';
        $mailData1['task_title'] = $task->task_title;
        $mailData1['description'] = $task->description;
        $mailData1['building'] = $building->building_name;
        $mailData1['appartment'] = $appartment->apartment_name;
        $mailData1['maintext'] = 'New task assigned to ' . trim(($manager->first_name ?? '') . ' ' . ($manager->middle_name ?? '') . ' ' . ($manager->last_name ?? '')) . ', check details below';
        $mailData1['date'] = date('d F y');
        $body = view('emails.assign_task', $mailData1);
        $userEmailsSend1[] = env('ADMIN_EMAIL');
        // to username, to email, from username, subject, body html
        sendMail('Admin', $userEmailsSend1, 'GALAXY CRM', 'Task Assigned', $body);


        return response()->json(['status' => 200, 'message' => "Task Added Successfully"]);
    }

    public function get_tasks_list()
    {
        $data['tasks_list'] = Tasks::with('building', 'appartment', 'manager')->whereIn('status', [0, 1, 2, 3, 4])
            ->orderByRaw('FIELD(priority, 2, 1, 0)')
            ->orderBy('created_at', 'desc')->get();
        $data['done_tasks_list'] = Tasks::with(['building', 'appartment', 'manager'])
            ->where('status', 5)
            ->orderByRaw('FIELD(priority, 2, 1, 0)')
            ->orderBy('created_at', 'desc')->get();
        $data['cancelled_tasks_list'] = Tasks::with(['building', 'appartment', 'manager'])
            ->where('status', 6)
            ->orderByRaw('FIELD(priority, 2, 1, 0)')
            ->orderBy('created_at', 'desc')->get();
        $data['total_tasks'] = Tasks::count();
        $data['assigned_tasks'] = Tasks::where('status', 1)->count();
        $data['working_on_tasks'] = Tasks::where('status', 2)->count();
        $data['hold_tasks'] = Tasks::where('status', 3)->count();
        $data['stuck_tasks'] = Tasks::where('status', 4)->count();
        $data['done_tasks'] = Tasks::where('status', 5)->count();
        $data['cancelled_tasks'] = Tasks::where('status', 6)->count();
        return response()->json(['status' => 200, 'tasks_list' => $data]);
    }

    public function edit_task($id)
    {
        $data['page'] = 'Tasks';
        $data['managers_list'] = User::where('type', 'manager')->where('status', 1)->get();
        $task = Tasks::where('id', $id)->with('createdBy', 'updatedBy')->first();
        // dd($task);
        // if($task->status != 5){
        //     return redirect()->back()->with('error','This task is not done yet, you can not edit this task');
        // }
        $data['task'] = $task;
        $data['buildings_list'] = Buildings::all();
        $data['appartments_list'] = Appartment::all();
        return view('admin/edit_task')->with($data);
    }

    // public function update_task(Request $request)
    // {
    //     $request->validate([
    //         'task_title' => 'required|string|max:50',
    //         'priority' => 'required|integer',
    //         'document_type' => 'required|integer',
    //         'building' => 'required|integer',
    //         'appartment' => 'required|integer',
    //         'manager' => 'required|integer',
    //         'description' => 'required',
    //         'status' => 'required',
    //         'edit_reason' => 'required'
    //     ]);

    //     if ($request->hasFile('attachment')) {
    //         $request->validate([
    //             'document_type' => 'required|integer',
    //         ]);
    //     }

    //     $task = Tasks::where('id', $request->task_id)->first();
    //     if ($task->manager != $request->manager) {
    //         $managerChanged = '1';
    //     } else {
    //         $managerChanged = '0';
    //     }


    //     if ($task->building != $request->building) {
    //         $buildingChanged = '1';
    //     } else {
    //         $buildingChanged = '0';
    //     }


    //     if ($task->appartment != $request->appartment) {
    //         $appartmentChanged = '1';
    //     } else {
    //         $appartmentChanged = '0';
    //     }


    //     if ($task->status != $request->status) {
    //         // was done and now reassigned 
    //         $statusChanged = '1';
    //         if ($task->status == '5' && $request->status == '1') {
    //             $reAssignedTask = '1';
    //         } else {
    //             $reAssignedTask = '0';
    //         }
    //         // was cancelled and now reopened 
    //         if ($task->status == '6' && $request->status == '1') {
    //             $reOpenedTask = '1';
    //         } else {
    //             $reOpenedTask = '1';
    //         }

    //         if ($request->hasFile('attachment')) {
    //             $attachmentChanged = '1';
    //         } else {
    //             $attachmentChanged = '0';
    //         }
    //     } else {
    //         $statusChanged = '0';
    //     }

    //     if ($request->hasFile('attachment')) {
    //         $del_path = str_replace(url('/public/'), '', $task->document);
    //         deleteImage($del_path);
    //         $path = '/uploads/tasks_attachments/' . $task->id;
    //         $uploadedFile = $request->file('attachment');
    //         $savedFile = saveSingleImage($uploadedFile, $path);
    //         $full_path = url('/public/') . $savedFile;
    //         $task->document = $full_path;
    //         $task->save();
    //     }

    //     $task->task_title = $request->task_title;
    //     $task->priority = $request->priority;
    //     $task->document_type = $request->document_type;
    //     $task->building = $request->building;
    //     $task->apartment = $request->appartment;
    //     $task->manager = $request->manager;
    //     $task->description = $request->description;
    //     if ($request->hasFile('attachment')) {
    //         $task->document_status = '0';
    //     }
    //     $task->status = $request->status;
    //     $task->updated_by = Auth::id();
    //     $task->save();

    //     $task = Tasks::with('building', 'appartment', 'manager')->where('id', $request->task_id)->first();
    //     $manager = User::find($task->manager);
    //     $building = Buildings::find($task->building);
    //     $appartment = Appartment::find($task->apartment);
    //     if ($statusChanged = '1' || $statusChanged = 1 ) {
    //         if ($reAssignedTask == '1' || $reAssignedTask == 1) {
    //             $task_notification = new TaskNotifications;
    //             $task_notification->task_id = $request->task_id;
    //             $task_notification->manager_email = $manager->email;
    //             $task_notification->admin_email = env('ADMIN_EMAIL');
    //             $task_notification->comment = 'Task Status Updated, ' . $request->edit_reason;
    //             $task_notification->created_by = Auth::id();
    //             $task_notification->task_status = 11; //Reassigned
    //             $task_notification->save();
    //         }
    //         else{

    //         }
    //         if ($reOpenedTask == '1' || $reOpenedTask == 1) {
    //             $task_notification = new TaskNotifications;
    //             $task_notification->task_id = $request->task_id;
    //             $task_notification->manager_email = $manager->email;
    //             $task_notification->admin_email = env('ADMIN_EMAIL');
    //             $task_notification->comment = 'Task Status Updated, ' . $request->edit_reason;
    //             $task_notification->created_by = Auth::id();
    //             $task_notification->task_status = 22; //Reopened
    //             $task_notification->save();
    //         }
    //         else{

    //         }
    //     }
    //     else{
    //     $task_notification = new TaskNotifications;
    //     $task_notification->task_id = $request->task_id;
    //     $task_notification->manager_email = $manager->email;
    //     $task_notification->admin_email = env('ADMIN_EMAIL');
    //     $task_notification->comment = $request->edit_reason;
    //     $task_notification->created_by = Auth::id();
    //     $task_notification->task_status = $request->status;
    //     $task_notification->save();
    //     }
    //     // now check manager changed 
    //     if($managerChanged = '1' || $managerChanged = 1){
    //         $task_notification = new TaskNotifications;
    //         $task_notification->task_id = $request->task_id;
    //         $task_notification->manager_email = $manager->email;
    //         $task_notification->admin_email = env('ADMIN_EMAIL');
    //         $task_notification->comment = 'Manager Changed, '.$request->edit_reason;
    //         $task_notification->created_by = Auth::id();
    //         $task_notification->task_status = $request->status;
    //         $task_notification->save();
    //     }
    //     else{

    //     }
    //     // now check building changed 
    //     if($buildingChanged == '1'  || $buildingChanged == 1 ){
    //         $task_notification = new TaskNotifications;
    //         $task_notification->task_id = $request->task_id;
    //         $task_notification->manager_email = $manager->email;
    //         $task_notification->admin_email = env('ADMIN_EMAIL');
    //         $task_notification->comment = 'Building Changed, '.$request->edit_reason;
    //         $task_notification->created_by = Auth::id();
    //         $task_notification->task_status = $request->status;
    //         $task_notification->save();
    //     }
    //     else{

    //     }

    //      // now check apartment changed 
    //      if($appartmentChanged == '1'  || $appartmentChanged == 1 ){
    //         $task_notification = new TaskNotifications;
    //         $task_notification->task_id = $request->task_id;
    //         $task_notification->manager_email = $manager->email;
    //         $task_notification->admin_email = env('ADMIN_EMAIL');
    //         $task_notification->comment = 'Apartment Changed, '.$request->edit_reason;
    //         $task_notification->created_by = Auth::id();
    //         $task_notification->task_status = $request->status;
    //         $task_notification->save();
    //     }
    //     else{

    //     }

    //     // now check document changed 
    //     if($attachmentChanged == '1'  || $attachmentChanged == 1 ){
    //         $task_notification = new TaskNotifications;
    //         $task_notification->task_id = $request->task_id;
    //         $task_notification->manager_email = $manager->email;
    //         $task_notification->admin_email = env('ADMIN_EMAIL');
    //         $task_notification->comment = 'Document Updated, '.$request->edit_reason;
    //         $task_notification->created_by = Auth::id();
    //         $task_notification->task_status = $request->status;
    //         $task_notification->save();
    //     }
    //     else{

    //     }

        
    //     if ($request->status == 0 || $request->status == '0') {
    //         $statustxt = 'Draft';
    //     }

    //     if ($request->status == 1 || $request->status == '1') {
    //         $statustxt = 'Assigned';
    //     }

    //     if ($request->status == 2 || $request->status == '2') {
    //         $statustxt = 'Working On it';
    //     }

    //     if ($request->status == 3 || $request->status == '3') {
    //         $statustxt = 'Hold';
    //     }

    //     if ($request->status == 4 || $request->status == '4') {
    //         $statustxt = 'Stuck';
    //     }

    //     if ($request->status == 5 || $request->status == '5') {
    //         $statustxt = 'Done';
    //     }
    //     if ($request->status == 6 || $request->status == '6') {
    //         $statustxt = 'Cancelled';
    //     }

    //     if ($managerChanged == 1 || $managerChanged == '1') {

    //         $mailData['name'] = trim(($manager->first_name ?? '') . ' ' . ($manager->middle_name ?? '') . ' ' . ($manager->last_name ?? ''));

    //         $mailData['task_title'] = $task->task_title;
    //         $mailData['description'] = $task->description;
    //         $mailData['building'] = $building->building_name;
    //         $mailData['appartment'] = $appartment->apartment_name;
    //         $mailData['maintext'] = 'You have been assigned new task, kindly check the details below';
    //         $mailData['date'] = date('d F y');
    //         $body = view('emails.assign_task', $mailData);
    //         $userEmailsSend[] = $manager->email;
    //         // to username, to email, from username, subject, body html
    //         sendMail(trim(($manager->first_name ?? '') . ' ' . ($manager->middle_name ?? '') . ' ' . ($manager->last_name ?? '')), $userEmailsSend, 'GALAXY CRM', 'Task Assigned', $body);

    //     } else {
    //         $mailData['name'] = $manager->first_name;
    //         $mailData['task_title'] = $task->task_title;
    //         $mailData['building'] = $building->building_name;
    //         $mailData['appartment'] = $appartment->apartment_name;
    //         $mailData['comment'] = $request->edit_reason;


    //         $mailData['statustxt'] = $statustxt;
    //         $body = view('emails.task_status_update', $mailData);
    //         $userEmailsSend[] = $manager->email;


    //         // to username, to email, from username, subject, body html

    //         sendMail($manager->first_name, $userEmailsSend, 'GALAXY CRM', 'Task Status Updated', $body);
    //     }


    //     // send mail to admin 
    //     $mailData1['name'] = 'ADMIN';
    //     $mailData1['task_title'] = $task->task_title;
    //     $mailData1['building'] = $building->building_name;
    //     $mailData1['appartment'] = $appartment->apartment_name;
    //     $mailData1['statustxt'] = $statustxt;
    //     $mailData1['comment'] = $request->edit_reason;
    //     $body = view('emails.task_status_update', $mailData1);
    //     $admin_mail = env('ADMIN_EMAIL');
    //     sendMail('Admin', $admin_mail, 'GALAXY CRM', 'Task Status Updated', $body);


    //     return response()->json(['status' => 200, 'message' => "Task Updated Successfully"]);

    // }

    public function update_task(Request $request)
    {
        $request->validate([
                    'task_title' => 'required|string|max:50',
                    'priority' => 'required|integer',
                    'document_type' => 'required|integer',
                    'building' => 'required|integer',
                    'appartment' => 'required|integer',
                    'manager' => 'required|integer',
                    'description' => 'required',
                    'status' => 'required',
                    'edit_reason' => 'required'
                ]);
        
                if ($request->hasFile('attachment')) {
                    $request->validate([
                        'document_type' => 'required|integer',
                    ]);
                }

        $task = Tasks::findOrFail($request->task_id);

        
        $managerChanged = $task->manager != $request->manager ? true : false;
        $buildingChanged = $task->building != $request->building ? true : false;
        $apartmentChanged = $task->apartment != $request->appartment ? true : false;
        $statusChanged = $task->status != $request->status ? true : false;

        $reAssignedTask = $statusChanged && $task->status == '5' && $request->status == '1';
        $reOpenedTask = $statusChanged && $task->status == '6' && $request->status == '1';

        if ($request->hasFile('attachment')) {
            $this->updateAttachment($task, $request->file('attachment'));
            $attachmentChanged = true;
        } else {
            $attachmentChanged = false;
        }

        $task->update([
            'task_title' => $request->task_title,
            'priority' => $request->priority,
            'document_type' => $request->document_type,
            'building' => $request->building,
            'apartment' => $request->apartment,
            'manager' => $request->manager,
            'description' => $request->description,
            'status' => $request->status,
            'updated_by' => Auth::id(),
            'document_status' => $attachmentChanged ? '0' : $task->document_status
        ]);

        $this->createNotifications($task, $request, $managerChanged, $buildingChanged, $apartmentChanged, $statusChanged, $reAssignedTask, $reOpenedTask, $attachmentChanged);

        $this->sendEmailNotifications($task, $request, $managerChanged);

        return response()->json(['status' => 200, 'message' => "Task Updated Successfully"]);
    }

    private function updateAttachment($task, $uploadedFile)
    {
        $del_path = str_replace(url('/public/'), '', $task->document);
        deleteImage($del_path);
        $path = '/uploads/tasks_attachments/' . $task->id;
        $savedFile = saveSingleImage($uploadedFile, $path);
        $task->document = url('/public/') . $savedFile;
        $task->save();
    }

    private function createNotifications($task, $request, $managerChanged, $buildingChanged, $apartmentChanged, $statusChanged, $reAssignedTask, $reOpenedTask, $attachmentChanged)
    {
        
        $manager = User::find($task->manager);
        $notificationData = [
            'task_id' => $request->task_id,
            'manager_email' => $manager->email,
            'admin_email' => env('ADMIN_EMAIL'),
            'comment' => $request->edit_reason,
            'created_by' => Auth::id(),
            'task_status' => $request->status
        ];

        if ($statusChanged) {
            if ($reAssignedTask) {
                $notificationData['task_status'] = 11; // Reassigned
                $notificationData['action'] = 'Improvements Needed';
                $this->saveNotification($notificationData);
            }
            if ($reOpenedTask) {
                $notificationData['task_status'] = 22; // Reopened
                $notificationData['action'] = 'Task Continued';
                $this->saveNotification($notificationData);
            }
            if($reAssignedTask == false && $reOpenedTask== false){
                $notificationData['task_status'] = $request->status;
                $notificationData['action'] = 'Task Status Updated';
                $notificationData['comment'] = $request->edit_reason;
                $this->saveNotification($notificationData);
            }
        }

        if ($managerChanged) {
            $notificationData['action'] = 'Manager Changed';
            $notificationData['comment'] = 'Manager Changed to ';
            $this->saveNotification($notificationData);
        }

        if ($buildingChanged) {
            $notificationData['action'] = 'Building Changed';
            $notificationData['comment'] = $request->edit_reason;
            $this->saveNotification($notificationData);
        }

        if ($apartmentChanged) {
            $notificationData['action'] = 'Apartment Changed';
            $notificationData['comment'] = $request->edit_reason;
            $this->saveNotification($notificationData);
        }

        if ($attachmentChanged) {
            $notificationData['action'] = 'Document Updated';
            $notificationData['comment'] = $request->edit_reason;
            $this->saveNotification($notificationData);
        }

        // Save the default notification
        if (!$statusChanged && !$managerChanged && !$buildingChanged && !$apartmentChanged && !$attachmentChanged) {
            $notificationData['action'] = 'Task Updated';
            $this->saveNotification($notificationData);
        }
    }

    private function saveNotification($data)
    {
        $user  = Auth::user();
        if($user->type=='admin'){
            $data['created_by'] = '1';
        }
        else{
            $data['created_by'] = '2';
        }
        $task_id =$data['task_id'];
        $task = Tasks::find($task_id);
        $data['manager_id'] = $task->manager;
        TaskNotifications::create($data);
    }

    private function sendEmailNotifications($task, $request, $managerChanged)
    {
        $manager = User::find($task->manager);
        $building = Buildings::find($task->building);
        $apartment = Appartment::find($task->apartment);

        $mailData = [
            'name' => trim(($manager->first_name ?? '') . ' ' . ($manager->middle_name ?? '') . ' ' . ($manager->last_name ?? '')),
            'task_title' => $task->task_title,
            'description' => $task->description,
            'building' => $building->building_name,
            'appartment' => $apartment->apartment_name,
            'maintext' => $managerChanged ? 'You have been assigned new task, kindly check the details below' : null,
            'date' => date('d F y'),
            'comment' => $request->edit_reason,
            'statustxt' => $this->getStatusText($request->status)
        ];

        $userEmailsSend[] = $manager->email;

        if ($managerChanged) {
            $body = view('emails.assign_task', $mailData);
            sendMail($mailData['name'], $userEmailsSend, 'GALAXY CRM', 'Task Assigned', $body);
        } else {
            $body = view('emails.task_status_update', $mailData);
            sendMail($manager->first_name, $userEmailsSend, 'GALAXY CRM', 'Task Status Updated', $body);
        }

        // send mail to admin
        $mailData1 = $mailData;
        $mailData1['name'] = 'ADMIN';
        $body = view('emails.task_status_update', $mailData1);
        sendMail('Admin', env('ADMIN_EMAIL'), 'GALAXY CRM', 'Task Status Updated', $body);
    }

    private function getStatusText($status)
    {
        switch ($status) {
            case 0:
                return 'Draft';
            case 1:
                return 'Assigned';
            case 2:
                return 'Working On it';
            case 3:
                return 'Hold';
            case 4:
                return 'Stuck';
            case 5:
                return 'Done';
            case 6:
                return 'Cancelled';
            case 11:
                return 'Reassigned';
            case 22:
                return 'Reopened';
            default:
                return 'Unknown Status';
        }
    }


    public function delete_task(Request $request)
    {
        $task_id = $request->del_id;
        $task = Tasks::find($task_id);
        if (!$task) {
            return response()->json(['status' => 402, 'message' => "Task not found"]);
        } else {
            $tasknotifications = TaskNotifications::where('task_id', $task_id)->get();
            if ($tasknotifications) {
                foreach ($tasknotifications as $notification) {
                    $notification->delete();
                }
            }
            if ($task->delete()) {
                // deleteImage(str_replace(url('/public/'), '', $task->document));
                return response()->json(['status' => 200, 'message' => "Task deleted successfully"]);
            } else {
                return response()->json(['status' => 402, 'message' => "Failed to delete task"]);
            }
        }

    }

    public function get_time_line_details(Request $request)
    {
        $task_id = $request->task_id;
        $data['to_do_details'] = TaskToDoList::where('task_id', $task_id)->get();
        $data['status_timeline_details'] = TaskNotifications::with('manager')->where('task_id', $task_id)->get();
        return response()->json(['status' => 200, 'data' => $data]);

    }

    public function forgotpassword()
    {
        return view('admin/forgot_password');
    }
    public function forgot_password_validate_email(Request $request)
    {

        $request->validate([
            'email' => 'required|email',

        ]);

        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return response()->json(['status' => 402, 'message' => "Email is not registered in our system"]);
        } else {
            $mailData = [];
            $otp = implode('', array_map(function () {
                return mt_rand(0, 9);
            }, range(1, 5)));
            $user->otp_code = $otp;
            $user->otp_created_at = date('Y-m-d H:i:s');
            $user->save();
            $mailData['otp'] = $otp;
            $mailData['username'] = $user->first_name;
            $body = view('emails.forgot_password', $mailData);
            $userEmailsSend[] = $user->email;
            // to username, to email, from username, subject, body html

            sendMail($user->first_name, $userEmailsSend, 'Galaxy CRM', 'Password Reset Request', $body); // send_to_name, send_to_email, email_from_name, subject, body
            return response()->json(['status' => 200, 'message' => "OTP is sent to your registered email"]);

        }

    }

    public function verify_otp(Request $request)
    {
        $request->validate([
            'otp' => 'required|max:5',

        ]);
        $otp = $request->otp;
        $email = $request->email;

        $user = User::where('email', $request->email)->first();
        if ($user->otp_code == null) {
            return response()->json(['status' => 402, 'message' => "Invalid request"]);
        }
        if ($otp == $user->otp_code) {
            return response()->json(['status' => 200, 'message' => "OTP validated, kindly enter your new password"]);
        } else {
            return response()->json(['status' => 402, 'message' => "OTP mismatch, kindly use the OTP we sent on your email"]);

        }
    }

    public function reset_password(Request $request)
    {
        $request->validate(
            [
                'password' => [
                    'required',
                    'string',
                    'min:8', // Minimum length of 8 characters
                    'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).+$/',
                    'confirmed',
                ],

            ],
            [
                'password.regex' => 'The new password must contain at least one uppercase letter, one lowercase letter, one number, and one special character.',
            ]
        );

        $user = User::where('email', $request->email)->first();
        if ($user) {
            $user->password = bcrypt($request->input('password'));
            $user->save();
            return response()->json(['status' => 200, 'message' => "Passwrd changed successfully, kindly return to login page and login again"]);

        }

    }

    public function get_filtered_tasks(Request $request)
    {

        $tasks = Tasks::with('building', 'appartment', 'manager')
            ->orderByRaw('FIELD(priority, 2, 1, 0)')
            ->orderBy('created_at', 'desc');

        if ($request->filled('building_filter')) {
            $tasks = $tasks->where('building', $request->building_filter);
        }
        if ($request->filled('appartment_filter')) {
            $tasks = $tasks->where('apartment', $request->appartment_filter);
        }
        if ($request->filled('priority_filter')) {
            $tasks = $tasks->where('priority', $request->priority_filter);
        }
        if ($request->filled('doc_status_filter')) {
            $tasks = $tasks->where('document_status', $request->doc_status_filter);
        }
        if ($request->filled('doc_type_filter')) {
            $tasks = $tasks->where('document_type', $request->doc_type_filter);
        }
        if ($request->filled('manager_filter')) {
            $tasks = $tasks->where('manager', $request->manager_filter);
        }
        if ($request->filled('task_status_filter')) {
            $tasks = $tasks->where('status', $request->task_status_filter);
        } else {
            $tasks = $tasks->whereIn('status', [0, 1, 2, 3, 4]);
        }

        $tasks = $tasks->get();

        if ($tasks->isNotEmpty()) {
            return response()->json(['status' => 200, 'tasks' => $tasks]);
        } else {
            return response()->json(['status' => 402, 'message' => "No record found"]);
        }
    }


    public function get_filtered_done_tasks(Request $request)
    {

        $tasks = Tasks::with('building', 'appartment', 'manager')->where('status', 5)
            ->orderByRaw('FIELD(priority, 2, 1, 0)')
            ->orderBy('created_at', 'desc');

        if ($request->filled('building_filter')) {
            $tasks = $tasks->where('building', $request->building_filter);
        }
        if ($request->filled('appartment_filter')) {
            $tasks = $tasks->where('apartment', $request->appartment_filter);
        }
        if ($request->filled('priority_filter')) {
            $tasks = $tasks->where('priority', $request->priority_filter);
        }
        if ($request->filled('doc_status_filter')) {
            $tasks = $tasks->where('document_status', $request->doc_status_filter);
        }
        if ($request->filled('document_type_filter1')) {
            $tasks = $tasks->where('document_type', $request->document_type_filter1);
        }
        if ($request->filled('manager_filter')) {
            $tasks = $tasks->where('manager', $request->manager_filter);
        }


        $tasks = $tasks->get();

        if ($tasks->isNotEmpty()) {
            return response()->json(['status' => 200, 'tasks' => $tasks]);
        } else {
            return response()->json(['status' => 402, 'message' => "No record found"]);
        }
    }


    public function makeanalyticsgraph()
    {

        $startDate = now()->subDays(15)->startOfDay();
        $endDate = now()->endOfDay();


        $results = Tasks::select(
            DB::raw('DATE(updated_at) AS task_date'),
            DB::raw('SUM(CASE WHEN status = "0" THEN 1 ELSE 0 END) AS draft_count'),
            DB::raw('SUM(CASE WHEN status = "1" THEN 1 ELSE 0 END) AS assigned_count'),
            DB::raw('SUM(CASE WHEN status = "2" THEN 1 ELSE 0 END) AS working_on_count'),
            DB::raw('SUM(CASE WHEN status = "3" THEN 1 ELSE 0 END) AS hold_count'),
            DB::raw('SUM(CASE WHEN status = "4" THEN 1 ELSE 0 END) AS stuck_count'),
            DB::raw('SUM(CASE WHEN status = "5" THEN 1 ELSE 0 END) AS done_count'),
        )
            ->whereBetween('updated_at', [$startDate, $endDate])
            ->groupBy('task_date')
            ->orderBy('task_date')
            ->get();


        echo json_encode($results);

    }

    public function edit_profile()
    {
        $data['page'] = 'Profile';
        return view('admin/profile')->with($data);
    }

    public function get_profile_data()
    {
        $user_id = Auth::id();
        $user = User::find($user_id);
        return response()->json(['status' => 200, 'user' => $user]);

    }

    public function update_profile(Request $request)
    {
        $user_id = Auth::id();
        $user = User::find($user_id);

        $request->validate([
            'first_name' => 'required| max:40',
            // 'middle_name' => 'required| max:40',
            'last_name' => 'required| max:40',
            'contact_number' => 'max:15|min:7'
        ]);

        if ($request->password) {
            $request->validate(
                [
                    'first_name' => 'required| max:40',
                    // 'middle_name' => 'required| max:40',
                    'last_name' => 'required| max:40',
                    'contact_number' => 'max:15|min:7',
                    'old_password' => 'required',
                    'password' => [
                            'required',
                            'string',
                            'min:8',
                            'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).+$/',
                            'confirmed',
                        ],

                ],
                [
                    'password.regex' => 'The new password must contain at least one uppercase letter, one lowercase letter, one number, and one special character.',
                ]
            );
            $credentials = [
                'email' => $user->email,
                'password' => $request->old_password,
            ];
            if (Auth::attempt($credentials)) {
                $user->first_name = $request->first_name;
                $user->middle_name = $request->middle_name;
                $user->last_name = $request->last_name;
                $user->contact_number = $request->contact_number;
                $user->password = Hash::make($request->password);
                $user->save();
            } else {
                return response()->json(['status' => 402, 'message' => "Old password is incorrect"]);
            }
        } else {
            $user->first_name = $request->first_name;
            $user->middle_name = $request->middle_name;
            $user->last_name = $request->last_name;
            $user->contact_number = $request->contact_number;
            $user->save();
        }

        if ($request->hasFile('profile_image')) {
            if ($user->profile_image != null) {
                deleteImage(str_replace(url('/public/'), '', $user->profile_image));
            }
            $path = '/uploads/profile_images/' . $user_id;
            $uploadedFile = $request->file('profile_image');
            $savedImage = saveSingleImage($uploadedFile, $path);
            $user->profile_image = url('/public/') . $savedImage;
            $user->save();
        }
        return response()->json(['status' => 200, 'message' => 'Profile Updated Successfully']);

    }

    // get_task_details
    public function get_task_details(Request $request)
    {
        $task_id = $request->task_id;
        // get latest TaskNotifications
        $task = Tasks::with('building', 'appartment', 'manager','taskNotifications')->where('id', $task_id)->first();
        return response()->json(['status' => 200, 'task' => $task]);
    }   
    

}
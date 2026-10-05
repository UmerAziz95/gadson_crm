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
            $data['total_managers'] = User::where('type','manager')->count();
            $data['total_buildings'] = Buildings::count();
            $data['total_appartments'] = Appartment::count();
            $data['total_tasks'] = Tasks::count();
            $data['completed_tasks'] = Tasks::where('status',5)->count();
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
            $data['managers_list'] = User::where('type','manager')->where('status', 1)->get();
            return view('admin/managers')->with($data);
        }
        public function get_managers_list()
        {
            
            $data['managers_list'] = User::where('type','manager')->get();
            $data['inactive_managers'] = User::where('type','manager')->where('status', 0)->count();
            $data['active_managers'] = User::where('type','manager')->where('status', 1)->count();
            return response()->json(['status' => 200, 'managers_list' => $data]);
        }

        public function add_managers(Request $request){

            $validatedData = $request->validate([
                'first_name' => 'required|max:50',
                'middle_name' => 'max:50',
                'last_name' => 'max:50',
                'email' => 'required|email|max:50|unique:users',
                'contact_number' => 'max:15',
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
                
                sendMail($user->first_name, $userEmailsSend, 'GALAXY CRM', 'User Created', $body); // send_to_name, send_to_email, email_from_name, subject, body
        

                return response()->json(['status' => 200, 'message' => "Manager Added Successfully"]);

        }

        public function delete_manager(Request $request){
                $manager_id = $request->del_id;

                $manager = User::where('id',$manager_id)->where('type','manager')->first();
                if(!$manager){
                    return response()->json(['status' => 402, 'message' => "Manager Not found"]);
                }
                else{
                    
                    $manager->delete();
                    return response()->json(['status' => 200, 'message' => "Manager Deleted Successfully"]);
                }
                }

        public function change_status(Request $request){
            $manager_id = $request->id;

            $manager = User::where('id',$manager_id)->first();
            if($manager->status == 0){
                $manager->status = 1;
                $manager->updated_by = Auth::id();
                $manager->save();
                return response()->json(['status' => 200, 'message' => "Status Updated Successfully"]);    
            }
            else{
                $manager->status = 0;
                $manager->save();
                $manager->updated_by = Auth::id();
                return response()->json(['status' => 200, 'message' => "Status Updated Successfully"]);
            }
        }

        public function get_manager_data(Request $request){
            $user_id = $request->id;

            $manager = User::where('id', $user_id)->get();
            if(!$manager){
                return response()->json(['status' => 402, 'message' => "Manager Not found"]);
            }
            else{
                return response()->json(['status' => 200, 'data' => $manager]);
            }
        }

        public function update_manager(Request $request){
            $validatedData = $request->validate([
                'first_name_edit' => 'required|max:50',
                'middle_name_edit' => 'max:50',
                'last_name_edit' => 'max:50',
                // 'email' => 'required|email|max:50|unique:users',
                'contact_number_edit' => 'max:15',
            ]);

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

        public function buildings(){
            $data['page'] = 'Buildings';
            return view('admin/buildings')->with($data);
        }
        public function get_buildings_list(){
            $data['buildings_list'] = Buildings::with('images')->get();

            $data['residential_list'] = Buildings::where('building_type','Residential')->count();
            $data['commercial_list'] = Buildings::where('building_type','Commercial')->count();
            $data['mixed_list'] = Buildings::where('building_type','Mixed Use')->count();
            $data['total_buildings_list'] = Buildings::all()->count();
            return response()->json(['status' => 200, 'buildings_list' => $data]);   
        }
        public function get_states_list(Request $request){
            $data['states_list'] = State::where('country_id', $request->country)->get();
            return response()->json(['status' => 200, 'states_list' => $data]);   
        }
        public function get_cities_list(Request $request){
            $data['cities_list'] = City::where('state_id', $request->state)->get();
            return response()->json(['status' => 200, 'cities_list' => $data]);   
        }

        public function add_building(){
            $data['page'] = 'Buildings';
            $data['states'] = State::where('country_id',233)->get();
            return view('admin/addbuildings')->with($data);
        }
        public function store_building(Request $request){
            $request->validate([
                'building_name' => 'required|max:100',
                'building_type' => 'required|max:20',
                'building_address' => 'required|max:255',
                'number_of_apartments' => 'required|max:11',
                'number_of_floors' => 'required|max:11',
                'country' => 'required|max:100',
                'state' => 'required|max:100',
                'city' => 'required|max:100',
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
           
                $path = '/uploads/building_images/'.$building->id;
                $uploadedFile = $request->file('photos');
                $savedImages = saveMultipleImages($uploadedFile, $path);
                foreach($savedImages as $image){
                $building_image = new BuildingImages;
                $building_image->building_id = $building->id;
                $building_image->image_path = url('/') . $image;
                $building_image->save();
                }
        
    }
        
        return response()->json(['status' => 200, 'message' => "Building Added Successfully"]);

        }

        public function edit_building($id){
            $data['page'] = 'Buildings';
            $data['countries'] = Country::all();
            $data['building'] = Buildings::where('id',$id)->with('images')->first();
            $data['cities'] = City::where('state_id', $data['building']->state)->get();
            $data['states'] = State::where('country_id',233)->get();
            return view('admin/edit_building')->with($data);
        }

        public function update_building(Request $request){
            
            $request->validate([
                'building_name' => 'required|max:100',
                'building_type' => 'required|max:20',
                'building_address' => 'required|max:255',
                'number_of_apartments' => 'required|max:11',
                'number_of_floors' => 'required|max:11',
                'country' => 'required|max:100',
                'state' => 'required|max:100',
                'city' => 'required|max:100',
                'building_number' => 'required|max:100',
                'total_parkings' => 'required|max:11',
                'owner_name' => 'required|max:100',
                'building_size' => 'numeric|required',
                'building_description' => 'required|max:255',
                'building_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                'status' => 'required|max:50',
            ]); 

            if ($request->hasFile('photos')) {
                
                $path = '/uploads/building_images/'.$request->building_id;
                $uploadedFile = $request->file('photos');
                $savedImages = saveMultipleImages($uploadedFile, $path);
                foreach($savedImages as $image){
                $building_image = new BuildingImages;
                $building_image->building_id = $request->building_id;
                $building_image->image_path = url('/') . $image;
                $building_image->save();
                }
                }
                if(isset($request->removed_image_ids) && !empty($request->removed_image_ids)){       
                $removed_image_ids = $request->removed_image_ids;
                
                foreach($removed_image_ids as $removed_image_id){
                  
                        deleteImage(str_replace(url('/'), '', $removed_image_id));
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
        
            try {
                $images= BuildingImages::where('building_id', $building_id)->get();
                foreach($images as $image){

                    deleteImage(str_replace(url('/'), '', $image));
                }
                $building->delete();
                return response()->json(['status' => 200, 'message' => "Building deleted successfully"]);
            } catch (\Exception $e) {
                
                return response()->json(['status' => 500, 'message' => "Failed to delete building"]);
            }
        }
        



        public function appartments(){
            $data['page'] = 'Appartments';
            // $data['apartments'] = Appartment::all();
            return view('admin/appartments')->with($data);
        }

        public function get_appartments_list(){
            $data['appartments_list'] = Appartment::with('images')->get();
            $data['pent_house_appartments_list'] = Appartment::where('apartment_type','Penthouse')->count();
            $data['appartment_appartments_list'] = Appartment::where('apartment_type','Appartment')->count();
            $data['studio_appartments_list'] = Appartment::where('apartment_type','Studio')->count();
            $data['total_appartments_list'] = Appartment::all()->count();
            return response()->json(['status' => 200, 'appartments_list' => $data]);   
        }

        public function add_appartment(){
            $data['page'] = 'Appartments';
            $data['buildings'] = Buildings::all();
            return view('admin/addappartment')->with($data);
        }

        public function store_appartment(Request $request){
            // dd($request->all());
            $request->validate([
                'building' => 'required',
                'apartment_no' => 'string|max:20',
                'apartment_name' => 'required|max:50',
                'category' => 'required',
                'apartment_type' => 'required',
                'number_of_rooms' => 'required|numeric',
                'apartment_size' => 'required|numeric',
                // 'status' => 'required',
                'unit_purchase_price' => 'required|numeric',
                'landlord_name' => 'required|max:50',
                'landlord_contact_number' => 'required|max:18',
                'reference_number' => 'required|max:18',
                'description' => 'required|max:255',
                'photos.*' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                
            ]); 
            $appartment = new Appartment;
            $appartment->building_id = $request->building;
            $appartment->apartment_no = $request->apartment_no;
            $appartment->apartment_name = $request->apartment_name;
            $appartment->category = $request->category;
            $appartment->apartment_type = $request->apartment_type;
            $appartment->number_of_rooms = $request->number_of_rooms;
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
               
                    $path = '/uploads/appartment_images/'.$appartment->id;
                    $uploadedFile = $request->file('photos');
                    $savedImages = saveMultipleImages($uploadedFile, $path);
                    foreach($savedImages as $image){
                    $appartment_image = new AppartmentImages;
                    $appartment_image->appartment_id = $appartment->id;
                    $appartment_image->image_path = url('/') . $image;
                    $appartment_image->created_by = Auth::id();
                    $appartment_image->save();
                    }
            }
            return response()->json(['status' => 200, 'message' => "Appartment Added successfully"]);
        }


        public function delete_appartment(Request $request){
            $appartment_id = $request->del_id;
            $appartment = Appartment::find($appartment_id);
        
            if (!$appartment) {
                return response()->json(['status' => 402, 'message' => "Appartment not found"]);
            }
            else{
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


        public function edit_appartment($id){
            $data['page'] = 'Appartments';
            $data['buildings'] = Buildings::all();
            $data['appartment'] = Appartment::where('id',$id)->with('images')->first();
            return view('admin/editappartment')->with($data);
        }

        public function update_appartment(Request $request){
            // dd($request->all());

            $request->validate([
                'building' => 'required',
                'apartment_no' => 'string|max:20',
                'apartment_name' => 'required|max:50',
                'category' => 'required',
                'apartment_type' => 'required',
                'number_of_rooms' => 'required|numeric',
                'apartment_size' => 'required|numeric',
                'status' => 'required',
                'unit_purchase_price' => 'required|numeric',
                'landlord_name' => 'required|max:50',
                'landlord_contact_number' => 'required|max:18|numeric',
                'reference_number' => 'required|max:18|numeric',
                'description' => 'required|max:255',
                'photos.*' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                
            ]); 
            if ($request->hasFile('photos')) {
                
                $path = '/uploads/appartment_images/'.$request->appartment_id;
                $uploadedFile = $request->file('photos');
                $savedImages = saveMultipleImages($uploadedFile, $path);
                foreach($savedImages as $image){
                $appartment_image = new AppartmentImages;
                $appartment_image->appartment_id = $request->appartment_id;
                $appartment_image->image_path = url('/') . $image;
                $appartment_image->created_by = Auth::id();
                $appartment_image->save();
                }
                }
                if(isset($request->removed_image_ids) && !empty($request->removed_image_ids)){       
                $removed_image_ids = $request->removed_image_ids;
                
                foreach($removed_image_ids as $removed_image_id){
                  
                        deleteImage(str_replace(url('/'), '', $removed_image_id));
                        AppartmentImages::where('image_path', $removed_image_id)->delete();
                    
                    
                        
                }
               
            
            }
            
            $appartment = Appartment::where('id', $request->appartment_id)->first();
            
            $appartment->building_id = $request->building;
            $appartment->apartment_no = $request->apartment_no;
            $appartment->apartment_name = $request->apartment_name;
            $appartment->category = $request->category;
            $appartment->apartment_type = $request->apartment_type;
            $appartment->number_of_rooms = $request->number_of_rooms;
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

        public function assigned_tasks(){
            $data['page'] = 'Tasks';
            $data['managers'] = User::where('type','manager')->get();
            $data['buildings'] = Buildings::all();
            $data['appartments'] = Appartment::all();
            return view('admin/assigned_tasks')->with($data);
        }

        public function add_task_view(){
            $data['page'] = 'Tasks';
            $data['managers_list'] = User::where('type','manager')->where('status', 1)->get();
            $data['buildings_list'] = Buildings::all();
            return view('admin/add_task')->with($data);
        }

        public function get_appartment_list(Request $request){
            $data['appartment_list'] = Appartment::where('building_id', $request->building_id)->get();
            return response()->json(['status' => 200, 'appartment_list' => $data]);   
        }

        public function store_task(Request $request){
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
                
                $path = '/uploads/tasks_attachments/'.$task->id;
                $uploadedFile = $request->file('attachment');
                $savedFile = saveSingleImage($uploadedFile, $path);
                $full_path = url('/') . $savedFile;
                $task->document = $full_path;
                $task->save();
                }
                $task_notification = new TaskNotifications;
                $task_notification->task_id = $task->id;
                $task_notification->manager_email = $manager->email;
                $task_notification->admin_email = env('ADMIN_EMAIL');
                $task_notification->comment = 'Task Assigned';
                $task_notification->created_by = Auth::id();
                $task_notification->task_status = $task->status;
                $task_notification->save();
                $mailData['name'] = trim(($manager->first_name ?? '') . ' ' . ($manager->middle_name ?? '') . ' ' . ($manager->last_name ?? ''));

                $mailData['task_title'] = $task->task_title;
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
                $mailData1['building'] = $building->building_name;
                $mailData1['appartment'] = $appartment->apartment_name;
                $mailData1['maintext'] = 'New task assigned to '.trim(($manager->first_name ?? '') . ' ' . ($manager->middle_name ?? '') . ' ' . ($manager->last_name ?? '')).', check details below';
                $mailData1['date'] = date('d F y');
                $body = view('emails.assign_task', $mailData1);
                $userEmailsSend1[] = env('ADMIN_EMAIL');
                // to username, to email, from username, subject, body html
                sendMail('Admin', $userEmailsSend1, 'GALAXY CRM', 'Task Assigned', $body); 
                

            return response()->json(['status' => 200, 'message' => "Task Added Successfully"]);
        }

        public function get_tasks_list(){
            $data['tasks_list'] = Tasks::with('building','appartment', 'manager')->whereIn('status', [0, 1, 2, 3, 4])->get();
            $data['done_tasks_list'] = Tasks::with(['building', 'appartment', 'manager'])
            ->where('status', 5)->get();
            $data['total_tasks'] = Tasks::count();
            $data['assigned_tasks'] = Tasks::where('status', 1)->count();
            $data['hold_tasks'] = Tasks::where('status', 3)->count();
            $data['done_tasks'] = Tasks::where('status', 5)->count();
            return response()->json(['status' => 200, 'tasks_list' => $data]);
        }

        public function edit_task($id){
            $data['page'] = 'Tasks';
            $data['managers_list'] = User::where('type','manager')->where('status', 1)->get();
            $task = Tasks::where('id',$id)->first();
            if($task->status != 5){
                return redirect()->back()->with('error','This task is not done yet, you can not edit this task');
            }
            $data['task'] = $task;
            $data['buildings_list'] = Buildings::all();
            $data['appartments_list'] = Appartment::all();
            return view('admin/edit_task')->with($data);
        }

        public function update_task(Request $request){
            $request->validate([
                'task_title' => 'required|string|max:50',
                'priority' => 'required|integer',
                'document_type' => 'required|integer',
                'building' => 'required|integer',
                'appartment' => 'required|integer',
                'manager' => 'required|integer',
                'description' => 'required',
                'status' => 'required'
            ]);

            if ($request->hasFile('attachment')) {
                $request->validate([
                    'document_type' => 'required|integer',
                ]);
            }

            $task = Tasks::where('id', $request->task_id)->first();

            if ($request->hasFile('attachment')) {
                $del_path = str_replace(url('/'), '', $task->document);
                deleteImage($del_path);
                $path = '/uploads/tasks_attachments/'.$task->id;
                $uploadedFile = $request->file('attachment');
                $savedFile = saveSingleImage($uploadedFile, $path);
                $full_path = url('/') . $savedFile;
                $task->document = $full_path;
                $task->save();
            }
            
            $task->task_title = $request->task_title;
            $task->priority = $request->priority;
            $task->document_type = $request->document_type;
            $task->building = $request->building;
            $task->apartment = $request->appartment;
            $task->manager = $request->manager;
            $task->description = $request->description;
            if ($request->hasFile('attachment')) {
            $task->document_status = '0';
            }
            $task->status = $request->status;
            $task->updated_by = Auth::id();
            $task->save();

            $task = Tasks::with('building','appartment', 'manager')->where('id', $request->task_id)->first();
            $manager = User::find($task->manager);
            $building = Buildings::find($task->building);
            $appartment = Appartment::find($task->apartment);
            $task_notification = new TaskNotifications;
            $task_notification->task_id = $request->task_id;
            $task_notification->manager_email = $manager->email;
            $task_notification->admin_email = env('ADMIN_EMAIL');
            $task_notification->comment = 'Reopened by Admin';
            $task_notification->created_by = Auth::id();
            $task_notification->task_status = $request->status;
            $task_notification->save();


            $mailData['name'] = $manager->first_name;
            $mailData['task_title'] = $task->task_title;
            $mailData['building'] = $building->building_name;
            $mailData['appartment'] = $appartment->apartment_name;
            $mailData['comment'] = 'Reopened by Admin';
            if($request->status == 0 || $request->status == '0'){
                $statustxt = 'Draft';
                }

                if($request->status == 1 || $request->status == '1'){
                $statustxt ='Assigned';
                }

                if($request->status == 2 || $request->status == '2'){
                $statustxt = 'Working On';
                }
                
                if($request->status == 3 || $request->status == '3'){
                $statustxt = 'Hold';
                }
                
                if($request->status == 4 || $request->status == '4'){
                $statustxt='Stuck';
                }
                
                if($request->status == 5 || $request->status == '5'){
                $statustxt ='Done';
                }
                
            $mailData['statustxt'] = $statustxt;
            $body = view('emails.task_status_update', $mailData);
            $userEmailsSend[] = $manager->email;
            

            // to username, to email, from username, subject, body html
            
            sendMail($manager->first_name, $userEmailsSend, 'GALAXY CRM', 'Task Status Updated', $body); 
            // send mail to admin 
            $mailData1['name'] = 'ADMIN';
            $mailData1['task_title'] = $task->task_title;
            $mailData1['building'] = $building->building_name;
            $mailData1['appartment'] = $appartment->apartment_name;
            $mailData1['statustxt'] = $statustxt;
            $mailData1['comment'] = 'Reopened by Admin';
            $body = view('emails.task_status_update', $mailData1);
            $admin_mail = env('ADMIN_EMAIL');
            sendMail('Admin', $admin_mail, 'GALAXY CRM', 'Task Status Updated', $body); 
           
           
            return response()->json(['status' => 200, 'message' => "Task Updated Successfully"]);
        
        }

        public function delete_task(Request $request){
            $task_id = $request->del_id;
            $task = Tasks::find($task_id);
        
            if (!$task) {
                return response()->json(['status' => 402, 'message' => "Task not found"]);
            }
        
            try {

                    deleteImage(str_replace(url('/'), '', $task->document));
                    $task->delete();
                return response()->json(['status' => 200, 'message' => "Task deleted successfully"]);
            } catch (\Exception $e) {
                
                return response()->json(['status' => 500, 'message' => "Failed to delete task"]);
            }
        }

        public function get_time_line_details(Request $request){
            $task_id = $request->task_id;
            $data['to_do_details'] = TaskToDoList::where('task_id', $task_id)->get();
            $data['status_timeline_details'] = TaskNotifications::where('task_id', $task_id)->get();
            return response()->json(['status' => 200, 'data' => $data]);

        }

        public function forgotpassword(){
            return view('admin/forgot_password');
        }
        public function forgot_password_validate_email(Request $request){
          
            $request->validate([
                'email' => 'required|email',
    
            ]);
    
            $user = User::where('email', $request->email)->first();
            if(!$user){
                return response()->json(['status' => 402, 'message' => "Email is not registered in our system"]);
            }
            else{
                    $mailData = [];
                    $otp = implode('', array_map(function() {
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
    
        public function verify_otp(Request $request){
            $request->validate([
                'otp' => 'required|max:5',
    
            ]);
            $otp = $request->otp;
            $email = $request->email;
    
            $user = User::where('email', $request->email)->first();
            if($user->otp_code == null){
                return response()->json(['status' => 402, 'message' => "Invalid request"]);
            }
            if($otp == $user->otp_code){
                return response()->json(['status' => 200, 'message' => "OTP validated, kindly enter your new password"]);
            }
            else{
                return response()->json(['status' => 402, 'message' => "OTP mismatch, kindly use the OTP we sent on your email"]);
                
            }
        }
    
        public function reset_password(Request $request){
            $request->validate([
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
            ]);
    
            $user = User::where('email', $request->email)->first();
            if($user){
                $user->password = bcrypt($request->input('password'));
                $user->save();
                return response()->json(['status' => 200, 'message' => "Passwrd changed successfully, kindly return to login page and login again"]);
    
            }
            
        }

        public function get_filtered_tasks(Request $request){
           
            $tasks = Tasks::with('building', 'appartment', 'manager');
        
            if($request->filled('building_filter')){
                $tasks = $tasks->where('building', $request->building_filter);
            }
            if($request->filled('appartment_filter')){
                $tasks = $tasks->where('apartment', $request->appartment_filter);
            }
            if($request->filled('priority_filter')){
                $tasks = $tasks->where('priority', $request->priority_filter);
            }
            if($request->filled('doc_status_filter')){
                $tasks = $tasks->where('document_status', $request->doc_status_filter);
            }
            if($request->filled('manager_filter')){
                $tasks = $tasks->where('manager', $request->manager_filter);
            }
            if($request->filled('task_status_filter')){
                $tasks = $tasks->where('status', $request->task_status_filter);
            } else {
                $tasks = $tasks->whereIn('status', [0, 1, 2, 3, 4]);
            }
        
            $tasks = $tasks->get();
        
            if($tasks->isNotEmpty()){
                return response()->json(['status' => 200, 'tasks' => $tasks]);
            } else {
                return response()->json(['status' => 402, 'message' => "No record found"]);
            }
        }
        

        public function get_filtered_done_tasks(Request $request){
           
            $tasks = Tasks::with('building', 'appartment', 'manager')->where('status',5);
        
            if($request->filled('building_filter')){
                $tasks = $tasks->where('building', $request->building_filter);
            }
            if($request->filled('appartment_filter')){
                $tasks = $tasks->where('apartment', $request->appartment_filter);
            }
            if($request->filled('priority_filter')){
                $tasks = $tasks->where('priority', $request->priority_filter);
            }
            if($request->filled('doc_status_filter')){
                $tasks = $tasks->where('document_status', $request->doc_status_filter);
            }
            if($request->filled('manager_filter')){
                $tasks = $tasks->where('manager', $request->manager_filter);
            }
            
        
            $tasks = $tasks->get();
        
            if($tasks->isNotEmpty()){
                return response()->json(['status' => 200, 'tasks' => $tasks]);
            } else {
                return response()->json(['status' => 402, 'message' => "No record found"]);
            }
        }


        public function makeanalyticsgraph(){
           
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




































































































































































































































































    }
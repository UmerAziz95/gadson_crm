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


class ManagerController extends Controller
{
    public function __construct()
    {
        
    }


    
        public function login()
        {
        
            $data['page'] = 'Login';
            return view('manager/login')->with($data);
        }

        public function loginSubmit(Request $request)
        {
            $validatedData = $request->validate([
                'email' => 'required|exists:users,email',
            ]);
            $credentials = $request->only('email', 'password');

            if (Auth::attempt($credentials)) {

                $user = Auth::user();
                if($user->status == 1 || $user->status == '1'){
                    $request->session()->put('user', $user);
                // Authentication passed...
                return redirect()->intended('/manager/dashboard');
                }
                else{
                $request->session()->flash('error', 'Your account is inactive, kindly contact admin for more details');
                return redirect('manager/login');
                }
                
            }

            $request->session()->flash('error', 'The provided credentials do not match our records.');
            return redirect('manager/login');
        }

        public function logout(Request $request)
        {

            $request->session()->forget('user');

            return redirect('/manager');
        }

        public function dashboard(){
            if(Auth::user()->first_login == 1 || Auth::user()->first_login == '1'){
                return view('manager/changedefaultpassword');
                exit();
            }
            else{
            $data['page'] = 'Dashboard';
            $id = Auth::id();
            $data['total_tasks'] = Tasks::where('manager', $id)->count();
            $data['completed_tasks'] = Tasks::where('manager', $id)->where('status',5)->count();
            $data['working_on_tasks'] = Tasks::where('manager', $id)->where('status',2)->count();
            $data['stuck_tasks'] = Tasks::where('manager', $id)->where('status',4)->count();
            $data['hold_tasks'] = Tasks::where('manager', $id)->where('status', 3)->count();
            $data['cancelled_tasks'] = Tasks::where('manager', $id)->where('status', 6)->count();
            $data['assigned_tasks'] = Tasks::where('manager', $id)->where('status',1)->count();
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
            ->where('manager',$id)
            ->groupBy('task_date')
            ->orderBy('task_date')
            ->get();
            
            return view('manager/dashboard')->with($data);
            }
        }

        public function changeDefaultPassword(Request $request){
            $validatedData = $request->validate([
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
            ]);
            $user = Auth::user();
            $user->password = Hash::make($request->password);
            $user->first_login = 0;
            $user->save();
            $request->session()->flash('error', 'Password changed Successfully, Please login again');
            return redirect('manager/login'); 

        }

        public function assigned_tasks(){
            $data['page'] = 'Tasks';
            $data['buildings'] = Buildings::all();
            $data['appartments'] = Appartment::all();
            return view('manager/assigned_tasks')->with($data);
        }

        public function get_tasks_list(){
            $id = Auth::id();
            $data['tasks_list'] = Tasks::with(['building', 'appartment', 'manager'])->where('manager', $id)
            ->orderByRaw('FIELD(priority, 2, 1, 0)')
            ->orderBy('created_at', 'desc')
            ->whereIn('status', [0, 1, 2, 3, 4])->get();

            $data['total_tasks'] = Tasks::where('manager', $id)->count();
            $data['assigned_tasks'] = Tasks::where('manager', $id)->where('status', 1)->count();
            $data['hold_tasks'] = Tasks::where('manager', $id)->where('status', 3)->count();
            $data['draft_tasks'] = Tasks::where('manager', $id)->where('status', 0)->count();
            $data['done_tasks'] = Tasks::where('manager', $id)->where('status', 5)->count();
            $data['cancelled_tasks'] = Tasks::where('manager', $id)->where('status', 6)->count();
            $data['stuck_tasks'] = Tasks::where('manager', $id)->where('status', 4)->count();
            $data['working_on_tasks'] = Tasks::where('manager', $id)->where('status', 2)->count();
            return response()->json(['status' => 200, 'tasks_list' => $data]);
        
        }
        public function get_done_tasks_list(){
            $id = Auth::id();
            $data['tasks_list'] = Tasks::with(['building', 'appartment', 'manager'])
            ->where('manager', $id)
            ->where('status', 5)
            ->orderBy('created_at', 'desc')
            ->get();


            $data['total_tasks'] = Tasks::where('manager', $id)->count();
            $data['assigned_tasks'] = Tasks::where('manager', $id)->where('status', 1)->count();
            $data['hold_tasks'] = Tasks::where('manager', $id)->where('status', 3)->count();
            $data['draft_tasks'] = Tasks::where('manager', $id)->where('status', 0)->count();
            $data['done_tasks'] = Tasks::where('manager', $id)->where('status', 5)->count();
            $data['cancelled_tasks'] = Tasks::where('manager', $id)->where('status', 6)->count();
            $data['stuck_tasks'] = Tasks::where('manager', $id)->where('status', 4)->count();
            $data['working_on_tasks'] = Tasks::where('manager', $id)->where('status', 2)->count();

            return response()->json(['status' => 200, 'tasks_list' => $data]);
        
        }
        public function get_cancelled_tasks_list(){
            $id = Auth::id();
            $data['tasks_list'] = Tasks::with(['building', 'appartment', 'manager'])
            ->where('manager', $id)
            ->where('status', 6)
            ->orderBy('created_at', 'desc')
            ->get();


            $data['total_tasks'] = Tasks::where('manager', $id)->count();
            $data['assigned_tasks'] = Tasks::where('manager', $id)->where('status', 1)->count();
            $data['hold_tasks'] = Tasks::where('manager', $id)->where('status', 3)->count();
            $data['draft_tasks'] = Tasks::where('manager', $id)->where('status', 0)->count();
            $data['done_tasks'] = Tasks::where('manager', $id)->where('status', 5)->count();
            $data['cancelled_tasks'] = Tasks::where('manager', $id)->where('status', 6)->count();
            $data['stuck_tasks'] = Tasks::where('manager', $id)->where('status', 4)->count();
            $data['working_on_tasks'] = Tasks::where('manager', $id)->where('status', 2)->count();
            
            return response()->json(['status' => 200, 'tasks_list' => $data]);
        
        }

        public function change_document_status(Request $request){
            $task_id = $request->task_id;
            $task = Tasks::find($task_id);
        
            if (!$task) {
                return response()->json(['status' => 402, 'message' => "Task not found"]);
            }
            else{
                $task->document_status = 1;
                $task->save();
                return response()->json(['status' => 200, 'message' => "Document Downloaded successfully"]);
            }
        
           
        }

        public function change_task_status(Request $request){
            $validatedData = $request->validate([
                'status' => 'required',
                'comment' => 'required',
                'attachment_upload' => 'file|max:2048'
            ]);

            $task = Tasks::with('building','appartment', 'manager')->where('id', $request->task_id)->first();
            if (!$task) {
                return response()->json(['status' => 402, 'message' => "Task not found"]);
            }
            else{
                $task->status = $request->status;
                $task->updated_by = Auth::id();
                $task->save();
                $task = Tasks::with('building','appartment', 'manager')->where('id', $request->task_id)->first();
                $manager = User::find($task->manager);
                $building = Buildings::find($task->building);
                $appartment = Appartment::find($task->apartment);
                $task_notification = new TaskNotifications;
                $task_notification->task_id = $request->task_id;
                $task_notification->action = 'Task Status Updated';
                $task_notification->manager_email = $manager->email;
                $task_notification->admin_email = env('ADMIN_EMAIL');
                $task_notification->comment = $request->comment;
                $task_notification->created_by = '2';
                $task_notification->manager_id = Auth::id();
                $task_notification->task_status = $request->status;
                if ($request->hasFile('attachment_upload')) {
                    $path = '/uploads/tasks_status_attachments/'.$task->id;
                    $uploadedFile = $request->file('attachment_upload');
                    $savedFile = saveSingleImage($uploadedFile, $path);
                    $full_path = url('/public/') . $savedFile;
                    $task_notification->attachment = $full_path;
                    
                }
                $task_notification->save();

                $mailData['name'] = $manager->first_name;
                $mailData['task_title'] = $task->task_title;
                $mailData['building'] = $building->building_name;
                $mailData['appartment'] = $appartment->apartment_name;
                $mailData['comment'] = $request->comment;
                if($request->status == 0 || $request->status == '0'){
                    $statustxt = 'Draft';
                    }

                    if($request->status == 1 || $request->status == '1'){
                    $statustxt ='Assigned';
                    }

                    if($request->status == 2 || $request->status == '2'){
                    $statustxt = 'Working On it';
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
                $mailData1['comment'] = $request->comment;
                $body = view('emails.task_status_update', $mailData1);
                $admin_mail = env('ADMIN_EMAIL');
                sendMail('Admin', $admin_mail, 'GALAXY CRM', 'Task Status Updated', $body); 

                return response()->json(['status' => 200, 'message' => "Status Updated successfully"]);

            }
        }

        public function get_task_todoList(Request $request){
            $task_id = $request->task_id;
            $todolist = TaskToDoList::where('task_id',$task_id)->get();
            if($todolist){
                return response()->json(['status' => 200, 'todolist' => $todolist]);
            }
            else{
                return response()->json(['status' => 402, 'message' => "To do list not found"]);
            }
        }

        public function add_to_do_list(Request $request){
            $request->validate([
                'to_do_item.*' => 'required|string|max:255',
            ],
        ['to_do_item.required' => 'to do item is required']);

        $task_id = $request->task_id;

        $existingItems = TaskToDoList::where('task_id', $task_id)->get();
        
        if ($existingItems->isNotEmpty()) {
            TaskToDoList::where('task_id', $task_id)->delete();
        }
        
            $to_do_items = $request->to_do_item;
            foreach($to_do_items as $i=> $to_do_item){
                $to_do = new TaskToDoList;
                $to_do->task_id = $task_id;
                $to_do->to_do_item = $to_do_item;
                $to_do->created_by = Auth::id();
                $to_do->save();
            }
            return response()->json(['status' => 200, 'message' => "To do list added successfully"]);
        }

        public function get_time_line_details(Request $request){
            $task_id = $request->task_id;
           
            $data['status_timeline_details'] = TaskNotifications::with('manager')->where('task_id', $task_id)->get();
            return response()->json(['status' => 200, 'data' => $data]);

        }

        public function forgotpassword(){
            return view('manager/forgot_password');
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
            $manager_id = Auth::id();
            $tasks = Tasks::with('building', 'appartment', 'manager')
                          ->where('manager', $manager_id)
                          ->orderByRaw('FIELD(priority, 2, 1, 0)')
                          ->orderBy('created_at', 'desc');
        
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
            if($request->filled('document_type_filter')){
                $tasks = $tasks->where('document_type', $request->document_type_filter);
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
            $manager_id = Auth::id();
            $tasks = Tasks::with('building', 'appartment', 'manager')
                          ->where('manager', $manager_id)
                          ->orderByRaw('FIELD(priority, 2, 1, 0)')
                          ->orderBy('created_at', 'desc')
                          ->where('status',5);
        
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
            if($request->filled('document_type_filter1')){
                $tasks = $tasks->where('document_type', $request->document_type_filter1);
            }
            
        
            $tasks = $tasks->get();
        
            if($tasks->isNotEmpty()){
                return response()->json(['status' => 200, 'tasks' => $tasks]);
            } else {
                return response()->json(['status' => 402, 'message' => "No record found"]);
            }
        }

        public function edit_profile(){
            $data['page']= 'Profile';
            return view('manager/profile')->with($data);
        }

        public function get_profile_data(){
            $user_id = Auth::id();
            $user = User::find($user_id);
            return response()->json(['status' => 200, 'user' => $user]);

        }

        public function update_profile(Request $request){
            $user_id = Auth::id();
            $user = User::find($user_id);

            $request->validate([
                'first_name' => 'required| max:40',
                // 'middle_name' => 'required| max:40',
                'last_name' => 'required| max:40',
                'contact_number' => 'max:15|min:7'
            ]);
            
            if($request->password){
                 $request->validate([
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
            ]);
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
            }
            else{
                return response()->json(['status' => 402, 'message' => "Old password is incorrect"]);
            }
        }else{
            $user->first_name = $request->first_name;
            $user->middle_name = $request->middle_name;
            $user->last_name = $request->last_name;
            $user->contact_number = $request->contact_number;
            $user->save();
        }

        if ($request->hasFile('profile_image')) {
            if($user->profile_image != null){
                deleteImage(str_replace(url('/public/'), '', $user->profile_image));
            }
            $path = '/uploads/profile_images/'.$user_id;
            $uploadedFile = $request->file('profile_image');
            $savedImage = saveSingleImage($uploadedFile, $path);            
            $user->profile_image = url('/public/') . $savedImage;
            $user->save();
        }
        return response()->json(['status' => 200, 'message' => 'Profile Updated Successfully']);

        }

        public function get_appartment_list(Request $request){
            $data['appartment_list'] = Appartment::where('building_id', $request->building_id)
            ->get();
            return response()->json(['status' => 200, 'appartment_list' => $data]);   
        }

        

}
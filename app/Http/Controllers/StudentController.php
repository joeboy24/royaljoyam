<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\QueryException;
use Exception;
use App\Models\User;
use App\Models\Fee;
use App\Models\Stage;
use App\Models\Course;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\Company;
use App\Models\Payable;
use Validator;
use App\Models\StudentBackup;
use DB;
 
class StudentController extends Controller
{

    public function __construct(){
        $this->middleware(['auth', 'load_auth']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $c = 1;
        $i = 1;
        $stages = Stage::All();
        $payables = Payable::All();
        $allStudents = Student::where('del', 'no')->get();
        //$students = Student::where('del', 'no')->orderBy('id', 'desc')->paginate(10);
        $searchquery = request()->query('searchquery');
        $students = Student::where('fname', 'LIKE', '%'.$searchquery.'%')->paginate(10);
        $std_pop = count($allStudents);

        $pass = [
            'i' => $i,
            'c' => $c,
            'stages' => $stages,
            'std_pop' => $std_pop,
            'payables' => $payables,
            'students' => $students
        ];
        return view('pages.dash.studentsview')->with($pass);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //

        switch ($request->input('store_action')) {

            case 'create_user':

                $user = new User;
                $ps1 = $request->input('password');
                $ps2 = $request->input('password_confirmation');


                if($ps1 == $ps2){
                    $user->name = $request->input('name');
                    $user->email = $request->input('email');
                    $user->password = Hash::make($request->input('password'));
                    $user->save();
                    return redirect('/dashuser')->with('success', 'User Created Successfully');
                }else{
                    return redirect('/dashuser')->with('error', 'Passwords do not match');
                }

                //Hash::make($data['password']);

            break;

            case 'admi_create_crs':

                $course = new Course;
                $name = $request->input('name');

                $matchThese = ['name' => $name];

                    $results = Course::where($matchThese)->get();


                    if (count($results) > 0){
                        return redirect('/dashuser')->with('error', 'Oops..! '.$name.' already exist');
                    }else{
                        $course->name = $name;
                        $course->assign_tch = $request->input('assign_tch');
                        $course->desc = $request->input('desc');
                        $course->save();
                    }

                return redirect('/dashuser')->with('success', 'Subject Added Successfully');
                

            break;


            case 'admi_create_cls':

                $stage = new Stage;
                $name = $request->input('cls_name');

                $matchThese = ['cls_name' => $name];

                    $results = Stage::where($matchThese)->get();


                    if (count($results) > 0){
                        return redirect('/dashuser')->with('error', 'Oops..! '.$name.' already exist');
                    }else{
                        $stage->cls_name = $name;
                        $stage->subjects = $request->input('sel_sub');
                        $stage->cls_tch = $request->input('cls_tch');
                        $stage->save();
                    }

                return redirect('/dashuser')->with('success', $name.' Added Successfully');
                

            break;

            case 'config_create_class':

                $stage = new Stage;
                $name = $request->input('cls_name');

                $matchThese = ['cls_name' => $name];

                    $results = Stage::where($matchThese)->get();


                    if (count($results) > 0){
                        return redirect('/config')->with('error', 'Oops..! '.$name.' already exist');
                    }else{
                        $stage->cls_name = $name;
                        $stage->subjects = '';
                        $stage->cls_tch = '';
                        $stage->save();
                    }

                return redirect('/config')->with('success', $name.' Added Successfully');
                

            break;

            case 'create_payable':

                
                $payable = new Payable;
                $name = $request->input('name');

                $matchThese = ['name' => $name];

                    $results = Payable::where($matchThese)->get();


                    if (count($results) > 0){
                        return redirect('/config')->with('error', 'Oops..! '.$name.' already exist');
                    }else{
                        $payable->user_id = auth()->user()->id;
                        $payable->name = $name;
                        $payable->desc = $request->input('desc');
                        $payable->cost = $request->input('item_cost');
                        $payable->save();
                    }

                return redirect('/config')->with('success', $name.' Successfully Added');
                

            break;


            case 'std_perm_del_all':

                $std_Backup = new StudentBackup;

                $oldStudents = Student::All();
                // for ($i=0; $i < count($oldStudents); $i++) { 
                //     # code...
                // }
                foreach ($oldStudents as $oldStu){

                    if ($oldStu->del == 'yes'){

                        $backup = StudentBackup::firstOrCreate(
                            ['std_id' => $oldStu->std_id,
                            'user_id' => auth()->user()->id, 
                            'fname' => $oldStu->fname, 
                            'sname' => $oldStu->sname, 
                            'dob' => $oldStu->dob,  
                            'sex' => $oldStu->sex, 
                            'class' => $oldStu->class, 
                            'guardian' => $oldStu->guardian,  
                            'contact' => $oldStu->contact, 
                            'email' => $oldStu->email, 
                            'residence' => $oldStu->residence, 
                            'bill' => $oldStu->bill, 
                            'photo' => $oldStu->photo]
                        );

                    $oldStu->delete(); 

                    }
                      
                }
                
                return redirect('/studentsrecycler')->with('success', 'Records Successfully Deleted.');
            break;


        }

        //
                

        $user = new User;
        $fee = new Fee;
        $teacher = new Teacher;
        $student = new Student;

        $company = new Company;

        $fname = $request->input('fname');
        $sname = $request->input('sname');
        $dob = $request->input('dob');
        

        try {

            switch ($request->input('store_action')) {

                case 'admi_create_trs':

                    try {
                        $this->validate($request, [
                            'dob'   => 'required|date_format:Y-m-d',
                            'tch_img'   => 'max:5000|mimes:jpeg,jpg,png'
                        ]);
                    } catch (Exception $ex) {
                            return redirect('/dashuser')->with('error', 'Ooops..! Unhandled Error -->'.$ex->getMessage());
                    }

                    $matchThese = ['fname' => $fname, 'sname' => $sname, 'dob' => $dob, 'del' => 'no'];

                    $results = Teacher::where($matchThese)->get();


                    if (count($results) > 0){
                        return redirect('/dashuser')->with('error', 'Oops..! '.$fname.'`s details already exist');
                    }else{


                        if($request->hasFile('tch_img')){
                            //get filename with ext
                            $filenameWithExt = $request->file('tch_img')->getClientOriginalName();
                            //get filename
                            $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
                            //get file ext
                            $fileExt = $request->file('tch_img')->getClientOriginalExtension();
                            //filename to store
                            $filenameToStore = $request->input('fname').'_'.time().'.'.$fileExt;
                            //upload path
                            $path = $request->file('tch_img')->storeAs('public/tch_imgs', $filenameToStore);
                        }else{
                            $filenameToStore = 'noimage.png';
                        }
        

                        $calc = Teacher::All();
                        $final = date('Y').(count($calc) + 1);
                        
                        $teacher->tch_id = $final;
                        $teacher->user_id = auth()->user()->id;
                        //$teacher->user_id = "Self";
                        $teacher->fname = $fname;
                        $teacher->sname = $sname;

                        $teacher->dob = $dob;
                        $teacher->sex = $request->input('sex');
                        $teacher->mstatus = $request->input('mstatus');

                        $teacher->role = $request->input('role');
                        $teacher->role_desc = $request->input('role_desc');
                        $teacher->contact = $request->input('contact');

                        $teacher->email = $request->input('email');
                        $teacher->residence = $request->input('residence');
                        $teacher->photo = $filenameToStore;

                        $teacher->save();
                        return redirect('/dashuser')->with('success', $fname.'`s details successfully added');
                        
                    }
                break;

                case 'admi_create_std':

                    // return redirect('/addstudent');
                    // try {
                    //     $this->validate($request, [
                    //         'bill_total'   => 'required',
                    //         'dob'   => 'required|date_format:Y-m-d'
                    //     ]);
                    // } catch (ValidationException $ex) {
                    //         return redirect('/addstudent')->with('error', 'Ooops..! Unhandled Error -->'.$ex->getMessage());
                    // }

                    // $matchThese = ['fname' => $fname, 'sname' => $sname, 'dob' => $dob, 'del' => 'no'];

                    // $results = Student::where($matchThese)->get();


                    // if (count($results) > 0){
                    //     return redirect('/addstudent')->with('error', 'Oops..! '.$fname.'`s details already exist');
                    // }else{

                        try {

                            if($request->hasFile('std_img')){
                                //get filename with ext
                                $filenameWithExt = $request->file('std_img')->getClientOriginalName();
                                //get filename
                                $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
                                //get file ext
                                $fileExt = $request->file('std_img')->getClientOriginalExtension();
                                //filename to store
                                $filenameToStore = $request->input('fname').'_'.time().'.'.$fileExt;
                                //upload path
                                $path = $request->file('std_img')->storeAs('public/std_imgs', $filenameToStore);
                            }else{
                                $filenameToStore = 'noimage.png';
                            }

                            $company = Company::Find(1);
                            $calc = Student::latest('id')->first();
                            // $calc = Student::count('id');
                            $calc = substr($calc->std_id, 4);
                            $final = date('Y').($calc + 1);

                            $std_insert = Student::firstOrCreate(
                                ['std_id' => $final,
                                'user_id' => auth()->user()->id, 
                                'fname' => $fname, 
                                'sname' => $sname, 
                                'dob' => $dob,  
                                'sex' => $request->input('sex'), 
                                'class' => $request->input('std_cls'), 
                                'guardian' => $request->input('guardian'),  
                                'contact' => $request->input('contact'), 
                                'email' => $request->input('email'), 
                                'residence' => $request->input('residence'), 
                                'bill' => $request->input('bill_total'), 
                                'photo' => $filenameToStore]
                            );
            
                            $get_id = Student::latest('id')->first();
                            $get_id = $get_id->id;
                    
                            // $fee->student_id = $calc + 1;
                            $fee->student_id = $get_id;
                            $fee->user_id = auth()->user()->id;
                            $fee->fullname = $fname.' '.$sname;
                            $fee->class = $request->input('std_cls');
                            $fee->term = $company->ac_term;
                            $fee->year = $company->ac_year;
                            
                            $fee->save();

                            return redirect('/addstudent')->with('success', $fname.'`s details successfully added');

                        }catch(Exception $ex) {
                            $ex2 = $ex->getMessage();
                            $ex2 = substr($ex2,0,100).'.....!';
                            return redirect('/addstudent')->with('error', 'Ooops..! Unhandled Error --> Invalid information provided. Check input(Class / Date of Birth / Add Items To Bill)'.$ex2);
                           
                        }
                    // }
                break;

                case 'admi_config':

                    $name = $request->input('name');
                    $loc = $request->input('loc');
                    $matchThese = ['name' => $name, 'location' => $loc, 'del' => 'no'];

                    $results = Company::where($matchThese)->get();


                    if (count($results) > 0){
                        return redirect('/config')->with('error', 'Oops..! School`s details already exist');
                    }else{

                        try {
                            $this->validate($request, [
                                'sch_logo'   => 'required|max:5000|mimes:jpeg,jpg,png'
                            ]);
                            if($request->hasFile('sch_logo')){
                                //get filename with ext
                                $filenameWithExt = $request->file('sch_logo')->getClientOriginalName();
                                //get filename
                                $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
                                //get file ext
                                $fileExt = $request->file('sch_logo')->getClientOriginalExtension();
                                //filename to store
                                $filenameToStore = 'schlogo'.$fileExt;
                                //upload path
                                $path = $request->file('sch_logo')->storeAs('public/tch_imgs', $filenameToStore);
                            }else{
                                $filenameToStore = '';
                            }
                
                        } catch (Exception $ex) {
                            return redirect('/config')->with('error', 'Ooops..! Unhandled Error -->'.$ex->getMessage());
                        }
                        


                        try {
                            $company->user_id = auth()->user()->id;
                            $company->name = $name;
                            session('company')->address = $request->input('sch_add');
    
                            $company->location = $loc;
                            session('company')->contact = $request->input('contact');
    
                            session('company')->email = $request->input('email');
                            $company->website = $request->input('sch_web');
                            $company->ac_year = 'Not Set';
                            $company->ac_term = 'Not Set';
                            $company->photo = $filenameToStore;
    
                            $company->save();
                            return redirect('/config')->with('success', 'School`s details successfully added');
                        } catch(Exception $ex){
                            return redirect('/config')->with('error', $ex->getMessage());
                        }
                        
                    }
                break;

                case 'update_student':

                    //$student = Student::find($id);
                    $fname = $request->input('fname');
                    $sname = $request->input('sname');
    
    
                    try {
                        if($request->hasFile('std_img')){
                            //get filename with ext
                            $filenameWithExt = $request->file('std_img')->getClientOriginalName();
                            //get filename
                            $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
                            //get file ext
                            $fileExt = $request->file('std_img')->getClientOriginalExtension();
                            //filename to store
                            $filenameToStore = $fname.'_'.time().'.'.$fileExt;
                            //upload path
                            $path = $request->file('std_img')->storeAs('public/std_imgs', $filenameToStore);
        
                            return redirect('/dashboard')->with('success', $fname.'`s details successfully updated');
                        }
                    } catch (Exception $ex) {
                        return redirect('/addstudent')->with('error', 'Ooops..! Unhandled Error -->'.$ex->getMessage());
                    }
                break;

                }
        
        }catch(Exception $e) {
            //echo 'Message: ' .$e->getMessage();

            switch ($request->input('store_action')) {

                case 'admi_create_trs':
                    return redirect('/dashboard')->with('error', 'Ooops..! Unhandled Error -->'.$e->getMessage());
                break;
                }

        }

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
        switch ($request->input('sub_action')) {

            case 'update_student':

                $student = Student::find($id);
                $fname = $request->input('fname');
                $sname = $request->input('sname');


                // try {
                //     if($request->hasFile('std_img')){
                //         //get filename with ext
                //         $filenameWithExt = $request->file('std_img')->getClientOriginalName();
                //         //get filename
                //         $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
                //         //get file ext
                //         $fileExt = $request->file('std_img')->getClientOriginalExtension();
                //         //filename to store
                //         $filenameToStore = $fname.'_'.time().'.'.$fileExt;
                //         //upload path
                //         $path = $request->file('std_img')->storeAs('public/std_imgs', $filenameToStore);
    
                //         return redirect('/students')->with('success', $fname.'`s details successfully updated');
                //     }
                // } catch (Exception $exception) {
                //     return redirect('/students')->with('Error', $exception->errors());
                // }


                if($request->hasFile('std_img')){
                    //get filename with ext
                    $filenameWithExt = $request->file('std_img')->getClientOriginalName();
                    //get filename
                    $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
                    //get file ext
                    $fileExt = $request->file('std_img')->getClientOriginalExtension();
                    //filename to store
                    $filenameToStore = $request->input('fname').'_'.time().'.'.$fileExt;
                    //upload path
                    $path = $request->file('std_img')->storeAs('public/std_imgs', $filenameToStore);

                }else{
                    $filenameToStore = $request->input('photo');;
                }

                $company = Company::Find(1);
                $calc = Student::All();
                $final = date('Y').(count($calc) + 1);

                //$student = new Student;
                $student->fname = $fname;
                $student->sname = $sname;

                $student->dob = $request->input('dob');
                $student->sex = $request->input('sex');
                $student->class = $request->input('std_cls');

                $student->guardian = $request->input('guardian');
                $student->contact = $request->input('contact');

                $student->email = $request->input('email');
                $student->residence = $request->input('residence');
                $student->bill = $request->input('bill_total');
                $student->photo = $filenameToStore;

                $student->save();


                $fees = Fee::where('student_id', $id)->get();
                foreach ($fees as $fee){
                    $fee->fullname = $fname.' '.$sname;
                    $fee->class = $request->input('std_cls');
                    $fee->save();
                }

                return redirect('/students')->with('success', $fname.'`s details successfully updated');
                

            break;

            case 'std_del':
                $student = Student::find($id);
                $student->del = 'yes';
                $student->save();

                $fees = Fee::where('student_id', $id);
                foreach ($fees as $fee){
                    $fee->del = 'yes';
                    $fee->save();
                }
                return redirect('/students')->with('success', 'Record Successfully Deleted.');
            break;

            case 'std_restore':
                $student = Student::find($id);
                $student->del = 'no';
                $student->save();

                $fees = Fee::where('student_id', $id)->get();
                foreach ($fees as $fee){
                    $fee->del = 'no';
                    $fee->save();
                }
                return redirect('/studentsrecycler')->with('success', 'Record Successfully Restored.');
            break;

            case 'std_perm_del':
                $oldStu = Student::find($id);

                // $matchThese = ['id' => $id, 'del' => 'yes'];
                // $oldStu = Student::where($matchThese)->get();
               
                $std_Backup = new StudentBackup;
                $std_Backup->std_id = $oldStu->std_id;
                $std_Backup->user_id = auth()->user()->id;;
                $std_Backup->fname = $oldStu->fname;
                $std_Backup->sname = $oldStu->sname;

                $std_Backup->dob = $oldStu->dob;
                $std_Backup->sex = $oldStu->sex;
                $std_Backup->class = $oldStu->class;

                $std_Backup->guardian = $oldStu->guardian;
                $std_Backup->contact = $oldStu->contact;

                $std_Backup->email = $oldStu->email;
                $std_Backup->residence = $oldStu->residence;
                $std_Backup->bill = $oldStu->bill;
                $std_Backup->photo = $oldStu->photo;

                $std_Backup->save();  
                $oldStu->delete();    
                
                // $fees = Fee::where('student_id', $id)->get();
                // foreach ($fees as $fee){
                //     $fee->delete();
                // }
              
                return redirect('/studentsrecycler')->with('success', 'Record has being permanently deleted.');
            break;
            
            default:
                # code...
                break;
        }

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        //

        switch ($request->input('sub_action')) {
            case 'crs_del':
                $department = Course::find($id);
                $department->delete();
                return redirect('/dashuser')->with('success', 'Course Successfully Deleted');
            break;

            case 'user_del':
                $user = User::find($id);
                $user->delete();
                return redirect('/dashuser')->with('success', 'User Successfully Deleted.');
            break;

            case 'trs_del':
                $teacher = Teacher::find($id);
                $teacher->delete();
                return redirect('/dashuser')->with('success', 'Details Successfully Deleted.');
            break;

            case 'cls_del':
                $stage = Stage::find($id);
                $stage->delete();
                return redirect('/config')->with('success', 'Class Deleted.');
            break;

            case 'item_del':
                $payable = Payable::find($id);
                $payable->delete();
                return redirect('/config')->with('success', 'Item Deleted.');
            break;

        }
        
    }
}

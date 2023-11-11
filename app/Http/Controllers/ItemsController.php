<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use App\Models\Company;
use App\Models\User;
use App\Models\Item;
use App\Models\Cart;
use App\Models\Sale;
use App\Models\Order;
use App\Models\Expense;
use App\Models\Waybill;
use App\Models\ItemAudit;
use App\Models\SalesHistory;
use App\Models\SalesPayment;
use App\Models\CompanyBranch;
use App\Models\ItemImage;
use App\Models\Category;
use App\Models\Wbcontent;
use App\Models\Wbdistribution;
use App\Models\Closure;
use Exception;

class ItemsController extends Controller
{
    public function __construct(){
        $this->middleware(['auth', 'load_auth']);
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //
        if(auth()->user()->status != 'Administrator'){
            return redirect('/dashboard'); 
        }

        $match = ['del' => 'no'];
        $itemsearch = $request->query('itemsearch');
        if(!empty($itemsearch)){
            $items = Item::where($match)->where('name', 'like', '%'.$itemsearch.'%')->orderBy('id', 'desc')->paginate(10);
        }else{
            $items = Item::where($match)->orderBy('id', 'desc')->paginate(10);
        }

        // $items = Item::All();
        $ITM = ItemImage::All();
        $cats = Category::All();
        
        // $allStudents = Student::where('del', 'no')->get();
        
        // $searchquery = request()->query('searchquery');
        // $students = Student::where('fname', 'LIKE', '%'.$searchquery.'%')->paginate(10);
        // $std_pop = count($allStudents);

        $pass = [
            'c' => 1,
            'i' => 1,
            'ITM' => $ITM,
            'cats' => $cats,
            'items' => $items
        ];
        // return $items;
        return view('pages.dash.itemsview')->with($pass);
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
        $company = new Company;
        $user = new User;
        $cat = new Category;
        $item = new Item;
        //
        try {

            switch ($request->input('store_action')) {

                case 'create_user':

                    // $user = new User;
                    $ps1 = $request->input('password');
                    $ps2 = $request->input('password_confirmation');
                    $status = $request->input('status');

                    // $uc = CompanyBranch::where('name', $status)->first();
                    if($status == 'Administrator'){
                        $bv = 'A';
                        $br = 1;
                    }else{
                        $uc = CompanyBranch::find($status);
                        $bv = $uc->tag;
                        $br = $status;
                        $status = $uc->name;
                    }
    
    
                    try {
                        if($ps1 == $ps2){
                            $user->name = $request->input('name');
                            $user->email = $request->input('email');
                            $user->password = Hash::make($ps1);
                            $user->company_branch_id = $br;
                            $user->bv = $bv;
                            $user->status = $status;
                            $user->save();


                            return redirect('/dashuser')->with('success', 'User Created Successfully');
                        }else{
                            return redirect('/dashuser')->with('error', 'Passwords do not match');
                        }
                        
                            // // Update branch user id... Assign to this user
                            // $this_id = User::Latest('id')->first();
                            // $companybranch_id = CompanyBranch::where('name', $request->input('name'))->get('id');
                            // $bfind = CompanyBranch::find($companybranch_id);
                            // $bfind->user_id = $this_id;
                            // $bfind->save();

                    } catch (\Throwable $th) {
                        // throw $th;
                        return redirect('/dashuser')->with('error', 'Ooops... Username / Email already exists '.$th);
                    }
    
                break;

                case 'add_cat':

                    $cat->user_id = auth()->user()->id;
                    $cat->name = $request->input('name');
                    $cat->desc = $request->input('desc');
                    $cat->save();
                    return redirect('/dashuser')->with('success', 'User Created Successfully');
                   
                    //Hash::make($data['password']);
    
                break;

                case 'add_to_cart':

                    try {
                        //code...
                        $it_id = $request->input('item_id');
                        $name = $request->input('name');
                        $qty = $request->input('qty');
                        $sp = $request->input('price');

                        // Get available qty
                        $uId = auth()->user()->bv;
                        $q = 'q'.$uId;
                        $item = Item::find($it_id);
                        $cp = $item->cost_price;
                        $mainQty = $item->qty;
                        $avQty = $item->$q;

                        if ($qty > $avQty) {
                            # code...
                            return redirect('/sales')->with('error', 'Sorry..! Available Stock Quantity: '.$avQty);
                        }elseif ($sp == 0) {
                            # code...
                            return redirect('/sales')->with('error', 'Oops..! Define price for this item before purchase');
                        }
                        // Available Qty for q1, q2, q3....
                        $avQty = $avQty - $qty;
                        // Available Qty main 
                        $mainQty = $mainQty - $qty;

                        $matchThese = ['user_id' => auth()->user()->id, 'name' => $name];
                        $results = Cart::where($matchThese)->get();
                        
                        if (count($results) == 1){
                            return redirect('/sales')->with('error', 'Oops..! Item already added.. Edit from table ');
                        }else{

                            $cart = Cart::firstOrCreate([
                                'user_id' => auth()->user()->id,
                                'item_id' => $request->input('item_id'),
                                'item_no' => $request->input('item_no'),
                                'name' => $name,
                                'qty' => $qty,
                                'profits' => ($sp - $cp)*$qty,
                                'cost_price' => $cp,
                                'unit_price' => $sp,
                                'tot' => $qty*$sp,
                            ]);

                            // Update qty in stock
                            $qtyUp = Item::find($it_id);
                            $qtyUp->qty = $mainQty;
                            $qtyUp->$q = $avQty;
                            $qtyUp->save();

                            return redirect('/sales'); 
                            // return redirect('/sales')->with('success', $name.' added Successfully');
                        }
                    } catch (\Throwable $th) {
                        //throw $th;
                            return redirect('/sales')->with('error', 'Oops..! Something Happened ');
                    }
    
                break;

                case 'add_order':

                    $ref = $request->input('ref');

                    try {
                        // $this->validate($request, [
                        //     'repfile'   => 'required|max:5000|mimes:jpeg,jpg,png'
                        // ]);
                        if($request->hasFile('repfile')){
                            //get filename with ext
                            $filenameWithExt = $request->file('repfile')->getClientOriginalName();
                            //get filename
                            $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
                            //get file ext
                            $fileExt = $request->file('repfile')->getClientOriginalExtension();
                            //filename to store
                            $filenameToStore = 'rjv.'.$fileExt;
                            //upload path
                            $path = $request->file('repfile')->storeAs('public/rjv_receipts', $filenameToStore);
                            // return redirect('/order')->with('success', 'Successfull 3');

                        }else{
                            $filenameToStore = 'noimage.jpg';
                        }
            
                    } catch (Exception $ex) {
                        return redirect('/order')->with('error', 'Ooops..! Unhandled Error -->');
                    }



                    try {

                        $order = new Order;
                        $order->ref = $ref;
                        $order->user_id = auth()->user()->id;
                        $order->company_name = $request->input('company_name');
                        $order->contact = $request->input('contact');
                        $order->desc = $request->input('desc');
                        $order->tot = $request->input('tot');
                        $order->order_receipt = $filenameToStore;
                        
                        $order->save();

                        return redirect('/orders')->with('success', 'Order '.$ref.' successfully added');

                    }catch(Exception $ex) {
                        $ex2 = $ex->getMessage();
                        $ex2 = substr($ex2,0,100).'.....!';
                        return redirect('/orders')->with('error', 'Ooops..! Records already exists.');
                       
                    }
    
                break;

                case 'add_item':

                    $it_no = 'MT'.date('dis');
                    $name = $request->input('name');
                    $barcode = $request->input('barcode');
                    $matchThese = ['name' => $name, 'del' => 'no'];

                    $results = Item::where($matchThese)->get();


                    if (count($results) > 0){
                        return redirect('/dashuser')->with('error', 'Oops..! Item already exist');
                    }else{
                        
                        try {
                            
                            $im = new ItemImage;
                            $im->item_id = $it_no;
                            $im->save();


                            $im_search = ItemImage::where('item_id', $it_no)->first();
                            $Id = $im_search->id;
                            //return redirect('/dashuser')->with('success', $a);

                            if($request->hasFile('items')){

                                // $this->validate($request, [
                                //     'items' => 'required|max:5000|mimes:jpg,jpeg,png'
                                // ]);

                                $c = 1;
                                $hold = '';
                                foreach($request->file('items') as $file){
                                    $filenameWithExt = $file->getClientOriginalName();
                                    //get filename
                                    $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
                                    //get file ext
                                    $fileExt = $file->getClientOriginalExtension();
                                    //filename to store
                                    $tmpFile = 'item'.date('i-s').$c.'.';
                                    $filenameToStore = $tmpFile.$fileExt;
                                    //upload path
                                    $path = $file->storeAs('public/rjv_items', $filenameToStore);

                                    //$im_search = ItemImage::where('item_no', $it_no)->get();
                                    $im2 = ItemImage::find($Id);
                                    $h = 'img'.$c;
                                    $im2->$h = $filenameToStore;
                                    $im2->save();

                                    $c++;
                                }
                                
                            }else{
                                $filenameToStore = 'no_image.png';
                                $tmpFile = '';
                                //exit;
                            }
                

                        } catch (\Throwable $th) {
                            $im2 = ItemImage::find($Id);
                            $im2->delete();
                            return redirect('/dashuser')->with('error', 'Ooops..! Unhandled Error ');
                        }

                        $full = ItemImage::latest('id')->first();

                        try {
                            $cp = $request->input('price');
                            // $sp = $request->input('price');
                            // $profits = $cp - $sp;

                            $item->user_id = auth()->user()->id;
                            $item->itemimage_id = $full->id;
                            $item->item_no = $it_no;
                            $item->name = $name;
                            $item->desc = $request->input('desc');
    
                            $item->cat = $request->input('cat');
                            $item->brand = $request->input('brand');
                            $item->barcode = $barcode;

                            $item->qty = $request->input('qty');
                            $item->cost_price = $cp;
                            $item->price = $cp;
                            // $item->profits = $profits;
                            $item->img = $filenameToStore;
                            $item->thumb_img = $filenameToStore;
    
                            $item->save();

                            $search2 = Item::where('item_no', $it_no)->first();
                            $id2 = $search2->id;

                            // Change item_id from M... to $id in ItemImage table
                            $im3 = ItemImage::find($Id);
                            $im3->item_id = $id2;
                            $im3->save();
                            

                            return redirect('/dashuser')->with('success', 'Item successfully added');
                        } catch(\Throwable $th){
                            return redirect('/dashuser')->with('error', 'Oops..! Unhandled Error! ');
                        }
                        
                    }

                break;

                case 'update_item':

                    try {
                        $item->del = 'yes';
                        $item->save();
                        return redirect('/itemsview')->with('success', 'Item successfully deleted');
                    } catch(Exception $ex){
                        return redirect('/itemsview')->with('error', 'Oops..! Unhandled Error!');
                    }      

                        
                break;

                case 'admi_config':

                    $name = $request->input('name');
                    $loc = $request->input('loc');
                    // $matchThese = ['name' => $name, 'location' => $loc, 'del' => 'no'];

                    $results = Company::find(1);

                    if ($results){
                        try {
                            $company = Company::find(1);
                            $company->user_id = auth()->user()->id;
                            $company->name = $name;
                            session('company')->address = $request->input('company_add');
    
                            $company->location = $loc;
                            session('company')->contact = $request->input('contact');
    
                            session('company')->email = $request->input('email');
                            $company->website = $request->input('company_web');
                            $company->reg_date = Date('d-m-Y');
                            $company->logo = $request->input('company_logo');
    
                            $company->save();
                            return redirect('/config')->with('success', 'Company`s details successfully updated');
                        } catch(Exception $ex){
                            return redirect('/config')->with('error', 'Oops..! Unhandled error');
                        }

                    }else{

                        try {
                            $this->validate($request, [
                                'company_logo'   => 'required|max:5000|mimes:jpeg,jpg,png'
                            ]);
                            if($request->hasFile('company_logo')){
                                //get filename with ext
                                $filenameWithExt = $request->file('company_logo')->getClientOriginalName();
                                //get filename
                                $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
                                //get file ext
                                $fileExt = $request->file('company_logo')->getClientOriginalExtension();
                                //filename to store
                                $filenameToStore = 'company_logo.'.$fileExt;
                                //upload path
                                $path = $request->file('company_logo')->storeAs('public/ss_imgs', $filenameToStore);
                            }else{
                                $filenameToStore = '';
                            }
                
                        } catch (Exception $ex) {
                            return redirect('/config')->with('error', 'Ooops..! Unhandled Error');
                        }
                        

                        try {
                            $company->user_id = auth()->user()->id;
                            $company->name = $name;
                            session('company')->address = $request->input('company_add');
    
                            $company->location = $loc;
                            session('company')->contact = $request->input('contact');
    
                            session('company')->email = $request->input('email');
                            $company->website = $request->input('company_web');
                            $company->reg_date = Date('d-m-Y');
                            $company->logo = $filenameToStore;
    
                            $company->save();
                            return redirect('/config')->with('success', 'Company`s details successfully added');
                        } catch(Exception $ex){
                            return redirect('/config')->with('error', 'Ooops..! Unhandled Error');
                        }
                        
                    }
                    
                break;

                case 'admi_create_std':

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
                            return redirect('/addstudent')->with('error', 'Ooops..! Unhandled Error --> Invalid information provided. Check input(Class / Date of Birth / Add Items To Bill)');
                           
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
                        return redirect('/addstudent')->with('error', 'Ooops..! Unhandled Error');
                    }
                break;

                case 'add_waybill':

                    $xter = substr(str_shuffle(str_repeat("0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ", 4)), 0, 4);
                    $time = date('is');
                    $stockNo = 'ST'.$xter.$time;

                    try {
                        //code...
                        $waybill = Waybill::firstOrCreate([
                            'user_id' => auth()->user()->id,
                            'stock_no' => $stockNo,
                            'comp_name' => $request->input('comp_name'),
                            'comp_add' => $request->input('comp_add'),
                            'comp_contact' => $request->input('comp_contact'),
                            'drv_name' => $request->input('drv_name'),
                            'drv_contact' => $request->input('drv_contact'),
                            'vno' => $request->input('vno'),
                            'bill_no' => $request->input('bill_no'),
                            'weight' => $request->input('weight'),
                            'nop' => $request->input('nop'),
                            'tot_qty' => $request->input('tot_qty'),
                            'del_date' => $request->input('del_date'),
                            'status' => $request->input('status')
                            ]);
                        
                        // $waybill = new Waybill;
                        // $waybill->user_id = auth()->user()->id;
                        // $waybill->comp_name = $request->input('comp_name');
                        // $waybill->comp_add = $request->input('comp_add');
                        // $waybill->comp_contact = $request->input('comp_contact');
                        // $waybill->drv_name = $request->input('drv_name');
                        // $waybill->drv_contact = $request->input('drv_contact');
                        // $waybill->vno = $request->input('vno');
                        // $waybill->bill_no = $request->input('bill_no');
                        // $waybill->weight = $request->input('weight');
                        // $waybill->nop = $request->input('nop');
                        // $waybill->tot_qty = $request->input('tot_qty');
                        // $waybill->del_date = $request->input('del_date');
                        // $waybill->status = $request->input('status');
                        // $waybill->save();

                        return redirect('/waybill')->with('success', 'Bill Successfully Saved');

                    } catch (\Throwable $th) {
                        //throw $th;
                        return redirect('/waybill')->with('error', 'Oops..! Unhandled Error. ');
                    }
                   
                    //Hash::make($data['password']);
    
                break;

                case 'create_branch':

                    try {
                        //code...
                        $bc = CompanyBranch::all()->count();
                        $branch = new CompanyBranch;
                        $branch->user_id = auth()->user()->id;
                        $branch->name = $request->input('name');
                        $branch->loc = $request->input('loc');
                        $branch->contact = $request->input('contact');
                        // $branch->tag = 'b'.($bc + 1);
                        $branch->tag = $bc + 1;
                        $branch->save();
                        return redirect('/config')->with('success', 'Branch Created Successfully');
                    } catch (\Throwable $th) {
                        //throw $th;
                        return redirect('/config')->with('error', 'Oops..! '.$request->input('name').' already created under company branches.');
                    }
    
                break;

                case 'update_branch':

                    $my_id = $request->input('id');
                    $item_up = Item::find($my_id);
                    $item_up->b1 = $request->input('b1');
                    $item_up->b2 = $request->input('b2');
                    $item_up->b3 = $request->input('b3');
                    $item_up->save();
                    return redirect('/dashuser')->with('success', 'Branch prices for "'.$request->input('comp_name').'" has been updated');
                   
                    //Hash::make($data['password']);
    
                break;

                case 'update_branch_qty':

                    $my_id = $request->input('id2');
                    $item_up = Item::find($my_id);

                    $q1 = $request->input('x1');
                    $q2 = $request->input('y2');
                    $q3 = $request->input('z3');
                    $qs = $q1 + $q2 + $q3;

                    if($qs == $item_up->qty){
                        $item_up->q1 = $q1;
                        $item_up->q2 = $q2;
                        $item_up->q3 = $q3;
                        $item_up->save();
                        return redirect('/dashuser')->with('success', 'Branch quantities for "'.$request->input('comp_name2').'" has been updated');
                    }else{
                        return redirect('/dashuser')->with('error', 'Oops...! Sum of branch quantities cannot be greater or less than the QUANTITY AVAILABLE');
                    }
                    // return $my_id;
    
                break;

                case 'add_to_sales':

                    $xter = substr(str_shuffle(str_repeat("0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ", 4)), 0, 4);
                    $time = date('is');
                    $order_no = 'M'.$xter.$time;

                    try {
                        //code...

                        $pd = 'Paid';
                        $pmt = $request->input('payment');                        
                        $pay_mode = $request->input('pay_mode');
                        $del_status = $request->input('del_status');

                        if ($pay_mode == '-- Mode of Payment --' or $del_status == '-- Delivery Status --'){
                            return redirect('/sales')->with('error', "Select -- Mode of Payment -- / -- Delivery Status -- to proceed..");
                        }
                        if ($pay_mode == 'Post Payment(Debt)'){
                            $pd = 'No';
                        }

                        // Transport Cart to Sales History

                        $carts = Cart::where('user_id', auth()->user()->id)->get();
                        // return count($carts);
                        $qty = $carts->sum('qty');
                        $tot = $carts->sum('tot');

                        if ($request->input('discount') > $tot) {
                            return redirect(url()->previous())->with('error', "Oops..! Discount cannot be greater than total amount");
                        }

                        if($request->input('discount') == ''){
                            $discount = 0;
                        }else{
                            // $percentage = $request->input('discount');
                            // $discount = ($percentage / 100) * $tot;
                            $discount = $request->input('discount');
                            $tot = $tot - $discount;
                        }

                        if ($pmt < $tot && $pay_mode != 'Post Payment(Debt)') {
                            return redirect(url()->previous())->with('error', "Oops..! Amount paid cannot be less than total cost. Otherwise select the `Post Payment(Debt)` option");
                        }

                        if(count($carts) > 0){

                            // Insert Sales Record
                            $sales = Sale::firstOrCreate([
                                'user_id' => auth()->user()->id,
                                'user_bv' => auth()->user()->bv,
                                'order_no' => $order_no,
                                'qty' => $qty,
                                'tot' => $tot,
                                'pay_mode' => $pay_mode,
                                'buy_name' => $request->input('buy_name'),
                                'buy_contact' => $request->input('buy_contact'),
                                'del_status' => $del_status,
                                'discount' => $discount,
                                'payment' => $pmt,
                                'change' => $pmt - $tot,
                                'paid' => $pd
                            ]);

                            if ($pay_mode == 'Post Payment(Debt)' && $pmt > 0) {
                                
                                if ($pmt > $tot) {
                                    $pmt = $tot;
                                }
                                $sales_pay = new SalesPayment;
                                $sales_pay->user_id = auth()->user()->id;
                                $sales_pay->sale_id = $sales->id;
                                $sales_pay->amt_paid = $pmt;
                                $sales_pay->bal = $tot - $pmt;
                                $sales_pay->save();
                                if ($pmt == $tot){
                                    $pd = 'Paid';
                                }
                                $sales->paid = $pd;
                                $sales->paid_debt = $pmt;
                                $sales->save();

                            }

                            $sale_id = Sale::latest()->limit(1)->get();
                            foreach ($sale_id as $sid) {
                                # code...
                                $new_sid = $sid->id;
                            }

                            foreach ($carts as $cart) {
                                # code...
                                
                                $sales_history = SalesHistory::firstOrCreate([
                                    'user_id' => $cart->user_id,
                                    'sale_id' => $new_sid,
                                    'item_id' => $cart->item_id,
                                    'user_bv' => auth()->user()->bv,
                                    'item_no' => $cart->item_no,
                                    'name' => $cart->name,
                                    'qty' => $cart->qty,
                                    'cost_price' => $cart->cost_price,
                                    'unit_price' => $cart->unit_price,
                                    'profits' => $cart->profits,
                                    'tot' => $cart->tot,
                                    'del_status' => $del_status,
                                ]);
                                // Reduce stock
                                // $itm = Item::where('id', $cart->item_id)->first();
                                // $bvv = 'q'.auth()->user()->bv;
                                // $itm->$bvv = $itm->$bvv - $cart->qty;
                                // $itm->save();

                                // Empty specific user cart after transport
                                $cart_del = Cart::find($cart->id);
                                $cart_del->delete();
                            }


                            return redirect('/sales')->with('success', 'Purchase Complete..!');
                        }

                    } catch (\Throwable $th) {
                        //throw $th;
                        return redirect('/sales')->with('error', 'Oops..! Unhandled Error... '.$th);
                    }
    
                break;

                case 'create_expense':
                    // $exps = Expense::where('companybranch_id', '')->get();
                    // $ex = Expense::all();
                    // return $ex[1]->companybranch;
                    // foreach ($exps as $exp) {
                    //     $exp->companybranch_id = 2;
                    //     $exp->save();
                    // }
                    // return 'Done..!';

                    // if (auth()->user()->status != 'Administrator'){
                    //     $branch = auth()->user()->status;
                    // }else{
                    //     $branch = $request->input('branch');
                    // }
                    $branch = $request->input('branch');
                    if ($branch == 0){
                        return redirect(url()->previous())->with('error', 'Oops..! Choose branch to apply expenses to.');
                    }

                    $expense = new Expense;
                    $expense->user_id = auth()->user()->id;
                    $expense->companybranch_id = $branch;
                    $expense->title = $request->input('title');
                    $expense->desc = $request->input('desc');
                    $expense->expense_cost = $request->input('expense_cost');
                    $expense->save();
    
                    return redirect('/expenses')->with('success', 'Expense Record Added Successfully');
                    
                break;

                case 'pay_debt':

                    $uid= auth()->user()->id;
                    $sale_id = $request->input('send_id');
                    $send_tot = $request->input('send_tot');
                    $amt_paid = $request->input('amt_paid');

                    if($amt_paid > $send_tot){
                        return redirect('/sales')->with('error', 'Oops..! Amount paying cannot be greater than amount owing.');
                    }

                    $sum_debts = SalesPayment::where('del', 'no')->where('sale_id', $sale_id)->sum('amt_paid');
                    if($sum_debts == 0){
                        $sum_debts = $amt_paid;
                        // return redirect(url()->previous())->with('error', 'No entries for Sales Payments');
                    }else{
                        $sum_debts = $sum_debts + $amt_paid;
                        // $amt_paid = $sum_debts;
                    }
                    
                    $sales_pay = new SalesPayment;
                    $sales_pay->user_id = $uid;
                    $sales_pay->sale_id = $sale_id;
                    $sales_pay->amt_paid = $amt_paid;
                    $sales_pay->bal = $send_tot - $sum_debts;
                    $sales_pay->save();

                    // $sum_debts = SalesPayment::where('sale_id', $sale_id)->sum('amt_paid');

                    $sale = Sale::find($sale_id);
                    $sale->paid_debt = $sum_debts;
                    if($send_tot == $sum_debts){
                        $sale->paid = 'Paid';
                    }
                    $sale->save();


                    // $cat->save();
                    return redirect(url()->previous())->with('success', 'Payment of Gh₵ '.$amt_paid.' successfull made.');
                   
                    //Hash::make($data['password']);
    
                break;

                case 'add_wbcontent':

                    $wb_id = $request->input('wb_id');
                    $qty = $request->input('qty');
                    $item_id = $request->input('item');

                    $wbcont_check = Wbcontent::where('waybill_id', $wb_id)->where('item_id', $item_id)->get();
                    $item = Item::find($item_id);
                    if (count($wbcont_check) > 0) {
                        return redirect(url()->previous())->with('error', 'Oops..! Item `'.$item->item_no.' - '.$item->name.'` already added.');
                    }

                    try {
                        $wb_insert = Wbcontent::firstOrCreate([
                            'user_id' => auth()->user()->id,
                            'waybill_id' => $wb_id,
                            'item_id' => $item_id,
                            'qty' => $qty,
                        ]);
                    } catch (\Throwable $th) {
                        throw $th;
                    }
                    return redirect(url()->previous())->with('success', 'Item `'.$item->item_no.' - '.$item->name.'` successfully added.');
    
                break;

                case 'set_closure':

                    $month = session('cldate');
                    $m = date('m', strtotime($month));
                    $y = date('Y', strtotime($month));
                    $om = $m - 1;
                    if ($om < 10) {
                        $om = '0'.$om;
                    }
                    if ($m == '01' || $m == '1') {
                        $y = $y - 1;
                        $old_month = date($y.'-12-01');
                    } else {
                        $old_month = date($y.'-'.$om.'-01');
                    }
                    // return $old_month;
                    

                    if ($m < date('m')) {
                        return redirect(url()->previous())->with('error', 'Oops..! Openings cannot be made for previous month');
                    }

                    $closure_check = Closure::where('month', $old_month)->latest()->first();
                    // return $closure_check;
                    
                    if ($closure_check != '') {
                        if ($closure_check->status == 'open') {
                            return redirect(url()->previous())->with('error', 'Warning..! Set closure for previous month to proceed');
                        }
                    }else{
                        return redirect(url()->previous())->with('error', 'Opps..! close previous month in order to create openning for '.date('F, Y', strtotime($month)));
                    }
                    $month = session('cldate');
                    $closure = Closure::firstOrCreate([
                        'user_id' => auth()->user()->id,
                        'month' => $month,
                        'status' => 'open',
                    ]);
                    return redirect(url()->previous())->with('success', 'Openning for '.date('F, Y', strtotime($month)).' successfully set');
                break;

                case 'closure':

                    $month = session('cldate');
                    $m = date('m', strtotime($month));
                    $y = date('Y', strtotime($month));
                    $om = $m - 1;
                    if ($om < 10) {
                        $om = '0'.$om;
                    }
                    if ($m == '01' || $m == '1') {
                        $y = $y - 1;
                        $old_month = date($y.'-12-01');
                    } else {
                        $old_month = date($y.'-'.$om.'-01');
                    }
                    $closure_check = Closure::where('month', $old_month)->count();
                    // return $old_month;
                    if ($closure_check > 0) {
                    } else {
                        return redirect(url()->previous())->with('error', 'Warning..! Set closure for previous month to proceed');
                        return redirect(url()->previous())->with('error', 'Closure not set for '.date('F, Y', strtotime($old_month)));
                    }
                    // return date('Y-m-t', strtotime($month));

                    try {
                        $closure = Closure::where('month', $month)->latest()->first();
                        // $closure = Closure::firstOrCreate([
                        //     'user_id' => auth()->user()->id,
                        //     'month' => $month,
                        //     'tot_qty' => session('sales_history')->sum('qty'),
                        //     'avl_qty' => session('items')->sum('qty'),
                        //     'amt_sold' => session('sales_history')->sum('tot'),
                        //     // 'exp_amt' => ($sp - $cp)*$qty,
                        //     'profits' => session('sales_history')->sum('profits'),
                        //     'status' => 'open',
                        // ]);

                        $closure->month = $month;
                        $closure->tot_qty = session('sales_history')->sum('qty');
                        $closure->avl_qty = session('items')->sum('qty');
                        $closure->amt_sold = session('sales_history')->sum('tot');
                        // $closure->exp_amt = ($sp - $cp)*$qty;
                        $closure->profits = session('sales_history')->sum('profits');
                        $closure->status = 'closed';
                        $closure->save();

                        for ($i=1; $i <= count(session('compbranch')); $i++) { 
                            $salesH = SalesHistory::where('user_bv', $i)->whereBetween('created_at', [$month, date('Y-m-t', strtotime($month))])->get();
                            $qq = 'q'.$i;
                            $closure->$qq = $salesH->sum('qty');
                            $closure->save();
                        }
                        // return $salesH;
                    } catch (\Throwable $th) {
                        throw $th;
                    }
                    return redirect(url()->previous())->with('success', 'Closure set for '.date('F, Y', strtotime($month)));
                   

                break;

            }
        
        }catch(Exception $e) {
            //echo 'Message: ' .$e->getMessage();

            switch ($request->input('store_action')) {

                case 'admi_create_trs':
                    return redirect('/dashboard')->with('error', 'Ooops..! Unhandled Error');
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
        
        $item = Item::find($id);
        $qty = $item->qty;

        if ($qty < 1){
            $qty = "Out of Stock";
        }else{
            $qty = "Available";
        }
        //$itemImgs = ItemImage::find($id);

        $pass = [
            'item' => $item,
            'qty' => $qty
        ];
        return view('pages.products_det')->with($pass);

        // $item = Item::find($id);
        // $item2 = $item->itemimage->img1;
        // return $item2;
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
        switch ($request->input('store_action')) {

            case 'update_user':

                $ps1 = $request->input('password');
                $ps2 = $request->input('password_confirmation');

                if($ps1 == $ps2){
                    $user = User::find($id);
                    $user->name = $request->input('name');
                    $user->email = $request->input('email');
                    $user->password = Hash::make($ps1);
                    $user->save();

                    return redirect(url()->previous())->with('success', 'Update Successful');
                }else{
                    return redirect(url()->previous())->with('error', 'Passwords do not match');
                }

            break;

            case 'update_item':

                $qtySum = $request->input('q1') + $request->input('q2') + $request->input('q3');
                // if($qtySum != $request->input('qty')){
                //     # code...
                //     return redirect('/items')->with('error', 'Oops..! Sum of branch quantities should be equal to Total Quantity.');
                // }

                $item = Item::find($id);
                // try {

                    $itemAudit = ItemAudit::firstOrCreate([
                        'item_no' => $item->item_no,
                        'user_id' => auth()->user()->id,
                        'name' => $item->name,
                        'desc' => $item->desc,
                        'cat' => $item->cat,
                        'brand' => $item->brand,
                        'barcode' => $item->barcode,
                        'qty' => $item->qty,
                        'q1' => $item->q1,
                        'q2' => $item->q2,
                        'q3' => $item->q3,
                        'q4' => $item->q4,
                        'q5' => $item->q5,
                        'q6' => $item->q6,
                        'q7' => $item->q7,
                        'price' => $item->price,
                        'cost_price' => $item->price,
                        'b1' => $item->b1,
                        'b2' => $item->b2,
                        'b3' => $item->b3,
                        'b4' => $item->b4,
                        'b5' => $item->b5,
                        'b6' => $item->b6,
                        'b7' => $item->b7,
                    ]);
                    
                    $item->name = $request->input('name');
                    $item->desc = $request->input('desc');
                    $item->cat = $request->input('cat');
                    $item->brand = $request->input('brand');
                    $item->barcode = $request->input('barcode');
                    $item->qty = $request->input('qty');
                    $item->price = $request->input('price');
                    $item->cost_price = $request->input('price');
                    $item->save();

                    for ($i=0; $i < count(session('compbranch')); $i++) { 
                        $qq = 'q'.$i + 1;
                        $bb = 'b'.$i + 1;
                        $item->$qq = $request->input($qq);
                        $item->$bb = $request->input($bb);
                        $item->save();
                    }
                    $item = '';


                    $item = Item::find($id);
                    $itemAudit = ItemAudit::firstOrCreate([
                        'item_no' => $item->item_no,
                        'user_id' => auth()->user()->id,
                        'name' => $item->name,
                        'desc' => $item->desc,
                        'cat' => $item->cat,
                        'brand' => $item->brand,
                        'barcode' => $item->barcode,
                        'qty' => $item->qty,
                        'q1' => $item->q1,
                        'q2' => $item->q2,
                        'q3' => $item->q3,
                        'q4' => $item->q4,
                        'q5' => $item->q5,
                        'q6' => $item->q6,
                        'q7' => $item->q7,
                        'price' => $item->price,
                        'cost_price' => $item->price,
                        'b1' => $item->b1,
                        'b2' => $item->b2,
                        'b3' => $item->b3,
                        'b4' => $item->b4,
                        'b5' => $item->b5,
                        'b6' => $item->b6,
                        'b7' => $item->b7,
                    ]);

                    return redirect(url()->previous())->with('success', 'Record successfully updated');
                // } catch(Exception $ex){
                //     return redirect('/items')->with('error', 'Oops..! Unhandled Error! ');
                // }      

                    
            break;

            case 'del_waybil':

                $wb = Waybill::find($id);
                $wb->del = 'yes';
                $wb->save();
                return redirect(url()->previous())->with('success', 'Waybill deletion successful');
                
            break;

            case 'del_item':

                $item = Item::find($id);
                try {
                    $item->del = 'yes';
                    $item->save();
                    return redirect('/items')->with('success', 'Item successfully deleted');
                } catch(Exception $ex){
                    return redirect('/items')->with('error', 'Oops..! Unhandled Error!');
                }      

                    
            break;

            case 'update_sales':

                $pay_mode = $request->input('pay_mode');
                if($pay_mode == '-- Mode of Payment --'){
                    return redirect('/sales')->with('error', "Select -- Mode of Payment -- / -- Delivery Status -- to proceed..");
                }

                $sale = Sale::find($id);
                if ($sale->pay_mode == 'Post Payment(Debt)' && $sale->paid != 'Paid' && $pay_mode != 'Post Payment(Debt)') {
                    return redirect('/sales')->with('error', "Oops..! Pay debt in full before changing pay mode to ".$pay_mode);
                }
                try {
                    $sale->pay_mode = $pay_mode;
                    $sale->buy_name = $request->input('buy_name');
                    $sale->buy_contact = $request->input('buy_contact');
                    $sale->save();
                    return redirect(url()->previous())->with('success', 'Update Successful');
                } catch(Exception $ex){
                    return redirect(url()->previous())->with('error', 'Oops..! Error updating record.');
                }      
                    
            break;

            case 'update_waybill':

                $waybill = Waybill::find($id);

                try {
                    //code...
                    
                    $waybill->user_id = auth()->user()->id;
                    $waybill->comp_name = $request->input('comp_name');
                    $waybill->comp_add = $request->input('comp_add');
                    $waybill->comp_contact = $request->input('comp_contact');
                    $waybill->drv_name = $request->input('drv_name');
                    $waybill->drv_contact = $request->input('drv_contact');
                    $waybill->vno = $request->input('vno');
                    $waybill->bill_no = $request->input('bill_no');
                    $waybill->weight = $request->input('weight');
                    $waybill->nop = $request->input('nop');
                    $waybill->tot_qty = $request->input('tot_qty');
                    $waybill->del_date = $request->input('del_date');
                    $waybill->status = $request->input('status');
                    $waybill->save();

                    return redirect('/waybillview')->with('success', 'Bill Successfully Updated');

                } catch (\Throwable $th) {
                    //throw $th;
                    return redirect('/waybillview')->with('error', 'Oops..! Unhandled Error!');
                }

            break;

            case 'qty_change':

                $my_url = $request->input('my_url');

                $cart = Cart::find($id);
                $cart_qty = $cart->qty;
                $it_id = $cart->item_id;
                $price = $request->input('price');
                $change = $request->input('change');


                // Get available qty
                $uId = auth()->user()->bv;
                $q = 'q'.$uId;
                $item = Item::find($it_id);
                $mainQty = $item->qty;
                $avQty = $item->$q;

                // return $it_id;

                if (($change - $cart_qty) > $avQty) {
                    # code...
                    return redirect('/sales')->with('error', 'Sorry..! Available Stock Quantity: '.$avQty);
                }

                if ($change > $cart_qty){
                    $diff = $change - $cart_qty;
                    // if increase... Available Qty for q1, q2, q3....
                    $avQty = $avQty - $diff;
                    // Available Qty main 
                    $mainQty = $mainQty - $diff;
                }elseif ($change < $cart_qty){
                    $diff = $cart_qty - $change;
                    // if decrease... Available Qty for q1, q2, q3....
                    $avQty = $avQty + $diff;
                    // Available Qty main 
                    $mainQty = $mainQty + $diff;
                }else{
                }

                try {
                    $newTot = $price*$change;
                    $cart->qty = $change;
                    $cart->profits = $change*($cart->unit_price - $cart->cost_price);
                    $cart->tot = $newTot;
                    $cart->save();

                    // Update qty in stock
                    $qtyUp = Item::find($it_id);
                    $qtyUp->qty = $mainQty;
                    $qtyUp->$q = $avQty;
                    $qtyUp->save();

                    return redirect('/sales')->with('success', ' quantity updated..');
                } catch(Exception $ex){
                    return redirect('/sales')->with('error', 'Oops..! Unhandled Error!');
                }      

                    
            break;

            case 'del_cart':

                $cart = Cart::find($id);
                $cart_qty = $cart->qty;
                $it_id = $cart->item_id;

                // Get item id
                $uId = auth()->user()->bv;
                $q = 'q'.$uId;

                $item = Item::find($it_id);
                $item_qty = $item->qty + $cart_qty;
                $avb_qty = $item->$q + $cart_qty;


                try {
                    $item->qty = $item_qty;
                    $item->$q = $avb_qty;
                    $item->save();
                    $cart->delete();
                    return redirect('/sales')->with('success', 'Item successfully deleted');
                } catch(Exception $ex){
                    return redirect('/sales')->with('error', 'Oops..! Unhandled Error!');
                }      

                    
            break;

            case 'print_invoice':

                return view('pages.dash.invoice');  
                    
            break;

            case 'deliver':

                $sale_id = $request->input('send_sale_id');
                $sh = SalesHistory::find($id);
                $uid = $sh->user_id;

                $match = ['sale_id' => $sale_id, 'del_status' => 'Not Delivered'];
                try {
                    $sh->del_status = 'Delivered';
                    $sh->save();

                    $sh_count = SalesHistory::where($match)->count();
                    // return $sh_count;
                    if ( $sh_count == 0) {
                        # code...
                        $sale = Sale::find($sale_id);
                        $sale->del_status = 'Delivered';
                        $sale->save();
                    }
                    return redirect('/sales')->with('success', 'Item delivered');
                } catch(Exception $ex){
                    return redirect('/sales')->with('error', 'Oops..! Unhandled Error! ');
                }      

                    
            break;

            case 'undeliver':

                $sale_id = $request->input('send_sale_id');
                $sh = SalesHistory::find($id);
                $uid = $sh->user_id;

                $match = ['sale_id' => $sale_id, 'del_status' => 'Not Delivered'];
                $sh_count = SalesHistory::where($match)->count();
                // try {
                    $sh->del_status = 'Not Delivered';
                    $sh->save();

                    if ( $sh_count == 0) {
                        # code...
                        $sale = Sale::find($sale_id);
                        $sale->del_status = 'Not Delivered';
                        $sale->save();
                    }
                    return redirect('/sales')->with('success', 'Item undelivered');
                // } catch(Exception $ex){
                //     return redirect('/sales')->with('error', 'Oops..! Unhandled Error! ');
                // }      

                    
            break;

            case 'up_wbcontent':
                $wb = Wbcontent::find($id);
                $wb->qty = $request->input('qty');
                $wb->save();
                return redirect(url()->previous())->with('success', 'Waybill quantity update successful');
            break;

            case 'del_wbcontent':
                // Check availabity or if dustributed in Wbdistribution before deletion
                $wb = Wbcontent::find($id);
                // $wb->del = 'yes';
                $wb->delete();
                return redirect(url()->previous())->with('success', 'Item removed from waybill');
            break;

            case 'up_wbdist':
                $q1=0; $q2=0; $q3=0; $q4=0; $q5=0; $q6=0; $q7=0;
                $wbc = Wbcontent::find($id);
                $wbd = Wbdistribution::where('waybill_id', $wbc->waybill_id)->where('item_id', $wbc->item_id)->latest()->first();
                if ($request->input('q1'.$wbc->item_id)) {
                    $q1 = $request->input('q1'.$wbc->item_id);
                }
                if ($request->input('q2'.$wbc->item_id)) {
                    $q2 = $request->input('q2'.$wbc->item_id);
                }
                if ($request->input('q3'.$wbc->item_id)) {
                    $q3 = $request->input('q3'.$wbc->item_id);
                }
                if ($request->input('q4'.$wbc->item_id)) {
                    $q4 = $request->input('q4'.$wbc->item_id);
                }
                if ($request->input('q5'.$wbc->item_id)) {
                    $q5 = $request->input('q5'.$wbc->item_id);
                }
                if ($request->input('q6'.$wbc->item_id)) {
                    $q6 = $request->input('q6'.$wbc->item_id);
                }
                if ($request->input('q7'.$wbc->item_id)) {
                    $q7 = $request->input('q7'.$wbc->item_id);
                }
                // return $q1.', '.$q2.', '.$q3.', '.$q4.', '.$q5.', '.$q6.', '.$q7;

                $itup = Item::find($wbc->item_id);
                $totQs = $q1 + $q2 + $q3 + $q4 + $q5 + $q6 + $q7;
                if ($wbc->qty == $wbc->qty_dist) {
                    return redirect(url()->previous())->with('error', 'Oops..! Restock item `'.$itup->name.'` in order to distribute. 0 left');
                }
                if ($wbc->qty - ($wbc->qty_dist + $totQs) < 0) {
                    return redirect(url()->previous())->with('error', 'Oops..! Only '.$wbc->qty - $wbc->qty_dist.' available for distribution to branches');
                }
                $wbc->qty_dist = $wbc->qty_dist + $totQs;
                $wbc->save();

                // if ($wbd) {
                //     $wbd->q1 = $wbd->q1 + $q1;
                //     $wbd->q2 = $wbd->q2 + $q2;
                //     $wbd->q3 = $wbd->q3 + $q3;
                //     $wbd->q4 = $wbd->q4 + $q4;
                //     $wbd->q5 = $wbd->q5 + $q5;
                //     $wbd->q6 = $wbd->q6 + $q6;
                //     $wbd->q7 = $wbd->q7 + $q7;
                //     $wbd->save();
                // } else {
                    $wbd_insert = Wbdistribution::firstOrCreate([
                        'user_id' => auth()->user()->id,
                        'waybill_id' => $wbc->waybill_id,
                        'item_id' => $wbc->item_id,
                        'q1' => $q1,
                        'q2' => $q2,
                        'q3' => $q3,
                        'q4' => $q4,
                        'q5' => $q5,
                        'q6' => $q6,
                        'q7' => $q7,
                    ]);
                // }
                // return redirect(url()->previous())->with('success', 'Waybill branch quantities update successful');
                
                $itup->q1 = $itup->q1 + $q1;
                $itup->q2 = $itup->q2 + $q2;
                $itup->q3 = $itup->q3 + $q3;
                $itup->q4 = $itup->q4 + $q4;
                $itup->q5 = $itup->q5 + $q5;
                $itup->q6 = $itup->q6 + $q6;
                $itup->q7 = $itup->q7 + $q7;
                $itup->qty = $itup->qty + $totQs;
                $itup->save();
                
                // Update wbc quantiy distributed. To obtain remaining qty.
                // $wb = Wbcontent::where('waybill_id', $wbc->waybill_id)->where('item_id', $wbc->item_id)->latest()->first();
                return redirect(url()->previous())->with('success', 'Waybill branch quantities update successful');
            break;

            case 'del_paid_debt':
                $sp = SalesPayment::find($id);
                $order = Sale::where('id', $sp->sale_id)->first();
                $replace = $order->paid_debt - $sp->amt_paid;
                $order->paid_debt = $replace;
                $order->paid = 'No';
                $order->save();
                $sp->del = 'yes';
                $sp->save();
                return redirect(url()->previous())->with('success', 'Record deletion successfull');
            break;

            case 'cart_del':
                $cart = Cart::find($id);
                // Reduce stock
                $itm = Item::where('id', $cart->item_id)->first();
                $bvv = 'q'.auth()->user()->bv;
                $itm->$bvv = $itm->$bvv + $cart->qty;
                $itm->save();
                $cart->delete();
                return redirect(url()->previous())->with('success', $cart->name.' deleted successfully');
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
        switch ($request->input('del_action')) {

            case 'cat_del':
                $cat = Category::find($id);
                $cat->delete();
                return redirect(url()->previous())->with('success', 'Category Deleted.');
            break;

            case 'expense_del':
                $exp = Expense::find($id);
                $exp->del = 'yes';
                $exp->save();
                return redirect(url()->previous())->with('success', 'Category Deleted.');
            break;

            case 'item_del':
                $item = Item::find($id);
                $item->del = 'yes';
                $item->save();
                return redirect(url()->previous())->with('success', 'Course Deleted');
            break;

            case 'usr_del':
                $user = User::find($id);
                $user->del = 'yes';
                $user->save();
                return redirect(url()->previous())->with('success', 'User Deleted.');
            break;

            case 'usr_restore':
                $user = User::find($id);
                $user->del = 'no';
                $user->save();
                return redirect(url()->previous())->with('success', 'User Successfully Restored.');
            break;

            case 'branch_del':
                $branch = CompanyBranch::find($id);
                $branch->del = 'yes';
                $branch->save();
                return redirect(url()->previous())->with('success', 'Branch Deleted.');
            break;
        }
    }
}

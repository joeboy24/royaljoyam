$results=DB::table('fees')->where('fullname','LIKE','%'.$request->search.'%')->orderBy('id', 'desc')->get();
            if($results){
                foreach ($results as $result) {
                    //         if($result->del != 'yes'){

                    //             if ($c % 2 == 0) {
                    //                 $tr = '<tr class="rowColour"><td>'.$c++.'</td>';
                    //             }else{
                    //                 $tr = '<tr><td>'.$c++.'</td>';
                    //             }
                    //             $output.=$tr.
                    //             '<td>'.$company->name.'</td>
                    //             <td>'.$result->class.'</td>
                    //             <td>'.$result->term.'</td>
                    //             <td>'.$result->year.'</td>
                    //             <td>'.$result->inst1.'</td>
                    //             <td>'.$result->created_at.'</td>
                    //             </tr>
                    //             ';

                    //         }

                    //     }
                    //     return Response($output);
                    // }

                    $getStd = Student::find($result->student_id);
                    $bill = $getStd->bill;

                    

                    // Status ie. Success & Warning

                    if($result->del != 'yes'){
                        $sum = 0;
                        $sum = $sum + $result->inst1 + $result->inst2 + $result->inst3 + $result->inst4;
                        $bal = $bill - $sum;
                    

                        
                        if($c % 2 == 0){
                            $tr = '<tr class="rowColour"><td>'.$c++.'</td>';
                        }else{
                            $tr = '<tr><td>'.$c++.'</td>';
                        }



                        if($bill == 0 and $sum == 0){
                            $str = '<td class="feeStatus"><i class="fa fa-warning myWarning"></i></td>';
                        }else{
                            if($sum >= $bill){
                                $str = '<td class="feeStatus"><i class="fa fa-check mySuccess"></i></td>';
                              }else{
                                $str = '<td class="feeStatus"><i class="fa fa-warning myWarning"></i></td>';
                            }
                        }

                        



                        //Being

                        //$turn = 0.1;

                        // if($sum >= $bill && $bill != 0.00){
                        //     $being = '<tr><td>Being: </td><td>Full payment for '.$company->ac_year.' academic year school fees.</td></tr>';
                            // }else{

                                // if($sum == 0){}
                                // if($sum == 0 and $bill == 0){}
                                // if($sum != 0){
                                //     $being = '<tr><td>Being: </td><td>Part payment for '.$company->ac_year.' academic year school fees.</td></tr>';
                                // }

                        // }          
                        
                        
                        

                        // Payments Made

                        if ($result->inst4 > 0){
                            $i4 = '<tr><td>'.$result->updated_at.'</td><td>Gh₵ '.$result->inst4.'.00</td></tr>';
                        }else{ $i4 = ''; }
                        if ($result->inst3 > 0){
                            $i3 = '<tr><td>'.$result->updated_at.'</td><td>Gh₵ '.$result->inst3.'.00</td></tr>';
                        }else{ $i3 = ''; }
                        if ($result->inst2 > 0){
                            $i2 = '<tr><td>'.$result->updated_at.'</td><td>Gh₵ '.$result->inst2.'.00</td></tr>';
                        }else{ $i2 = ''; }
                        if ($result->inst1 > 0){
                            $i1 = '<tr><td>'.$result->updated_at.'</td><td>Gh₵ '.$result->inst1.'.00</td></tr>';
                        }else{ $i1 = ''; }   

                    
                    
                        $output.=$tr.
                        '<td>'.$getStd->std_id.'</td>
                        <td>'.$result->fullname.'</td>
                        <td>'.$result->class.'</td>
                        <td>'.$result->term.'</td>
                        <td>'.$result->year.'</td>
                        <td>'.$result->inst1.'</td>
                        <td>'.$result->inst2.'</td>
                        <td>'.$result->inst3.'</td>
                        <td>'.$result->inst4.'</td>
                        <td class="totAmt">'.$sum.'</td>
                        <td class="balAmt">'.$bal.'</td>'.
                        $str
                        .'<td class="ryt">
                        <form action="'. action("FeesController@update", ''.$result->id.'') .'" method="POST">
                            <input type="hidden" name="_method" value="PUT">
                            '.csrf_field().'
                    
                            <a href="" class="edit" data-toggle="modal" rel="tooltip" title="Edit Record" data-target="#edit_'.$result->id.'"><i class="fa fa-pencil"></i></a>
                            <button type="button" class="view2" rel="tooltip" title="View Record" data-toggle="modal" data-target="#'.$result->id.'"><i class="fa fa-folder-open"></i></button>
                            <!--textarea class="form-control" id="article-ckeditor" name="body" placeholder="Body/Text" rows="5"></textarea-->
                            <input type="hidden" class="form-control" name="mid" value="'.$result->id.'"/> 
                                            
                    
                            <div class="modal fade" id="'.$result->id.'" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                            <div class="modal-dialog modtop" role="document">
                                <div id="printarea" class="modal-content">
                                    <div class="card card-profile">
                                    <div class="card-avatar">
                                        <a href="#pablo">
                                        
                                        </a>
                                    </div>
                                    <div class="card-body">
                                        <h6 class="card-category text-gray"></h6>
                                        <div style="height: 30px">
                                        </div>
                                        <h3 class="card-title">'.$company->name.'</h3>
                                        <p class="card-category text-gray pp">'.session('company')->address.'</p>
                                        <p class="card-category text-gray pp">Contact:'.session('company')->contact.' / Email: '.session('company')->email.'</p>
                                        <h4 class="card-title">Payment Receipt</h4>
                    
                                        <a class="btn btn-info" onclick="window.print()"><p  id="print_btn" style="margin-top: 5px; color:#fff"><i class="fa fa-print"></i>&nbsp; Print</p></a>
                    
                                        <table id="reports_tbl">
                                        <tbody>
                                            <tr><td>Name: </td><td>'.$result->fullname.'</td></tr>


                                // being


                                            <tr><td>Payments Made: </td><td></td></tr>
                                            <tr><td><h5 class="card-title">Date</h5></td><td class="feeTot">Amount</td></tr>'.

                                            $i4.$i3.$i2.$i1

                                            .'<tr><td><h5 class="card-title">Total: </h5></td><td class="feeTot">Gh₵ '.$sum.'.00</td></tr>

                                            <tr><td></td><td></td></tr>
                                            <tr><td></td><td>Accountant`s Signature</td></tr>
                                            <tr><td></td><td>______________________</td></tr>
                                            </tbody>
                                        </table>
                    
                                    </div>
                                    </div>
                                </div>
                        
                            </div>
                            </div>
                    
                    
                            <div class="modal fade" id="edit_'.$result->id.'" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                            <div class="modal-dialog modtop" role="document">
                                <div class="modal-content">
                                    
                                    <div class="card card-profile">
                                    <div class="card-avatar">
                                        <a href="#pablo">
                                        <img class="img" src="/storage/std_imgs/  " />
                                        </a>
                                    </div>
                                    <div class="card-body">
                                        <h6 class="card-category text-gray">Class: '.$result->class.'  /  Term: '.$result->term.'</h6>
                                        <h4 class="card-title">'.$result->fullname.'</h4>
                    
                                        <table class="user_view_tbl">
                                        <tbody>
                    
                                            <tr class="tbl_tr"><td class="tl">Student ID</td><td class="tr">'.$getStd->std_id.'
                                            
                                            </td></tr>
                                            
                                            <tr><td class="tl">Select Installment no.</td>
                                            <td>
                                                <select id="sel_inst" name="sel_inst" class="form-control">
                                                <option value="None Selected">None Selected</option>
                                                <option value="inst1">Installment 1</option>
                                                <option value="inst2">Installment 2</option>
                                                <option value="inst3">Installment 3</option>
                                                <option value="inst4">Installment 4</option>
                                                </select>
                                            </td>
                                            </tr>
                    
                                            <tr class="tbl_tr">
                                            <td class="tl">Type Value</td>
                                            
                                            <td class="tr">
                                                <div class="form-group">
                                                <input id="instValue" type="text" name="instValue" class="form-control"/>
                                                </div>
                                            </td>
                                            </tr>
                    
                                        </tbody>
                                        </table>
                    
                                    </div>
                                    </div>
                                    
                                    <div class="modal-footer">
                                    <button type="submit" class="btn btn-info" name="sub_action" value="update_fee"><i class="fa fa-save"></i> &nbsp; Update Record</button>
                                    </div>
                    
                                </div>
                        
                            </div>
                            </div>
                            
                        </form>
                        </td>
                    
                    
                        </tr>';  
                    }        

                }

                $output.='<tr><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td><h6>Total: </h6></td><td class="totAmt"><h6>'.number_format($sum5).'.00</h6></td></tr>';

                
                return Response($output);

            }

        }
    }

    public function searchstudent(Request $request){
        if($request->ajax()){
            $output="";
            $c = 1;

            $students=DB::table('students')->where('fname','LIKE','%'.$request->search."%")->orderBy('id', 'desc')->paginate(10);
            if(count($students) == 0){
                $students=DB::table('students')->where('sname','LIKE','%'.$request->search."%")->orderBy('id', 'desc')->paginate(10);
            }
            if($students){

                $stageHold = '';
                $stages = Stage::All();
                if(count($stages) != 0){
                    foreach ($stages as $stage) {
                        # code...
                        $stageHold = $stageHold.'<option>'.$stage->cls_name.'</option>';
                    }
                }

                $fee_warn = 'Fees records will be deleted as well.. Are you sure?';

                foreach ($students as $student) {

                    $user = User::find($student->user_id);

                    if($student->del != 'yes'){

                        if ($c % 2 == 0) {
                            $tr = '<tr class="rowColour"><td>'.$c++.'</td>';
                        }else{
                            $tr = '<tr><td>'.$c++.'</td>';
                        }
                        $output.=$tr.
                        '<td>'.$student->std_id.'</td>
                        <td>'.$student->fname.' '.$student->sname.'</td>
                        <td>'.$student->sex.'</td>
                        <td>'.$student->dob.'</td>
                        <td>'.$student->class.'</td>
                        <td>'.$student->guardian.'</td>
                        <td>'.$student->contact.'</td>
                        <td>'.$student->email.'</td>
                        <td>'.$student->residence.'</td>
                        <td>'.$student->bill.'</td>
                        <td>'.$user->name.'</td>
                        <td>'.$student->created_at.'</td>
                        <td class="ryt">
                        
                          <form action="'. action("StudentController@update", ''.$student->id.'') .'" method="POST" enctype="multipart/form-data">
                            <input type="hidden" name="_method" value="PUT">
                            '.csrf_field().'

                            <a href="" class="edit" data-toggle="modal" rel="tooltip" title="Edit Record" data-target="#edit_'.$student->id.'"><i class="fa fa-pencil"></i></a>
                          
                            <button type="submit" name="sub_action" value="std_del" rel="tooltip" title="Delete Student" class="close2" onclick="return confirm('.$fee_warn.');"><i class="fa fa-close"></i></button>
                          

                            <div class="modal fade" id="edit_'.$student->id.'" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                              <div class="modal-dialog modtop" role="document">
                                <div class="modal-content">
                                    
                                    <div class="card card-profile">
                                      <div class="card-avatar">
                                        <a href="#pablo">
                                        <img class="img" src="/storage/std_imgs/'.$student->photo.'" />
                                        </a>
                                      </div>
                                      <div class="card-body">
                                        <h4 class="card-category text-gray">STUDENT ID: '.$student->std_id.'</h4>
                                        <h6 class="card-title">Created by: '.$user->name.'</h6>




                                        <table class="user_view_tbl">
                                          <tbody>

                                            <tr class="tbl_tr"><td class="tl">Firstname</td><td class="tr">
                                              <div class="form-group">
                                                <input type="text" class="form-control" name="fname" placeholder="Firstname" value="'.$student->fname.'" required/>
                                              </div>
                                            </td></tr>

                                            <tr class="tbl_tr"><td class="tl">Other names</td><td class="tr">
                                              <div class="form-group">
                                                <input type="text" class="form-control" name="sname" placeholder="Other names" value="'.$student->sname.'" required/>
                                              </div>
                                            </td></tr>

                                            <tr class="tbl_tr"><td class="tl">Birth Date</td><td class="tr">
                                              <div class="form-group">
                                                <input type="date" class="form-control" placeholder="YYYY-MM-DD" name="dob" value="'.$student->dob.'"/>
                                                </div>
                                            </td></tr>

                                            <tr class="tbl_tr"><td class="tl">Gender</td><td class="tr">
                                              <div class="form-group">
                                                <select name="sex" class="form-control" id="sex">
                                                  <option selected>'.$student->sex.'</option>
                                                  <option>Male</option>
                                                  <option>Female</option>
                                                </select>
                                              </div>
                                            </td></tr>

                                            <tr class="tbl_tr"><td class="tl">Class</td><td class="tr">
                                              <div class="form-group">
                                                <select name="std_cls" class="form-control" id="std_cls">
                                                  <option selected>'.$student->class.'</option>'.

                                                  $stageHold

                                                .'</select>
                                              </div>
                                            </td></tr>


                                            <tr class="tbl_tr"><td class="tl">Guardian`s Name</td><td class="tr">
                                              <div class="form-group">
                                                <input type="text" class="form-control" name="guardian" placeholder="Guardian`s Full Name" value="'.$student->guardian.'" required/>
                                              </div>
                                            </td></tr>


                                            <tr class="tbl_tr"><td class="tl">Contact</td><td class="tr">
                                              <div class="form-group">
                                                <input type="text" class="form-control" name="contact" placeholder="Contact No." value="'.$student->contact.'" required/>
                                              </div>
                                            </td></tr>


                                            <tr class="tbl_tr"><td class="tl">Email</td><td class="tr">
                                              <div class="form-group">
                                                <input type="email" class="form-control" name="email" placeholder="Email" value="'.$student->email.'"/>
                                              </div>
                                            </td></tr>
                                            
                                            <tr class="tbl_tr"><td class="tl">Residence</td><td class="tr">
                                              <div class="form-group">
                                                <input type="text" class="form-control" name="residence" placeholder="Place of Recidence" value="'.$student->residence.'"/>
                                              </div>
                                            </td></tr>

                                            <tr class="tbl_tr"><td class="tl">Photo</td><td class="tr">
                                              <div class="">
                                                <input type="file" name="std_img">
                                              </div>
                                            </td></tr>

                                                <input type="hidden" name="photo" value="'.$student->photo.'"/>

                                            <tr class="tbl_tr"><td class="tl">Bill</td><td class="tr">
                                              <div class="form-group">
                                                <input id="bill_total" type="text" class="form-control" name="bill_total" placeholder="Current Bill Total: Gh₵ '.$student->bill.'.00" value="'.$student->bill.'"  required/>
                                              </div>  
                                            </td></tr>

                                          </tbody>
                                        </table>


                                                         

                                      </div>
                                    </div>
                                    
                                    <div class="modal-footer">
                                      <button type="submit" class="btn btn-info" name="sub_action" value="update_student"><i class="fa fa-save"></i> &nbsp; Update Record</button>
                                    </div>

                                </div>
                          
                              </div>
                            </div>

                          </form>                  
                          
                        </td>
                      </tr>

                        ';
                    }
                }
                return Response($output);
            }
        
        }
    }

    public function studentsrecycler(Request $request){
        if($request->ajax()){
            $output="";
            $c = 1;

            $students=DB::table('students')->where('fname','LIKE','%'.$request->search."%")->orderBy('id', 'desc')->paginate(10);
            if(count($students) == 0){
                $students=DB::table('students')->where('sname','LIKE','%'.$request->search."%")->orderBy('id', 'desc')->paginate(10);
            }
            if($students){

                $stageHold = '';
                $stages = Stage::All();
                if(count($stages) != 0){
                    foreach ($stages as $stage) {
                        # code...
                        $stageHold = $stageHold.'<option>'.$stage->cls_name.'</option>';
                    }
                }

                $fee_warn = 'Fees records will be deleted as well.. Are you sure?';

                foreach ($students as $student) {

                    $user = User::find($student->user_id);

                    if($student->del == 'yes'){

                        if ($c % 2 == 0) {
                            $tr = '<tr class="rowColour"><td>'.$c++.'</td>';
                        }else{
                            $tr = '<tr><td>'.$c++.'</td>';
                        }
                        $output.=$tr.
                        '<td>'.$student->std_id.'</td>
                        <td>'.$student->fname.' '.$student->sname.'</td>
                        <td>'.$student->sex.'</td>
                        <td>'.$student->dob.'</td>
                        <td>'.$student->class.'</td>
                        <td>'.$student->guardian.'</td>
                        <td>'.$student->contact.'</td>
                        <td>'.$student->email.'</td>
                        <td>'.$student->residence.'</td>
                        <td>'.$student->bill.'</td>
                        <td>'.$user->name.'</td>
                        <td>'.$student->created_at.'</td>
                        <td class="ryt">
                        
                          <form action="'. action("StudentController@update", ''.$student->id.'') .'" method="POST" enctype="multipart/form-data">
                            <input type="hidden" name="_method" value="PUT">
                            '.csrf_field().'

                            <a href="" class="edit" data-toggle="modal" rel="tooltip" title="Edit Record" data-target="#edit_'.$student->id.'"><i class="fa fa-pencil"></i></a>
                          
                            <button type="submit" name="sub_action" value="std_del" rel="tooltip" title="Delete Student" class="close2" onclick="return confirm('.$fee_warn.');"><i class="fa fa-close"></i></button>
                          

                            <div class="modal fade" id="edit_'.$student->id.'" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                              <div class="modal-dialog modtop" role="document">
                                <div class="modal-content">
                                    
                                    <div class="card card-profile">
                                      <div class="card-avatar">
                                        <a href="#pablo">
                                        <img class="img" src="/storage/std_imgs/'.$student->photo.'" />
                                        </a>
                                      </div>
                                      <div class="card-body">
                                        <h4 class="card-category text-gray">STUDENT ID: '.$student->std_id.'</h4>
                                        <h6 class="card-title">Created by: '.$user->name.'</h6>




                                        <table class="user_view_tbl">
                                          <tbody>

                                            <tr class="tbl_tr"><td class="tl">Firstname</td><td class="tr">
                                              <div class="form-group">
                                                <input type="text" class="form-control" name="fname" placeholder="Firstname" value="'.$student->fname.'" required/>
                                              </div>
                                            </td></tr>

                                            <tr class="tbl_tr"><td class="tl">Other names</td><td class="tr">
                                              <div class="form-group">
                                                <input type="text" class="form-control" name="sname" placeholder="Other names" value="'.$student->sname.'" required/>
                                              </div>
                                            </td></tr>

                                            <tr class="tbl_tr"><td class="tl">Birth Date</td><td class="tr">
                                              <div class="form-group">
                                                <input type="date" class="form-control" placeholder="YYYY-MM-DD" name="dob" value="'.$student->dob.'"/>
                                                </div>
                                            </td></tr>

                                            <tr class="tbl_tr"><td class="tl">Gender</td><td class="tr">
                                              <div class="form-group">
                                                <select name="sex" class="form-control" id="sex">
                                                  <option selected>'.$student->sex.'</option>
                                                  <option>Male</option>
                                                  <option>Female</option>
                                                </select>
                                              </div>
                                            </td></tr>

                                            <tr class="tbl_tr"><td class="tl">Class</td><td class="tr">
                                              <div class="form-group">
                                                <select name="std_cls" class="form-control" id="std_cls">
                                                  <option selected>'.$student->class.'</option>'.

                                                  $stageHold

                                                .'</select>
                                              </div>
                                            </td></tr>


                                            <tr class="tbl_tr"><td class="tl">Guardian`s Name</td><td class="tr">
                                              <div class="form-group">
                                                <input type="text" class="form-control" name="guardian" placeholder="Guardian`s Full Name" value="'.$student->guardian.'" required/>
                                              </div>
                                            </td></tr>


                                            <tr class="tbl_tr"><td class="tl">Contact</td><td class="tr">
                                              <div class="form-group">
                                                <input type="text" class="form-control" name="contact" placeholder="Contact No." value="'.$student->contact.'" required/>
                                              </div>
                                            </td></tr>


                                            <tr class="tbl_tr"><td class="tl">Email</td><td class="tr">
                                              <div class="form-group">
                                                <input type="email" class="form-control" name="email" placeholder="Email" value="'.$student->email.'"/>
                                              </div>
                                            </td></tr>
                                            
                                            <tr class="tbl_tr"><td class="tl">Residence</td><td class="tr">
                                              <div class="form-group">
                                                <input type="text" class="form-control" name="residence" placeholder="Place of Recidence" value="'.$student->residence.'"/>
                                              </div>
                                            </td></tr>

                                            <tr class="tbl_tr"><td class="tl">Photo</td><td class="tr">
                                              <div class="">
                                                <input type="file" name="std_img">
                                              </div>
                                            </td></tr>

                                                <input type="hidden" name="photo" value="'.$student->photo.'"/>

                                            <tr class="tbl_tr"><td class="tl">Bill</td><td class="tr">
                                              <div class="form-group">
                                                <input id="bill_total" type="text" class="form-control" name="bill_total" placeholder="Current Bill Total: Gh₵ '.$student->bill.'.00" value="'.$student->bill.'"  required/>
                                              </div>  
                                            </td></tr>

                                          </tbody>
                                        </table>


                                                         

                                      </div>
                                    </div>
                                    
                                    <div class="modal-footer">
                                      <button type="submit" class="btn btn-info" name="sub_action" value="update_student"><i class="fa fa-save"></i> &nbsp; Update Record</button>
                                    </div>

                                </div>
                          
                              </div>
                            </div>

                          </form>                  
                          
                        </td>
                      </tr>

                        ';
                    }
                }
                return Response($output);
            }
     
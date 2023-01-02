



                                        <div class="modal fade" id="edit_{{$tithe->id}}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                          <div class="modal-dialog modtop" role="document">
                                            <div class="modal-content">
                                                
                                                <div class="card card-profile">
                                                  <div class="card-avatar">
                                                    <a href="#pablo">
                                                    <img class="img" src="/storage/members_imgs/{{$member->photo}}" />
                                                    </a>
                                                  </div>
                                                  <div class="card-body">
                                                    <h6 class="card-category text-gray">{{$member->department}} Department</h6>
                                                    <h4 class="card-title">{{$member->fname.' '.$member->sname}}</h4>

                                                    <table class="user_view_tbl">
                                                      <tbody>

                                                        <tr class="tbl_tr"><td class="tl">Firstname</td><td class="tr">
                                                          <input type="text" class="form-control" name="fname" placeholder="Firstname" value="{{$member->fname}}" required/> 
                                                        </td></tr>

                                                        <tr class="tbl_tr"><td class="tl">Other names</td><td class="tr">
                                                          <input type="text" class="form-control" name="sname" placeholder="Other names" value="{{$member->sname}}" required/> 
                                                        </td></tr>

                                                        <tr class="tbl_tr"><td class="tl">Ministry</td><td class="tr">
                                                          <select name="min" class="form-control" id="min" required>
                                                            <option>Adult</option>
                                                            <option>Youth</option>
                                                            <option>Children</option>
                                                          </select>
                                                        </td></tr>
                                                        
                                                        <tr class="tbl_tr"><td class="tl">Department</td><td class="tr">
                                                          <select name="dept" class="form-control" id="dept" required>
                                                            @if(count($deptms) > 0)
                                                              @foreach ($deptms as $dept)
                                                                @if($dept->del != 'yes')
                                                                  <option>{{$dept->name}}</option>
                                                                @endif
                                                              @endforeach
                                                            @endif
                                                          </select>
                                                        </td></tr>

                                                        <tr class="tbl_tr"><td class="tl">Gender</td><td class="tr">
                                                          <select name="sex" class="form-control" id="sex" required>
                                                            <option>Male</option>
                                                            <option>Female</option>
                                                          </select>  
                                                        </td></tr>
                                                        
                                                        <tr class="tbl_tr"><td class="tl">Date of Birth</td><td class="tr">
                                                          <div class="form-group">
                                                            <input type='date' name="dob" class="form-control" value="{{$member->dob}}" required/>
                                                          </div>
                                                        </td></tr>

                                                        <tr class="tbl_tr"><td class="tl">Marital Status</td><td class="tr">
                                                          <select name="mstatus" class="form-control" id="mstatus" required>
                                                            <option>Single</option>
                                                            <option>Married</option>
                                                            <option>Divorced</option>
                                                            <option>Widow/er</option>
                                                          </select>
                                                        </td></tr>
                                                        
                                                        <tr class="tbl_tr"><td class="tl">Contact</td><td class="tr">
                                                          <input type="text" class="form-control" name="contact" placeholder="Contact No." value="{{$member->contact}}" required/>
                                                        </td></tr>
                                                        
                                                        <tr class="tbl_tr"><td class="tl">Email</td><td class="tr">
                                                          <input type="text" class="form-control" name="email" placeholder="Email" value="{{$member->email}}" required/>
                                                        </td></tr>

                                                        <tr class="tbl_tr"><td class="tl">Residence</td><td class="tr">
                                                          <input type="text" class="form-control" name="residence" placeholder="Place of Recidence" value="{{$member->residence}}" required/>
                                                        </td></tr>

                                                        <tr><td class="tl">Address</td><td class="tr">
                                                          <textarea name="address" id="article-ckeditorr" class="form-control" placeholder="address" rows="2" required>{{$member->address}}</textarea>
                                                        </td></tr>

                                                      </tbody>
                                                    </table>

                                                    <!--p class="card-description">
                                                      Don't be scared of the truth because we need to restart the human foundation in truth And I love you like Kanye loves Kanye I love Rick Owens’ bed design but the back is...
                                                    </p-->
                                                  </div>
                                                </div>
                                                
                                                <div class="modal-footer">
                                                  <button type="submit" class="btn btn-primary" name="sub_action" value="update_member" onclick="return confirm('Are you sure you want to update this member record?');"><i class="fa fa-save"></i> &nbsp; Update Details</button>
                                                </div>

                                            </div>
                                      
                                          </div>
                                        </div>
@extends('pages.main-content')
@section('css')
<style>
   .apv-tbl{
   border-bottom: 2px solid #00AB8E !important;
   }
   .th-active {
   background: #444444 !important;
   color:white;
   border-bottom: 2px solid #444444 !important; */
   }
   th{
   font-size:0.80rem;
   height:5vh;
   }
   th:hover{
   background: #F7F6FB;
   }
   td{
   font-size:0.80rem;
   }
   .tr-comment
   {
   line-height:5vh;
   }
   #btm-btn
   {
    margin-bottom: 50px;
   }
</style>
@endsection
@section('content')

@if($fw)
<div class="container-fluid">
   <br>
    <div class="row ">
        <div class="col-12 col-xs-12 col-sm-12 col-md-6 col-lg-6 col-xl-6 p-0 mt-0 ">
            <input type="hidden" id="fw_number" value="{{$fw->fw_number}}">
            <table class="table table-bordered table-responsive table-fw-widget text-center apv-tbl " id="table4">
                <thead>
                    <tr>
                        <th @if($fw->fw_current_stg == "UR") class="th-active" @endif>User Request</th>
                        <th @if($fw->fw_current_stg == "DH") class="th-active" @endif>Department Head</th>
                        <th @if($fw->fw_current_stg == "ITOPS") class="th-active" @endif>IT-Operations</th>
                        <th @if($fw->fw_current_stg == "NETAD") class="th-active" @endif>Network Administrator</th>
                        <th @if($fw->fw_current_stg == "UA") class="th-active" @endif>User Acceptance</th>
                        @if($fw->fw_current_stg == "CLOSED/DONE") <th class="th-active">Closed/Done</th>  @endif
                    </tr>
                </thead>
            </table>
        </div>
        <div class="col-3 col-xs-3 col-sm-3 col-md-1 col-lg-1 col-xl-1 p-0 mt-0">
                
        </div>
            <div class="col-12 col-xs-12 col-sm-12 col-md-5 col-lg-5 col-xl-5 p-0 mt-0 text-right">
            @if($fw->fw_current_stg == "UR" && $fw->fw_user == $user)
                    <button type="button" class="btn btn-primary btn-block text-white" id="submit_fw" name="submit_fw">
                    <span class="spinner-border spinner-border-sm d-none" id="sp_submit" aria-hidden="true"></span>
                    <i class="fas fa-check" id="icon_submit"></i> SUBMIT REQUEST
                    </button>
            @endif
            @if($fw->fw_current_stg == "UA" && $fw->fw_user == $user && $fw->fw_status != "Closed" )
                    <button type="button" class="btn btn-secondary btn-block  text-white" data-bs-toggle="modal" data-bs-target="#staticBackdrop"><i class="fas fa-check"></i> Close Request</button>
            @endif
            @if($fw->fw_user == $user && $fw->fw_status != "Cancelled" &&  $fw->fw_status != "Disapproved")
                    <button type="button" class="btn btn-default btn-block  border-1 btn-outline-secondary " onclick= "location.href='{{route('fw.edit',$fw->fw_id)}}'" ><i class="fas fa-edit"></i> EDIT</button>
            @endif
                    <button type="button" class="btn btn-default btn-block  border-1 btn-outline-secondary d-none" onclick=><i class="fas fa-print "></i> PRINT</button>
                    <button type="button" class="btn btn-default btn-block  border-1 btn-outline-secondary" onclick= "location.href='{{route('fw.index')}}'" ><i class="fas fa-arrow-circle-left"></i> RETURN TO LIST</button>
                </div>
            </div>
        </div>
        @if($fw)  
            <div class="row">
                <div class="col-12 col-xs-12 col-sm-12 col-md-6 col-lg-6 col-xl-6 ">
                    <h5 class="text-truncate col-10 mt-3"> <span class="far fa-user icons"></span> <b> Requestor Details</b></h5>
                    <hr class="col-10"></hr>
                <div class="row">
                    <div class="col-10 col-xs-10 col-sm-10 col-md-10 col-lg-10 col-xl-10 ">
                        <div class="form-group">
                            <table class="table table-bordered table-responsive table-fw-widget " id="">
                                <tbody>
                                    <tr>
                                        <td>Requestor</td>
                                        <td><b>{{$fw->userbasic->user_xfirstname.' '.$fw->userbasic->user_xlastname}}</b></td>
                                    </tr>
                                    <tr>
                                        <td>Company</td>
                                        <td><b>{{$fw->fw_user_company ?? ''}}</b></td>
                                    </tr>
                                    <tr>
                                        <td>Department</td>
                                        <td><b>{{$fw->fw_user_dept ?? ''}}</b></td>
                                    </tr>
                                    <tr>
                                        <td>Date Created</td>
                                        <td><b>{{$fw->created_at ?? ''}}</b></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-xs-12 col-sm-12 col-md-6 col-lg-6 col-xl-6 ">
                <h5 class="text-truncate col-10 mt-3"><span class="far fa-building icons"></span> <b> Project Details</b></h5>
                <hr class="col-10"></hr>
                    <div class="row">
                        <div class="col-10 col-xs-10 col-sm-10 col-md-10 col-lg-10 col-xl-10 ">
                            <div class="form-group">
                                <table class="table table-bordered table-responsive table-fw-widget text-wrap " id="">
                                    <tbody>
                                        <tr>
                                            <td>Project Name</td>
                                            <td><b>{{$fw->fw_project_name ?? ''}}</b></td>
                                        </tr>
                                        <tr>
                                            <td>Request Type</td>
                                            <td><b>FIREWALL</b></td>
                                        </tr>
                                        <tr style="line-height: 30px;" class="text-justify ">
                                            <td>Remarks</td>
                                            <td class="text-muted">{{$fw->fw_remarks ?? ''}}</td>
                                        </tr>
                                        <tr>
                                            <td><b>Server Instance</b> </td>
                                            <td><b>{{$fw->fw_server_instance ?? ''}}<b></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
                <div class="row">
                    <div class="col-12 col-xs-12 col-sm-12 col-md-6 col-lg-6 col-xl-6  ">
                        <h5 class="text-truncate col-10 mt-3"> <span class="fas fa-list-ol icons"></span><b> Request Details</b></h5>
                        <hr class="col-10"></hr>
                        <div class="row">
                            <div class="col-10 col-xs-10 col-sm-10 col-md-10 col-lg-10 col-xl-10 ">
                                <div class="form-group">
                                    <table class="table table-bordered table-responsive table-fw-widget text-wrap " id="">
                                        <tbody>
                                            <tr>
                                                <td>Purpose of Request</td>
                                                <td row = "4"><b>{{$fw->fw_purpose ?? ''}}</b>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>Expected Result</td>
                                                <td row = "4"><b>{{$fw->fw_expected_result ?? ''}}<b></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>                
                </div>
                
                
            </div>
            <br>
            <div class="row">
                    <div class="col-12 col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 ">
                        <h5 class="text-truncate col-10 mt-3"> <span class="fas fa-list-ol icons"></span> <b> Source and Destination</b></h5>
                        <hr class="col-11 col-xs-11 col-sm-11"></hr>
                        <div class="row col-13 col-xs-12 col-sm-12 ">
                            <table class="table table-hover table-responsive table-fw-widget " id="tblItems">
                                <thead>
                                    <td class="  text-center border-0">
                                        <td class="col-md-2 col-3 col-xs-3 col-sm-3 border-0 text-center">Source Servername</td>
                                        <td class="col-md-1 col-2 col-xs-2 col-sm-2 border-0 text-center">IP address</td>
                                        <td class="col-md-2 col-2 col-xs-2 col-sm-2 border-0 text-center">Destination Servername</td>
                                        <td class="col-md-1 col-1 col-xs-1 col-sm-1 border-0 text-center">IP address</td>
                                        <td class="col-md-2 col-3 col-xs-3 col-sm-3 border-0 text-center">Ports</td>
                                    </thead>
                                    <tbody>
                                        @foreach($fw->fwipmany as $ip)  
                                        <tr id="{{$ip->fw_dtl_id}}">
                                            <td class="text-center">
                                                <br>
                                                <input type="hidden" name="ip_id[]" value="{{$ip->fw_dtl_id}}">
                                                <label class="form-label" id="row_no"><b>#{{$count}}</b></label></td>
                                                <td class="col-md-1">
                                                    <input type="text"  class="form-control text-center" value="{{$ip->fw_src_servername}}" readonly>
                                                </td>
                                                <td class="col-md-3">
                                                    <input type="text" class="form-control text-center" value="{{$ip->fw_src_ip}}" readonly>
                                                </td>
                                                <td class="col-md-1">
                                                        <input type="text" class="form-control text-center" value="{{$ip->fw_dest_servername}}" readonly>
                                                </td>
                                                <td class="col-md-3">
                                                    <input type="text" class="form-control text-center" value="{{$ip->fw_dest_ip}}" readonly>
                                                </td>
                                                <td class="col-md-8">
                                                <input type="text" class="form-control text-center" value="{{$ip->fw_ip_ports}}" readonly>
                                                </td>
                                            </tr>
                                                @php $count++ @endphp
                                                @endforeach
                                            </tbody>
                                        </table> 
                                    </div>
                                </div>
                            </div>
                            <br>
                            <div class="row d-none">
                                <div class="col-12 col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 ">
                                    <h5 class="text-truncate col-10 mt-3"> <span class="fas fa-paperclip icons"></span> <b> Attachments</b></h5>
                                    <hr class="col-11 col-xs-11 col-sm-11"></hr>
                                    <div class="row">
                                        <div class="col-11 col-xs-11 col-sm-11 col-md-11 col-lg-11 col-xl-11 ">
                                            <nav class="nav" style="display:inline-block">
                                                <a class="nav-link " onclick="" aria-current="page" href="#">
                                                    <div class="card border-1 btn-outline-danger border-danger">
                                                        <div class="card-body text-center"> 
                                                            <span class="bx bx-file-blank bx-lg"> </span>
                                                            <br>
                                                        </div>
                                                    </div>
                                                </a>
                                            </nav>
                                        </div>
                                    </div>
                                    <br>
                                </div>
                            </div>
                            <br>
                            
                            <div class="row">
                                <div class="col-12 col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 ">
                                    <h5 class="text-truncate col-10 mt-3"> <span class="fas fa-exchange-alt icons"></span> <b> Activity & History</b></h5>
                                    <hr class="col-11 col-xs-11 col-sm-11"></hr>
                                    <label class="form-label for_label"><b>Add Comment</b></label>
                                    <div class="row">
                                        <div class="col-12 col-xs-12 col-sm-12 col-md-6 col-lg-6 col-xl-6 ">
                                            <textarea class="form-control" id="stg_comment" rows="2"></textarea>
                                        </div>
                                        <div class="col-12 col-xs-12 col-sm-12 col-md-3 col-lg-3 col-xl-3 ">
                                  <button type="button" id="btn_comment" class="btn btn-block btn-secondary mt-1">COMMENT</button>
                            </div> 
                        </div>
                        <br>
                    <div class="row">
                    <div class="col-11 col-xs-11 col-sm-11 col-md-10 col-lg-10 col-xl-7">
                        <table class="table table-hover table-responsive table-fw-widget">
                            @foreach($curstages as $stages)
                            
                            <tr class="tr-comment" >
                                
                                @if($stages->cmstg_dateaction != "" && $stages->cmstg_stage != "REV2")
                                <td width="15%" class="bs-callout bs-callout-info td-comment">{{date('m-d-Y H:i', strtotime($stages->cmstg_dateaction))}}</td>
                                @if($stages->cmstg_desc == "Cancelled")
                                <td width="40%"><b>Firewall Request {{$stages->cmstg_desc}}</b> by Requestor ({{$stages->userbasic->user_xfirstname.' '.$stages->userbasic->user_xlastname}})</td>
                                @elseif($stages->cmstg_stage == "DH" )
                                <td width="40%"><b>Firewall Request {{$stages->cmstg_desc}}</b> by Department Head ({{$fw->userbasic->user_xfirstname.' '.$fw->userbasic->user_xlastname}})</td>
                                @elseif($stages->cmstg_stage == "ITOPS" )
                                <td width="40%"><b>Firewall Request {{$stages->cmstg_desc}}</b> by IT-Operations Head ({{$fw->userbasic->user_xfirstname.' '.$fw->userbasic->user_xlastname}})</td>
                                @elseif($stages->cmstg_stage == "NETAD" )
                                <td width="40%"><b>Firewall Request {{$stages->cmstg_desc}}</b> by Network Administrator <b>({{$stages->userbasic->user_xfirstname.' '.$stages->userbasic->user_xlastname}})</b> </td>
                                @elseif($stages->cmstg_stage == "COM" )
                                <td width="40%"><b>Firewall Request Comment </b>by ({{$stages->userbasic->user_xfirstname.' '.$stages->userbasic->user_xlastname}})</td>
                                @elseif($stages->cmstg_stage == "REV" )
                                @php 
                                $position = $stages->revision_stage; 
                                $value = $position;
                                if($position == "DH") $value = "Department Head";
                                elseif($position == "ITOPS") $value = "IT-Operations Head";
                                elseif($position == "NETAD") $value = "Network Administrator";
                                @endphp
                                <td width="40%"><b>FWR {{$stages->cmstg_desc}} </b>by {{$value}} ({{$stages->userbasic->user_xfirstname.' '.$stages->userbasic->user_xlastname}})</td>
                                @endif
                                <td width="40%"><b>Notes: </b>{{$stages->cmstg_note}}</td>
                                @elseif($stages->cmstg_stage == "REV2" )
                                <td width="15%" class="bs-callout bs-callout-warning td-comment">{{date('m-d-Y H:i', strtotime($stages->cmstg_dateaction))}}</td>
                                <td width="40%" ><b>Request for Revision </b>by ({{$stages->userbasic->user_xfirstname.' '.$stages->userbasic->user_xlastname}})</td>
                                <td width="40%"><b>Notes: </b>{{$stages->cmstg_note}}</td>
                                
                                @endif
                            </tr>
                            @endforeach
                            
                            <tr class="tr-comment" >
                                <td class="bs-callout bs-callout-dark td-comment">{{date('m-d-Y H:i', strtotime($fw->created_at))}}</td>
                                <td><b> Firewall Request Submitted - {{$fw->fw_number}} </b></td>
                                <td></td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-6 col-xs-6 col-sm-6 col-md-6 col-lg-6 col-xl-6">
                    @if($fw->fw_status != "Disapproved" && $fw->fw_status != "Cancelled")
                    @if(($fw->fw_current_stg == "DH" || $fw->fw_current_stg == "ITOPS") && $upcoming->cmstg_user == $user )
                    <button type="button" class="btn btn-success btn-block" data-bs-toggle="modal" data-bs-target="#staticBackdrop"><i class="fas fa-check"></i> APPROVE</button>
                    @elseif($fw->fw_current_stg == "NETAD" && $now->cmstg_desc  == "Acknowledged"  &&  ($user == "jvagbay" ||$user == "agprotacio" ))
                    <button type="button" class="btn btn-info btn-block  text-white" data-bs-toggle="modal" data-bs-target="#staticmarkasdone"><i class="fas fa-check"></i> MARK AS DONE</button>
                    @elseif($fw->fw_current_stg == "NETAD" &&  ($user == "jvagbay" ||$user == "agprotacio" ))
                    <button type="button" class="btn btn-primary btn-block" data-bs-toggle="modal" data-bs-target="#staticacknowledge"><i class="fas fa-check"></i> ACKNOWLEDGED REQUEST</button>
                    @endif
                    <!-- disapprove -->
                    @if(!$fw->current == "UA" && $latest_approver == $user) 
                    <button type="button" class="btn btn-danger btn-block" data-bs-toggle="modal" data-bs-target="#staticBackdropDA"><i class="fas fa-check"></i> DISAPPROVE</button>
                    @endif
                    @endif
                    <!-- revision -->
                    @if(($fw->fw_current_stg == "DH" || $fw->fw_current_stg == "NETAD") && $latest_approver == $user)
                    <button type="button" class="btn btn-warning text-light" data-bs-toggle="modal" data-bs-target="#staticBackdropRV" >FOR REVISION</button>
                    @endif
                    <!-- cancelled -->
                    @if($fw->fw_user == $user && $fw->fw_status == "Open")
                    <button type="button" class="btn btn-danger text-light" data-bs-toggle="modal" data-bs-target="#staticBackdropCancel" ><i class="fas fa-ban"></i> CANCEL REQUEST</button>
                    @endif
                </div>
            </div>
        </div>
        
        <br><br><br><br><br><br><br><br>
        <br><br><br><br><br><br><br><br>
        @endif
        <!-- Modal -->
        <div class="modal fade" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="staticBackdropLabel">Add Comment</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <textarea class="form-control" id="comment" placeholder="(Optional)"></textarea>
                        <div class="invalid-feedback">Please add comment..</div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-allcard" id="btnProceed" onclick="proceed_fw()"><span class="spinner-border spinner-border-sm d-none" id="sp_proceed" aria-hidden="true"></span>Proceed</button>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Modal Acknowledge-->
        <div class="modal fade" id="staticacknowledge" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="staticBackdropLabel">Add Comment</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <textarea class="form-control" id="comment" placeholder="(Optional)"></textarea>
                        <div class="invalid-feedback">Please add comment..</div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-allcard" id="btnProceed" onclick="Acknowledged_fw()"><span class="spinner-border spinner-border-sm d-none" id="sp_proceed" aria-hidden="true"></span>Proceed</button>
                    </div>
                </div>
            </div>
        </div>
        <!-- Modal markasdone-->
        <div class="modal fade" id="staticmarkasdone" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="staticBackdropLabel">Add Comment</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <textarea class="form-control" id="comment" placeholder="(Optional)"></textarea>
                        <div class="invalid-feedback">Please add comment..</div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-allcard" id="btnProceed" onclick="markasdone_fw()"><span class="spinner-border spinner-border-sm d-none" id="sp_proceed" aria-hidden="true"></span>Proceed</button>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Modal Cancel-->
        <div class="modal fade" id="staticBackdropCancel" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-body">
                        <br><br>
                        <center>
                            <h3 class="modal-title " id="staticBackdropLabel"><i class="far fa-question-circle fa-lg "></i></h3></center>
                            <br><br>
                            <h5 class="text-danger text-center"><b>Cancel request?</b></h5>
                            <h6 class="text-muted text-center">This action cannot be undone.</h6>
                            
                            <h6><b>Remarks</b></h6>
                            <textarea class="form-control" id="cancel_comment" placeholder="(Required)"></textarea>
                            <div class="invalid-feedback">Please add comment..</div>
                        </div>
                        <div class="modal-footer">
                            
                            <button type="button" class="btn btn-allcard " id="btnCancel" onclick="cancel_fw()">SUBMIT</button>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">CANCEL</button>
                            
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Modal Disapprove-->
            <div class="modal fade" id="staticBackdropDA" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-body">
                            <br><br>
                            <center>
                                <h3 class="modal-title " id="staticBackdropLabel"><i class="far fa-question-circle fa-lg "></i></h3></center>
                                <br><br>
                                <h5 class="text-danger text-center"><b>Disapprove request?</b></h5>
                                <h6 class="text-muted text-center">This action cannot be undone.</h6>
                                
                                <h6><b>Remarks</b></h6>
                                <textarea class="form-control" id="disapp_comment" placeholder="(Required)"></textarea>
                                <div class="invalid-feedback">Please add comment..</div>
                            </div>
                            <div class="modal-footer">
                                
                                <button type="button" class="btn btn-allcard " id="btnDisapp" onclick="disapp_fw()">SUBMIT</button>
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">CANCEL</button>
                                
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Modal Revision-->
                <div class="modal fade" id="staticBackdropRV" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-body">
                                <br><br>
                                <center>
                                    <h3 class="modal-title " id="staticBackdropLabel"><i class="fas fa-user-edit fa-lg "></i></h3></center>
                                    <br><br>
                                    <h5 class="text-warning text-center"><b>For Revision</b></h5>
                                    <h6 class="text-muted text-center">This action cannot be undone.</h6>
                                    
                                    <h6><b>Remarks</b></h6>
                                    <textarea class="form-control" id="revision_comment" placeholder="(Required)"></textarea>
                                    <div class="invalid-feedback">Please add comment..</div>
                                </div>
                                <div class="modal-footer">
                                    
                                    <button type="button" class="btn btn-allcard " id="btnRevise" onclick="revise_fw()">SUBMIT</button>
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">CANCEL</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif
@endsection
@section('scripts')
<script type="text/javascript">
var rowCount = $("#tblItems tbody>tr").length;
$(document).ready(function() {
  
    $('#btn_comment').click(function()
    {
        $(this).attr('disabled',true);
        var comment = $('#stg_comment').val();
        var user = '{{ $user }}';
        var fwnumber = '{{ $fw->fw_number ?? ""}}';

        if(comment != "")
        {
            $.ajax({
                url: "{{route('fw.comment')}}",
                type: "post",
                data:{
                comment:comment,
                user:user,
                fwnumber:fwnumber,
                },
                success: function(result){
                    
                    if(result == "success")
                    {
                        Swal.fire({
                        title: 'Adding Comment..', 
                        text: 'Your comment is successfully Added',
                        icon:'success',
                        confirmButtonText: 'OK',
                        confirmButtonColor: '#fa0031',
                        }).then((result) => {
                        if (result.value) {

                            location.reload();
                        }
                        });
                    }
                },
           
            });
        }
        else {
            Swal.fire({
                    title:'Comment is required', 
                    text:'Please add comment.',
                    icon:'warning',
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#fa0031',
                    }).then((result) => {
                    if (result.value) {

                       Swal.close();
                    }
                    });
        }
    });

    $('#submit_fw').click(function()
    {
        $('#sp_submit').removeClass('d-none');
        $('#icon_submit').addClass('d-none');


        Swal.fire({
            title: 'Proceed Firewall Request?', 
            text: 'Are you sure you want to submit this Firewall Request to your Department Head?',
            icon:'question',
            confirmButtonText: 'YES',
            confirmButtonColor: '#fa0031',
            cancelButtonColor: '#fa0031',
            showCancelButton:'true',
            cancelButtonText:'NO',  
            }).then((result) => {
            if (result.value) {
                $.ajax({
                    url: "{{route('fw.submitFW')}}",
                    type:"post",
                    data:{
                        fw_number:$('#fw_number').val(),

                    },
                    success: function(result){
                        
                        if(result == "submitted")
                        {
                            Swal.fire({
                            title: 'Success',
                            text: 'Your Firewall Request has been submitted to your Department Head',
                            icon: 'success',
                            confirmButtonText: 'View Request',
                            confirmButtonColor: '#fa0031',
                            }).then((result) => {
                                if (result.value) {
                                    location.reload();
                                }
                            });
                        }
                    }
                });     
            }
            else 
            {
                
            }
            });

        $(this).prop('disabled',true);
    });
});

function updated_at(action)
{
    
    $.ajax({
            url: "{{route('fw.cancel')}}",
            type:"post",
            data:{
                pr_number:$('#fw_number').val(),
                comment:$('#comment').val(),
                user:'{{auth()->user()->status_xuser}}',

            },
            success: function(result){

            }
        });

}

function cancel_fw()
{
    var comment = $('#cancel_comment').val();
    if(comment != "")
    {
        $('#btnCancel').prop('disabled',true);
        $('#cancel_comment').removeClass('is-invalid');
        $('#cancel_comment').prop('disabled',true);

        $.ajax({
            url: "{{route('fw.cancel')}}",
            type:"post",
            data:{
                fw_number:$('#fw_number').val(),
                comment:$('#cancel_comment').val(),
                user:'{{auth()->user()->status_xuser}}',

            },
            success: function(result){

                if(result == "cancelled")
               {
                    Swal.fire({
                    title: 'Firewall Request Cancelled', 
                    text: '#'+$('#fw_number').val()+' has been cancelled.',
                    iconHtml: '<i class="fas fa-trash-alt fa-sm"></i>',
                    confirmButtonText: 'View Request',
                    confirmButtonColor: '#fa0031',
                    }).then((result) => {
                    if (result.value) {

                        location.reload();
                    }
                    });
               }
            }
            
        });
    }
    else 
    {
        $('#cancel_comment').addClass('is-invalid');

    }
      
}

function proceed_fw()
{
    var comment = $('#comment').val();

    $('#sp_proceed').removeClass('d-none');
    $('#btnProceed').prop('disabled',true);
    $('#comment').removeClass('is-invalid');
    $('#comment').prop('disabled',true);

    $.ajax({
        url: "{{route('fw.status')}}",
        type:"post",
        data:{
            fw_number:$('#fw_number').val(),
            comment:$('#comment').val(),
            user:'{{auth()->user()->status_xuser}}',

        },
        success: function(result){
           $('#sp_proceed').removeClass('d-none');
           if(result == "approved")
           alert(result);
           {
                Swal.fire({
                title: 'Firewall Request Approved', 
                text: '#'+$('#fw_number').val()+' has been marked approved.',
                icon:'success',
                confirmButtonText: 'View Request',
                confirmButtonColor: '#fa0031',
                }).then((result) => {
                if (result.value) {

                    location.reload();
                }
                });
           }
        },
            error:function(result)
            {
                alert(result);
            }
        });


}
function disapp_fw()
{
    var comment = $('#disapp_comment').val();
    if(comment != "")
    {
        $('#btnDisapp').prop('disabled',true);
        $('#disapp_comment').removeClass('is-invalid');
        $('#disapp_comment').prop('disabled',true);

        $.ajax({
            url: "{{route('fw.disapprove')}}",
            type:"post",
            data:{
                fw_number:$('#fw_number').val(),
                comment:$('#disapp_comment').val(),
                user:'{{auth()->user()->status_xuser}}',
            },
            success: function(result){
                if(result == "disapproved")
               {
                    Swal.fire({
                    title: 'Firewall Request Disapproved', 
                    text: '#'+$('#fw_number').val()+' has been disapproved.',
                    iconHtml: '<i class="far fa-times-circle fa-sm"></i>',
                    confirmButtonText: 'BACK TO LIST',
                    confirmButtonColor: '#fa0031',
                    }).then((result) => {
                    if (result.value) {

                        location.reload();
                    }
                    });
               }
            },
            error:function(result)
            {
                alert(result);
            }
        });
    }
    else 
    {
        ('#disapp_comment').addClass('is-invalid');
    }
       
}
function revise_fw()
{
    var comment = $('#revision_comment').val();
    if(comment != "")
    {
        $('#btnRevise').prop('disabled',true);
        $('#revision_comment').removeClass('is-invalid');
        $('#revision_comment').prop('disabled',true);

        $.ajax({
            url: "{{route('fw.revise')}}",
            type:"post",
            data:{
                fw_number:$('#fw_number').val(),
                comment:$('#revision_comment').val(),
                user:'{{auth()->user()->status_xuser}}',

            },
            success: function(result){

                if(result == "revision")
               {
                    Swal.fire({
                    title: 'Firewall Request', 
                    text: '#'+$('#fw_number').val()+' has been set for revision.',
                    iconHtml: '<i class="fas fa-check text-warning fa-sm"></i>',
                    confirmButtonText: 'View Request',
                    confirmButtonColor: '#fa0031',
                    }).then((result) => {
                    if (result.value) {

                        location.reload();
                    }
                    });
               }
            },
            error:function(result)
            {
                alert(result);
            }
        });
    }
    else 
    {
        ('#revision_comment').addClass('is-invalid');
    }
       
}
function Acknowledged_fw()
{
    var comment = $('#comment').val();
    var user = '{{ $user }}';
   
        $('#sp_proceed').removeClass('d-none');
        $('#btnProceed').prop('disabled',true);
        $('#comment').removeClass('is-invalid');
        $('#comment').prop('disabled',true);

        $.ajax({
            url: "{{route('fw.acknowledged')}}",
            type:"post",
            data:{
                fw_number:$('#fw_number').val(),
                comment:$('#comment').val(),
                user:'{{auth()->user()->status_xuser}}',

            },
            success: function(result){
               $('#sp_proceed').removeClass('d-none');
               if(result == "Acknowledged")
               {
                    Swal.fire({
                    title: 'Firewall Request Acknowledged', 
                    text: '#'+$('#fw_number').val()+' has been Acknowledged.',
                    icon:'success',
                    confirmButtonText: 'View Request',
                    confirmButtonColor: '#fa0031',
                    }).then((result) => {
                    if (result.value) {

                        location.reload();
                    }
                    });
               }
            },
        });
   
}

function markasdone_fw()
{
    var comment = $('#comment').val();
    var user = '{{ $user }}';
   
        $('#sp_proceed').removeClass('d-none');
        $('#btnProceed').prop('disabled',true);
        $('#comment').removeClass('is-invalid');
        $('#comment').prop('disabled',true);

        $.ajax({
            url: "{{route('fw.markasdone')}}",
            type:"post",
            data:{
                fw_number:$('#fw_number').val(),
                comment:$('#comment').val(),
                user:user,

            },
            success: function(result){
               $('#sp_proceed').removeClass('d-none');
               if(result == "Mark As done")
               alert(result);
               {
                    Swal.fire({
                    title: 'Firewall Request ', 
                    text: '#'+$('#fw_number').val()+' Mark as Done.',
                    icon:'success',
                    confirmButtonText: 'View Request',
                    confirmButtonColor: '#fa0031',
                    }).then((result) => {
                    if (result.value) {

                        location.reload();
                    }
                    });
               }
            },
            error:function(result)
            {
                alert(result);
            }
        });
   
}





</script>
@endsection
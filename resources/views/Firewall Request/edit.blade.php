@extends('pages.main-content')
@section('css')
<style>
  

</style>
@include('layouts.datatables-css')
@endsection
@section('content')
<div class="container-fluid">
   <br>
   <form method="POST" action="{{route('fw.update',['fw' => $fw->fw_number])}}" enctype="multipart/form-data">
   @method('put')
   @csrf
   <div class="row">
        <div class="col-12 col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-11">
            <h5 class="text-truncate col-10 mt-3"><span class="far fa-building icons"></span> <b> Client / Project Details</b></h5>
            <hr class="col-10"></hr>
                <div class="row">
                    <div class="col-5 col-xs-5 col-sm-5 col-md-5 col-lg-5 col-xl-5 ">
                        <div class="form-group">    
                            <label for="exampleFormControlSelect1" class="form-label text-truncate col-10"><b>Client/Project Name</b></label>
                                <input type="text" class="form-control @error('xproject') is-invalid @endif" name="xproject" value="{{$fw->fw_project_name}}"> 
                                
                                @error('xproject') <div class="invalid-feedback">Required*</div> @endif
                        </div>
                    </div>
                    <div class="col-5 col-xs-3 col-sm-3 col-md-3 col-lg-3 col-xl-3 ">
                        <div class="form-group">    
                            <label for="exampleFormControlSelect1"  class="form-label"><b>Request Type</b></label>
                            <input type="text" class="form-control read_only" name="xtype" value="FIREWALL" readonly>
                        </div>
                    </div>  
                    <div class="col-12 col-xs-12 col-sm-12 col-md-3 col-lg-3 col-xl-3 ">
                        
                    </div>      
                </div>
            </div>
        </div>
    <div class="row">
            <div class="col-12 col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-11">
            <h5 class="text-truncate col-10 mt-3"><span class="far fa-building icons"></span> <b>Request Description</b></h5>
            <hr class="col-10"></hr>
                <div class="row">
                    <div class="col-5 col-xs-5 col-sm-5 col-md-5 col-lg-5 col-xl-5 ">
                        <div class="form-group">    
                            <label for="exampleFormControlSelect1" class="form-label text-truncate col-10"><b>Purpose of Request</b></label>
                            <textarea class="form-control @error('xpurpose') is-invalid @endif" id="xpurpose" name="xpurpose" rows="2" value="{{old('xpurpose')}}">{{$fw->fw_purpose}}</textarea>
                            @error('xpurpose') <div class="invalid-feedback">Required*</div> @endif
                        </div>
                    </div>
                    <div class="col-5 col-xs-5 col-sm-5 col-md-3 col-lg-5 col-x10">
                        <div class="form-group">    
                            <label for="exampleFormControlSelect1" class="form-label text-truncate col-10"><b>Expected Result</b></label>
                            <textarea class="form-control @error('xresult') is-invalid @endif " id="xresult" name= "xresult" rows="2" value="{{old('xresult')}}"> {{$fw->fw_expected_result}}</textarea>
                            @error('xresult') <div class="invalid-feedback">Required*</div> @endif
                        </div>
                    </div>  
                    <div class="col-12 col-xs-12 col-sm-12 col-md-3 col-lg-3 col-xl-3 ">
                </div>
                <div class="form-group">
                        <div class="col-12 col-xs-12 col-sm-12 col-md-12 col-lg-2 col-xl-3 form-group p-1">
                            <label for="exampleFormControlSelect1"  class="form-label "><b>Server Instance</b></label>
                            <select name="xinstance" id="" class="form-select for_label @error('xinstance') is-invalid @endif" value="{{$fw->fw_server_instance}}">
                            <option disabled selected>--Select--</option>
                                @if($serverinstance)
                                @foreach($serverinstance as $instance)
                                <option value='{{$instance->Server_instance}}' @if($fw->fw_server_instance == $instance->Server_instance) selected @endif> {{$instance->Server_instance}}</option>
                                @endforeach
                                @endif
                            </select>
                            @error('$serverinstance') 
                            <div class="invalid-feedback">Server Instance is required</div>
                            @endif
                            </select>
                        </div>
                    </div>
                </div>    
            </div>
        <br>
        <div class="row">
           <div class="col-12 col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 ">
                <h5 class="text-truncate col-10 mt-3"> <span class="fas fa-list-ol icons"></span> <b>IP Description</b></h5>
                <hr class="col-11 col-xs-11 col-sm-11"></hr>
                <div class="tbl_div row col-11 col-xs-11 col-sm-11 ">
                    <div class="row col-2 col-xs-2 col-sm-2 col-md-3">
                        <a href="#" class="p-2" id="addrow"><span class="fas fa-plus"></span>Add Source / Destination</a>
                    </div>  
                    <table class="table table-hover table-responsive table-fw-widget " id="tblItems" >
                        <thead>
                            <td class="  text-center border-0">
                            <td class="col-md-2 col-3 col-xs-3 col-sm-3 border-0 text-center">Source Servername</td>
                            <td class="col-md-1 col-2 col-xs-2 col-sm-2 border-0 text-center">IP address</td>
                            <td class="col-md-2 col-2 col-xs-2 col-sm-2 border-0 text-center">Destination Servername</td>
                            <td class="col-md-1 col-1 col-xs-1 col-sm-1 border-0 text-center">IP address</td>
                            <td class="col-md-2 col-3 col-xs-3 col-sm-3 border-0 text-center">Ports</td>
                        </thead>
                        <tbody>
                            @if($ipdet)
                                @foreach($ipdet as $ip)
                                <tr>
                                    <td class="col-md-1  text-center">
                                    <input type="hidden" name="ipdet[]" value="{{$ip->ip_id}}">
                                        <label class="form-label" id="row_no"><b>#{{$count}}</b></label>
                                    <td class="col-md-2">
                                        <!-- <label class="form-label">Item</label> -->
                                        <input type="text" name="source_name[]" class="form-control" value="{{$ip->fw_src_servername}}" >
                                    </td>
                                    <td class="col-md-2">
                                        <!-- <label class="form-label">Quantity</label> -->
                                        <input type="text" name="sourceip[]" class="form-control text-center"  value="{{$ip->fw_src_ip}}" maxlength="15" placeholder="xxx.xxx.xxx.xxx" pattern="^((\d{1,2}|1\d\d|2[0-4]\d|25[0-5])\.){3}(\d{1,2}|1\d\d|2[0-4]\d|25[0-5])$">
                                    </td>
                                    <td class="col-md-2">
                                        <!-- <label class="form-label">Unit of Measurement</label> -->
                                        <input type="name" name="destname[]" class="form-control" value="{{$ip->fw_dest_servername}}">
                                    </td>
                                    <td class="col-md-2">
                                        <!-- <label class="form-label">Description</label> -->
                                        <input type="text" name="destip[]" class="form-control text-center"  value="{{$ip->fw_dest_ip}}" maxlength="15"  placeholder="xxx.xxx.xxx.xxx" pattern="^((\d{1,2}|1\d\d|2[0-4]\d|25[0-5])\.){3}(\d{1,2}|1\d\d|2[0-4]\d|25[0-5])$">
                                    </td>
                                    <td class="col-md-2">
                                        <input type="name" name="ports[]" class="form-control" value="{{$ip->fw_ip_ports}}">
                                    </td>
                                    <td class="col-md-2">
                                        <center><span class="fas fa-trash-alt fa-lg mt-2 icons removerow"></span></center>
                                    </td>
                                </tr>
                                @php $count++; @endphp
                                @endforeach
                            @endif
                            </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="row">
           <div class="col-12 col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 ">
                <h5 class="text-truncate col-10 mt-3"> <span class="fas fa-paperclip icons"></span> <b> Additional Details</b></h5>
                <hr class="col-11 col-xs-11 col-sm-11"></hr>
                <div class="row d-none">
                    <div class="col-11 col-xs-11 col-sm-11 col-md-11 col-lg-11 col-xl-11 ">
                        <div class="form-group">
                            <label for="input-6b" class="form-label mb-2"><b>Attach Files </b></label>
                            <div class="file-loading"> 
                                <input id="input-b6" name="filename[]" type="file" accept=".jpg,.jpeg,.png,.pdf,.docx,.xls,.xlsx,.pptx" multiple>
                            </div>
                        </div>
                        <small class="text-muted p-2">(Maximum individual file size is 10MB)</small>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-11 col-xs-11 col-sm-11 col-md-11 col-lg-11 col-xl-11 ">
                        <div class="form-group">
                            <label class="form-label"><B>Remarks</B></label>
                            <textarea class="form-control @error('xremarks') is-invalid @endif" id="exampleFormControlTextarea1" name="xremarks" rows="3">{{$fw->fw_remarks}}</textarea>
                            @error('xremarks') <div class="invalid-feedback">Remarks is required</div> @endif
                        </div>
                    </div>
                </div>
                <br>
                <div class="row">
                    <div class="col-6 col-xs-6 col-sm-6 col-md-3 col-lg-3 col-xl-2 ">
                        <div class="form-group">
                            <label class="form-label"><b>Date Needed</b></label>
                            <input type="date" class="form-control @error('xdatecr') is-invalid @endif"  id="xdatecr" name="xdatecr" value="{{$fw->fw_date_requested}}">
                            @error('xdatecr') <div class="invalid-feedback">Date Needed is required</div> @endif
                        </div>
                    </div>
                </div>
                <br>
                <div class="row">
                    <div class="col-6 col-xs-6 col-sm-6 col-md-3 col-lg-3 col-xl-2 ">
                        <div class="form-group">
                          <button type="submit" class="btn btn-allcard btn-block col-10">Submit</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <br><br><br>
</form>

<div class="modal fade" id="modal_docs" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-top modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="staticBackdropLabel">Attached Files</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="row "> 
            <div class="col-12">
                <div class=" container">
                    <table class="table w-100 table-hover table-responsive table-fw-widget " id="table_files">
                    <thead>
                    <td>Files</td>
                    <td></td>
                   
                        
                    
                    </thead>
                    <tbody>
                    </tbody>
                    </table>
            </div>
        </div>
       </div>
      </div>
      <div class="modal-footer">
       
      </div>
    </div>
  </div>
</div>
@endsection
@section('scripts')
@include('layouts.datatables-scripts')
<script type="text/javascript">
  
  var rowCount = $("#tblItems tbody>tr").length;
    $(document).ready(function() {
        $("#input-b6").fileinput({
        showUpload: false,
        dropZoneEnabled: false,
        maxFileCount: 15,
        inputGroupClass: "input-group-sm",
         });
         $('#table_files').DataTable({
            columnDefs: [
            { width: 100, targets: 0 }
        ],

         });
         loadDatatable();


    });

    $('.type').change(function()
    {
       
    });
    $('#addrow').click(function()
    {
        rowCount += 1;
        $("#tblItems tbody>tr:first").clone(true).insertAfter("#tblItems tbody>tr:last");
        $("#tblItems tbody>tr:last").attr("id", "tr"+rowCount);
        $("#tblItems tbody>tr:last #row_no").text("#"+rowCount);
        $("#tblItems tbody>tr:last #row_no").css('font-weight','bold');
        $("#tblItems tbody>tr:last :input").val("");
    });
    $('.removerow').click(function()
    {
        var id = $(this).closest('tr').attr('id')
        $('table#tblItems tr#'+id).remove();
        rowCount -=1;
    });

    function loadDatatable()
        {
            @php
            $columns = array(['data' => 'details','sortable' => false],['data' => 'remove','sortable' => false]);
            $based_url =  url('/');
            @endphp
          
            
            var url =  "{!!route('fw.uploadfiles',['id' => $fw->fw_number ])!!}";
          


            
            load_datables('#table_files',url,{!! json_encode($columns) !!},null);
        }

    function remove_files(id)
    {

         $.ajax({
            url: "{{route('fw.removefile')}}",
            type:"post",
            data:{
                id:id

            },
            success: function(result){
                Swal.fire({
                    title: 'Attached File', 
                    text: 'File has been removed.',
                    icon:'success',
                    confirmButtonText: 'Close',
                    confirmButtonColor: '#fa0031',
                    }).then((result) => {
                    if (result.value) {
                        $('#modal').modal('toggle');
                        loadDatatable();
                    }
                    });
            },
            error:function(result)
            {
               console.log(result);
            }
        });
    }

  var todayDate = new Date();
      var month = todayDate.getMonth() + 0; 
      var year = todayDate.getUTCFullYear() - 0; 
      var tdate = todayDate.getDate(); 
      if(month < 10){
        month = "0" + month 
      }
      if(tdate < 10){
        tdate = "0" + tdate;
      }
      var maxDate = year + "-" + month + "-" + tdate;
      document.getElementById("datecr").setAttribute("min", maxDate);
     
      $(document).ready(function() {

        $("#input-b6").fileinput({
        showUpload: false,
        dropZoneEnabled: false,
        maxFileCount: 15,
        maxFileSize:10000,
        inputGroupClass: "input-group-sm"
        });
    });
      
</script>
@endsection
@extends('pages.main-content')
@section('css')
@include('layouts.datatables-css')
@endsection
@section('content')
<div class="container-fluid">
    <br>
    <form>
        <div class="row">
            <div class="col-12">
                <div class="form-group">
                    <div class="row">
                        <div class="col-xs-3 col-sm-3 col-md-3 col-lg-1">
                                <label class="form-label"data-bs-toggle="modal" data-bs-target="#modal_filter" >
                                  
                                </label>
                        </div>  
                        <div class="col-xs-1 col-sm-1 col-md-1 col-lg-1">
                        </div>  
                        
                        <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="1"  id="checkAll">
                                <label class="form-check-label" for="flexCheckDefault">
                                    Show my requests
                                </label>
                            </div>
                            
                        </div>
                        <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
                        @if($dept != null )
                            <div class="form-check">
                                <input class="form-check-input " type="checkbox" value="1" id="open">
                                <label class="form-check-label" for="flexCheckDefault">
                                    Network Administrator Open Tickets
                                </label>
                            </div>
                            @endif
                        </div>
                        <div class="col-xs-2 col-sm-2 col-md-2 col-lg-3">
                        @if($dept != null && $user != 'agprotacio' && $user != 'jvagbay') 
                            <div class="form-check">
                                <input class="form-check-input " type="checkbox" value="1"  checked id="assigned">
                                <label class="form-check-label" for="flexCheckDefault">
                                    Assigned to me
                                </label>
                            </div>
                        @endif
                        </div>
                        <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3 ">
                            <button type="button" class="btn btn-block btn-allcard col-12" onclick="location.href='{{route('fw.create')}}'"><span class="fas fa-plus fa-sm"> </span> Create Firewall Request</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
            <div class="row "> 
                <div class="col-12">
                    <div class="card-body">
                        <table class="table table-hover table-responsive table-fw-widget " id="table4" style="overflow-x: auto">
                            <thead>
                            
                                <td> <b>Request No #</b></td>
                                <td><b>Requestor</b></td>
                                <td><b>Project name</b></td> 
                                <td><b>Department</b></td>
                                <td><b>Stage</b></td>
                                <td><b>Status</b></td>
                            </tr>
                            </thead>
                            <tbody>
                              
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="col-1"></div>
            </div> 
    </form>
    <br><br><br><br><br>
</div>


@endsection
@section('scripts')
@include('layouts.datatables-scripts')
    <script type="text/javascript">
       
        $(document).ready(function(){
            $('#header-toggle').click();
            if($('#assigned').is(':checked')) $('#assigned').change();
            else loadDatatable();
          
        });
        
        $('#table4 tbody').on('click', 'tr', function() {
            var url = '{{route("fw.show",["fw" => ":firenum"])}}';
            url = url.replace(':firenum',$(this).closest('tr').attr('id'));
            

            location.href = url;
             
        });
        $('#btn_filter').click(function()
        {
            var dpt = $('#xdepartment').val();
            var stats = $('#xstatus').val();
           
           
             loadDatatable(dpt,stats);
            $("#modal_filter .btn-close").click();
        });
        $('#checkAll').change(function()
        {
            filter_tickmark();
        });
        $('#assigned').change(function()
        {
            filter_tickmark();
        });
        $('#open').change(function()
        {
            filter_tickmark();
        });
        function filter_tickmark()
        {
            var showreq = "";
            if($('#checkAll').is(':checked')) showreq="1";
            else showreq="none";
            var assigned = "";
            if($('#assigned').is(':checked')) assigned="1";
            else assigned="none";
            var open = "";
            if($('#open').is(':checked')) open="1";
            else open="none";
            

            loadDatatable("none","none",showreq,assigned,open);
        }

        function loadDatatable(dpt = "none",stats = "none",showreq = "none",assigned = "none",open = "none")
        {
            @php
            $columns = array(['data' => 'fw_number','searchable'=>true]
            ,['data' => 'client']
            ,['data' => 'fw_project_name']
            ,['data' => 'fw_user_dept']
            ,['data' => 'fw_current_stg']
            ,['data' => 'fw_status']);
            $based_url =  url('/');
            @endphp
          
            var url =  "{!!route('fw.datatables',['id' => auth()->user()->status_xuser,'dept'=> ':dpt','status'=>':stats','myreq' => ':myreq','assigned' => ':assigned','open' => ':open'])!!}";
            url = url.replace(':dpt',dpt != null ? dpt : "none");
            url = url.replace(':stats',stats != null ? stats : "none");
            url = url.replace(':myreq',showreq != null ? showreq : "none");
            url = url.replace(':assigned',assigned != null ? assigned : "none");
            url = url.replace(':open',open != null ? open : "none");
            
            load_datables('#table4',url,{!! json_encode($columns) !!},null);
        } 
    </script>
@endsection
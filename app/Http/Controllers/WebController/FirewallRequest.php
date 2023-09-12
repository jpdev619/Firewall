<?php

namespace App\Http\Controllers\WebController;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SysOrgDept;
use App\Models\Company;
use App\Models\Department;
use App\Models\UserEmployment;
use App\Models\UserBasic;
use App\Models\ChangeManagement;
use App\Models\ChangeManagementType;
use App\Models\ChangeManagementUploads;
use App\Models\ChangeManagementStages;
use App\Models\FirewallServerInstance;
use App\Models\FirewallRequest;
use App\Models\FirewallComment;
use App\Models\FirewallDetail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Auth;
use DB;

class CMFireWallController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $department = Department::orderBy('orgdep_xname')->get();
        $user = auth()->user()->status_xuser;
        

        $status = array(
            ['name' => 'For Approval'],
            ['name' => 'Approved'],
            ['name' => 'Rejected'],
            ['name' => 'Closed'],
            ['name' => 'Done'],
            ['name' => 'Open']);
        
        $dept = SysOrgDept::where('orgdep_xdephead',$user)->first();
        session(['message'=>'Firewall Request','icon'=>'bi bi-bezier2','icon-code'=>'']);

        return view ('pages.change_management.cm_firewall.index', compact('user','department','status','dept'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        
        $company = Company::all();
        $department = Department::orderBy('orgdep_xname')->get();
        $serverinstance = FirewallServerInstance::orderby('Server_Instance','asc')->get();
        session(['message'=>'Request Firewall','icon'=>'bx bx-cart-alt','icon-code'=>'']);
        return view('pages.change_management.cm_firewall.create',compact(['company','department','serverinstance']));
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        
        $request->validate([
           'xproject' => 'required',
            'xpurpose' => 'required',
            'xresult' => 'required',
            'xremarks' => 'required',
            'xresult' => 'required',
            'datecr' => 'required'
        ]);
        try {
            $userEmployment = UserEmployment::where('emp_xuser',Auth::user()->status_xuser)->first();
            $stages = "UR,DH,ITOPS,NETAD";
            
            $firewall = new FirewallRequest;
            $firewall->fw_number          =self::getFirewallNumber();
            $firewall->fw_user            =Auth::user()->status_xuser;
            $firewall->fw_purpose         =$request->xpurpose;
            $firewall->fw_project_name    =$request->xproject;
            $firewall->fw_user_company    =$userEmployment->emp_xcompany;
            $firewall->fw_user_dept       =$userEmployment->emp_xdepartment;
            $firewall->fw_stage           =$stages;
            $firewall->fw_current_stg     ="UR";
            $firewall->fw_status          ="For Approval";
            $firewall->fw_expected_result =$request->xresult;
            $firewall->fw_server_instance =$request->xinstance;
            $firewall->fw_remarks         =$request->xremarks;
            $firewall->fw_date_requested  =$request->datecr;
            $firewall->save();
  
            $dh_email = "";
            $stgray = explode(',', $stages);
            $stagecount = count($stgray);
            $count = 0;
            while($count<$stagecount) {
                $cmstg_stage = $stgray[$count];
                switch($cmstg_stage){
                    case "UR":
                        $cmstg_user = Auth::user()->status_xuser;
                        break;
                    case "DH":
                        $cmstg_user = self::getdephead($userEmployment->emp_xdepartment);
                        $dh_email = $cmstg_user;
                        break;
                    case "ITOPS":
                        $cmstg_user = self::getdephead('IT-Operations');
                        break;    
                    case "NETAD":
                        $cmstg_user = self::getdephead('Network Administrator');
                        break;
                }
                $cmstg = new ChangeManagementStages;
                $cmstg->cmstg_cmnumber     = $firewall->fw_number;
                $cmstg->cmstg_req_type     = $request->xtype;
                $cmstg->cmstg_stage        = $cmstg_stage;
                $cmstg->cmstg_user         = $cmstg_user;
                $cmstg->save();
                $count++;   
            }
            $ipcount = 0; 
            while ($ipcount < count($request->source_name)) {
                $source = new FirewallDetail;
                $source->fw_dtl_id           =self::IPNumber($firewall->fw_number);
                $source->fw_number           =$firewall->fw_number;
                $source->fw_src_servername   =$request->source_name[$ipcount];
                $source->fw_dest_servername  =$request->destname[$ipcount];
                $source->fw_src_ip           =$request->sourceip[$ipcount];
                $source->fw_dest_ip          =$request->destip[$ipcount];
                $source->fw_ip_ports         =$request->ports[$ipcount];
                $source->save();
                $ipcount++;
            }

            if($request->hasFile('filename'))
            {
                $path = storage_path('FirewallRequest/'.$firewall->fw_number);
                if(!File::exists($path)) {
                    Storage::disk('fw_upload')->makeDirectory($firewall->fw_number);
                }
                $uploadcount = 0;
                while ($uploadcount < count($request->filename)) {
                    $filename = $request->filename[$uploadcount];
                    $fwupload = new ChangeManagementUploads;
                    $fwupload->fw_number      =$firewall->fw_number;
                    $fwupload->filename       =$filename->getClientOriginalName();
                    $fwupload->save();
                    Storage::disk('fw_upload')->put( '/'.$firewall->fw_number.'/'.$filename->getClientOriginalName(),File::get($filename));
                    $uploadcount++;
                }
            }
                alert()->success('Request submitted',$firewall->fw_number.' has been submitted for review and approval')
                ->showConfirmButton(
                    $btnText = '<a class="add-padding" href="'.route('fw.show',$firewall->fw_number).'">View Request</a>', // here is class for link
                    $btnColor = '#fa0031',
                    ['className'  => 'no-padding'], 
                )->autoClose(false);
                return redirect()->back();

            }
        catch (\Throwable $th) 
        {
            alert()->error($th->getMessage())->showConfirmButton('Return', '#fa0031');
            return redirect()->back();
        }
    }
    public static function getFirewallNumber() {
       
        $fw = FirewallRequest::orderBy('fw_id','desc')->first();

        $last_ticket = $fw->fw_number;
		$tick_date	= substr($last_ticket, 0, 10);
		$tick_date_now = date('Y-m-d');
		$tick_pre 	= "-FW-";
		$tick_start = "000";
		$tick_num	= substr($last_ticket, 14, 17);
		
		if ($tick_date == $tick_date_now) { 
			$tick_d1 = $tick_date; 
			$tick_d2 = $tick_pre;
			$tick_dx = $tick_num + 1;
			$tick_d3 = str_pad($tick_dx, 3, '0', STR_PAD_LEFT);
		} else { 
			$tick_d1 = $tick_date_now;
		    $tick_d2 = $tick_pre;
			$tick_d3 = $tick_start;
		}
		 return $tick_dt = $tick_d1.$tick_d2.$tick_d3;
    }

    public static function IPNumber($fw_number) {
       
        $ipnum = FirewallDetail::orderBy('ip_id','desc')->first();
      
        $last_ticket = $ipnum->fw_dtl_id;
		$ip_number	= substr($last_ticket, 0, 17); 
		$tick_date_now = date('Y-m-d');
		$tick_pre 	= "-";
		$tick_start = "00";
		$item_num	= substr($last_ticket, 18, 20);
		
		if ($fw_number == $ip_number) { 

			$tick_d2 = $tick_pre;
			$tick_dx = $item_num + 1;
			$tick_d3 = str_pad($tick_dx, 2, '0', STR_PAD_LEFT);

		} else { 
			$tick_d1 = $tick_date_now;
		    $tick_d2 = $tick_pre;
			$tick_d3 = $tick_start;
		}
		return $tick_dt = $fw_number.$tick_pre.$tick_d3;
    }

    public static function getdephead($department){
        $depthead = SysOrgDept::where('orgdep_xname',$department)->first();
        return $depthead->orgdep_xdephead;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $fw = FirewallRequest::with('fwipmany','userbasic')->where('fw_number',$id)->first();
        $fwcomment = FirewallComment::with('userbasic')
        ->selectRaw('ifnull(approval_dateaction,created_at),comment_user_id,ifnull(revision_desc,""),comment,if(revision = "1","REV",if(revision = "2","REV2","COM")),revision_stage')
        ->where('fw_number',$id);
        $user = auth()->user()->status_xuser;

        $curstages = ChangeManagementStages::with('userbasic')
        ->selectRaw('cmstg_dateaction,cmstg_user,cmstg_desc,cmstg_note,cmstg_stage,"" as revise_stage')
        ->where('cmstg_cmnumber',$id)->union($fwcomment)->orderby('cmstg_dateaction','desc')->get();
        

        $now = ChangeManagementStages::where('cmstg_cmnumber',$id)->whereNotnull('cmstg_desc')->orderBy('cmstg_id','desc')->first();
        $upcoming = ChangeManagementStages::where('cmstg_cmnumber',$id)->wherenull('cmstg_desc')->first();
        $approval =  $now->cmstg_desc ?? '';
        $latest_approver = $approval == "Acknowledged" ? $now->cmstg_user  : $upcoming->cmstg_user ?? '';
        //dd($fw->userbasic);
        $user = auth()->user()->status_xuser;
        $count = 1;
        session(['message'=>'Request Firewall','icon'=>'bi bi-bezier2 nav_icon  ','icon-code'=>'']);

        return view('pages.change_management.cm_firewall.show', compact(['fw','curstages','now','upcoming','approval','latest_approver','user','count','fwcomment']));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        
        $company = Company::all();
        $department = Department::orderBy('orgdep_xname')->get();
        $fw = FirewallRequest::find($id);
        $count = 1;
        $ipdet = FirewallDetail::where('fw_number',$fw->fw_number)->get();
        $serverinstance = FirewallServerInstance::orderby('Server_Instance','asc')->get();
        $fileuploads = ChangeManagementUploads::where('fw_number',$fw->fw_number)->get();
        $userEmployment = UserEmployment::where('emp_xuser',Auth::user()->status_xuser)->first();
        session(['message'=>'Request Firewall','icon'=>'bx bx-cart-alt','icon-code'=>'']);
        return view('pages.change_management.cm_firewall.edit',compact(['company','fw','department','serverinstance','fileuploads','userEmployment','ipdet','count']));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $fwnumber)
    {
        
        try {
            $fw = FirewallRequest::where('fw_number',$fwnumber)->first();
            $fw->fw_project_name            =$request->xproject;
            $fw->fw_purpose                 =$request->xpurpose;
            $fw->fw_expected_result         =$request->xresult;
            $fw->fw_server_instance         =$request->xinstance;
            $fw->fw_remarks                 =$request->xremarks;
            $fw->fw_date_requested          =$request->xdatecr;
            $fw->update();

            $ipcount = 0;
            foreach($request->ipdet as $val) {
                if($val != null)
                {
                    $source = FirewallDetail::where('ip_id',$val)->first();
                    $source->fw_src_servername   = $request->source_name[$ipcount];
                    $source->fw_dest_servername  = $request->destname[$ipcount];
                    $source->fw_src_ip           = $request->sourceip[$ipcount];
                    $source->fw_dest_ip          = $request->destip[$ipcount];
                    $source->fw_ip_ports         = $request->ports[$ipcount];
                    $source->save();
                    
                }
                else 
                {
                    $ipnew = new FirewallDetail;
                    $ipnew->fw_dtl_id          = self::IPNumber($fw->fw_number);
                    $ipnew->fw_number          = $fw->fw_number;
                    $ipnew->fw_src_servername  = $request->source_name[$ipcount];
                    $ipnew->fw_dest_servername = $request->destname[$ipcount];
                    $ipnew->fw_src_ip          = $request->sourceip[$ipcount];
                    $ipnew->fw_dest_ip         = $request->destip[$ipcount];
                    $ipnew->fw_ip_ports         =$request->ports[$ipcount];
                    $ipnew->save();
                }
                $ipcount++;
            }

            if($request->hasFile('filename'))
            {
                $path = storage_path('FirewallRequest/'.$fw->fw_number);
                if(!File::exists($path)) {
                    Storage::disk('fw_upload')->makeDirectory($fw->fw_number);
                }
                $uploadcount = 0;
                while ($uploadcount < count($request->filename)) {
                    $filename = $request->filename[$uploadcount];
                    $fwupload = new ChangeManagementUploads;
                    $fwupload->cm_number      =$fw->fw_number;
                    $fwupload->filename       =$filename->getClientOriginalName();
                    $fwupload->save();
                    Storage::disk('fw_upload')->put( '/'.$fw->fw_number.'/'.$filename->getClientOriginalName(),File::get($filename));
                    $uploadcount++;
                }
            }

            alert()->success('Firewall Request Updated',$fw->fw_number.' has been updated.')
            ->showConfirmButton(
                $btnText = '<a class="add-padding" href="'.route('fw.show',$fw->fw_number).'">View Request</a>', // here is class for link
                $btnColor = '#fa0031',
                ['className'  => 'no-padding'], 
            )->autoClose(false);
            return redirect()->back();
        } 
        catch (\Throwable $th) {
        //throw $th;
            alert()->error($th->getMessage())->showConfirmButton('Return', '#fa0031');
            return redirect()->back();
        }
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}

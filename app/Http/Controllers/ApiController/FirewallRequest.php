<?php

namespace App\Http\Controllers\ApiController;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\UserEmployment;
use DB;
use App\Models\UserBasic;
use App\Models\FirewallRequest;
use App\Models\FirewallComment;
use App\Models\SysOrgDept;
use App\Models\ChangeManagementStages;
use App\Models\ChangeManagementUploads;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class CMFireWallController extends Controller
{
    //
    public function fw_datas(){

        //$user = auth()->user()->status_xuser;
        $data = DB::table('firewall_request')
        ->leftJoin('user_basic','user_basic.user_xusern','firewall_request.fw_user','user_employment','user_employment.emp_xuser')
        ->selectRaw('firewall_request.fw_number,firewall_request.fw_user,fw_status,fw_current_stg,fw_user_dept,fw_project_name, CONCAT(user_xfirstname," ",user_xlastname) as client');
            
        return $data->groupBy('firewall_request.fw_number')->orderBy('firewall_request.fw_date_requested','desc')->get();

    }

    public function fw_datatable($username,$department = "none",$status = "none",$myreq = "none",$assigned = "none",$open = "none")
    {
       

        try{
            $data = array();
            $data = $this->get_fw($username,$department,$status,$myreq,$assigned,$open);
            $fw = $this->fw_datas();
            $datatables = Datatables::of($data)
            ->addColumn('fw_number',function($data)
            {
                $column = '<a class="tableData" href="'.route('fw.index',$data->fw_number).'"><b>'.$data->fw_number.'</b></a>';
                return $column;
            })
            
            ->addColumn('client',function($data)
            {
                $column = '<a class="tableData"><b>'.$data->client.'</b></a>';
                return $column;
            })

            ->addColumn('fw_current_stg',function($data)
            {
                $column = "";
        
                if($data->fw_current_stg == "DH") $column = $column.'Department Head';
                else if($data->fw_current_stg == "ITOPS") $column = $column.'IT-Operations Head';
                else if($data->fw_current_stg == "NETAD")$column = $column .'Network Administrator';
                else if($data->fw_current_stg == "UA") $column = $column.'User Acceptance';
                else if($data->fw_current_stg == "CLOSED/DONE") $column = $column.'Closed/Done';
                else if($data->fw_current_stg == "UR") $column = $column.'User Request';

                $column = $column.'</label>';
                return $column;
            })
            
            ->setRowId(function($data){
                return $data->fw_number;
            })

            ->addColumn('fw_status',function($data)
            {
            
                $column = "";
                if($data->fw_status =="Open") $column = '<label class="text-success">';
                else if($data->fw_status == 'For Approval') $column = '<label class="text-info">';
                else if($data->fw_status == 'Approved') $column = '<label class="text-secondary">';
                else if($data->fw_status == 'Disapproved') $column = '<label class="text-danger">';
                else if($data->fw_status == 'Closed/Done') $column = '<label class="text-dark">';
                else if($data->fw_status == 'Revision') $column = '<label class="text-warning">';
                else if($data->fw_status == 'Done') $column = '<label class="text-primary">';


                else  $column = '<label class="text-danger text-truncate">';

                if($data->fw_status == "Open") $column = $column.'<b> Open</b></label>';
                else if($data->fw_status == "For Approval") $column = $column.'<b>'.$data->fw_status.'</b></label>';
                else if($data->fw_status == "Approved")  $column = $column.'<b> Approved </b></label>'; 
                else if($data->fw_status == "Disapproved")  $column = $column.'<b> Disapproved </b></label>';
                else if($data->fw_status == "Closed")  $column = $column.'<b>Closed</b></label>';
                else if($data->fw_status == "Done")  $column = $column.'<b>Done</b></label>';
                else $column = $column.'<b> '.$data->fw_status.'</b></label>';
               

                return $column;
            })

            ->setRowId(function($data){
                return $data->fw_number;
            });
            

            $rawColumns = ['fw_number','fw_user_dept','fw_project_name','client','fw_status','fw_stage','fw_current_stg'];
        }
        catch(Exception $e)
        {
            return response(['errors' => $e->getMessage()], 201);
        }
        return $datatables->rawColumns($rawColumns)->make(true);
        
    }
    public static function submitFW(Request $request)
    {
        try {
            //code...
            $fw = FirewallRequest::where('fw_number',$request->fw_number)->first();
            $fwUpdate = FirewallRequest::find($fw->fw_id);
            $fwUpdate->fw_current_stg     = "DH";
            $fwUpdate->fw_status          = "Open";
            $fwUpdate->update();
            $depthead = self::getdephead($fw->fw_user_dept);
            
            $cmstg = ChangeManagementStages::where('cmstg_cmnumber',$request->fw_number)
            ->wherenull('cmstg_dateaction')->first();
            $cmstg->cmstg_dateaction   = Carbon::now()->toDateTimeString(); 
            $cmstg->cmstg_desc         = 'Submitted';
            $cmstg->update();
            
            
            //self::email_notif($depthead,$request->fw_number);
            
            return "submitted";
        } catch (\Throwable $th) {
            //throw $th;
            alert()->error($th->getMessage())->showConfirmButton('Return', '#fa0031');
            return $th->getMessage();
        }
      
    }

    public static function getfullname ($username = ""){
        $val = UserBasic::where('user_xusern',$username)->first();
        $firstname =$val->user_xfirstname ?? '';
        $lastname = $val->user_xlastname ?? '';
        $name = $firstname.' '.$lastname ;
        return $name;
    }


    public static function getdephead($department){
        $depthead = SysOrgDept::where('orgdep_xname',$department)->first();
        return $depthead->orgdep_xdephead;
    }
    public static function getdept($username){
        $dept = UserEmployment::where('emp_xuser',$username)->first();
        return $dept->emp_xdepartment;
    }
    public static function adding_comment(Request $request){
        
        {
            try {
                $user = $request->user;
                $comment = $request->comment;
                $fw_number = $request->fwnumber;


                $fwc = new FirewallComment;
                $fwc->fw_number             =           $fw_number;
                $fwc->comment               =           $comment;
                $fwc->comment_user_id       =           $user;
                $fwc->save();
                return "success";  
            } 
            catch (\Throwable $th) {
                alert()->error($th->getMessage())->showConfirmButton('Return', '#fa0031');
                //dd($request);
                return  alert()->error($th->getMessage())->showConfirmButton('Return', '#fa0031');
            }
           
        }
    }
    public static function cancelStatusFW(Request $request)
    {
        try {
            //code...
            $fw = FirewallRequest::where('fw_number',$request->fw_number)->first();
            $fwUpdate = FirewallRequest::find($fw->fw_id);
            $fwUpdate->updated_at      = Carbon::now()->toDateTimeString(); 
            $fwUpdate->fw_status       = 'Cancelled';
            $fwUpdate->save();
            
            $cmstg = ChangeManagementStages::where('cmstg_cmnumber',$request->fw_number)
            ->wherenull('cmstg_dateaction')->first();
            $cmstg->cmstg_dateaction   = Carbon::now()->toDateTimeString(); 
            $cmstg->cmstg_desc         = 'Cancelled';
            $cmstg->cmstg_note         = $request->comment;


            $cmstg->save();

            return "cancelled";
        } catch (\Throwable $th) {
            //throw $th;
            alert()->error($th->getMessage())->showConfirmButton('Return', '#fa0031');
            return $th->getMessage();
        }
      
    }
public function StatusFW(Request $request)
    {
        $isAcknowledge = false;
        $isClosed = false;
        $fw_number = $request->fw_number;
        $comment = $request->comment;
        $fw = FirewallRequest::where('fw_number',$fw_number)->first();
        $stages = $fw->fw_stage;
        $array = explode(',',$stages);
        $count = count($array);
        $current_stage = $fw->fw_current_stg;
        $fw_stage = ChangeManagementStages::where('cmstg_cmnumber',$fw_number)
        ->where('cmstg_stage',$current_stage)->first();
        if(($count == 4 || $count == 3) && $current_stage == "DH")
        {
            $updated_current = 'ITOPS';    

        }
        else if(($count == 4 ||$count == 3) && $current_stage == "ITOPS")
        {
            $updated_current = 'NETAD';    
        }
        else if(($count == 3 || $count == 2) && $current_stage == "DH")
        {
            $updated_current = 'NETAD';    
        }
        else if($current_stage == "NETAD" && $fw_current_stg->cmstg_desc == "Acknowledged")
        {
            $updated_current = 'UA';    
        } 
        else if( $current_stage == "NETAD")
        {
            $updated_current = 'NETAD';    
            $isAcknowledge = true;
        }
        else if($current_stage == "UA")
        {
            $isClosed = true;
            $updated_current = 'CLOSED/DONE';    
        }
        try {

            $fwUpdate = FirewallRequest::find($fw->fw_id);
            $fwUpdate->updated_at         = Carbon::now()->toDateTimeString(); 
            $fwUpdate->fw_current_stg     = $updated_current;
            if($isClosed)
            {
                $fwUpdate->fw_status    = 'Done';
                $fwUpdate->fw_uacomment = $comment;
                $fwUpdate->fw_uadate    = Carbon::now()->toDateTimeString(); 
            }
            $fwUpdate->save();
            
            $stageUpdate = ChangeManagementStages::find($cmstg_stage->cmstg_id);
            
                $stageUpdate->cmstg_dateaction     = Carbon::now()->toDateTimeString(); 
                $stageUpdate->cmstg_desc           = $isAcknowledge ? 'acknowledged':'Approved'; 
                $stageUpdate->cmstg_note           = $comment; 
                $stageUpdate->cmstg_user           = $request->user; 
                $stageUpdate->save();
            
            //email notification
        
            //if == Closed/Acknowledged/Delivered
            if($isClosed) {
                return "closed";
            }  
            else {
               
                

                // try {
                //       // email notif
                //     if($updated_current == "ITOPS"){
                //         self::email_notif(self::getdephead('IT-Operations'),$pr->pr_number);
                //         if($current_stage == "DH"){
                //             self::email_stgnotif($pr->pr_user,$pr->pr_number,'Department Head',$comment,$current_stage);
                //         }
                //         if($current_stage == "ITOPS"){
                //             self::email_stgnotif($pr->pr_user,$pr->pr_number,'IT-Operations Head',$comment,$current_stage);
                //         }
                //     }
                //     else if($updated_current == "PURCH"){
                //         self::email_notif('atko',$pr->pr_number);
                //         self::email_notif('wschua',$pr->pr_number);
                //         self::email_notif('jpramos',$pr->pr_number);
        
                //         if($current_stage == "DH"){
                //             self::email_stgnotif($pr->pr_user,$pr->pr_number,'Department Head',$comment,$current_stage);
                //         }
                //         else if($current_stage == "ITOPS") {
                //             self::email_stgnotif($pr->pr_user,$pr->pr_number,'IT-Operations Head',$comment,$current_stage);
                //         }
                //         // }
                //         else if($current_stage == "PURCH") {
                //        // else if($current_stage == "PRES") {
                //         //     self::email_stgnotif($pr->pr_user,$pr->pr_number,'President',$comment,$current_stage);
                //              self::email_stgnotif($pr->pr_user,$pr->pr_number,'Purchasing Department',$comment,$current_stage);
                //         }
                //     }
                // } catch (\Throwable $th) {
                //     info($th);
                // }
                $rt_val = "approved";
                if($isAcknowledge) $rt_val = "acknowledged";
                return $rt_val;
            }
            
         
        } 
        catch (\Throwable $th) 
        {
            alert()->error($th->getMessage())->showConfirmButton('Return', '#fa0031');
            return $th->getMessage();
        }
       
    }

    public static function disappprovefw(Request $request)
    {
        try {
            //code...
            $fw = FirewallRequest::where('fw_number',$request->fw_number)->first();
            $fwUpdate = FirewallRequest::find($fw->fw_id);
            $fwUpdate->updated_at      = Carbon::now()->toDateTimeString(); 
            $fwUpdate->fw_status       = 'Disapproved';
            $fwUpdate->save();
            
            $cmstg = ChangeManagementStages::where('cmstg_cmnumber',$request->fw_number)
            ->wherenull('cmstg_dateaction')->first();
            $cmstg->cmstg_dateaction   = Carbon::now()->toDateTimeString();
            $cmstg->cmstg_desc         = 'Disapproved';
            $cmstg->cmstg_note         = $request->comment;


            $cmstg->save();

            return "disapproved";
        } catch (\Throwable $th) {
            //throw $th;
            alert()->error($th->getMessage())->showConfirmButton('Return', '#fa0031');
            return $th->getMessage();
        }
      
    }
    public static function reviseFW(Request $request)
    {
        try {
            //code...
            $fw = FirewallRequest::where('fw_number',$request->fw_number)->first();
            $fwUpdate = FirewallRequest::find($fw->fw_id);
            $fwUpdate->updated_at        = Carbon::now()->toDateTimeString(); 
            $fwUpdate->fw_status         = 'Revision';
            $fwUpdate->fw_current_stg    = 'UR';
            $fwUpdate->save();
            
            $stgCopy = ChangeManagementStages::where('cmstg_cmnumber',$request->fw_number)
            ->wherenotnull('cmstg_dateaction')
            ->get();
            foreach($stgCopy as $val)
            {
                $stg = new FirewallComment;
                $stg->fw_number                  = $val->cmstg_cmnumber;
                $stg->comment                    = $val->cmstg_note;
                $stg->comment_user_id            = $val->cmstg_user;
                $stg->revision_stage             = $val->cmstg_stage;
                $stg->revision_desc              = $val->cmstg_desc;
                $stg->approval_dateaction        = $val->cmstg_dateaction;
                $stg->revision                   = "1";

                $stg->save();
            }
            $add_revise = new FirewallComment;
            $add_revise->fw_number               = $request->fw_number;
            $add_revise->comment                 = $request->comment;
            $add_revise->comment_user_id         = $request->user;
            $add_revise->revision                = "2";
            $add_revise->save();

            foreach($stgCopy as $stg)
            {
                $stg->cmstg_dateaction   = null; 
                $stg->cmstg_desc         = null;
                $stg->cmstg_note         = null;
                $stg->save();
            }
         

            return "revision";
        } catch (\Throwable $th) {
            //throw $th;
            alert()->error($th->getMessage())->showConfirmButton('Return', '#fa0031');
            return $th->getMessage();
        }
      
    }
    public static function acknowledgedFW(Request $request)
    {
        try {
                //code...
                $fw = FirewallRequest::where('fw_number',$request->fw_number)->first();
                $fwUpdate = FirewallRequest::find($fw->fw_id);
                $fwUpdate->updated_at      = Carbon::now()->toDateTimeString(); 
                $fwUpdate->fw_status       = 'Acknowledged';
                $fwUpdate->save();
                
                $user = $request->user;
                $cmstg = ChangeManagementStages::where('cmstg_cmnumber',$request->fw_number)
                ->Where('cmstg_stage' , 'NETAD')->first();
                    $cmstg->cmstg_user         = $user;
                    $cmstg->cmstg_dateaction   = Carbon::now()->toDateTimeString();
                    $cmstg->cmstg_desc         = 'Acknowledged';
                    $cmstg->cmstg_note         = $request->comment;
                    $cmstg->save();
                
                
    
                return "Acknowledged";
            } catch (\Throwable $th) {
                //throw $th;
                alert()->error($th->getMessage())->showConfirmButton('Return', '#fa0031');
                return $th->getMessage();
            }
      
    }
    
    public static function markasdoneFW(Request $request)
    {
        try {
            //code...
            $fw = FirewallRequest::where('fw_number',$request->fw_number)->first();
            $fwUpdate = FirewallRequest::find($fw->fw_id);
            $fwUpdate->updated_at      = Carbon::now()->toDateTimeString(); 
            $fwUpdate->fw_status       = 'Mark as Done';
            $fwUpdate->fw_current_stg  = 'UA';
            $fwUpdate->save();
            
            // $user = $request->user;
            // $cmstg = ChangeManagementStages::where('cmstg_cmnumber',$request->fw_number)
            // ->Where('cmstg_stage' , 'NETAD')->first();
            // $cmstg->cmstg_user         = $user;
            // $cmstg->cmstg_dateaction   = Carbon::now()->toDateTimeString();
            // $cmstg->cmstg_desc         = 'Mark as Done';
            // $cmstg->cmstg_note         = $request->comment;

            $user = $request->user;
            $cmstg = new ChangeManagementStages;
            $cmstg->cmstg_cmnumber     = $request->fw_number;
            $cmstg->cmstg_user         = $user;
            $cmstg->cmstg_stage        = 'NETAD';
            $cmstg->cmstg_req_type     = 'FIREWALL';
            $cmstg->cmstg_dateaction   = Carbon::now()->toDateTimeString();
            $cmstg->cmstg_desc         = 'Mark as Done';
            $cmstg->cmstg_note         = $request->comment;


            $cmstg->save();

            return "Mark as Done";
        } catch (\Throwable $th) {
            //throw $th;
            alert()->error($th->getMessage())->showConfirmButton('Return', '#fa0031');
            return $th->getMessage();
        }
      
    }
    public function fw_uploads($id)
    {
        try
        {  
            $data = array();
            $data = ChangeManagementUploads::where('fw_number',$id)->get();
            info($id);
            $datatables = Datatables::of($data)
            ->addColumn('details',function($data)
            {
                $url = asset("storage/FirewallRequest/".$data->fw_number."/".$data->filename);
                $column = '<a href="'.$url.'" target="_blank">'.$data->filename.'</a>';
                return $column;
            })
            ->addColumn('remove',function($data)
            {
                $column = '<a href="#" class="text-danger text-right" onclick="remove_files('.$data->id.')"><i class="fas fa-trash-alt"></i> Remove</a>';
                return $column;
            });
          
          

            $rawColumns = ['details','remove'];

        }
        catch(Exception $e)
        {
            return response(['errors' => $e->getMessage()], 201);
        }
       
        return $datatables->rawColumns($rawColumns)->make(true);
    }
    public static function fw_remove_files(Request $request)
    {
        $data = ChangeManagementUploads::find($request->id)->delete();
    }
    public static function get_fw($username,$department,$status,$myreq,$assigned,$open)
    {
        //info($username.'-'.$department.'-'.$status.'-'.$myreq.'-'.$assigned.'-'.$open);
         $getdepartment = self::getdept($username);
         $get  = DB::table('change_management_stages')->selectRaw('cmstg_desc, cmstg_cmnumber,cmstg_stage,cmstg_user')->wherenull('cmstg_dateaction')->orWhere('cmstg_desc','Acknowledged')->groupby('cmstg_cmnumber');
         $get1 =  DB::table('change_management_stages')->selectRaw(' COUNT(if(cmstg_dateaction IS NOT NULL and cmstg_desc != "Acknowledged",cmstg_id,null))+1 as occur,COUNT(cmstg_id)+1 as total_occur,MAX(DATE_FORMAT(cmstg_dateaction,"%Y-%m-%d")) received_since,cmstg_cmnumber as cmstages')->groupBy('cmstg_cmnumber');
         $get2 = DB::table('firewall_request')->selectRaw("fw_number as fwnum,
            if(date(created_at) = date(NOW()),
                    IF(TIMESTAMPDIFF(HOUR,created_at,now()) > 0,CONCAT(TIMESTAMPDIFF(HOUR,created_at,NOW()) ,'Hr(s) Ago'),CONCAT(TIMESTAMPDIFF(MINUTE,created_at,NOW()),' Min(s) Ago')),
                if(MONTH(created_at) = MONTH(NOW()) and YEAR(created_at) = YEAR(NOW()) AND DAY(created_at) != DAY(NOW()),CONCAT(DATEDIFF(NOW(),created_at),' Day(s) Ago'),
                    if(MONTH(created_at) != MONTH(NOW()) AND YEAR(created_at) = YEAR(NOW()),CONCAT(MONTH(NOW()) -MONTH(created_at),' Month(s) Ago'),
                        if( YEAR(created_at) != YEAR(NOW()),CONCAT(YEAR(NOW()) - YEAR(created_at) ,' Year(s) Ago')
                    ,'')))) AS time_interval
            ");
        
        $data = DB::table('firewall_request as a')->leftJoin('user_basic','user_basic.user_xusern','a.fw_user')
        ->selectRaw('dh_approve,a.fw_number,a.fw_current_stg,fw_user_dept,if(fw_project_name ="","N/A",fw_project_name) as fw_project_name,time_interval,cmstg_user,CONCAT(user_xfirstname," ",user_xlastname) as client
                    ,if(cmstg_desc ="Acknowledged" and fw_status ="Open","Acknowledged",fw_status) fw_status, occur , total_occur')  
        ->joinSub($get,'b',function($join)
        {
            $join->on('b.cmstg_cmnumber','=','a.fw_number');
        })

        ->joinSub($get1,'d',function($join)
        {
            $join->on('d.cmstages','=','a.fw_number');
        })
        ->joinSub($get2,'e',function($join)
        {
            $join->on('e.fwnum','=','a.fw_number');
        })
        ->leftjoin(DB::raw('(select cmstg_dateaction as dh_approve,cmstg_cmnumber from change_management_stages where cmstg_stage = "DH") as f'),'f.cmstg_cmnumber','a.fw_number')
        ->where(function ($query) use ($username,$getdepartment) {
                 $query->where('fw_user_dept', $getdepartment)
                       ->orWhere('cmstg_user', $username);

                if($username == "agprotacio" || $username=="jvagbay")
                {
                    $query->orWhere('fw_current_stg','NETAD');
                }
        })
        ->where('created_at','>=','2021-01-01'); //cutoff (jan 1, 2021)
         if($department!="none") $data->where('fw_dhead',$department);
        if($status !="none")
        {
            if($status == "Acknowledged") $data->where('cmstg_desc',$status);
            else if($status == "All") $data->wherenotnull('fw_status');
            else $data->where('fw_status',$status);
        }
        if($myreq =="1") $data->where('fw_user',$username);
        if($assigned == "1" ) $data->where('cmstg_user',$username)->where('fw_status','Open');
        if($open == "1") $data->where('fw_current_stg','NETAD')->where('fw_status', 'Open')->orWhere('fw_status','Acknowledged');
  
        $data->orderByRaw('if(dh_approve != "",dh_approve,a.fw_number) desc')->groupBy('a.fw_number');
        info($data->toSql());
        return $data->get();
    }
    public static function email_notif($for_email,$fw_number){
        $forUser = UserEmail::where('email_xuser',$for_email)->first();
        if($for_email == "atko") $send_email = [$forUser->email_xemail,'wschua@acccorp.com.ph','jpramos@avls.com.ph'];
        else $send_email = $forUser->email_xemail;
        $email = new FW($fw_number);
        Mail::to($send_email)
        ->send($email);
    }
    public static function email_stgnotif($for_email,$pr_number,$approver,$comment,$current){
        $forUser = UserEmail::where('email_xuser',$for_email)->first();
        // $to_email = "joelmarano1@gmail.com";
        $email = new FWStage($fw_number,$approver,$comment,$current);
        Mail::to($forUser->email_xemail)
        ->send($email);
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ApiModel;
use App\Models\OtpModel;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class ApiController extends Controller
{
    public function user_signup(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'mobile' => 'required|unique:users|max:10',
            'email' => 'required|unique:users',
            'password' => 'required',
            'email_otp' => 'required',
            'mobile_otp' => 'required',
           
           
        ]);
        $data =array();
        $code = 422;
        if ($validator->fails()) 
        {
            $data['message'] = $validator->errors(); 
            $data['status']=false;
            //$code = 422;  
        }
        else
        {
          
        // $flight = ApiModel::create([
        //     'name' => $request->name,
        //     'mobile'=>$request->mobile,
        //     'email'=>$request->email,
        //     'password'=>md5($request->password),
        //     'original_password'=>$request->password,
        //     'status'=>1,
        // ]);
        // $response =  $flight->save();
        $flight = DB::table('users')->insert([
            'name' => $request->name,
            'mobile'=>$request->mobile,
            'email'=>$request->email,
            'password'=>md5($request->password),
            'original_password'=>$request->password,
            'status'=>1,
        ]);
        $response =  $flight;
    //print_r($flight);
        $otp_data = OtpModel::create([
           
            'email_otp'=>$request->email_otp,
            'mobile_otp'=>$request->mobile_otp,
            'email'=>$request->email,
            'mobile'=>$request->mobile,
            'status'=>1,
        ]);
        $response = $otp_data->save();
       
        if($response):
            $data['message'] = "Register Successfully";
            $data['status']=true;
          
           // $code = 200;
        else:
            $data['message'] = "Something Went Wrong";
            $data['status']=false;
           // $code = 500;   
        endif; 
    }
return response()->json($data,$code);
    }
        public function user_signin(Request $request)
        {
            $validator = Validator::make($request->all(),[
                'user_id' =>'required',
                'password' => 'required',
            ]);   
            $data =array();
            $code = 422;  
            if ($validator->fails()) 
            {
            $data['message'] = $validator->errors(); 
            $data['status']=false;
            //$code = 422;  
            }
        else
        {
        $user_id=$request->user_id;
            // DB::enableQueryLog();
        $user_data = DB::table('users')->select('id','name','mobile','email','status','password','original_password')->where('email',$user_id)->where('status',1)->orWhere('mobile',$user_id);
       // dd(DB::getQueryLog());
   // print_r($user_data);exit;
        if($user_data->count()>0)
        {
          $row = $user_data->first();
          if($row->password == md5($request->password))
            {
                $data['message'] = "Login Successfully! ";
                $data['status']=true;
                $data['data']=$row ;
            }
            else
            {
                $data['message'] = "User Not Found!";
                $data['status']=false;
                $data['data']=array() ;
            }
        }
        else
        {
            $data['message'] = "User Not Found!";
            $data['status']=false;
            $data['data']=array() ;
        }
        }
        return response()->json($data,$code); 
        }
        public function send_otp(Request $request)
        {
            $validator=Validator::make($request->all(),[
                'type'=>'required'
            ]);
            $type=$request->type;
            $data=array();
            if($type=='email')
            {
            $validator=Validator::make($request->all(),['email'=>'required']);  
            }
            if($type=='mobile')
            {
            $validator=Validator::make($request->all(),['mobile'=>'required']);  
            }
            if ($validator->fails()) 
            {
            $data['message'] = $validator->errors(); 
            $data['status'] = false;
            //$code = 422;  
            }
            else
            {
                if($type=='email')
                {
                    $email=$request->email;
                    $user_data=DB::table('users')->where('email',$email)->count();
            
                    if($user_data>0)
                    {
                        $check_otp=DB::table('tbl_otp')->where('email',$email)->count();
                        $otp=rand(1000,9999);
                    if($check_otp>0)
                    {
                        
                        //DB::enableQueryLog();
                        $q2 = DB::table('tbl_otp')->where('email','=',$request->email)->update(['email_otp' => $otp]);
                          // dd(DB::getQueryLog());
                    //DB::table('tbl_otp')->where('email',$email)->update('',$otp);
                       // print_r($q2);exit;
                    }
                    else 
                    {
                        $otp_data = OtpModel::create([
                            'email_otp'=>$request->otp,
                            'email'=>$request->email,
                            'status'=>1,
                        ]);
                        $q2 = $otp_data->save();
                    }
                    if($q2)
                   {
                     $data['message']='Otp Send Successfully';
                    $data['status']=true;
                    $data['otp']=$otp; 
                   }
                   else
                   {
                $data['status']=false;
                $data['message']='Invalid Email Id';
                $data['otp']=array();    
                   }
                    }
                    else
                {
                $data['status']=false;
                $data['message']='Invalid Email Id';
                $data['otp']=array();  
                }
             
                   
                }
                // else
                // {
                // $data['status']=false;
                // $data['message']='Invalid Email Id';
                // $data['otp']=array();  
                // }
             
                 else
                {
                    $mobile=$request->mobile;
                    $user_data=DB::table('users')->where('mobile',$mobile)->count();
            
                    if($user_data>0)
                    {
            
                        $check_otp=DB::table('tbl_otp')->where('mobile',$mobile)->count();
                        $otp=rand(1000,9999);
                    if($check_otp>0)
                
                    {
                        
              
                        //DB::enableQueryLog();
                        $q2 = DB::table('tbl_otp')->where('mobile','=',$request->mobile)->update(['mobile_otp' => $otp]);
                          // dd(DB::getQueryLog());
                    //DB::table('tbl_otp')->where('email',$email)->update('',$otp);
                       // print_r($q2);exit;
                    }
                    else 
                    {
                        $otp_data = OtpModel::create([
                            'mobile_otp'=>$request->otp,
                            'mobile'=>$request->mobile,
                            'status'=>1,
                        ]);
                        $q2 = $otp_data->save();
                    }
                    if($q2)
                   {
                     $data['message']='Otp Send Successfully';
                    $data['status']=true;
                    $data['otp']=$otp; 
                   }
                   else
                   {
                $data['status']=false;
                $data['message']='Invalid Mobile Number';
                $data['otp']=array();    
                   }
                    }
                 else
                {
                $data['status']=false;
                $data['message']='Invalid Mobile Number';
                $data['otp']=array();  
                }
                   
                }
                // else
                // {
                // $data['status']=false;
                // $data['message']='Invalid Mobile Number';
                // $data['otp']=array();  
                // }
               
            }
            return response()->json($data); 
        }
        public function verify_otp(Request $request)
        {
        $validator=Validator::make($request->all(),[
        'mobile'=>'required',
        'otp'=>'required',
        ]);
        $data=array();
        if ($validator->fails()) 
        {
        $data['message'] = $validator->errors(); 
        $data['status']=false;
        //$code = 422;  
        }
        else
        {
            $mobile=$request->mobile;
            $otp=$request->otp;
            $user_otp_data=DB::table('tbl_otp')->where('mobile',$mobile);
        if($user_otp_data->count()>0)
        {
            $user_data_otp=$user_otp_data->first();
            if($user_data_otp->mobile_otp== $otp)
            {
            $user_data=DB::table('users')->select('id as user_id')->where('mobile',$mobile)->first();
          
            $data['status']=true;
            $data['message']='Otp Verify Successfully';
            $data['data']=$user_data; 
            }
            else
            {
          
                $data['status']=false;
                $data['message']='Unable To Verify Otp';
                $data['data']=array(); 
             
            }
        }
        else
        {
            
            $data['status']=false;
            $data['message']='Phone Number Does Not Exits';
            //$data['data']=array(); 
        }
    }
        return response()->json($data); 
        }
        public function change_password(Request $request)
        {
        $validator=Validator::make($request->all(),[
        'user_id'=>'required',
        'new_password'=>'required',
        'confirm_password'=>'required',
        ]);
        $data=array();
        if($validator->fails())
        {
        $data['message'] = $validator->errors(); 
        $data['status']=false;
        }
        else
        {
            $new_password=$request->new_password;
            $confirm_password=$request->confirm_password;
            if($new_password== $confirm_password)
            {
             $update=md5($new_password);
             $response=DB::table('users')->where('id', $_POST['user_id'])->update(['password'=> $update]);
             if($response)
             {
             $data['status']=true;
             $data['message']='Password Reset Successfully';
             
             }
             else
             {
                 $data['status']=false;
                 $data['message']='Unable To Reset Password';
             }
            }
            else
            {
                $data['status']=false;
                $data['message']='Password And Confirm Password Does Not Match';
            }
        }
        return response()->json($data);

        }

public function get_requrement(Request $request)
{
    $data=array();
    $response=DB::table('travel_types')->select('tt_id as id','tt_name as title')->where('status',1)->get();
    if(!$response->isEmpty())
    {
    $data['status']=true;
    $data['data']=$response;
    }
    else
    {
        $data['status']=false;
        $data['data']=array();
    }
return response()->json($data);
}
public function get_enquiry_type(Request $request)
{
    $data=array();
    $response=DB::table('client_types')->select('id','name as title')->where('status',1)->get();
    if(!$response->isEmpty())
    {
    $data['status']=true;
    $data['data']=$response;
    }
    else
    {
        $data['status']=false;
        $data['data']=array();
    }
return response()->json($data);
}
public function get_lead_source(Request $request)
{
    $data=array();
    $response=DB::table('lead_sources')->select('id','name as title')->where('status',1)->get();
    if(!$response->isEmpty())
    {
    $data['status']=true;
    $data['data']=$response;
    }
    else
    {
        $data['status']=false;
        $data['data']=array();
    }
return response()->json($data);
}

        public function create_lead(Request $request)
        {


            //dd(random_strings(7));
            $validator=Validator::make($request->all(),[
              'name'=>'required',
              'user_id'=>'required',
                'email_id'=>'required|email',
                'mobile'=>'required|max:10',
                'enquiry_type'=>'required',
                'travel_location'=>'required',
                'travel_type'=>'required',
                'start_date'=>'required',
                'end_date'=>'required',
                'adults'=>'required',
                'kids'=>'required',
                'trip_budget'=>'required',
                'lead_source'=>'required',
                'lead_owner'=>'required',
                'requrement'=>'required',
            ]);
              $data=array();
              if($validator->fails())
              {
                  $data['message'] = $validator->errors(); 
                  $data['status']=false;
              }
              else
              {
                // $str_result = 'ABCDEF1234567890';
                // $unique_id=substr(str_shuffle($str_result), 0, 8); 
                $unique_id= get_unique_code();


                 $response=DB::table('leads')->insert([
                    'cust_name'=>$request->name,
                    'user_id'=>$request->user_id,
                    'cust_email'=>$request->email_id,
                    'cust_mobile'=>$request->mobile,
                    'client_type'=>$request->enquiry_type,
                    'travel_location'=>$request->travel_location,
                    'travel_type'=>$request->travel_type,
                    'start_date'=>$request->start_date,
                    'end_date'=>$request->end_date,
                    'adults'=>$request->adults,
                    'kids'=>$request->kids,
                    'budget_payment_value'=>$request->trip_budget,
                    'budget_payment_type'=>2,
                    'lead_source'=>$request->lead_source,
                    'lead_owner'=>$request->lead_owner,
                    'tt_id'=>$request->requrement,
                    'unique_code'=>"#".$unique_id,
                    'status'=>1,
                 ]);
                 if($response)
                 {
                 $data['status']=true;
                 $data['message']='Lead Added Successfully';
                 
                 }
                 else
                 {
                     $data['status']=false;
                     $data['message']='Unable To Add Lead';
                 }
                 
              }
              return response()->json($data);
        }
        public function add_call_follow_up(Request $request)
        {
            $validator=Validator::make($request->all(),[
                'date_time'=>'required',
                'call_duration'=>'required',
                'call_reason'=>'required',
                'call_description'=>'required',
                'user_id'=>'required',
            ]);
              $data=array();
              if($validator->fails())
              {
                  $data['message'] = $validator->errors(); 
                  $data['status']=false;
              }
              else
              {
                 $response=DB::table('tbl_followup')->insert([
                    'date_time'=>$request->date_time,
                    'call_duration'=>$request->call_duration,
                    'call_reason'=>$request->call_reason,
                    'call_description'=>$request->call_description,
                    'user_id'=>$request->user_id,
                    'status'=>1,
                 ]);
                 if($response)
                 {
                 $data['status']=true;
                 $data['message']='FollwUp Added Successfully';
                 
                 }
                 else
                 {
                     $data['status']=false;
                     $data['message']='Unable To Add FollwUp';
                 }
                 
              }
              return response()->json($data);
        }
        public function fix_metting(Request $request)
        {
            $validator=Validator::make($request->all(),[
                'date_time'=>'required',
                'metting_reason'=>'required',
                'metting_description'=>'required',
                'user_id'=>'required',
            ]);
              $data=array();
              if($validator->fails())
              {
                  $data['message'] = $validator->errors(); 
                  $data['status']=false;
              }
              else
              {
                 $response=DB::table('tbl_metting')->insert([
                    'date_time'=>$request->date_time,
                    'metting_reason'=>$request->metting_reason,
                    'metting_description'=>$request->metting_description,
                    'user_id'=>$request->user_id,
                    'status'=>1,
                 ]);
                 if($response)
                 {
                 $data['status']=true;
                 $data['message']='Metting Fixed Successfully';
                 
                 }
                 else
                 {
                     $data['status']=false;
                     $data['message']='Unable To Fix Metting';
                 }
                 
              }
              return response()->json($data);
        }
        public function add_notes(Request $request)
        {
            $validator=Validator::make($request->all(),[
                'date_time'=>'required',
                'notes'=>'required',
                 'user_id'=>'required',
                 'lead_id'=>'required',
            ]);
              $data=array();
              if($validator->fails())
              {
                  $data['message'] = $validator->errors(); 
                  $data['status']=false;
              }
              else
              {
                 $response=DB::table('tbl_notes')->insert([
                    'date_time'=>$request->date_time,
                    'notes'=>$request->notes,
                    'user_id'=>$request->user_id,
                    'lead_id'=>$request->lead_id,
                    'status'=>1,
                 ]);
                 if($response)
                 {
                 $data['status']=true;
                 $data['message']='Notes Added Successfully';
                 
                 }
                 else
                 {
                     $data['status']=false;
                     $data['message']='Unable To Add Notes';
                 }
                 
              }
              return response()->json($data);
        }
        public function send_quotation(Request $request)
        {
            $validator=Validator::make($request->all(),[
                'date_time'=>'required',
                 'user_id'=>'required',
                 'lead_id'=>'required',
               //  'image' => 'required|image|mimes:jpg,png,jpeg,gif',
            ]);
              $data=array();
              if($validator->fails())
              {
                  $data['message'] = $validator->errors(); 
                  $data['status']=false;
              }
              else
              {
                 // dd($request->has('image'));
                if($request->has('image'))               
                {

                $upload_path='uploads/images/';
                $image_base64 = base64_decode($request->image);
                $file = $upload_path . time().uniqid() . '.png';
                file_put_contents($file, $image_base64); 
                $img = $file;
                $type="File";

                }
                 $response=DB::table('tbl_quotation')->insert([
                    'date_time'=>$request->date_time,
                    'user_id'=>$request->user_id,
                    'lead_id'=>$request->lead_id,
                    'file'=> $img,
                    'status'=>1,
                 ]);
                 if($response)
                 {
                 $data['status']=true;
                 $data['message']='Quotation Added Successfully';
                 
                 }
                 else
                 {
                     $data['status']=false;
                     $data['message']='Unable To Add Quotation';
                 }
                 
              }
              return response()->json($data);
        }
       
public function get_quotation_by_id(Request $request)
{
    $data=array();
    $lead_id=$request->lead_id;
    $response=DB::table('tbl_quotation')->select('id','file as file_link','date_time')->where(['status'=>1,'lead_id'=>$lead_id])->get();
    if(!$response->isEmpty())
    {
    $data['status']=true;
    $data['data']=$response;
    }
    else
    {
        $data['status']=false;
        $data['data']=array();
    }
return response()->json($data);
}
        public function get_lead_by_id(Request $request)
        {
        $validator=Validator::make($request->all(),[
            'user_id'=>'required',
        ]);
        $data=array();
        if($validator->fails())
        {
         $data['message']=$validator->errors();
         $data['status']=false;
        }
        else
        {
            $user_id=$request->user_id;
            $response=DB::table('users')->join('leads','leads.user_id','=','users.id')
            ->leftjoin('client_types', 'client_types.id', '=', 'leads.client_type')
            ->leftjoin('lead_sources', 'lead_sources.id', '=', 'leads.lead_source')
            ->leftjoin('tbl_pax', 'tbl_pax.lead_id', '=', 'leads.id')
            ->select('tbl_pax.first_name','tbl_pax.last_name','tbl_pax.dob','tbl_pax.passport_number','tbl_pax.more_details','leads.id as lead_id','client_types.name as lead_type','lead_sources.name as lead_source','lead_owner','users.name','users.email','users.mobile','leads.start_date','leads.end_date','leads.travel_location','leads.adults','leads.kids','leads.budget_payment_value as budget_for_trip','leads.requirements',DB::raw('IFNULL(leads.travel_type, 0) as travel_type'))->where(['users.id'=>$user_id,'users.status'=>1])->get()
            ->map(function($row){
                $start_date = date_create($row->start_date);
                $end_date = date_create($row->end_date);
                $interval = date_diff($start_date,$end_date);
                $date_diff= $interval->format('%d');
                return  array(
                    'lead_id'=>$row->lead_id,
                    'lead_type'=>$row->lead_type,
                    'lead_source'=>$row->lead_source,
                    'lead_owner'=>$row->lead_owner,
                    'name'=>$row->name, 
                     'email'=>$row->email,
                    'mobile'=>$row->mobile,
                    'start_date'=>$row->start_date,
                    'end_date'=>$row->end_date,
                    'travel_type'=>$row->travel_type,
                    'travel_location'=>$row->travel_location,
                    'no_of_days'=>$date_diff,
                    'adults'=>$row->adults,
                    'kids'=>$row->kids,
                    'no_of_days'=>$date_diff,
                    'requirements'=>$row->requirements,
                    'first_name'=>$row->first_name,
                    'last_name'=>$row->last_name,
                    'dob'=>$row->dob,
                    'passport_number'=>$row->passport_number,
                    'more_details'=>$row->more_details,
                );

            });
                $data['status'] = true;
                $data['users']=$response;
        }
        return response()->json($data);
        }

public function get_all_leads(Request $request)
{
    $data=array();
    $user_id=$request->user_id;
    $type=$request->type;
   $response= DB::table('leads')->select('id','cust_name as name','cust_email as email','cust_mobile as mobile','budget_payment_value as total','unique_code',DB::raw('DATE_FORMAT(leads.created_at,"%H:%i %p | %d-%m-%y ") as date'));
   
    if($type==1)
    {
        $response->where(['lead_type'=>$type]);
    }
    if($type==2)
    {
        $response->where(['lead_type'=>$type]);
    }
    if($type==3)
    {
        $response->where(['lead_type'=>$type]);
    }
    if($type==4)
    {
        $response->where(['lead_type'=>$type]);
    }
    if($type==5)
    {
        $response->where(['lead_type'=>$type]);
    }
    if($type==6)
    {
        $response->where(['lead_type'=>$type]);
    }
    if($type==7)
   {
       $response->where(['lead_type'=>0]);
   }
    $response->where(['status'=>1,'user_id'=>$user_id]);
    
    $response_data= $response->get();
    if(!$response_data->isEmpty())
    {
    $data['status']=true;
    $data['data']=$response_data;
    }
    else
    {
        $data['status']=false;
        $data['data']=array();
    }
return response()->json($data);
}
            public function delete_lead(Request $request)
            {

            $validator = Validator::make($request->all(),[
            'lead_id' =>'required',
            ]);   
            $data =array();
            $code = 422;  
            if ($validator->fails()) 
            {
            $data['message'] = $validator->errors(); 
            $data['status']=false;  
            }
            else
            {
            $lead_id = $request->lead_id;
            $status=2;
            $user_data = DB::table('leads')->where('id','=',$lead_id)->update(['status' => $status]);


            if($user_data)
            {
            $data['message'] = "Lead Deleted Successfully! ";
            $data['status']=true;
            }
            else
            {
            $data['message'] = "Unable To Delete Lead!";
            $data['status']=false;
            }

            }
            return response()->json($data); 
            }
     
   
  public function update_lead(Request $request)
        {
            $validator=Validator::make($request->all(),[
              'lead_id'=>'required',
            ]);
              $data=array();
              if($validator->fails())
              {
                  $data['message'] = $validator->errors(); 
                  $data['status']=false;
              }
              else
              {
                  $lead_id=$request->lead_id;
                $updateDetails = [
                    'cust_name'=>$request->name,
                    'user_id'=>$request->user_id,
                    'cust_email'=>$request->email_id,
                    'cust_mobile'=>$request->mobile,
                    'client_type'=>$request->enquiry_type,
                    'travel_location'=>$request->travel_location,
                    'travel_type'=>$request->travel_type,
                    'start_date'=>$request->start_date,
                    'end_date'=>$request->end_date,
                    'adults'=>$request->adults,
                    'kids'=>$request->kids,
                    'budget_payment_value'=>$request->trip_budget,
                    'lead_source'=>$request->lead_source,
                    'lead_owner'=>$request->lead_owner,
                    'tt_id'=>$request->requrement,
                ];

                 $response=DB::table('leads')->where('id',$lead_id)->update($updateDetails);
                 if($response)
                 {
                 $data['status']=true;
                 $data['message']='Lead Updated Successfully';
                 
                 }
                 else
                 {
                     $data['status']=false;
                     $data['message']='Unable To Update Lead';
                 }
                 
              }
              return response()->json($data);
        }  
public function dashboard_data(Request $request)
{
        $data =array();
     
        $new_lead = DB::table('leads')->where(['lead_type'=>1,'status'=>1])->get()->count();
        $follwup_lead = DB::table('leads')->where(['lead_type'=>2,'status'=>1])->get()->count();
        $potential_lead = DB::table('leads')->where(['lead_type'=>3,'status'=>1])->get()->count();
        $positive_lead = DB::table('leads')->where(['lead_type'=>4,'status'=>1])->get()->count();
        $converted_lead = DB::table('leads')->where(['lead_type'=>5,'status'=>1])->get()->count();
        $close_lead = DB::table('leads')->where(['lead_type'=>6,'status'=>1])->get()->count();
        $unassigned_lead = DB::table('leads')->where(['lead_type'=>0,'status'=>1])->get()->count();
        $data_count=array(
        'new_lead_count'=>$new_lead,
        'follwup_lead_count'=>$follwup_lead,
        'potential_lead_count'=>$potential_lead,
        'positive_lead_count'=>$positive_lead,
        'converted_lead_count'=>$converted_lead,
        'close_lead_count'=>$close_lead,
        'unassigned_lead_count'=>$unassigned_lead,
        );
        if($data_count)
        {
        $data['data'] = $data_count;
    
        $data['status']=true;
        }
        else
        {
        $data['data'] = array();
        $data['status']=false;
        }
        return response()->json($data);
         }
         public function add_pax(Request $request)
         {
             $validator = Validator::make($request->all(), [
                 'lead_id' => 'required',
                 'first_name' => 'required',
                 'last_name' => 'required',
                 'dob' => 'required',
                 'passport_number' => 'required',
                 'more_details' => 'required',
             ]);
             $data =array();
             $code = 422;
             if ($validator->fails()) 
             {
                 $data['message'] = $validator->errors(); 
                 $data['status']=false;
                 //$code = 422;  
             }
             else
             {
             $flight = DB::table('tbl_pax')->insert([
                 'lead_id' => $request->lead_id,
                 'first_name'=>$request->first_name,
                 'last_name'=>$request->last_name,
                 'dob'=>$request->dob,
                 'passport_number'=>$request->passport_number,
                 'more_details'=>$request->more_details,
                 'status'=>1,
             ]);
             $response =  $flight;
         //print_r($flight);

             if($response):
                 $data['message'] = "Pax Details Added Successfully";
                 $data['status']=true;
                 else:
                 $data['message'] = "Unable To Added Pax Details";
                 $data['status']=false; 
             endif; 
         }
     return response()->json($data);
         }
        
                public function assign_lead(Request $request)
                {
                $validator = Validator::make($request->all(), [
                'user_id' => 'required',
                'lead_id' => 'required',
                ]);
                $data =array();
                $code = 422;
                if ($validator->fails()) 
                {
                $data['message'] = $validator->errors(); 
                $data['status']=false;
                }
                else
                {
                $assigned_to=$request->lead_id;
                $user_id=$request->user_id;
                $response = DB::table('leads')->where('user_id',$user_id)->update(['assigned_to'=>$assigned_to]);
                if($response):
                $data['message'] = "Lead Assigned Successfully";
                $data['status']=true;
                else:
                $data['message'] = "Unable To Assign Leads";
                $data['status']=false; 
                endif; 
                }
                return response()->json($data);
                }


 //end file  
}

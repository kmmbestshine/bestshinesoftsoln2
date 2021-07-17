<?php

namespace App\Http\Controllers\Backend;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
use Auth;

class LeadController extends Controller
{
     public function index()
    {
    	 $role_user = DB::table('role_user')->where('user_id', Auth::user()->id)->first();
        if($role_user->role_id == 1){
            $clients = DB::table('leads')->get();
            $clients = $clients->unique('lead_no');
           // dd($clients);
        }elseif($role_user->role_id == 2){
            $clients = DB::table('leads')->get();
            $clients = $clients->unique('lead_no');

        }else{
            $clients = DB::table('leads')->where('user_id', Auth::user()->id)->get();
            $clients = $clients->unique('lead_no');
        }
        return view('backend.activities.index', compact('clients'));
    }
    public function create()
    {
    	$clients = DB::table('clients')->where('user_id', Auth::user()->id)->get();
        foreach ($clients as $key => $value) {
            $clientid=$value->lead_no;
        }
       // dd('kkkkk',$clients,$clientid);
        return view('backend.activities.create',compact('clients'));
           
    }
    public function store(Request $request)
    {
    	$input= \Request::all();
        $clients = DB::table('clients')->where('user_id', Auth::user()->id)->where('id',$input['client_id'])->first();
        //dd($clients);
        
        $message =  DB::table('leads')->insert(
                array(
            'user_id' => Auth::user()->id,
            'name' => $clients->name,
            'activity' => $request->activity,
            'date' => $request->date,
            'time' => $request->time,
            'details' => $request->details,
            'status' => $request->status,
            'client_id' => $request->client_id,
            'lead_no' => $clients->lead_no,
            'company_name' => $clients->company_name,
            'created_at' => date('Y-m-d H:i:s'),
                ));
        //dd($input,$message);
        return redirect()->route('backend.lead.index');
        
    }
     public function statusedit($id)
    {
      // dd('ffffff',$id);

       $leadinform = DB::table('leads')->join('clients', 'clients.id', '=', 'leads.client_id')->where('leads.id', $id)->first();
        $activities = DB::table('leads')->where('client_id', $leadinform->client_id)->get();
        $clients = DB::table('clients')->where('id', $leadinform->client_id)->get();
        $projects = DB::table('projects')->where('client_id', $leadinform->client_id)->first();
        $documents = DB::table('documents')->where('client_id', $leadinform->client_id)->get();
        
        //dd('ffffff',$leadinform,$activities,$clients,$projects,$documents);
        return view('backend.activities.statusedit', compact('leadinform','activities','clients','projects','documents'));
    }

    public function projectstore(Request $request)
    {
        $input= \Request::all();
        //dd($input);
         $file = $request->logo;
            $extension = $file->getClientOriginalExtension();
            $originalName= $file->getClientOriginalName();
            $filename = substr(str_shuffle(sha1(rand(3,300).time())), 0, 10) . "." . $extension;
            $file = \Image::make($file);
            $success = $file->resize(350,null, function ($constraint)
            {
                $constraint->aspectRatio();

            })->save('project/' . $filename);
       
if($success)
            {
        
        $message =  DB::table('projects')->insert(
                array(
            'user_id' => Auth::user()->id,
            'lead_no' => $request->lead_no,
            'client_id' => $request->client_id,
            'company_name' => $request->company_name,
            'softwaretype' => $request->softwaretype,
            'plan' => $request->plan,
            'projectvalue' => $request->projectvalue,
            'advance_amt' => $request->advance_amt,
            'handoverdate' => $request->handoverdate,
            'domainname' => $request->domainname,
            'domainamt' => $request->domainamt,
            'first_installment_dt' => $request->firstinsalmentdtae,
            'first_installment' => $request->firstinstallment,
            'sec_installment_dt' => $request->sec_installmentdate,
            'sec_installment' => $request->sec_installment,
            'onhand_amt_dtae' => $request->onhand_amt_dtae,
            'onhandover_amt' => $request->onhandover_amt,
            'logo' => 'project/'.$filename,
            'requirements' => $request->requirements,
            'created_at' => date('Y-m-d H:i:s'),
                ));
        }
       // dd($input,$message);
       // return redirect()->route('backend.lead.statusedit');
        if($message){
            return redirect()->back()->with('success_message', 'Successfully created your Project');
        
        }
        
    }
    public function documenttstore(Request $request)
    {
        //$input= \Request::all();
       $input = \Request::all();
        $date = date('d-m-Y', strtotime($input['document_date']));
        
       if($input)
        {
            if(isset($input['image']))
            {
                $image = $input['image'];
                $extension = $image->getClientOriginalExtension();
                $originalName= $image->getClientOriginalName();
                $directory = 'document';
                $filename = substr(str_shuffle(sha1(rand(3,300).time())), 0, 10) . "." . $extension;
                $image= \Image::make($image);
                $image->resize(500,null, function ($constraint)
                {
                    $constraint->aspectRatio();
                })->save($directory. '/' . $filename);
                $imagefile = $directory.'/'.$filename;
            }
            else
            {
                $imagefile = '';
            }

            if(isset($input['pdf']))
            {
                $pdf = $input['pdf'];
                $ex = $pdf->getClientOriginalExtension();
                $name = $pdf->getClientOriginalName();
                $destinationPath = 'document';
                $pdfname = substr(str_shuffle(sha1(rand(3,300).time())), 0, 10) . "." . $ex;
                $upload_pdf = $pdf->move($destinationPath, $pdfname);
                $pdffile = $destinationPath.'/'.$pdfname;
            }
            else
            {
                $pdffile = '';
            }
           
            $project =  DB::table('projects')->where('client_id',$input['client_id'])->where('lead_no',$input['lead_no'])->first();
            // dd($project);
           $message= \DB::table('documents')->insert([
                     'user_id' => Auth::user()->id,
                    'lead_no' => $input['lead_no'],
                    'client_id' => $input['client_id'],
                    'project_id' => $project->id,
                    'document_name' => $input['document_name'],
                    'description' => $input['description'],
                    'document_date' => $input['document_date'],
                    'image'=> $imagefile,
                    'pdf' => $pdffile,
                    'created_at' => date('Y-m-d H:i:s'),
                ]);
            if($message){
            return redirect()->back()->with('success_message', 'Successfully Uploaded your Documents');
        
        }
        }
        
    }
    public function download_document_image($id)
    {
         
        $documents=\DB::table('documents')->where('id', $id)->first();
        if($documents->image)
        {
             $path = public_path().'/'. $documents->image;
        }

         if ( file_exists( $path ) ) {
            // Send Download
            return response()->download($path);
        } 
        //dd($build_details->image);
    }
    public function download_document_pdf($id)
    {
         
        $documents=\DB::table('documents')->where('id', $id)->first();
        if($documents->pdf)
        {
             $path = public_path().'/'. $documents->pdf;
        }
        
         if ( file_exists( $path ) ) {
            // Send Download
            return response()->download($path);
        } 
        //dd($build_details->image);
    }
}

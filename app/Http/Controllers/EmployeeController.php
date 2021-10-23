<?php

namespace App\Http\Controllers;

use App\Models\Designation;
use App\Models\Employee;
use Illuminate\Http\Request;
use Yajra\Datatables\Datatables;
use Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

use Mail;
class EmployeeController extends Controller
{
    public function datatable(Request $request)
    {
        $employees = Employee::with(['designation'])->orderBy("id", "DESC")->get();
        return Datatables::of($employees)
        ->rawColumns(['dp','name','email','designation','action'])
        ->editColumn('dp', function ($employee) {
            $html = '';
            $data = !empty($employee->image && file_exists('uploads/'.$employee->image)) ? '<img style="width: 40px; height: 40px" src="'.asset('uploads/'.$employee->image).'"': '<img style="width: 40px; height: 40px" src="'.asset('storage/img/man.jpg').'" />';

            $html = '<div class="img_wrap contact_list"><div class="dp_name">'.$data.'</div></div>';
            return $html;

        })
        ->editColumn('designation', function ($employee) {
            $designations = $employee->designation->name;
            return $designations;

        })
         ->editColumn('action', function ($employee) {
            $html = '';

              $html = '<a class="btn btn-info btn-sm" href="'.route('employee.edit', encrypt($employee->id)).'">
              <i class="fas fa-pencil-alt">
              </i>
              Edit
          </a>
          <a class="btn btn-danger btn-sm delete_employee" data-href="'.route('employee.destroy', encrypt($employee->id)).'">
              <i class="fas fa-trash">
              </i>
              Delete
          </a>';
            return $html;
        })
    ->filter(function ($instance) use ($request) {

            if (!empty($request->get('search'))) {
                $instance->collection = $instance->collection->filter(function ($row) use ($request) {
                    if (Str::contains(Str::lower($row['name']), Str::lower($request->get('search')))) {
                        return true;
                    } else if(Str::contains(Str::lower($row['email']), Str::lower($request->get('search')))) {
                        return true;
                    } else if(Str::contains(Str::lower($row['designation']), Str::lower($request->get('search')))) {
                        return true;
                    }

                    return false;
                });
            }
        })

        ->make(true);;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $designations = Designation::all();
        return view('Employee.employee-add',compact('designations'));
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
        $input = $request->all();
        $validator = Validator::make($request->all(), [
            'name'=>'required',
            'email'=>'required|email',
            'image' => 'image|mimes:jpeg,png,jpg|max:5120',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 406);
       }
        $exist = Employee::where('email',$input['email'])->first();
        if($exist){
            return response()->json(['status' => 3000, 'message' => 'Email Already Exist']);
        }
        $password =Str::random(8);
            $data['body'] = "<h2>Your account has been created</h2><br><b>Password:</b>".$password;
            $data['from']="mehboobaysha@gmail.com";
            $data['subject']="Employee";
            $data['to']=$input['email'];
            $this->sendemail($data);
            if ($image = $request->file('image')) {
                $destinationPath = 'image/';
                $profileImage = date('YmdHis') . "." . $image->getClientOriginalExtension();
                $image->move(public_path().'/uploads/', $profileImage);
                $input['image'] = $profileImage;
            }

        $employee = new Employee;
        $employee->name = $input['name'];
        $employee->email = $input['email'];
        $employee->designation_id = $input['designation'];
        $employee->image = isset($input['image'])? $input['image'] : '';
        $employee->password = Hash::make($password);
        $employee->save();
        return response()->json(['status' => 1000, 'message' => 'Employee Created Successfully Password has been sent to the employees Email Address']);

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $id = decrypt($id);
        $designations = Designation::all();
        $employee = Employee::find($id);
        return view('Employee.employee-edit',compact('employee','designations'));
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
        $id = decrypt($id);
        $input = $request->all();
        $validator = Validator::make($request->all(), [
            'name'=>'required',
            'email'=>'required|email',
            'image' => 'image|mimes:jpeg,png,jpg|max:5120',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 406);
       }
        $exist = Employee::where('email',$input['email'])->where('id','!=',$id)->first();
        if($exist){
            return response()->json(['status' => 3000, 'message' => 'Email Already Exist']);
        }

            if ($image = $request->file('image')) {
                $destinationPath = 'image/';
                $profileImage = date('YmdHis') . "." . $image->getClientOriginalExtension();
                $image->move(public_path().'/uploads/', $profileImage);
                $input['image'] = $profileImage;
            }

        $employee =  Employee::where('id',$id)->first();
        if($request->has('new_password')&& !empty($input['new_password'])){
            if (Hash::check($input['old_password'], $employee->password)) {
                $employee->fill([
                    'password' => Hash::make($request->new_password)
                    ])->save();
                } else {
                    return response()->json(['status' => 3000, 'message' => 'Old Password is wrong']);
                }
        }
        $employee->name = $input['name'];
        $employee->email = $input['email'];
        $employee->designation_id = $input['designation'];
        $employee->image = $request->hasFile('image')? $input['image'] : $employee->image;
        $employee->save();
        return response()->json(['status' => 1000, 'message' => 'Employee Updated Successfully']);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $id = decrypt($id);

        $employee = Employee::where('id', $id)->delete();
        if($employee){
            return response()->json(['status' => 1000, 'success' => ' delete successfully!']);

        } else {
            return response()->json(['status' => 2000, 'success' => ' delete successfully!']);

        }

    }

    function sendemail($datas)
    {
    $data = ['from' => $datas['from'],
    'to' => $datas['to'],
    'subject' => $datas['subject'],
    'body' => $datas['body'],

];
    try {

        Mail::send(
            'email.messages',
            $data,
            function ($message) use ($data) {
                $message->from($data['from'])
                    ->subject($data["subject"])
                    ->to($data["to"]);
            }
        );
    } catch (Exception $e) {
        // $e->getMessage();
        \Log::error($$e->getMessage());
        report($e);
        return false;
    }
}
}

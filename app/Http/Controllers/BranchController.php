<?php



namespace App\Http\Controllers;



use App\Models\Branch;

use App\Models\Products;

use App\Model\Branchable;

use App\Models\User;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Crypt;

use App\Models\Agency;

use Illuminate\Support\Facades\DB;

use Validator;
use App\Imports\BranchesImport;
use App\Exports\BranchExport;

use Maatwebsite\Excel\Facades\Excel;



class BranchController extends Controller

{

    /**

     * Display a listing of the resource.

     *

     * @return \Illuminate\Http\Response

     */

    public function index()
    {
        $data = Branch::with('city')->where('status',0)->orderBy('name', 'ASC')->get();
        return view('branch.list', compact('data'));
    }



    /**

     * Show the form for creating a new resource.

     *

     * @return \Illuminate\Http\Response

     */

    public function create()
    {

        $regions = DB::table("regions")->get();
        // $users = User::role('Collection Manager')->get(['id', 'name']);
        $users = User::get(['id', 'name']);

        $product=Products::get();

        return view('branch.create', compact('regions', 'users','product')

        );

    }



    /**

     * Store a newly created resource in storage.

     *

     * @param  \Illuminate\Http\Request $request

     * @return \Illuminate\Http\Response

     */

    public function store(Request $request)

    {



        // dd($request->all());

        $validator = Validator::make($request->all(), [

            'name' => 'required',

            'city_id' => 'required',

            // 'manager_id' => 'required',

            // 'owner_id' => 'required',

            // 'uuid' => 'required',

            'location' => 'required',

        //    'address' => 'required',

        ]);

        if ($validator->fails()) {



            return redirect()->back()->with('error', [$validator->errors()->all()])->withInput();

        } else {

            $branch = Branch::create(

                    ['name' => $request->name,

                    'city_id' => $request->city_id,

                    // 'manager_id' => $request->manager_id,

                    'manager_id' => $request->Collection_Manager,

                    // 'owner_id' => $request->owner_id,

                    'uuid' => $request->uuid,

                    'location' => $request->location,

//                    'addresss' => $request->address

                    'lob'=> $request->lob,

                ]

            );

            $this->branchProduct($branch,$request);

            if ($branch) {

                return redirect()->route('branch.index')->with('success', ['Branch created successfully.']);

            } else {

                return redirect()->back()->with('error', ['Branch creation unsuccessfully.']);

            }

        }

    }

    public function branchProduct($branch,$request){

        // Branch::where('id',$branch->id)->delete();

        $i=1;

        $data=[];

        // $data[]=['branch_id'=>$branch->id,'product_id'=>$request['product_id'],'manager_id'=>$request['manager_id']];

        $data[]=['branch_id'=>$branch->id,'product_id'=>$request['product_id'],'manager_id'=>$request['Collection_Manager'],'type'=>'Collection_Manager'];

        if(isset($request['Area_Collection_Manager']) && $request['Area_Collection_Manager']!=null){

            $data[]=['branch_id'=>$branch->id,'product_id'=>$request['product_id'],'manager_id'=>$request['Area_Collection_Manager'],'type'=>'Area_Collection_Manager'];

        }

        if(isset($request['Regional_Collection_Manager']) && $request['Regional_Collection_Manager']!=null){

            $data[]=['branch_id'=>$branch->id,'product_id'=>$request['product_id'],'manager_id'=>$request['Regional_Collection_Manager'],'type'=>'Regional_Collection_Manager'];

        }

        if(isset($request['Zonal_Collection_Manager']) && $request['Zonal_Collection_Manager']!=null){

        $data[]=['branch_id'=>$branch->id,'product_id'=>$request['product_id'],'manager_id'=>$request['Zonal_Collection_Manager'],'type'=>'Zonal_Collection_Manager'];

        }

        if(isset($request['National_Collection_Manager']) && $request['National_Collection_Manager']!=null){

        $data[]=['branch_id'=>$branch->id,'product_id'=>$request['product_id'],'manager_id'=>$request['National_Collection_Manager'],'type'=>'National_Collection_Manager'];

        }

        if(isset($request['Group_Product_Head']) && $request['Group_Product_Head']!=null){

        $data[]=['branch_id'=>$branch->id,'product_id'=>$request['product_id'],'manager_id'=>$request['Group_Product_Head'],'type'=>'Group_Product_Head'];

        }

        while (isset($request['manager_id'.$i])) {

            // $data[]=['branch_id'=>$branch->id,'product_id'=>$request['product_id'.$i],'manager_id'=>$request['manager_id'.$i]];

            $data[]=['branch_id'=>$branch->id,'product_id'=>$request['product_id'.$i],'manager_id'=>$request['Collection_Manager'.$i],'type'=>'Collection_Manager'];

            if(isset($request['Area_Collection_Manager'.$i]) && $request['Area_Collection_Manager'.$i]!=null){

                $data[]=['branch_id'=>$branch->id,'product_id'=>$request['product_id'.$i],'manager_id'=>$request['Area_Collection_Manager'.$i],'type'=>'Area_Collection_Manager'];

            }

            if(isset($request['Regional_Collection_Manager'.$i]) && $request['Regional_Collection_Manager'.$i]!=null){

                $data[]=['branch_id'=>$branch->id,'product_id'=>$request['product_id'.$i],'manager_id'=>$request['Regional_Collection_Manager'.$i],'type'=>'Regional_Collection_Manager'];

            }

            if(isset($request['Zonal_Collection_Manager'.$i]) && $request['Zonal_Collection_Manager'.$i]!=null){

                $data[]=['branch_id'=>$branch->id,'product_id'=>$request['product_id'.$i],'manager_id'=>$request['Zonal_Collection_Manager'.$i],'type'=>'Zonal_Collection_Manager'];

            }

            if(isset($request['National_Collection_Manager'.$i]) && $request['National_Collection_Manager'.$i]!=null){

                $data[]=['branch_id'=>$branch->id,'product_id'=>$request['product_id'.$i],'manager_id'=>$request['National_Collection_Manager'.$i],'type'=>'National_Collection_Manager'];

            }

            if(isset($request['Group_Product_Head'.$i]) && $request['Group_Product_Head'.$i]!=null){

                $data[]=['branch_id'=>$branch->id,'product_id'=>$request['product_id'.$i],'manager_id'=>$request['Group_Product_Head'.$i],'type'=>'Group_Product_Head'];

            }

            $i++;

        }

        Branchable::insert($data);

        // dd($branch,$request->all(),$data);

    }

    /**

     * Display the specified resource.

     *

     * @param  int $id

     * @return \Illuminate\Http\Response

     */

    public function show($id)

    {

        // $data = Branch::where('id', Crypt::decrypt($id))->delete();
    $data=Branch::where('id',Crypt::decrypt($id))->update(['status'=>1]);
        if ($data) {
            return redirect()->route('branch.index')->with('success', ['Branch deleted successfully.']);
        } else {
            return redirect()->back()->with('error', ['Branch deletion unsuccessfully.']);
        }

    }



    /**

     * Show the form for editing the specified resource.

     *

     * @param  int $id

     * @return \Illuminate\Http\Response

     */

    public function edit($id)

    {

        $data = Branch::with('city.state.region','branchable')->find(Crypt::decrypt($id));

        $region = $data->city->state->region->id;

        $regions = DB::table("regions")->get();

        $users = User::get(['id', 'name']);

        $product=Products::get();

        $userData=[];

        // dd($data);

        foreach($data->branchable as $item){

            if($item->type!=null){

                $userData[$item->product_id][$item->type]=$item;

            }

        }



        return view('branch.edit', compact('data', 'regions', 'users','region','product','userData'));

    }



    /**

     * Update the specified resource in storage.

     *

     * @param  \Illuminate\Http\Request $request

     * @param  int $id

     * @return \Illuminate\Http\Response

     */

    public function update(Request $request, $id)

    {

        $validator = Validator::make($request->all(), [

            'name' => 'required',

            'city_id' => 'required',

            // 'manager_id' => 'required',

            // 'owner_id' => 'required',

            // 'uuid' => 'required',

            'location' => 'required',

//            'address' => 'required',

        ]);



        if ($validator->fails()) {



            return redirect()->back()->with('error', [$validator->errors()->all()])->withInput();

        } else {



            $branch = Branch::where('id', Crypt::decrypt($id))->update(

                ['name' => $request->name,

                    'city_id' => $request->city_id,

                    // 'manager_id' => $request->manager_id,

                    // 'owner_id' => $request->owner_id,

                    'uuid' => $request->uuid,

                    'location' => $request->location,

//                    'addresss' => $request->address

                    'lob'=> $request->lob,

                ]

            );

            if ($branch) {

                    

                $i=1;

            $data=[];

            $data[]=['branch_id'=>Crypt::decrypt($id),'product_id'=>$request['product_id'],'manager_id'=>$request['manager_id'],'type'=>'manager'];

            $data[]=['branch_id'=>Crypt::decrypt($id),'product_id'=>$request['product_id'],'manager_id'=>$request['Collection_Manager'],'type'=>'Collection_Manager'];

            $data[]=['branch_id'=>Crypt::decrypt($id),'product_id'=>$request['product_id'],'manager_id'=>$request['Area_Collection_Manager'],'type'=>'Area_Collection_Manager'];

            $data[]=['branch_id'=>Crypt::decrypt($id),'product_id'=>$request['product_id'],'manager_id'=>$request['Regional_Collection_Manager'],'type'=>'Regional_Collection_Manager'];

            $data[]=['branch_id'=>Crypt::decrypt($id),'product_id'=>$request['product_id'],'manager_id'=>$request['Zonal_Collection_Manager'],'type'=>'Zonal_Collection_Manager'];

            $data[]=['branch_id'=>Crypt::decrypt($id),'product_id'=>$request['product_id'],'manager_id'=>$request['National_Collection_Manager'],'type'=>'National_Collection_Manager'];

            $data[]=['branch_id'=>Crypt::decrypt($id),'product_id'=>$request['product_id'],'manager_id'=>$request['Group_Product_Head'],'type'=>'Group_Product_Head'];

            while (isset($request['manager_id'.$i])) {

                $data[]=['branch_id'=>Crypt::decrypt($id),'product_id'=>$request['product_id'.$i],'manager_id'=>$request['manager_id'.$i],'type'=>'manager'];

                $data[]=['branch_id'=>Crypt::decrypt($id),'product_id'=>$request['product_id'.$i],'manager_id'=>$request['Collection_Manager'.$i],'type'=>'Collection_Manager'];

                $data[]=['branch_id'=>Crypt::decrypt($id),'product_id'=>$request['product_id'.$i],'manager_id'=>$request['Area_Collection_Manager'.$i],'type'=>'Area_Collection_Manager'];

                $data[]=['branch_id'=>Crypt::decrypt($id),'product_id'=>$request['product_id'.$i],'manager_id'=>$request['Regional_Collection_Manager'.$i],'type'=>'Regional_Collection_Manager'];

                $data[]=['branch_id'=>Crypt::decrypt($id),'product_id'=>$request['product_id'.$i],'manager_id'=>$request['Zonal_Collection_Manager'.$i],'type'=>'Zonal_Collection_Manager'];

                $data[]=['branch_id'=>Crypt::decrypt($id),'product_id'=>$request['product_id'.$i],'manager_id'=>$request['National_Collection_Manager'.$i],'type'=>'National_Collection_Manager'];

                $data[]=['branch_id'=>Crypt::decrypt($id),'product_id'=>$request['product_id'.$i],'manager_id'=>$request['Group_Product_Head'.$i],'type'=>'Group_Product_Head'];

                    $i++;

            }

            // dd($data);

            foreach($data as $item){

                Branchable::where(['branch_id'=>$item['branch_id'],'product_id'=>$item['product_id'],'type'=>$item['type']])->update($item);

            }

                return redirect()->route('branch.index')->with('success', ['Branch updated successfully.']);

            } else {

                return redirect()->back()->with('error', ['Branch updation unsuccessfully.']);

            }

        }

    }



    /**

     * Remove the specified resource from storage.

     *

     * @param  int $id

     * @return \Illuminate\Http\Response

     */

    public function destroy($id)

    {

        //

    }





//For fetching states

    public function getStates($id)

    {

        $states = DB::table("states")

            ->where("region_id", $id)

            ->pluck("name", "id");

        return response()->json($states);

    }



//For fetching cities

    public function getCities($id)
    {
        $cities = DB::table("cities")

            ->where("state_id", $id)

            ->pluck("name", "id");

        return response()->json($cities);
    }

    public function excelDownloadBranch(){

        ini_set('memory_limit', '-1');

        ini_set('max_execution_time', 3000);

        return Excel::download(new BranchExport, 'Branch.xlsx');

    }


    //V Upload Page Show
     public function showBranchImport()
     {
         return view('branch.upload');
     }
 
 
    //V Upload Excel Data
     public function branchImport()
     {
         Excel::import(new BranchesImport, request()->file('file'));
 
         return redirect()->back()->with('success', 'Excel file imported successfully.');
     }

}


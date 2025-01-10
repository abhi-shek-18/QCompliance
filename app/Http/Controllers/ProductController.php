<?php



namespace App\Http\Controllers;



use Illuminate\Http\Request;
use App\Models\Products;
use App\Model\ProductUser;
use App\User;
use App\Model\Branch;
use App\Model\Branchable;
use App\Model\UserHierarchy;
use Validator;
use Illuminate\Support\Facades\Crypt;
use DB;
use App\Exports\ProductHierarchyExport;
use Maatwebsite\Excel\Facades\Excel;
use Auth;

class ProductController extends Controller
{

    /**

     * Display a listing of the resource.

     *

     * @return \Illuminate\Http\Response

     */

    public function index()

    {
        $product=Products::where('status',0)->latest()->get();
        return view('product.list',compact('product'));
    }



    /**

     * Show the form for creating a new resource.

     *

     * @return \Illuminate\Http\Response

     */

    public function create()

    {

        return view('product.create');

    }



    /**

     * Store a newly created resource in storage.

     *

     * @param  \Illuminate\Http\Request  $request

     * @return \Illuminate\Http\Response

     */

    public function store(Request $request)

    {
        $camelCaseText = ucwords($request->name);

        $validator = Validator::make($request->all(), [

            'name' => 'required',

            // 'type' => 'required'

        ]);



        if ($validator->fails()) {

            return redirect()->back()->with('error', [$validator->errors()->all()])->withInput();

        } else {

        $yard=Products::create(

            [

            'name'=>$camelCaseText,

            'bucket'=>$request->bucket,

            'type'=>0

            ]

        );

        if($yard){

            return redirect('product')->with('success', ['Product updated successfully.']);

        }

        else{

            return redirect()->back()->with('error', ['Product updation unsuccessfully.']);

        }

        }

    }



    /**

     * Display the specified resource.

     *

     * @param  int  $id

     * @return \Illuminate\Http\Response

     */

    public function show($id)

    {

        // $data = Products::where('id', Crypt::decrypt($id))->delete();
        $data = Products::where('id', Crypt::decrypt($id))->update(['status'=>1]);

        if ($data) {

            return redirect()->route('product.index')->with('success', ['Product deleted successfully.']);

        } else {

            return redirect()->back()->with('error', ['Product deletion unsuccessfully.']);

        }

    }



    /**

     * Show the form for editing the specified resource.

     *

     * @param  int  $id

     * @return \Illuminate\Http\Response

     */

    public function edit($id)

    {

        $data=Products::find(Crypt::decrypt($id));

        return view('product.edit', 

        compact('data'));

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
        $camelCaseText = ucwords($request->name);

        $validator = Validator::make($request->all(), [

            'name' => 'required',

            // 'type' => 'required'

        ]);



        if ($validator->fails()) {



            return redirect()->back()->with('error', [$validator->errors()->all()])->withInput();

        } else {

        $yard=Products::where('id',Crypt::decrypt($id))->update(

            [

                'name'=>$camelCaseText,

                'bucket'=>$request->bucket,

                'type'=>0

            ]

        );

        if($yard){

            return redirect('product')->with('success', ['Product created successfully.']);

        }

        else{

            return redirect()->back()->with('error', ['Product creation unsuccessfully.']);

        }

        }

    }



    /**

     * Remove the specified resource from storage.

     *

     * @param  int  $id

     * @return \Illuminate\Http\Response

     */

    // public function destroy($id)
    // {

    // }

    public function hierarchy(){

        $branch=Branch::all(['id','name']);
        $product=Products::all();
        $puser=ProductUser::all()->pluck('user_id');
        $user=User::with('roles')->whereNotIn('id',$puser)->get();
        //echo '<pre>';print_r($user);die();
    
        $col_manager_roles = User::where('user_role_id','3')->pluck('name','id')->toArray();
        $quality_auditor_roles = User::where('user_role_id','2')->pluck('name','id')->toArray();
        $quality_control_roles = User::where('user_role_id','10')->pluck('name','id')->toArray();
        $area_col_manager_roles = User::where('user_role_id','4')->pluck('name','id')->toArray();
        $reg_col_manager_roles = User::where('user_role_id','5')->pluck('name','id')->toArray();
        $zonal_col_manager_roles = User::where('user_role_id','6')->pluck('name','id')->toArray();
        $national_col_manager_roles = User::where('user_role_id','7')->pluck('name','id')->toArray();
        $group_product_head_roles = User::where('user_role_id','8')->pluck('name','id')->toArray();
        $head_collection_roles = User::where('user_role_id','9')->pluck('name','id')->toArray();

        return view('product.productHierarchy',compact('product','branch','col_manager_roles','quality_auditor_roles','quality_control_roles','area_col_manager_roles',
        'reg_col_manager_roles','zonal_col_manager_roles','national_col_manager_roles','group_product_head_roles','head_collection_roles'));

    }

    //V Fetch Data on chnage
    public function getCmanagerDetails($cm_id)
    {
        $userHierarchy = UserHierarchy::where('cm_id',$cm_id)->first()->toArray();
        //echo '<pre>'; print_r($col_manager_roles); die;
        if (!$userHierarchy) {
            return response()->json(['error' => 'Collection Manager not found'], 404);
        }
        $data = $userHierarchy;
        return response()->json($data);
    }


    public function doHierarchy(Request $request) {
        //echo '<pre>'; print_r($request->all()); die;

        $validator = Validator::make($request->all(), [
            'branch_id' => 'required',
            'product_id' => 'required',

        ]);
    
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        $data = Branchable::create([
            'created_by' => Auth::user()->id,
            'branch_id' => $request->branch_id,
            'product_id' => $request->product_id,
            'cm_id'  => $request->cm_id,
            'acm_id' => $request->acm_id,
            'rcm_id' => $request->rcm_id,
            'zcm_id' => $request->zcm_id,
            'ncm_id' => $request->ncm_id,
            'gph_id' => $request->gph_id,
        ]);
    
        if ($data) {
            return redirect()->route('HierarchyView')->with('success', 'Product Hierarchy created successfully.');
        } else {
            return redirect()->back()->with('error', 'Product Hierarchy creation unsuccessful.');
        }
    }

    public function destroy($id)
    {
        $producthierarchy = Branchable::findOrFail($id);
        $producthierarchy->delete();
        return redirect()->back()->with('success', ['Product Hierarchy deleted successfully.']);
    }
    

    public function hierarchyView(){

        $id = Auth::user()->id;
        $data = DB::table('branchables')
        ->leftJoin('branches', 'branches.id', '=', 'branchables.branch_id')
        ->leftJoin('products', 'products.id', '=', 'branchables.product_id')
        ->leftJoin('users as cm_users', 'cm_users.id', '=', 'branchables.cm_id')
        ->leftJoin('users as acm_users', 'acm_users.id', '=', 'branchables.acm_id')
        ->leftJoin('users as rcm_users', 'rcm_users.id', '=', 'branchables.rcm_id')
        ->leftJoin('users as zcm_users', 'zcm_users.id', '=', 'branchables.zcm_id')
        ->leftJoin('users as ncm_users', 'ncm_users.id', '=', 'branchables.ncm_id')
        ->leftJoin('users as gph_users', 'gph_users.id', '=', 'branchables.gph_id')
        ->leftJoin('roles', 'roles.id', '=', 'cm_users.user_role_id')
        ->select(
            'branches.name as br_name',
            'products.name as pr_name',
            'cm_users.name as cm_name',
            'acm_users.name as acm_name',
            'rcm_users.name as rcm_name',
            'zcm_users.name as zcm_name',
            'ncm_users.name as ncm_name',
            'gph_users.name as gph_name',
            'roles.name as cmrolename',
            'branchables.*'
        )
        ->where('branchables.status', 1)
        ->get();

        //echo '<pre>'; print_r($data); die();
        return view('product.view', ['data' => $data]);
    }

    public function hierarchyEdit($id){

        $id = Auth::user()->id;
        $branch=Branch::all(['id','name']);
        $product=Products::all();
        $puser=ProductUser::all()->pluck('user_id');
        $user=User::with('roles')->whereNotIn('id',$puser)->get();
        //echo '<pre>';print_r($user);die();
    
        $col_manager_roles = User::where('user_role_id','3')->pluck('name','id')->toArray();
        $quality_auditor_roles = User::where('user_role_id','2')->pluck('name','id')->toArray();
        $quality_control_roles = User::where('user_role_id','10')->pluck('name','id')->toArray();
        $area_col_manager_roles = User::where('user_role_id','4')->pluck('name','id')->toArray();
        $reg_col_manager_roles = User::where('user_role_id','5')->pluck('name','id')->toArray();
        $zonal_col_manager_roles = User::where('user_role_id','6')->pluck('name','id')->toArray();
        $national_col_manager_roles = User::where('user_role_id','7')->pluck('name','id')->toArray();
        $group_product_head_roles = User::where('user_role_id','8')->pluck('name','id')->toArray();
        $head_collection_roles = User::where('user_role_id','9')->pluck('name','id')->toArray();

        //echo '<pre>'; print_r($data); die();
       
        return view('product.productHierarchyEdit', compact('product','branch','col_manager_roles','quality_auditor_roles','quality_control_roles','area_col_manager_roles',
        'reg_col_manager_roles','zonal_col_manager_roles','national_col_manager_roles','group_product_head_roles','head_collection_roles'));
    }




    // public function doHierarchyUpdate(Request $request){
    //     // dd($request->all());
    //     try{
    //         DB::beginTransaction();
    //         $data=[];
    //         $old=$request->old;
    //         $branch=$request->branch;
    //         $type=$request->type;
    //         foreach($request->except(['_token','type','old','branch']) as $k=>$item)
    //         {
    //             if($item!=''){
    //                 $ProductUser=Branchable::updateOrCreate(['branch_id'=>$branch,'product_id'=>$type,'manager_id'=>$old[$k],'type'=>$k],
    //                 ['branch_id'=>$branch,'product_id'=>$type,'manager_id'=>$item,'type'=>$k,'bucket'=>'']);
    //             }

    //             if($old[$k]!=null && $item==null){
    //                 $data=Branchable::where(['branch_id'=>$branch,'product_id'=>$type,'manager_id'=>$old[$k],'type'=>$k])->delete();
    //                 // dd($data);
    //             }
    //         }

    //         DB::commit();
    //         if($ProductUser){
    //             return redirect('product')->with('success', ['Product created successfully.']);
    //         }
    //         else{
    //             return redirect()->back()->with('error', ['Product creation unsuccessfully.']);
    //         }
    //     }
    //     catch(\Exception $e) {
    //         dd($e->getMessage());
    //         DB::rollback();
    //         return redirect()->back()->with('error', $e->getMessage());
    //         // echo 'Caught exception: ',  $e->getMessage(), "\n";
    //     }
    // }

    // public function hierarchyView(){
    //     // $product=ProductUser::all();
    //     // $productids=$product->pluck(['product_id'])->unique();
    //     // $productUser=Products::with('productUser')->find($productids);
    //     // $productUserType=$product->pluck(['type'])->unique();
    //     // $productUser=Branch::with(['branchable'])->get();
    //     $data=Branchable::with(['branch','product','user'])->where('type','!=','Collection_Manager')->get();
    //     $productUserType=$data->pluck(['type'])->unique()->toArray();
    //     if(in_array('Head_of_the_Collections',$productUserType) ==false){
    //         $productUserType[]='Head_of_the_Collections';
    //     }
    //     $productUser=[];
    //     foreach ($data as $key => $value) {
    //         if($value->branch!=null){
    //             $productUser[$value->branch->name ?? $value->branch_id][$value->product->name ?? $value->product_id][$value->type]=$value; 
    //         }
    //     }
    //     // dd($productUser,$productUserType);
    //     return view('product.view',compact('productUser','productUserType'));
    // }


    // public function hierarchyEdit($branch_id,$product_id){
    //     $branch=Branch::where('name',$branch_id)->first();
    //     $product=Products::where('name',$product_id)->first();
    //     $data=Branchable::with(['branch','product','user'])->where('type','!=','Collection_Manager')
    //     ->where('branch_id',$branch->id)->where('product_id',$product->id)->get();
    //     // $productUserType=["Area_Collection_Manager","Regional_Collection_Manager","National_Collection_Manager","Group_Product_Head","Zonal_Collection_Manager","Head_of_the_Collections"];
    //     $productUser=[]; 
    //     $bid=$branch->id;
    //     $pid=$product->id;

    //     foreach ($data as $key => $value) {
    //         if($value->branch!=null){
    //             $productUser[$value->type]=$value->manager_id; 
    //         }
    //     }
    //     $branch=Branch::all(['id','name']);
    //     $product=Products::all();
    //     $puser=ProductUser::all()->pluck('user_id');
    //     $user=User::with('roles')->whereNotIn('id',$puser)->get();
    //     $finalUser=[];

    //     foreach($user as $k => $item){
    //         if(in_array('Area Collection Manager',$item->roles->pluck('name')->toArray())){
    //             $finalUser['Area Collection Manager'][]=$item;
    //         }

    //         if(in_array('Regional Collection Manager',$item->roles->pluck('name')->toArray())){
    //             $finalUser['Regional Collection Manager'][]=$item;
    //         }
    //         if(in_array('Zonal Collection Manager',$item->roles->pluck('name')->toArray())){
    //             $finalUser['Zonal Collection Manager'][]=$item;
    //         }
    //         if(in_array('National Collection Manager',$item->roles->pluck('name')->toArray())){
    //             $finalUser['National Collection Manager'][]=$item;
    //         }
    //         if(in_array('Group Product Head',$item->roles->pluck('name')->toArray())){
    //             $finalUser['Group Product Head'][]=$item;
    //         }
    //         if(in_array('Head of the Collections',$item->roles->pluck('name')->toArray())){
    //             $finalUser['Head of the Collections'][]=$item;
    //         }
    //     }
    //     return view('product.productHierarchyEdit',compact('product','finalUser','branch','productUser','bid','pid'));

    // }

    public function excelDownloadProduct(){
        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', 3000);
        return Excel::download(new ProductHierarchyExport, 'ProductHierarchy.xlsx');
        // return Excel::download(new QcAndQaChangesExport, 'users.xlsx');
    }

}


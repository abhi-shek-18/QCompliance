<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Products;
use App\Model\ProductUser;
use App\Models\Productattribute;
use App\User;
use App\Model\Branch;
use App\Model\Branchable;
use Validator;
use Illuminate\Support\Facades\Crypt;
use DB;
use App\Exports\ProductHierarchyExport;
use Maatwebsite\Excel\Facades\Excel;


class ProductattributeController extends Controller
{

    public function index()
    {
        // $productattdata=Productattribute::with('productattr')->where('status',0)->latest()->get();
        // echo '<pre>'; print_r($productattdata); die();
        // return view('productattribute.list',compact('productattdata'));

        //$productattdata = Productattribute::with('productName')->get();

        $productattdata = Productattribute::with('productName')
        ->join('products', 'productattributes.product_id', '=', 'products.id')
        ->orderBy('products.name', 'ASC')
        ->select('productattributes.*')
        ->get();
        //echo '<pre>'; print_r($productattdata); die();

        return view('productattribute.list', compact('productattdata'));
    }


    public function create()
    {
        $products=Products::where('status',0)->get();
        return view('productattribute.create', compact('products'));
    }


    public function store(Request $request)
    {
        $camelCaseText = ucwords($request->name);
        $validator = Validator::make($request->all(), [
            'product_attribute_name' => 'required',
            // 'type' => 'required'
        ]);


        if ($validator->fails()) {
            return redirect()->back()->with('error', [$validator->errors()->all()])->withInput();
        } else {

        $yard=Productattribute::create(
            [
            'product_attribute_name'=>$request->product_attribute_name,
            'product_id'=>$request->product_id,
            'type'=>0
            ]
        );

        

        if($yard){
            return redirect('productattribute')->with('success', ['Product Attributes created successfully.']);
        }

        else{
            return redirect()->back()->with('error', ['Product Attributes created unsuccessfully.']);
        }

        }

    }


    // public function show($id)
    // {
    //     //$data = Products::where('id', Crypt::decrypt($id))->delete();  
    //     $data =Productattribute::where('id', Crypt::decrypt($id))->update(['status'=>1]);
    //     if ($data) {
    //         return redirect()->route('productattribute.index')->with('success', ['Product Attributes deleted successfully.']);
    //     } else {
    //         return redirect()->back()->with('error', ['Product Attributes deletion unsuccessfully.']);
    //     }

    // }


    public function edit($id)
    {      
        $data=Productattribute::find(Crypt::decrypt($id));
        $products=Products::where('status',0)->latest()->get();
        return view('productattribute.edit', compact('data', 'products'));
    }

    public function show($id)
    {
        $productattdata=Productattribute::find(Crypt::decrypt($id));
        //echo '<pre>'; print_r($productattdata); die();
        $products=Products::where('status',0)->latest()->get();
        return view('productattribute.showproductattributes', compact('productattdata', 'products'));
    }



    public function update(Request $request, $id)
    {
        $camelCaseText = ucwords($request->name);
        $validator = Validator::make($request->all(), [
            'product_id' => 'required',
            // 'type' => 'required'
        ]);

        if ($validator->fails()) {
            return redirect()->back()->with('error', [$validator->errors()->all()])->withInput();

        } else {

        $yard=Productattribute::where('id',Crypt::decrypt($id))->update(
            [
                'product_attribute_name'=>$request->product_attribute_name,
                'product_id'=>$request->product_id,
                'type'=>0
            ]
        );

        // echo '<pre>'; print_r($yard);

        if($yard){
            return redirect('productattribute')->with('success', ['Product Attributes updated successfully.']);
        }
        else{
            return redirect()->back()->with('error', ['Product Attributes updated unsuccessfully.']);

        }

        }

    }


    // public function destroy($id)
    // {
        
    // }

    public function destroy($id)
    {
        $productattribute = Productattribute::findOrFail($id);
        $productattribute->delete();
        
        return redirect()->back()->with('success', 'Product Attributes deleted successfully.');
    }
    






    // public function destroy($id)
    // {
    //     $resource = Subproduct::findOrFail($id);
    //     $resource->delete();

    //     return redirect()->route('subproduct.list')
    //         ->with('success', 'Sub Product deleted successfully.');
    // }

    // public function hierarchy(){

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

    //     // dd($finalUser);

    //     return view('product.productHierarchy',compact('product','finalUser','branch'));

    // }

    // public function doHierarchy(Request $request){

    //     // dd($request->all());

    //     try{

    //         DB::beginTransaction();

    //         $data=[];

    //         $branch=$request->branch;

    //         $type=$request->type;

    //         foreach($request->except(['_token','type','branch']) as $k=>$item)

    //         {

    //             // $data[]=['product_id'=>$request->type,'user_id'=>$item,'type'=>$k];

    //             if($item!=''){

    //                 $data[]=['branch_id'=>$branch,'product_id'=>$type,'manager_id'=>$item,'type'=>$k,'bucket'=>''];

    //             }

    //         }

    //         // $ProductUser=ProductUser::insert($data);

    //         $ProductUser=Branchable::insert($data);

    //         DB::commit();

    //         if($ProductUser){

    //             return redirect('product')->with('success', ['Product created successfully.']);

    //         }

    //         else{

    //             return redirect()->back()->with('error', ['Product creation unsuccessfully.']);

    //         }

    //     }

    //     catch(\Exception $e) {

    //         DB::rollback();

    //         return redirect()->back()->with('error', $e->getMessage());

    //         // echo 'Caught exception: ',  $e->getMessage(), "\n";

    //     }

    // }

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

    // public function excelDownloadProduct(){

    //     ini_set('memory_limit', '-1');

    //     ini_set('max_execution_time', 3000);

    //     return Excel::download(new ProductHierarchyExport, 'ProductHierarchy.xlsx');

    //     // return Excel::download(new QcAndQaChangesExport, 'users.xlsx');

    // }

}


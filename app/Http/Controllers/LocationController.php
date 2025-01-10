<?php



namespace App\Http\Controllers;



use App\Models\City;

use App\Models\State;

use App\Model\Branchable;

use App\User;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Crypt;


use Illuminate\Support\Facades\DB;

use Validator;




class LocationController extends Controller

{

    /**

     * Display a listing of the resource.

     *

     * @return \Illuminate\Http\Response

     */

    public function index()

    {
       $regions = DB::table("regions")->get();

        // $users = User::role('Collection Manager')->get(['id', 'name']);

       
        return view('location.create1', compact('regions')

        );
    }

    public function cityView()

    {
        $cities = DB::table("cities")->where('status', 0)->paginate(10);
        // $users = User::role('Collection Manager')->get(['id', 'name']);

        $states= DB::table('states')->get();
        return view('location.city_view', compact('cities','states')

        );
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
        $states = DB::table('states')->get();

       
        return view('location.create', compact('regions','states')

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

            'region_id' => 'required',

            'state' => 'required',
            'city' => 'required'

        ]);

        if ($validator->fails()) {



            return redirect()->back()->with('error', [$validator->errors()->all()])->withInput();

        } else {
            
            $city = City::create(

                    ['name' => $request->city,

                    'state_id' => $request->state,

                ]

            );

            if ($city) {

                return redirect()->route('location.city_view')->with('success', ['City created successfully.']);

            } else {

                return redirect()->back()->with('error', ['City creation unsuccessfully.']);

            }

        }

    }

    



    public function update(Request $request)

    {
        $validator = Validator::make($request->all(), [

            'region_id' => 'required',

            'state' => 'required'

        ]);

        if ($validator->fails()) {



            return redirect()->back()->with('error', [$validator->errors()->all()])->withInput();

        } else {
            
            $state = State::create(

                    ['name' => $request->state,

                    'region_id' => $request->region_id,

                ]

            );

            if ($state) {

                return redirect()->route('location.index')->with('success', ['State created successfully.']);

            } else {

                return redirect()->back()->with('error', ['State creation unsuccessfully.']);

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

}


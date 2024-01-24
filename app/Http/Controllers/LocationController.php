<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LocationController extends Controller
{
    private function getLocationChildIds($locationId): array
    {
        $data = [];
        $childs = DB::table('location')
            ->where('location_parent_id', $locationId)
            ->get();

        foreach ($childs as $child) {
            $data[] = $child->location_id;
            $data = array_merge($data, $this->getLocationChildIds($child->location_id));
        }
        return $data;
    }

    private function getPath($id)
    {
        $result = DB::table('location')
            ->select('location_id as id', 'location_name as label', 'location_parent_id')
            ->where('location_id', $id)
            ->first();

        if ($result->location_parent_id) {
            return $this->getPath($result->location_parent_id) . ' , ' . $result->label;
        } else {
            return $result->label;
        }
    }

    private function getChain($parentId = 0)
    {
        $resultData = [];

        $resultArray = DB::table('location')
            ->select('location_id as id', 'location_name as label', 'location_parent_id', 'location_regular_fee', 'location_express_fee', 'location_regular_delivery_days', 'location_express_delivery_hours', 'location_preorder_delivery_days')
            ->where('location_parent_id', $parentId)
            ->where('location_status', 'active')
            ->get();

        foreach ($resultArray as $result) {
            $resultData[] = [
                'id' => $result->id,
                'chain' => $this->getPath($result->id),
                'label' => $result->label,
                'location_parent_id' => $result->location_parent_id,
                'regular_fee' => $result->location_regular_fee,
                'express_fee' => $result->location_express_fee,
                'location_regular_delivery_days' => $result->location_regular_delivery_days,
                'location_express_delivery_hours' => $result->location_express_delivery_hours,
                'location_preorder_delivery_days' => $result->location_preorder_delivery_days,
            ];

            $resultData = array_merge($resultData, $this->getChain($result->id));
        }

        return $resultData;
    }
    // For get all data
    public function index()
    {
        $locations = DB::table('location')->select('*')->where('location_status', 'active')->orderBy('location_id', 'ASC')->get();
        
        $finalLocation = $this->getChain();

      

        foreach ($finalLocation as $key => $value) {
            $parentChildIds = $this->getLocationChildIds($value['id']);
            if (count($parentChildIds) > 0) {
                $finalLocation[$key]['header'] = '';
            } else {
                $finalLocation[$key]['header'] = 'select';
            }
            $finalLocation[$key]['childIds'] = $parentChildIds;
        }

        $data = $this->buildTree($finalLocation);

        // dd($data);

        if ($data) {
            return response()->json($data);
            // return response()->json([
            //     'status' => 200,
            //     'error' => false,
            //     'message' => 'Success',
            //     'data' => $data,
            //     'location_list' => $finalLocation
            // ]);
        } else {
            return response()->json([
                'status' => 200,
                'error' => false,
                'message' => 'No Data Found',
                'data' => [],
            ]);
        }
    }

    private function buildTree($locations, $parentId = 0)
    {
        $tree = [];

        foreach ($locations as $index => $location) {
            $locations[$index]['location_chain'] = '';
            if ($location['location_parent_id'] == $parentId) {
                $children = $this->buildTree($locations, $location['id']);
                if ($children) {
                    $location['children'] = $children;
                }
                $tree[] = $location;
            }
        }
        return $tree;
    }



    // For Insert data
    public function storeData(Request $request){

        $data = [
            'location_name'                     => $request->location_name,
            'location_parent_id'                => $request->location_parent_id,
            'location_regular_fee'              => $request->location_regular_fee,
            'location_regular_delivery_days'    => $request->location_regular_delivery_days,
            'location_express_fee'              => $request->location_express_fee,
            'location_express_delivery_hours'   => $request->location_express_delivery_hours,
            'location_preorder_delivery_days'   => $request->location_preorder_delivery_days,
            'location_status'                   => $request->location_status,
        ];

        
        $inserted = DB::table('location')->insert($data);
        
        if ($inserted) {
            return response()->json([
                'status' => 200,
                'message' => 'Data inserted successfully',
            ]);
        } else {
            return response()->json([
                'status' => 500,
                'message' => 'Failed to insert data',
            ]);
        }

    }

//  For Delete

    public function delete(Request $request){
        // $data = $request->id;
        // return  response()->json($data);

        $record = DB::table('location')->where('location_id', $request->id)->first();
            if (!$record) {
                    return response()->json([
                        'status' => 404,
                        'message' => 'Record not found',
                    ]);
                }

            // check child location
            $checkLocation = $this->getChain($request->id); 

            if(!(count($checkLocation) === 0)){
                // Perform the deletion using Query Builder
                $deleted = DB::table('location')
                            ->where('location_id', $request->id)
                            ->delete();

                if ($deleted) {
                    return response()->json([
                        'status' => 200,
                        'message' => 'Record deleted successfully',
                    ]);
                } else {
                    return response()->json([
                        'status' => 500,
                        'message' => 'Failed to delete record',
                    ]);
                }
            }  
            else{
                return response()->json([
                    'status' => 200,
                    'message' => 'Record can not delete , This location has a child location',
                ]);
            } 

    }

 //  Update Function
    public function update(Request $request){
        // $data = $request->all();
        // dd($data);
        // return  response()->json($data);

        $record = DB::table('location')->where('location_id', $request->location_id)->first();
        if (!$record) {
            return response()->json([
                'status' => 404,
                'message' => 'Record not found',
            ]);
        }

         // check child location
         $checkLocation = $this->getChain($request->location_id); 

        if(!(count($checkLocation) === 0)){
        DB::table('location')
        ->where('location_id', $request->location_id)
        ->update([
            'location_name'                     => $request->location_name,
            'location_parent_id'                => $request->location_parent_id,
            'location_regular_fee'              => $request->location_regular_fee,
            'location_regular_delivery_days'    => $request->location_regular_delivery_days,
            'location_express_fee'              => $request->location_express_fee,
            'location_express_delivery_hours'   => $request->location_express_delivery_hours,
            'location_preorder_delivery_days'   => $request->location_preorder_delivery_days,
            'location_status'                   => $request->location_status,
        ]);
        }else{
        return response()->json([
            'status' => 200,
            'message' => 'Record can not Update ! , This location has a child location',
        ]);
        } 

    
        return response()->json([
            'status' => 200,
            'message' => 'Record updated successfully',
            'data' => $record,
        ]);

    }


 // New Update Function

    public function newUpdate(Request $request){

        $record = DB::table('location')->where('location_id', $request->location_id)->first();
        if ($record) {
                return response()->json([
                    'status' => 404,
                    'message' => 'Record not found',
                ]);
        }

        // Check Parent Id
        $checkParent = $this->getParent($request->locaton_parent_id);

        // return response()->json($checkParent);

        // Check Child Id
        $checkChild = $this->getChild($request->child_id);

        
   

        // Update the location's parent_location_id
        DB::table('location')
            ->where('location_id',$request->location_id)
            ->update(['parent_location_id' => $request->parent_location_id]);

        // Update child locations recursively
        $this->updateChildLocations($request->locationId, $request->childLocationId);
    }

    protected function updateChildLocations($oldParentId, $newParentId)
    {
        // Find child locations of the old parent
        $childLocations = DB::table('location')
            ->where('parent_location_id', $oldParentId)
            ->get();

        // Update each child location and call the function recursively
        foreach ($childLocations as $childLocation) {
            $this->updateChildLocations($request->location_id, $childLocation->location_id);
            DB::table('location')
                ->where('location_id', $request->location_id)
                ->update(['parent_location_id' => $newParentId]);
        }
        
    }

    protected function getParent($parentId = 0){
        $resultData = [];

        $resultArray = DB::table('location')
            ->select('location_id as id', 'location_name as label', 'location_parent_id', 'location_regular_fee', 'location_express_fee', 'location_regular_delivery_days', 'location_express_delivery_hours', 'location_preorder_delivery_days')
            ->where('location_parent_id', $parentId)
            ->where('location_status', 'active')
            ->get();

        foreach ($resultArray as $result) {
            $resultData[] = [
                'id' => $result->id,
                'chain' => $this->getPath($result->id),
                'label' => $result->label,
                'location_parent_id' => $result->location_parent_id,
                'regular_fee' => $result->location_regular_fee,
                'express_fee' => $result->location_express_fee,
                'location_regular_delivery_days' => $result->location_regular_delivery_days,
                'location_express_delivery_hours' => $result->location_express_delivery_hours,
                'location_preorder_delivery_days' => $result->location_preorder_delivery_days,
            ];

            $resultData = array_merge($resultData, $this->getChain($result->id));
        }
    }


    protected function getChild($childId = 0){
        $result = DB::table('location')
        ->select('location_id as id', 'location_name as label', 'location_parant_id')
        ->where('location_id', $childId)
        ->first();

       return $result;
    }



}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
// use Illuminate\Support\Facades\DB;

class FGListController extends Controller
{
    public function index(Request $request){

    //     $tag = $request->fg_tag_url;
    //  return  response()->json($tag);

    

    /*

        $show = DB::table('fg')
        ->where('fg_discount', '>', $request->check)
        ->select('fg_discount', 'fg_attribute_id')
        ->take(5)->get();

        return  response()->json($show);
*/


$tag = DB::table('fg_tag')
            ->select('fg_tag_id')
            ->where('fg_tag_url', $request->fg_tag_url)
            ->where('fg_tag_status', 1)
            ->first();
            // return  response()->json($tag);
            // dd(empty($tag));
            
        if (empty($tag)) {
            return response()->json([
                'status' => 200,
                'error' => false,
                'message' => 'No Data Found',
                'data' => [],
            ]);
        }

        $data = DB::table('fg')
            ->select(
                'acc_ledger_name',
                'acc_ledger_name_bn',
                'fg_discount',
                'fg_discount_end_date',
                'fg_discount_start_date',
                'fg_vat_tax',
                'fg_gender',
                'fg_image',
                'fg_up',
                'fg_url',
                'fg_featured',
                'fg_rating',
                'fg_view',
                'fg.fg_brand_id',
                'fg_brand_name',
                'fg_brand_logo',
                'fg.fg_type_id',
                'fg_type_name',
                'fg_type_url',
                'fg_category_name',
                'fg_category_url'
            )
            ->where('fg_status', 'p')
            ->where('fg_ecom_listing', 1)
            ->where('fg_creation_type', 'fg')
            // ->where('FIND_IN_SET(' . $tag->fg_tag_id . ', fg_tag_id) > 0')
            ->whereRaw('FIND_IN_SET(?, fg_tag_id) > 0',[$tag->fg_tag_id])
            ->orderByDesc('fg_view')
            ->orderByDesc('fg_sale_history')
            ->leftJoin('fg_category', 'fg_category.fg_category_id', '=', 'fg.fg_category_id')
            ->leftJoin('fg_brand', 'fg_brand.fg_brand_id', '=', 'fg.fg_brand_id')
            ->leftJoin('fg_type', 'fg_type.fg_type_id', '=', 'fg.fg_type_id')
            ->limit($request->limit, $request->start)
            ->get();
// dd($data);
        return response()->json($data);
  

   
   


    }
}

<?php

namespace App\Traits;

use Carbon\Carbon;
use Illuminate\Support\Facades\Response;


/*
|--------------------------------------------------------------------------
| Api Responser Trait
|--------------------------------------------------------------------------
|
| This trait will be used for any response we sent to clients.
|
*/

trait ApiResponser
{
	/**
     * Return a success JSON response.
     *
     * @param  array|string  $data
     * @param  string  $message
     * @param  int|null  $code
     * @return \Illuminate\Http\JsonResponse
     */
	protected function success($data, string $message = null, int $code = 200)
	{
		$response = [
            'is_success' => true,
            'message' => $message,
        ];
        
        if (!is_null($data)) {
            $response['data'] = $data;
        }
        
        return response()->json($response, $code);
	}
	
	protected function paginate_success($data, string $message = null, int $code = 200, array $extra = [])
	{
		
		$response = [
            'is_success' => true,
            'message' => $message,
            'data' => $data->items(), // Retrieve the paginated items
            'pagination' => [
                'current_page' => $data->currentPage(),
                'first_page_url' => $data->url(1),
                'from' => $data->firstItem(),
                'last_page' => $data->lastPage(),
                'last_page_url' => $data->url($data->lastPage()),
                'next_page_url' => $data->nextPageUrl(),
                'path' => $data->url($data->currentPage()),
                'per_page' => $data->perPage(),
                'prev_page_url' => $data->previousPageUrl(),
                'to' => $data->lastItem(),
                'total' => $data->total(),
            ],
        ];
        
        if (!empty($extra)) {
            $response = array_merge($response, $extra);
        }

        
        return response()->json($response, $code);
	}
	
	protected function paginate_success_with_count($data, $count1,$count2,string $message = null, int $code = 200)
	{
		
		$response = [
            'is_success' => true,
            'message' => $message,
            'no_of_clients' => $count1,
            'total_collection' => $count2,
            'data' => $data->items(), // Retrieve the paginated items
            'pagination' => [
                'current_page' => $data->currentPage(),
                'first_page_url' => $data->url(1),
                'from' => $data->firstItem(),
                'last_page' => $data->lastPage(),
                'last_page_url' => $data->url($data->lastPage()),
                'next_page_url' => $data->nextPageUrl(),
                'path' => $data->url($data->currentPage()),
                'per_page' => $data->perPage(),
                'prev_page_url' => $data->previousPageUrl(),
                'to' => $data->lastItem(),
                'total' => $data->total(),
            ],
        ];

        
        return response()->json($response, $code);
	}
	
	protected function paginate_success_list($data, string $message = null, int $total, int $perPage, int $currentPage)
{
    return response()->json([
        'is_success' => true,
        'message' => $message,
        'data' => $data,
        'pagination' => [
            'total' => $total,
            'per_page' => $perPage,
            'current_page' => $currentPage,
            'last_page' => ceil($total / $perPage),
            'next_page_url' => ($currentPage < ceil($total / $perPage)) ? url()->current() . '?page=' . ($currentPage + 1) : null,
            'prev_page_url' => ($currentPage > 1) ? url()->current() . '?page=' . ($currentPage - 1) : null,
        ]
    ]);
}


	/**
     * Return an error JSON response.
     *
     * @param  string  $message
     * @param  int  $code
     * @param  array|string|null  $data
     * @return \Illuminate\Http\JsonResponse
     */
	protected function error(string $message = null, $data = null,  int $code = 200)
	{
		$response = [
            'is_success' => false,
            'message' => $message,
        ];
        
        if (!is_null($data)) {
            $response['data'] = $data;
        }
        
        return response()->json($response, $code);
	}

}

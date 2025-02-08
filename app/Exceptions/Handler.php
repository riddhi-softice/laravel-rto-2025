<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\TooManyRequestsHttpException;
use Illuminate\Support\Facades\DB;
use Throwable;
use Illuminate\Support\Facades\Log;
// use Illuminate\Support\Facades\File;

class Handler extends ExceptionHandler
{
    /*public function register()
    {
        $this->renderable(function (NotFoundHttpException $e) {
            return redirect('/');
        });
        
        
        // Handle 429 TooManyRequestsHttpException and increment counter in DB
        $this->renderable(function (TooManyRequestsHttpException $e) {
           
            // Get the current count of 'app_install_count'
            $data = DB::table('common_settings')
                ->where('setting_key', '=', 'api_fail_count')
                ->pluck('setting_value')
                ->first();
    
            // Increment the counter
            $count = $data + 1;
    
            // Update the counter in the database
            DB::table('common_settings')
                ->where('setting_key', '=', 'api_fail_count')
                ->update(['setting_value' => $count]);
    
            // Return a custom 429 response with the updated counter
            return response()->json([
                'message' => 'Too many requests. Please try again later.',
                'retry_after' => $e->getHeaders()['Retry-After'] ?? 60,  // Retry-After header (default 60 seconds)
                'counter' => $count
            ], 429);
        });
        
        
        // Handle 500 Internal Server Error
        $this->renderable(function (Throwable $e) {
            // Optionally log the error or perform other actions here
    
            // You can also update the counter if needed for 500 errors
            $data = DB::table('common_settings')
                ->where('setting_key', '=', 'app_install_count')
                ->pluck('setting_value')
                ->first();
    
            // Increment the counter
            $count = $data + 1;
    
            // Update the counter in the database
            DB::table('common_settings')
                ->where('setting_key', '=', 'app_install_count')
                ->update(['setting_value' => $count]);
    
            // Return a generic error response
            return response()->json([
                'message' => 'An unexpected error occurred. Please try again later.',
                'counter' => $count
            ], 500);
        });
        
    }*/
    
    
    public function register()
    {
        // Handle 404 NotFoundHttpException and redirect to home page
        $this->renderable(function (NotFoundHttpException $e) {
             return redirect('https://rtovehicleinfo.com');
        });
    
        // Handle various exceptions, including TooManyRequestsHttpException
        $this->renderable(function (Throwable $e) {
            // Check if the exception is a TooManyRequestsHttpException
            if ($e instanceof TooManyRequestsHttpException) {
                // Increment the counter for too many requests
                return $this->handleTooManyRequests($e);
            }
    
            // Check if it's another exception you want to handle
            // (You can add other specific checks here if needed)
    
            // For all other exceptions, you can handle them generally
            // return $this->handleGeneralError($e);
        });
    }

    protected function handleTooManyRequests(TooManyRequestsHttpException $e)
    {
        // Get the current count of 'api_fail_count'
        $data = DB::table('common_settings')
            ->where('setting_key', '=', 'api_fail_count')
            ->pluck('setting_value')
            ->first();
    
        // Increment the counter
        $count = $data + 1;
    
        // Update the counter in the database
        DB::table('common_settings')
            ->where('setting_key', '=', 'api_fail_count')
            ->update(['setting_value' => $count]);
    
        // Return a custom response for too many requests
        return response()->json([
            'message' => 'Too many requests. Please try again later.',
            'retry_after' => $e->getHeaders()['Retry-After'] ?? 60, // Default to 60 seconds
            'counter' => $count
        ], 429);
    }
        
        
    
    protected function handleGeneralError(Throwable $e)
    {
        // Log the error for debugging (optional)
      
    
        // Optionally, increment the counter for general errors as well
        $data = DB::table('common_settings')
            ->where('setting_key', '=', 'api_fail_count')
            ->pluck('setting_value')
            ->first();
    
        // Increment the counter
        $count = $data + 1;
    
        // Update the counter in the database
        DB::table('common_settings')
            ->where('setting_key', '=', 'api_fail_count')
            ->update(['setting_value' => $count]);

        // Log::error('Internal Server Error: ' . $e->getMessage(), [
        //     'counter ==' => $count,
        //     'exception' => $e
        // ]);
    
        // Return a generic error response
        return response()->json([
            'message' => 'An unexpected error occurred. Please try again later.',
            'counter' => $count
        ], 500);
    }
}

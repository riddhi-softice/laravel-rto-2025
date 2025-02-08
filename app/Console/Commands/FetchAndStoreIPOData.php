<?php

namespace App\Console\Commands;
use Illuminate\Console\Command;
use App\Models\IPO;
use Illuminate\Support\Facades\Http;
use DB;
use App\Http\Controllers\ApiController; // Replace with the actual class name


class FetchAndStoreIPOData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ipo:fetch-and-store';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

        
    /*public function handle()
    {
        $controller = new ApiController(); // Replace with your controller
        $controller->fetchAndStore();
    }*/
    
    
    public function handle()
    {
        $apiUrl = 'https://api.ipoalerts.in/ipos?status=open';
        $apiKey = '94b1af57ba8e33a26572182d933361eb3f70cc0cc33617d1903db6c69d008db1';
        
        // Fetch data from the API
        $response = Http::withHeaders([
            'x-api-key' => $apiKey,
        ])->get($apiUrl);
        
        // Check for successful response
        if ($response->failed()) {
            $this->error('Failed to fetch data from API');
            return 1; // Non-zero exit code
        }
        $data = $response->json();
        
        // Validate data structure
        if (!isset($data['ipos']) || !is_array($data['ipos'])) {
            $this->error('Invalid API response structure');
            return 1; // Non-zero exit code
        }

        // Process and store IPOs
        foreach ($data['ipos'] as $ipoData) {

           IPO::updateOrCreate(
                    ['ipo_id' => $ipoData['id']], // Unique identifier
                    [
                    'status' => array_key_exists('status', $ipoData) ? $ipoData['status'] : null,
                    'slug' => array_key_exists('slug', $ipoData) ? $ipoData['slug'] : null,
                    'info_url' => array_key_exists('infoUrl', $ipoData) ? $ipoData['infoUrl'] : null,
                    'name' => array_key_exists('name', $ipoData) ? $ipoData['name'] : null,
                    'symbol' => array_key_exists('symbol', $ipoData) ? $ipoData['symbol'] : null,
                    'type' => array_key_exists('type', $ipoData) ? $ipoData['type'] : null,
                    'start_date' => array_key_exists('startDate', $ipoData) ? $ipoData['startDate'] : null,
                    'end_date' => array_key_exists('endDate', $ipoData) ? $ipoData['endDate'] : null,
                    'listing_date' => array_key_exists('listingDate', $ipoData) ? $ipoData['listingDate'] : null,
                    'price_range' => array_key_exists('priceRange', $ipoData) ? $ipoData['priceRange'] : null,
                    'min_qty' => array_key_exists('minQty', $ipoData) ? $ipoData['minQty'] : null,
                    'logo' => array_key_exists('logo', $ipoData) ? $ipoData['logo'] : null,
                    'min_amount' => array_key_exists('minAmount', $ipoData) ? $ipoData['minAmount'] : null,
                    'issue_size' => array_key_exists('issueSize', $ipoData) ? $ipoData['issueSize'] : null,
                    'prospectus_url' => array_key_exists('prospectusUrl', $ipoData) ? $ipoData['prospectusUrl'] : null,
                ]
            );
            //DB::table('ipos')->insert($data);
        }
    }

}

<?php

namespace App\Http\Controllers\GeoLocation;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Config;


class GeoLocationController extends Controller
{

    //Get Geo Location
    public function get(Request $request) {
        
        // $ip_address = '119.160.71.57';
        $ip_address = $request->get('client_ip');
        if($ip_address == null){
            $ip_address = $request->ip();
        }
        
        try {
            $query = $request->get('query');
            $response = self::getQueryBasedService($query, $ip_address);            
            return $response;

        } catch (\Exception $ex) {
            return response(['status' => false, 'message' => $ex->getMessage() ], 500);
        }
    }

    private static function getQueryBasedService($key, $ip_address){
        
        $client = new Client();
        $message = ''; $geo = [];
        switch ($key) {

            case 'ip-api':
                try{
                    $base_url = Config::get('constants.IP_API_URL');
                    $url = $base_url.$ip_address;            
                    $request = $client->get($url);
                    $response = $request->getBody();
                    $response =  unserialize($response);
                    // return $response;
                    if(isset($response['status']) && $response['status']  == 'success'){
                        $geo = [
                            'service' => $key,
                            'city' => $response['city'],
                            'region' => $response['region'],
                            'country' => $response['country'],
                        ];
                        return ['ip' => $ip_address, 'geo' => $geo];
                    }
                }catch(\Exception $ex){
                    return ['ip' => $ip_address, 'geo' => $ex->getMessage()];
                }
                break;
            case 'freegeoip':

                try{

                    $base_url = Config::get('constants.GEO_IP_API_URL');
                    $api_key = Config::get('constants.GEO_API_KEY');
                    $url = $base_url.$ip_address.'?access_key='.$api_key;
                    $request = $client->get($url);
                    $response = $request->getBody();
                    $response =  json_decode($response, true);
                    // return $response;
                    if(!(isset($response['success']) && $response['success']  == false)){
                        $geo = [
                            'service' => $key,
                            'city' => $response['city'],
                            'region' => $response['region_name'],
                            'country' => $response['country_name'],
                        ];
                        return ['ip' => $ip_address, 'geo' => $geo];
                    }
                    
                }catch(\Exception $ex){
                    return ['ip' => $ip_address, 'geo' => $ex->getMessage()];
                }
                break;
            default:

                try{
                    $base_url = Config::get('constants.IP_API_URL');
                    $url = $base_url.$ip_address;            
                    $request = $client->get($url);
                    $response = $request->getBody();
                    $response =  unserialize($response);
                    if(isset($response['status']) && $response['status']  == 'success'){
                        $geo = [
                            'service' => $key,
                            'city' => $response['city'],
                            'region' => $response['region'],
                            'country' => $response['country'],
                        ];
                        return ['ip' => $ip_address, 'geo' => $geo];
                    }
                }catch(\Exception $ex){
                    return ['ip' => $ip_address, 'geo' => $ex->getMessage()];
                }
        }

        // // dd($service);
        // $client = new Client();
        // $message = '';
        // $geo = [];
        // if($service == 'ip-api'){
        //     $base_url = Config::get('constants.IP_API_URL');
        //     $url = $base_url.$ip_address;            
        //     $request = $client->get($url);
        //     $response = $request->getBody();
        //     $response =  unserialize($response);
        //     if(isset($response['status']) && $response['status']  == 'success'){
        //         $geo = [
        //             'service' => $service,
        //             'city' => $response['city'],
        //             'region' => $response['region'],
        //             'country' => $response['country'],
        //         ];
        //         return ['ip' => $ip_address, 'geo' => $geo];
        //     }else{
        //         return ['ip' => $ip_address, 'geo' => $geo];
        //     }
            
            
        // }
        

        // if($service == 'freegeoip'){

        //     $base_url = Config::get('constants.GEO_IP_API_URL');
        //     $api_key = Config::get('constants.GEO_API_KEY');
        //     $url = $base_url.$ip_address.'?access_key='.$api_key;
        //     // dd($url);
        //     $request = $client->get($url);
        //     $response = $request->getBody();
        //     $response =  json_decode($response, true);
            
        //     if(isset($response['success']) && $response['success']  == true){
        //         $geo = [
        //             'service' => $geo_service,
        //             'city' => $response['city'],
        //             'region' => $response['region'],
        //             'country' => $response['country'],
        //         ];
        //         return ['ip' => $ip_address, 'geo' => $geo];
        //     }else{
        //         $message = $response['error']['info'];
        //         return ['ip' => $ip_address, 'geo' => $message];
        //     }
            
        // }
        
    }
}

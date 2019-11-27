<?php

namespace App\Http\Controllers\LocationWeather;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Config;


class LocationWeatherController extends Controller
{

    //Get Geo Location Weather
    public function get(Request $request) {
        
        // $ip_address = '119.160.65.120';
        $ip_address = $request->get('client_ip');
        if($ip_address == null){
            $ip_address = $request->ip();
        }
        
        try {
            $query = $request->get('query');
            $response = self::getQueryBasedService($query, $ip_address);   
            // dd($response);
            if((isset($response['lat']) && isset($response['lon'])) || (isset($response['latitude']) && isset($response['longitude']))) {
                $lat = isset($response['lat']) ? $response['lat'] : $response['latitude'];
                $lon = isset($response['lon']) ? $response['lon'] : $response['longitude'];
                try{
                    $base_url = Config::get('constants.WEATHER_API');
                    $appid = Config::get('constants.WEATHER_APP_ID');
                    $url = $base_url."?lat=".$lat."&lon=".$lon."&appid=".$appid;
                    // var_dump($url);exit;
                    $client = new Client();
                    $request = $client->get($url);
                    $response = $request->getBody();
                    $response = json_decode($response, true);
                    // dd($response);
                    $data_resp = [];
                    if(isset($response) && count($response)){
                        $data_resp['ip'] = $ip_address;
                        $data_resp['city'] = $response['name'];
                        if(isset($response['main'])){
                            $data_resp['temprature'] = array(
                                'current' => $response['main']['temp'],
                                'low' => $response['main']['temp_min'],
                                'high' => $response['main']['temp_max'],
                            );
                        }
                        if(isset($response['wind'])){
                            $data_resp['wind'] = array(
                                'speed' => $response['wind']['speed'],
                                'direction' => $response['wind']['deg']
                            );
                        }
                        return $data_resp;
                    }
                }catch(\Exception $ex){
                    return $ex->getMessage();
                }
                
            } 
            return 'Sorry! Failed to find weather info.';exit;

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
                    if(isset($response['status']) && $response['status']  == 'success'){
                        return $response;
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
                        return $response;
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
                        return $response;
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

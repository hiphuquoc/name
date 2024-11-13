<?php

namespace App\Helpers;

use GeoIp2\Exception\AddressNotFoundException;

class GeoIP {

    public static function getLocation(){
        $reader = app('geoip'); // Lấy instance từ container
        $ip = request()->ip();

        // Kiểm tra nếu đang chạy trên localhost, gán IP public để test
        if ($ip === '127.0.0.1') {
            $ip = '81.2.69.142'; // Dùng IP để test
        }

        try {
            $record = $reader->city($ip);

            return [
                'ip'         => $ip,
                'country'    => $record->country->name,
                'iso_code'   => $record->country->isoCode,
                'continent'  => $record->continent->name,
                'region'     => $record->mostSpecificSubdivision->name,  // Lấy tên vùng (region)
                'region_code'=> $record->mostSpecificSubdivision->isoCode // Lấy mã vùng (region code)
            ];
        } catch (AddressNotFoundException $e) {
            return ['error' => 'IP not found in database'];
        } catch (\Exception $e) {
            return ['error' => 'Unable to determine location'];
        }
    }

}
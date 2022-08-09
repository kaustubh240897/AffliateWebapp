<?php

namespace App\Imports;
use Carbon\Carbon;
use App\Models\Coupon;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class CouponsImport implements ToModel
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new Coupon([
            "id" => $row[0],
            "title" => $row[1],
            "merchant" => $row[2],
            "categories" => $row[3],
            "description" => $row[4],
            "terms" => $row[5],
            "couponCode" => $row[6],
            "URL" => $row[7],
            "status" => $row[8],
            "startDate" => Carbon::parse($row[9])->format('Y-m-d'),
            "endDate" => Carbon::parse($row[10])->format('Y-m-d'),
            "offerAddedAt" => $row[11],
            "imageURL" => $row[12],
            "campaignID" => $row[13],
            "campaignName" => $row[14]
            //
        ]);
    }
}

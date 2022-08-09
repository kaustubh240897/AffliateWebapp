<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    use HasFactory;
    
    protected $fillable = ["id","title","merchant","categories","description","terms","couponCode","URL","status","startDate","endDate","offerAddedAt","imageURL","campaignID","campaignName"];
}


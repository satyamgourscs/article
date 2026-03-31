<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BankDetail;

class BankController extends Controller{
public function save(Request $request){BankDetail::updateOrCreate(["buyer_id"=>auth()->id()],["account_holder_name"=>$request->account_holder_name,"account_number"=>$request->account_number,"bank_name"=>$request->bank_name,"ifsc"=>$request->ifsc,"upi_id"=>$request->upi_id]);return back()->with("success","Bank details saved");}}

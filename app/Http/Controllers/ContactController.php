<?php

namespace App\Http\Controllers;
use App\Models\Contact;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    //
    public function getContact(Request $request){
        return view('contact');
    }

    public function storeContact(Request $request){
        $request->validate([
            'email' => 'required',
        ]);
        $contact = new Contact;
        $contact->name = $request->name;
        $contact->email = $request->email;
        $contact->subject = $request->subject;
        $contact->description = $request->description;
        $contact->save();
         
        
        return redirect(route('index'))->with('successMsg','your info Successfully added');

    }

}

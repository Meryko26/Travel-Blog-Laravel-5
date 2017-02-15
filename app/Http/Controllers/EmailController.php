<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Http\RedirectResponse;
use Mail;

class EmailController extends Controller
{
	public function send(Request $request)
	{
        $this->validate($request, [
            'name' => 'required|max:40',
            'email' => 'required|email|max:40',
            'subject' => 'required|max:40',
            'message' => 'required|max:200'
        ]); 

        $data = array(
			'name' => $request->name,
			'message' => $request->message
        );

		Mail::send(
			'menu.top_nav.email',
			$data, 
			function($message) use ($data) {
				$message->from( $request->email );
				$message->to('ruslan_aliyev_@hotmail.com', 'Admin');
				$message->subject( $request->subject );
			}
		);

        \Session::flash('success', 'Email Sent');

		return Redirect::to('/contact');
	}
}

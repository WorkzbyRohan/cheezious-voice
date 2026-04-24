<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class VapiController extends Controller
{
    public function index()
    {
        return view('vapi.assistant', [
            'vapiKey'     => env('VAPI_PUBLIC_KEY'),
            'assistantId' => env('VAPI_ASSISTANT_ID'),
        ]);
    }

    public function webhook(Request $request)
    {
        $payload = $request->all();
        \Log::info('VAPI Webhook:', $payload);

        $type = $payload['type'] ?? '';

        if ($type === 'function-call') {
            $fn   = $payload['functionCall']['name']       ?? '';
            $args = $payload['functionCall']['parameters'] ?? [];

            if ($fn === 'getMenu') {
                return response()->json([
                    'result' => 'Menu: Crown Crust Pizza, Bazinga Burger, Crunchy Chicken Pasta, Hot Wings, Cheesy Fries. Call 042-111-446-699 to order!'
                ]);
            }

            if ($fn === 'getDeals') {
                return response()->json([
                    'result' => 'Current deals: Happy Hour 20% off 5-7 PM daily. Weekday lunch specials Mon-Fri 12-3 PM. Download the Cheezious app for exclusive deals!'
                ]);
            }
        }

        return response()->json(['status' => 'ok']);
    }
}

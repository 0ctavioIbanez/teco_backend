<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Card;

class CardController extends Controller
{
    public function create(Request $request) {
        return response(Card::create($request));
    }

    public function get() {
        return response(Card::getCards());
    }

    public function remove(Request $request) {
        return response(Card::remove($request->id));
    }

    public function update(Request $request) {
        return response(Card::updateCard($request));
    }
}

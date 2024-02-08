<?php

namespace App\Http\Controllers\Question;

use App\Http\Controllers\Controller;
use App\Http\Requests\Question\StoreRequest;
use App\Http\Resources\QuestionResource;
use Illuminate\Http\Request;

class StoreController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(StoreRequest $request)
    {
        $question = user()->questions()
            ->create([
                'question' => $request->question,
                'status'   => 'draft',
                'user_id'  => auth()->user()->id,
            ]);

        return QuestionResource::make($question);
    }
}

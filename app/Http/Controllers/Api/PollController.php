<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Choice;
use App\Models\Option;
use App\Models\Poll;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class PollController extends Controller
{
    public function createPoll(Request $request)
    {
        $this->validate($request, [
            'question' => 'required|string',
            'options' => 'required|array',
            'options.*' => 'required|string'
        ]);
        $data = $request->only('question');
        $data['user_id'] = auth()->id();
        $data['count'] = 0;
        $poll = Poll::query()->create($data);
        $options = [];
        foreach ($request->options as $option) {
            $options[] = ['poll_id' => $poll->id, 'option' => $option];
        }
        Option::query()->insert($options);
        return response()->json(['message' => 'success', 'items' => $poll]);
    }

    public function getPoll($poll)
    {
        $poll = Poll::query()->with('options')->find($poll);
        return response()->json(['message' => 'success', 'items' => $poll]);
    }

    public function takePoll(Request $request)
    {
        $validator = Validator::make($request->all(), [
                'poll_id' => ['required', 'exists:polls,id', Rule::unique('choices')->where(function ($query) {
                    return $query->where('user_id', auth()->id());
                })],
                'option_id' => ['required', Rule::exists('options', 'id')->where(function ($query) use ($request) {
                    return $query->where('poll_id', $request->poll_id);
                })]

            ]
        );
        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()->first(), 'items' => $validator->errors()->messages()]);
        }

        Choice::query()->create(['option_id' => $request->option_id, 'poll_id' => $request->poll_id, 'user_id' => auth()->id()]);

        $poll = Poll::query()->find($request->poll_id);
        $poll->update(['count' => $poll->count + 1]);

        $option = Option::query()->find($request->option_id);
        $option->update(['count' => $option->count + 1]);

        return response()->json(['message' => 'success', 'items' => []]);
    }

    public function listPolls(Request $request)
    {
        $polls = Poll::query()->orderByDesc('id')->get();
        return response()->json(['message' => 'success', 'items' => $polls]);
    }

    public function listOwnerPolls(Request $request)
    {
        $polls = Poll::query()->where('user_id', auth()->id())->orderByDesc('id')->get();
        return response()->json(['message' => 'success', 'items' => $polls]);
    }
}

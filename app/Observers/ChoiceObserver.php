<?php

namespace App\Observers;

use App\Models\Choice;
use App\Models\Option;
use App\Models\Poll;

class ChoiceObserver
{
    /**
     * Handle the Choice "created" event.
     *
     * @param  \App\Models\Choice  $choice
     * @return void
     */
    public function created(Choice $choice)
    {
        $poll = Poll::query()->find($choice->poll_id);
        $poll->update(['count' => $poll->count + 1]);

        $option = Option::query()->find($choice->option_id);
        $option->update(['count' => $option->count + 1]);

    }

    /**
     * Handle the Choice "updated" event.
     *
     * @param  \App\Models\Choice  $choice
     * @return void
     */
    public function updated(Choice $choice)
    {
        //
    }

    /**
     * Handle the Choice "deleted" event.
     *
     * @param  \App\Models\Choice  $choice
     * @return void
     */
    public function deleted(Choice $choice)
    {
        //
    }

    /**
     * Handle the Choice "restored" event.
     *
     * @param  \App\Models\Choice  $choice
     * @return void
     */
    public function restored(Choice $choice)
    {
        //
    }

    /**
     * Handle the Choice "force deleted" event.
     *
     * @param  \App\Models\Choice  $choice
     * @return void
     */
    public function forceDeleted(Choice $choice)
    {
        //
    }
}

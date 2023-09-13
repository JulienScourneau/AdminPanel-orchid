<?php

namespace App\Orchid\Screens;

use App\Models\Event;
use Illuminate\Http\Request;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\ModalToggle;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Screen;
use Orchid\Screen\TD;
use Orchid\Support\Facades\Layout;

class EventScreen extends Screen
{
    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public function query(): iterable
    {
        return [
            'events' => Event::latest()->get(),

        ];
    }

    /**
     * The name of the screen displayed in the header.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return 'EventScreen';
    }

    /**
     * The screen's action buttons.
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): iterable
    {
        return [
            ModalToggle::make('Add Event')
                ->modal('eventModal')
                ->method('create')
                ->icon('plus'),

        ];
    }

    /**
     * The screen's layout elements.
     *
     * @return \Orchid\Screen\Layout[]|string[]
     */
    public function layout(): iterable
    {
        return [
            Layout::table('events', [
                TD::make('name'),
                TD::make('uri'),
                TD::make('begin_at'),
                TD::make('end_at'),
                TD::make('price'),
                TD::make('Actions')
                    ->alignRight()
                    ->render(function (Event $event) {
                        return Button::make('Delete Event')
                            ->confirm('After deleting, the event will be gone forever')
                            ->method('delete', ['event' => $event->id]);
                    })
            ]),
            Layout::modal('eventModal', Layout::rows([
                Input::make('event.name')
                    ->title('Name')
                    ->placeholder('Enter event name')
                    ->help('The name of the event to be created'),
                Input::make('event.uri')
                    ->title('uri'),
                Input::make('event.price')
                    ->title('price'),
            ]))
                ->title('Create Event')
                ->applyButton('Add Event'),
        ];
    }

    public function create(Request $request)
    {
        $request->validate([
            'event.name' => 'required',

        ]);
        $event = new Event();
        $event->name = $request->input('event.name');
        $event->save();
    }

    public function delete(Event $event)
    {
        $event->delete();
    }
}

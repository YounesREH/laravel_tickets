<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class EventController extends Controller
{
    
    public function index()
    {
        $events = Event::with('organizer','ticketTypes')
        ->where('is_active',true)->latest()->get();

        return response()->json([
            'events'=>$events
        ]);
    }

    public function store(Request $request)
    {
        $validation = $request->validate([
            'title' => 'required|string|max:255',
            'description'=>'nullable|string',
            'location' => 'required|string|max:255',
            'starts_at'=> 'require|date',
            'ends_at' => 'required|date|after:starts_at',
            'image' => 'nullable|string|max:255',
            'is_active' => 'sometimes|boolean',
        ]);
        $event = $request->user()->enents()->create($validation);
        
        return response()->json([
            'message' => 'Event created successfully',
            'event'=>$event,
        ],201);
    }

    
    public function show(Event $event)
    {
        $event->load('organizer','ticketTypes');
        return response()->json([
            'event'=>$event
        ]);
    }

    
    public function update(Request $request, Event $event)
    {
        if($event->organizer_id !== $request->user()->id)
        {
            return response()->json([
                'message'=>'You are not allowed to update this event'
            ],403);
        }
        $validation = $request->update([
            'title' => 'required|string|max:255',
            'description'=>'nullable|string',
            'location' => 'required|string|max:255',
            'starts_at'=> 'require|date',
            'ends_at' => 'required|date|after:starts_at',
            'image' => 'nullable|string|max:255',
            'is_active' => 'sometimes|boolean',
        ]);
        $event->update($validation);
        return response()->json([
            'message' => 'Event updated successfully',
            'event'=>$event,
        ],201);

    }

    
    public function destroy(string $id, Event $event)
    {
        return response()->json([
            'message'=>'You are not allowed to delete this event'
        ],403);
        $event->delete();
        return response()->json([
            'message'=>'event event deleted succefully'
        ]);
    }
}

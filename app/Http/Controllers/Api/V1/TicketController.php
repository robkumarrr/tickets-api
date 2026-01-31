<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Filters\V1\TicketFilter;
use App\Http\Requests\Api\V1\Tickets\ReplaceTicketRequest;
use App\Http\Requests\Api\V1\Tickets\StoreTicketRequest;
use App\Http\Requests\Api\V1\Tickets\UpdateTicketRequest;
use App\Http\Resources\V1\TicketResource;
use App\Models\Ticket;
use App\Models\User;
use App\Policies\V1\TicketPolicy;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;

class TicketController extends ApiController
{
    protected $policyClass = TicketPolicy::class;

    /**
     * Display a listing of the resource.
     */
    public function index(TicketFilter $filters)
    {
        return TicketResource::collection(Ticket::filter($filters)->paginate());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTicketRequest $request)
    {
        $author_id = $request->input('data.relationships.author.data.id');
        try {
            $user = User::findOrFail($author_id);
            Gate::authorize('store', $user);
        } catch (ModelNotFoundException $exception) {
            Log::error('User cannot be found for ID: ' . $author_id);
            return $this->ok('User cannot be found for ID: ' . $author_id, [
                'error' => 'The provided user id does not exist.'
            ]);
        }

        return new TicketResource(Ticket::create($request->mappedAttributes()));
    }

    /**
     * Display the specified resource.
     */
    public function show($ticket_id)
    {
        try {
            $ticket = Ticket::findOrFail($ticket_id);

            if ($this->include('author')) {
                return new TicketResource($ticket->load('user'));
            }

            return new TicketResource($ticket);
        } catch (ModelNotFoundException $exception) {
            return $this->error('Ticket cannot be found', 404);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTicketRequest $request, $ticket_id)
    {
        try {
            $ticket = Ticket::findOrFail($ticket_id);

            Gate::authorize('update', $ticket);

        } catch (ModelNotFoundException $exception) {
            Log::error('Ticket cannot be found.', ['ticket' => $ticket_id]);
            return $this->error('Ticket cannot be found.', 404);
        } catch (AuthorizationException $exception) {
            Log::error('You are not authorized to update this ticket.', [
                'ticket' => $ticket_id,
                'user' => $request->user()
            ]);
            return $this->error('You are not authorized to update this ticket.', 403);
        }

        $ticket->update($request->mappedAttributes());

        return new TicketResource($ticket);
    }

    public function replace(ReplaceTicketRequest $request, $ticket_id)
    {
        try {
            $ticket = Ticket::findOrFail($ticket_id);
            Gate::authorize('replace', $ticket);
        } catch (ModelNotFoundException $exception) {
            return $this->error('Ticket cannot be found', 404);
        }

        $ticket->update($request->mappedAttributes());

        return new TicketResource($ticket);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($ticket_id)
    {
        try {
            $ticket = Ticket::findOrFail($ticket_id);
            Gate::authorize('destroy', $ticket);
            $ticket->delete();

            return $this->ok('Ticket successfully deleted.', [
                'ticket_id' => $ticket_id
            ]);
        } catch (ModelNotFoundException $exception) {
            return $this->error('Ticket cannot be found', 404);
        }
    }
}

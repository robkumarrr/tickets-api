<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Filters\V1\TicketFilter;
use App\Http\Requests\Api\V1\AuthorTickets\ReplaceAuthorTicketRequest;
use App\Http\Requests\Api\V1\AuthorTickets\StoreAuthorTicketRequest;
use App\Http\Resources\V1\TicketResource;
use App\Models\Ticket;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Response;

class AuthorTicketsController extends ApiController
{
    public function index($authorId, TicketFilter $filters)
    {
        return TicketResource::collection(
            Ticket::where('user_id', $authorId)
                ->filter($filters)
                ->paginate()
        );
    }

    public function store($author_id, StoreAuthorTicketRequest $request)
    {
        $model = [
            'title' => $request->input('data.attributes.title'),
            'description' => $request->input('data.attributes.description'),
            'status' => $request->input('data.attributes.status'),
            'user_id' => $author_id
        ];

        return new TicketResource(Ticket::create($model));
    }

    public function update(ReplaceAuthorTicketRequest $request, $author_id, $ticket_id)
    {
        try {
            $ticket = Ticket::findOrFail($ticket_id);

            if ($ticket->user_id == $author_id){
                $model = [
                    'title' => $request->input('data.attributes.title'),
                    'description' => $request->input('data.attributes.description'),
                    'status' => $request->input('data.attributes.status'),
                    'user_id' => $author_id
                ];

                $ticket->update($model);

                return new TicketResource($ticket);
            }

        } catch (ModelNotFoundException $exception) {
            return $this->error('Ticket cannot be found', Response::HTTP_NOT_FOUND);
        }
        return $this->error('Ticket cannot be found', Response::HTTP_NOT_FOUND);
    }

    public function replace(ReplaceAuthorTicketRequest $request, $author_id, $ticket_id)
    {
        try {
            $ticket = Ticket::findOrFail($ticket_id);

            if ($ticket->user_id == $author_id){
                $model = [
                    'title' => $request->input('data.attributes.title'),
                    'description' => $request->input('data.attributes.description'),
                    'status' => $request->input('data.attributes.status'),
                    'user_id' => $author_id
                ];

                $ticket->update($model);

                return new TicketResource($ticket);
            }

        } catch (ModelNotFoundException $exception) {
            return $this->error('Ticket cannot be found', Response::HTTP_NOT_FOUND);
        }
        return $this->error('Ticket cannot be found', Response::HTTP_NOT_FOUND);
    }

    public function destroy($author_id, $ticket_id)
    {
        try {
            $ticket = Ticket::findOrFail($ticket_id);

            if ($ticket->user_id != $author_id) {
                return $this->error('Ticket cannot be found', Response::HTTP_NOT_FOUND);
            }

            $ticket->delete();

            return $this->ok('Ticket successfully deleted.', [
                'ticket_id' => $ticket_id
            ]);
        } catch (ModelNotFoundException $exception) {
            return $this->error('Ticket cannot be found', Response::HTTP_NOT_FOUND);
        }
    }
}

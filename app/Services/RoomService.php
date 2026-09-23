<?php

namespace App\Services;

use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class RoomService
{
    public function all(Request $request): LengthAwarePaginator
    {
        $length = $request->input('per_page', 10);
        $search = $request->input('search');

        $query = Room::query()
            ->with('floor:id,name', 'roomType:id,name')
            ->latest('id');

        if ($search && is_string($search)) {
            $query->where(function ($q) use ($search) {
                foreach ((new Room)->getFillable() as $column) {
                    $q->orWhere($column, 'like', "%{$search}%");
                }
            });
        }

        return $query->paginate($length);
    }

    public function store(array $data): Room
    {
        return Room::create($data);
    }

    public function update(Room $room, array $data): Room
    {
        $room->update($data);

        return $room;
    }

    public function delete(Room $room): bool
    {
        return $room->delete();
    }
}

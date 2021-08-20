<?php

namespace App\Repositories;

use App\Models\User;

class UserRepository
{
    private $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     *
     * 取得員工清單
     * @return object
     *
     */
    public function listByFilter(array $filter, array $relation = [])
    {
        $query = $this->user->query();

        if (!empty($filter['id'])) {
            $query->where('id', $filter['id']);
        }

        // 如果有limit就分頁
        return isset($filter['limit']) ? $query->paginate($filter['limit']) : $query->get();
    }

    /**
     *
     * create
     *
     */
    public function create(array $data)
    {
        return $this->user->create($data);
    }

    /**
     * update by id
     */
    public function updateById(int $id, array $data)
    {
        return $this->user->where('id', $id)->update($data);
    }

    /**
     * delete by id
     */
    public function deleteById(int $id)
    {
        return $this->user->where('id', $id)->delete();
    }
}

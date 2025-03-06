<?php
namespace App\Repositories\Eloquent;

use App\Models\Log;
use App\Repositories\Contracts\LogRepositoryInterface;
use Illuminate\Support\Facades\Request;

class LogRepository extends BaseRepository implements LogRepositoryInterface
{
    public function __construct(Log $model)
    {
        parent::__construct($model);
    }

    public function createLog(int $userId, string $action, string $description, array $data = null)
    {
        return $this->model->create([
            'user_id' => $userId,
            'action' => $action,
            'description' => $description,
            'data' => $data,
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
        ]);
    }

    public function getLogsByUser(int $userId)
    {
        return $this->model
            ->where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function getLogsByAction(string $action)
    {
        return $this->model
            ->where('action', $action)
            ->orderBy('created_at', 'desc')
            ->get();
    }
}
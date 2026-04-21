<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class BaseController extends Controller
{
    public function HandleException(Exception $exception): JsonResponse
    {
        $statusCode = 500;
        $response = [];
        $message = "Exception Occurred.!";
        $response['message'] = 'Whoops, An exception prohibits the task from continuing with its execution. Please try again or contact the system administrator';

        if ($exception instanceof ValidationException) {
            $message = "Validation Error.!";
            $response["message"] = "Invalid data provided. Please check the entered data and try again";
            $response["errors"] = $exception->errors();
            $statusCode = 422;
        }

        if ($exception instanceof ModelNotFoundException) {
            $message = "Object Not Found.!";
            $response["message"] = "No result found for the given parameters";
            $statusCode = 404;
        }

        if ($exception instanceof QueryException) {
            $message = "Database Exception Occurred.!";
            $response["message"] = "Whoops, A Database exception prohibits the task from continuing with its execution. Please try again or contact the system administrator";
            $statusCode = 500;
        }

        if (config('app.debug')) {
            $response['exception'] = [
                'class' => get_class($exception),
                'message' => $exception->getMessage(),
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
                'code' => $exception->getCode()
            ];
        }

        $response['status'] = $statusCode;
        $response['success'] = false;

        report($exception);

        return $this->sendError($message, $response, $statusCode);
    }

    public function sendError($message, $data = [], int $code = 404): JsonResponse
    {
        $response = [
            'success' => false,
            'message' => $message,
        ];

        if (!empty($data)) {
            $response['data'] = $data;
        }

        return response()->json($response, $code);
    }

    public function sendResponse($data, $message, int $code = 200): JsonResponse
    {
        $response = [
            'success' => true,
            'data' => $data,
            'message' => $message,
        ];

        return response()->json($response, $code);
    }

    public function paginatedResourceCollection($resource, $modelObject): mixed
    {
        return $resource::collection($modelObject)->response()->getData(true);
    }


    public function updateOrCreateMultiple(
        Model $model,
        array $data,
        string $fkColName = null,
        string $pk = null,
        bool $deleteRemovedItem = true
    ): array {
        $rowIds = [];
        $savedObjects = [];

        foreach ($data as $row) {
            if (empty($pk)) {
                unset($row['id']);
            }

            if (!empty($pk) && !empty($fkColName)) {
                $row[$fkColName] = $pk;
            }

            $rowId = isset($row['id']) ? $row['id'] : null;

            $savedObjects[] = $rowObject = $model->updateOrCreate(['id' => $rowId], $row);
            $rowIds[] = $rowObject->id;
        }


        if ($deleteRemovedItem) {
            $model->where($fkColName, $pk)->whereNotIn('id', collect($savedObjects)->pluck('id'))->delete();
        }

        return $savedObjects;
    }
}

<?php


namespace controllers;


use Illuminate\Support\Facades\Response;
use Illuminate\Pagination\Paginator;
use Symfony\Component\HttpFoundation\Response as IlluminateResponse;

class ApiController extends \BaseController
{

    protected $statusCode = 200;

    /**
     * @return mixed
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }

    /**
     * @param mixed $statusCode
     *
     * @return $this
     */
    public function setStatusCode($statusCode)
    {
        $this->statusCode = $statusCode;

        return $this;
    }


    /**
     * @param       $data    - data to send trough the API
     * @param array $headers - optional headers for the HTTP Response
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function respond($data, $headers = [])
    {
        return Response::json($data, $this->getStatusCode(), $headers);
    }

    /**
     * @param $message
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function respondWithError($message)
    {
        return $this->respond([
            'error' => [
                'message'     => $message,
                'status_code' => $this->getStatusCode()
            ]
        ]);
    }

    public function respondWithPagination(Paginator $items, $data)
    {
        $response = array_merge($data, [
            'paginate' => [
                'total_count'  => $items->getTotal(),
                'total_pages'  => ceil($items->getTotal() / $items->getPerPage()),
                'current_page' => $items->getCurrentPage(),
                'limit'        => $items->getPerPage()
            ]
        ]);

        return $this->respond($response);
    }


    /**
     * @param string $message
     *
     * @return mixed
     */
    public function respondCreated($message = 'Object successfully created')
    {
        return $this
            ->setStatusCode(IlluminateResponse::HTTP_CREATED)
            ->respond([
                'message' => $message
            ]);
    }

    /**
     * @param string $message
     *
     * @return mixed
     */
    public function respondDeleted($message = 'Object successfully deleted')
    {
        return $this
            ->setStatusCode(IlluminateResponse::HTTP_OK)
            ->respond([
                'message' => $message
            ]);
    }


    public function respondNotFound($message = 'Not Found !')
    {
        return $this
            ->setStatusCode(IlluminateResponse::HTTP_NOT_FOUND)
            ->respondWithError($message);
    }

    /**
     * @param string $message
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function respondInternalError($message = 'Internal Error !')
    {
        return $this
            ->setStatusCode(IlluminateResponse::HTTP_INTERNAL_SERVER_ERROR)
            ->respondWithError($message);
    }

    /**
     * @param string $message
     *
     * @return mixed
     */
    public function respondFailedValidation($message = 'Parameters failed validation')
    {
        return $this
            ->setStatusCode(IlluminateResponse::HTTP_BAD_REQUEST)
            ->respondWithError($message);
    }
}
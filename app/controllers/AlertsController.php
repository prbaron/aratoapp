<?php

use Arato\Repositories\AlertRepository;
use controllers\ApiController;
use Illuminate\Support\Facades\Response;
use Arato\Transformers\AlertTransformer;

class AlertsController extends ApiController
{
    protected $alertTransformer;
    protected $alertRepository;

    function __construct(AlertTransformer $alertTransformer, AlertRepository $alertRepository)
    {
        $this->alertTransformer = $alertTransformer;
        $this->alertRepository = $alertRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $alerts = $this->alertRepository->filter(Input::all());

        return $this->respondWithPagination($alerts, [
            'data' => $this->alertTransformer->transformCollection($alerts->all())
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store()
    {
        $isValidAlert = $this->alertRepository->isValidForCreation(Input::all());

        if (!$isValidAlert) {
            return $this->respondFailedValidation();
        }

        $inputs = Input::all();
        $inputs['user_id'] = Auth::user()->id;

        $createdAlert = $this->alertRepository->create($inputs);

        return $this->respondCreated($createdAlert);
    }


    /**
     * Display the specified resource.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $alert = $this->alertRepository->find($id);

        if (!$alert) {
            return $this->respondNotFound('Alert does not exist.');
        }

        return $this->respond([
            'data' => $this->alertTransformer->transform($alert)
        ]);
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function update($id)
    {
        $alert = $this->alertRepository->find($id);

        if (!$alert) {
            return $this->respondNotFound('Alert does not exist.');
        }
        if (Auth::user()->id !== $alert['user_id']) {
            return $this->respondForbidden();
        }

        $isValidAlert = $this->alertRepository->isValidForUpdate(Input::all());

        if (!$isValidAlert) {
            return $this->respondFailedValidation();
        }

        $updatedUser = $this->alertRepository->update($id, Input::all());

        return $this->respond([
            'data' => $updatedUser
        ]);
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $alert = $this->alertRepository->find($id);

        if (!$alert) {
            return $this->respondNotFound('Alert does not exist.');
        }

        if (Auth::user()->id !== $alert['user_id']) {
            return $this->respondForbidden();
        }

        $this->alertRepository->delete($id);

        return $this->respondDeleted('Alert successfully deleted');
    }
}

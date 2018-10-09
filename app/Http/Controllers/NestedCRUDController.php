<?php

namespace App\Http\Controllers;

class NestedCRUDController extends Controller
{
    protected $repository;

    protected $foreign_key;

    protected $results_per_page = 20;

    /**
     * Show all resources.
     *
     * Get a JSON representation of all the registered resource.
     *
     * @Get("/{?page,limit}")
     * @Parameters({
     *      @Parameter("page", description="The page of results to view.", default=1),
     *      @Parameter("limit", description="The amount of results per page.", default=20)
     * })
     */
    public function index($parent_id)
    {
        $per_page = request()->get('limit', $this->results_per_page);

        $query = $this->repository->grid()->where($this->foreign_key, '=', $parent_id);

        $query = $this->scopeIndex($query, $parent_id);

        $paginated_collection = $query->paginate($per_page);

        return $paginated_collection;
    }

    protected function scopeIndex($query, $parent_id)
    {
        return $query;
    }

    /**
     * Show a resource.
     *
     * Get a JSON representation of the requested resource
     *
     * @GET("/{resource_id}")
     * @Parameters({
     *      @Parameter("resource_id", description="The resource id.", required=true, type="integer"),
     * })
     * @Response(200, body={"data": {"id": 1}})
     */
    public function show($parent_id, $resource_id)
    {
        if ($resource = $this->findByParent($parent_id, $resource_id)) {
            return $this->prepareResource($resource);
        }

        throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException('Resource not found');
    }

    /**
     * Create a new resource.
     *
     * @PUT("/")
     * @Transaction({
     *      @Request({}),
     *      @Response(200, body={"data": {"id": 1}}),
     *      @Response(422, body={"message": "There was a problem creating the resource", "errors": {}, "status_code": 422})
     * })
     */
    public function store($parent_id)
    {
        request()->merge([$this->foreign_key => $parent_id]);
        return $this->processForm();
    }

    /**
     * Update a resource.
     *
     * @PUT("/{resource_id}")
     * @Parameters({
     *      @Parameter("resource_id", description="The resource to be updated id.", required=true, type="integer"),
     * })
     * @Transaction({
     *      @Request({}),
     *      @Response(200, body={"data": {"id": 1}}),
     *      @Response(422, body={"message": "There was a problem creating the resource", "errors": {}, "status_code": 422})
     * })
     */
    public function update($parent_id, $resource_id)
    {
        if (!$this->findByParent($parent_id, $resource_id)) {
            throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException('Resource not found');
        }

        request()->merge([$this->foreign_key => $parent_id]);

        return $this->processForm($resource_id);
    }

    /**
     * Delete a resource.
     *
     * @DELETE("/{resource_id}")
     * @Parameters({
     *      @Parameter("resource_id", description="The resource to be deleted id.", required=true, type="integer"),
     * })
     * @Transaction({
     *      @Response(204),
     *      @Response(422, body={"message": "There was a problem deleting the resource", "status_code": 422})
     * })
     */
    public function delete($parent_id, $resource_id)
    {
        if (!$this->findByParent($parent_id, $resource_id)) {
            throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException('Resource not found');
        }

        $success = $this->repository->delete($resource_id);
        if ($success) {
            return response()->json(['message' => 'Resource deleted'], 200);
        }
        return response()->json(['message' => 'There was a problem deleting the resource'], 422);
    }

    /**
     * Processes the form.
     *
     * @param string $mode
     * @param int    $resource_id
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function processForm($resource_id = null)
    {
        $data = $this->getRequestData();

        // Store the resource
        list($messages, $resource) = $this->repository->store($resource_id, $data);

        if ($messages->isEmpty()) {
            return $resource;
        }
        return response()->json(['message' => 'There was a problem deleting the resource', 'messages' => $messages ], 422);
        
    }

    protected function findByParent($parent_id, $resource_id)
    {
        if ($resource = $this->repository->find($resource_id)) {
            if ($resource->{$this->foreign_key} == $parent_id) {
                return $resource;
            }
        }

        return false;
    }

    protected function getRequestData()
    {
        return request()->all();
    }

    protected function prepareResource($resource)
    {
        return $resource;
    }
}

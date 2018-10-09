<?php

namespace App\Http\Controllers;

use Symfony\Component\HttpFoundation\Response;
use Dingo\Api\Routing\Helpers;
use Spatie\Fractalistic\Fractal;
use League\Fractal\Resource\Collection;
use League\Fractal\Manager;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;
use Illuminate\Contracts\Pagination\Paginator as IlluminatePaginator;


class CRUDController extends Controller
{
    protected $repository;

    protected $results_per_page = 50;

    protected $transformer;

    private $fractal;

    public function __construct()
    {
        $this->fractal = new Manager();
    }

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
    public function index()
    {
        $per_page = request()->get('limit', $this->results_per_page);

        $query = $this->repository->grid();

        $query = $this->scopeIndex($query);
    
        $paginated_collection = $query->paginate($per_page);

        $resource = new Collection($paginated_collection->getCollection(), $this->transformer);

        $resource->setPaginator(new IlluminatePaginatorAdapter($paginated_collection));
        $result = $this->fractal->createData($resource)->toArray();
        return response()->json($result, 200);
    }

    protected function scopeIndex($query)
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
     * @Response(HTTP_OK, body={"data": {"id": 1}})
     */
    public function show($resource_id)
    {
        if ($resource = $this->repository->findOneBy($resource_id)) {
            return $this->prepareResource($resource);
        }
        throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException('Resource not found');
    }

    protected function prepareResource($resource)
    {
        return $resource;
    }

    /**
     * Create a new resource.
     *
     * @PUT("/")
     * @Transaction({
     *      @Request({}),
     *      @Response(HTTP_OK (200), body={"data": {"id": 1}}),
     *      @Response(HTTP_UNPROCESSABLE_ENTITY (422), body={"message": "There was a problem creating the resource", "errors": {}, "status_code": HTTP_UNPROCESSABLE_ENTITY (422) })
     * })
     */
    public function store()
    {
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
     *      @Response(HTTP_OK (200), body={"data": {"id": 1}}),
     *      @Response(HTTP_UNPROCESSABLE_ENTITY (422), body={"message": "There was a problem creating the resource", "errors": {}, "status_code": HTTP_UNPROCESSABLE_ENTITY (422)})
     * })
     */
    public function update($id)
    {
        return $this->processForm($id);
    }

    /**
     * Delete a resource.
     *
     * @DELETE("/{resource_id}")
     * @Parameters({
     *      @Parameter("resource_id", description="The resource to be deleted id.", required=true, type="integer"),
     * })
     * @Transaction({
     *      @Response(HTTP_OK (200)),
     *      @Response(HTTP_UNPROCESSABLE_ENTITY (422), body={"message": "There was a problem deleting the resource", "status_code": HTTP_UNPROCESSABLE_ENTITY (422)})
     * })
     */
    public function delete($id)
    {
        $success = $this->repository->delete($id);

        if ($success) {
            return response()->json(['success' => $success, 'message' => 'Resource deleted'], 200);
        }

        return response()->json(['success' => $success, 'message' => 'There was a problem deleting the resource'], 422);
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

        return response()->json([
            'error' => 'There was a problem posting the resource',
            'messages' => $messages,
        ], 422);
    }

    protected function getRequestData()
    {
        return request()->all();
    }
}

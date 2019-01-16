<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\Controller as BaseController;
use App\Http\Resources\Resource;
use Dingo\Api\Routing\Helpers;
use Domain\Query\Query;
use Domain\Query\QueryImp;
use Domain\Query\QueryParams;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpFoundation\Response as HttpResponse;

abstract class EntityController extends BaseController
{
   use Helpers;

   /**
    * HTTP header status code.
    *
    * @var int
    */
   protected $statusCode = 200;

   /**
    * Resource instance.
    *
    * @var \App\Http\Resources\Resource;
    */
   protected $resource;

   /**
    * Illuminate\Http\Request instance.
    *
    * @var Request
    */
   protected $request;

   /**
    * The parameters of the query.
    * @var QueryParams $queryParams
    */
   protected $queryParams;

   /**
    * Number of items displayed at once if not specified.
    * There is no limit if it is 0 or false.
    *
    * @var int|bool
    */
   protected $defaultLimit = false;

   /**
    * Maximum limit that can be set via $_GET['limit'].
    *
    * @var int|bool
    */
   protected $maximumLimit = false;
   /**
    * Do we need to unguard the model before create/update?
    *
    * @var bool
    */
   protected $unguard = false;

   /**
    * Constructor.
    *
    * @param Request $request
    */
   public function __construct(Request $request)
   {
      $this->request = $request;
      $this->resource = $this->resource();
   }

   /**
    * The resource associate to the query.
    * @return Resource
    */
   abstract protected function resource() : Resource;


   /**
    * List of the resource.
    * GET /api/{resource}.
    *
    * @return Response
    */
   public function index()
   {
      $this->queryParams = (new QueryParamsBuilder($this->request, $this->resource))->forFindCollection()->build();
      if ($this->queryParams->hasAllFields()) {
         $this->setStatusCode(HttpResponse::HTTP_OK);
      } else {
         $this->setStatusCode(HttpResponse::HTTP_PARTIAL_CONTENT);
      }
      $items = $this->resource->query()->findCollection($this->queryParams);
      return $this->respondWithCollection($items);
   }

   public function search()
   {
      $this->queryParams = (new QueryParamsBuilder($this->request, $this->resource))->forSearchCollection()->build();
      if ($this->queryParams->hasAllFields()) {
         $this->setStatusCode(HttpResponse::HTTP_OK);
      } else {
         $this->setStatusCode(HttpResponse::HTTP_PARTIAL_CONTENT);
      }
      $items = $this->resource->query()->findCollection($this->queryParams);
      return $this->respondWithCollection($items);
   }

   /**
    * Display the specified resource.
    * GET /api/{resource}/{id}.
    *
    * @param int $id
    *
    * @return Response
    */
   public function show($id)
   {
      $this->queryParams = (new QueryParamsBuilder($this->request, $this->resource))->forFindItem($id)->build();
      if ($this->queryParams->hasAllFields()) {
         $this->setStatusCode(HttpResponse::HTTP_OK);
      } else {
         $this->setStatusCode(HttpResponse::HTTP_PARTIAL_CONTENT);
      }
      $items = $this->resource->query()->findItem($id, $this->queryParams);
      return $this->respondWithItem($items);
   }

   /**
    * Store a newly created resource in storage.
    * POST /api/{resource}.
    *
    * @return Response
    */
   public function store()
   {
      $data = $this->request->json()->get($this->resource->keySingular());

      if (!$data) {
         throw new BadRequestHttpException('Empty data');
      }

      $validator = Validator::make($data, $this->resource->rulesForCreate());
      if ($validator->fails()) {
         throw new BadRequestHttpException('Not validated field : ' . $validator->messages());
      }

      $data = $this->resource->transformBeforeSave($data);
      $this->unguardIfNeeded();

      $item = $this->resource->entity()->create($data);
      return $this->setStatusCode(HttpResponse::HTTP_CREATED)->respondWithItem($item);
   }

   /**
    * Update the specified resource in storage.
    * PUT /api/{resource}/{id}.
    *
    * @param int $id
    *
    * @return Response
    */
   public function update($id)
   {
      $data = $this->request->json()->get($this->resource->keySingular());

      if (!$data) {
         return $this->resource->errorWrongArgs('Empty data');
      }

      $item = $this->resource->query()->findItem($id);
      if (!$item) {
         return $this->resource->errorNotFound();
      }

      $validator = Validator::make($data, $this->resource->rulesForUpdate($item->id));
      if ($validator->fails()) {
         return $this->resource->errorWrongArgs($validator->messages());
      }

      $data = $this->resource->transformBeforeSave($data, $item);
      $this->unguardIfNeeded();

      $item->fill($data);
      $item->save();

      return $this->respondWithItem($item);
   }

   /**
    * Remove the specified resource from storage.
    * DELETE /api/{resource}/{id}.
    *
    * @param int $id
    *
    * @return Response
    */
   public function destroy($id)
   {
      $item = $this->resource->query()->findItem($id);

      if (!$item) {
         return $this->resource->errorNotFound();
      }

      $item->delete();

      return response(null, HttpResponse::HTTP_NO_CONTENT);
   }

   /**
    * Getter for statusCode.
    *
    * @return int
    */
   protected function getStatusCode()
   {
      return $this->statusCode;
   }

   /**
    * Setter for statusCode.
    *
    * @param int $statusCode Value to set
    *
    * @return self
    */
   protected function setStatusCode($statusCode)
   {
      $this->statusCode = $statusCode;

      return $this;
   }

   /**
    * Respond with a given item.
    *
    * @param $item
    * @return mixed
    */
   protected function respondWithItem($item)
   {
      return $this->respondWithArray($this->resource->transformItem($item, $this->queryParams));
   }

   /**
    * Respond with a given collection.
    *
    * @param $collection
    * @return mixed
    */
   protected function respondWithCollection($collection)
   {
      return $this->respondWithArray($this->resource->transformCollection($collection, $this->queryParams));
   }

   /**
    * Respond with a given array of items.
    *
    * @param array $array
    * @param array $headers
    *
    * @return mixed
    */
   protected function respondWithArray(array $array, array $headers = [])
   {
      return response()->json($array, $this->statusCode, $headers);
   }

   /**
    * Response with the current error.
    *
    * @param string $message
    *
    * @return mixed
    */
   protected function respondWithError($message)
   {
      return $this->respondWithArray($this->resource->transformError($this->statusCode, $message));
   }

   /**
    * Unguard eloquent model if needed.
    */
   protected function unguardIfNeeded()
   {
      if ($this->unguard) {
         $this->resource->entity()->unguard();
      }
   }

}

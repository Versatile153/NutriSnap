<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * @OA\Info(
 *     title="NutriSnap API",
 *     version="1.0.0",
 *     description="API for NutriSnap widget to analyze food photos",
 *     @OA\Contact(email="support@bincone.apexjets.org")
 * )
 * @OA\Server(url="https://bincone.apexjets.org/api")
 */
class PhotoAnalysisController extends Controller
{
    /**
     * @OA\Post(
     *     path="/seller/register",
     *     operationId="registerSeller",
     *     tags={"Seller"},
     *     summary="Register a new seller",
     *     description="Creates a new seller account and returns an API token",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email","password","name"},
     *             @OA\Property(property="email", type="string", format="email", example="user@example.com"),
     *             @OA\Property(property="password", type="string", format="password", example="yourpassword"),
     *             @OA\Property(property="name", type="string", example="User Name")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful registration",
     *         @OA\JsonContent(
     *             @OA\Property(property="token", type="string", example="your-api-token")
     *         )
     *     ),
     *     @OA\Response(response=422, description="Validation error")
     * )
     */
    public function register(Request $request)
    {
        // Your registration logic
    }

    /**
     * @OA\Post(
     *     path="/seller/login",
     *     operationId="loginSeller",
     *     tags={"Seller"},
     *     summary="Login a seller",
     *     description="Authenticates a seller and returns an API token",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email","password"},
     *             @OA\Property(property="email", type="string", format="email", example="user@example.com"),
     *             @OA\Property(property="password", type="string", format="password", example="yourpassword")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful login",
     *         @OA\JsonContent(
     *             @OA\Property(property="token", type="string", example="your-api-token")
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unauthorized")
     * )
     */
    public function login(Request $request)
    {
        // Your login logic
    }

    /**
     * @OA\Post(
     *     path="/seller/analyze-photo",
     *     operationId="analyzePhoto",
     *     tags={"Photo Analysis"},
     *     summary="Analyze a food photo",
     *     description="Uploads a food photo for analysis and returns a result ID",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"photo","product_id"},
     *                 @OA\Property(property="photo", type="string", format="binary"),
     *                 @OA\Property(property="product_id", type="string", example="123"),
     *                 @OA\Property(property="meal_id", type="string", example="456")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful analysis",
     *         @OA\JsonContent(
     *             @OA\Property(property="result_id", type="string", example="789")
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unauthorized"),
     *     security={{"bearerAuth": {}}}
     * )
     */
    public function analyzePhoto(Request $request)
    {
        // Your photo analysis logic
    }

    /**
     * @OA\Post(
     *     path="/seller/analyze-leftover",
     *     operationId="analyzeLeftoverPhoto",
     *     tags={"Photo Analysis"},
     *     summary="Analyze a leftover food photo",
     *     description="Uploads a leftover food photo for analysis and returns a result ID",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"photo","product_id"},
     *                 @OA\Property(property="photo", type="string", format="binary"),
     *                 @OA\Property(property="product_id", type="string", example="123"),
     *                 @OA\Property(property="meal_id", type="string", example="456")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful analysis",
     *         @OA\JsonContent(
     *             @OA\Property(property="result_id", type="string", example="789")
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unauthorized"),
     *     security={{"bearerAuth": {}}}
     * )
     */
    public function analyzeLeftoverPhoto(Request $request)
    {
        // Your leftover photo analysis logic
    }

    /**
     * @OA\Get(
     *     path="/seller/analysis-results/{id}",
     *     operationId="getResults",
     *     tags={"Photo Analysis"},
     *     summary="Get analysis results",
     *     description="Retrieves the results of a photo analysis by ID",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful retrieval",
     *         @OA\JsonContent(
     *             @OA\Property(property="results", type="object")
     *         )
     *     ),
     *     @OA\Response(response=404, description="Result not found"),
     *     security={{"bearerAuth": {}}}
     * )
     */
    public function getResults(Request $request, $id)
    {
        // Your result retrieval logic
    }
}

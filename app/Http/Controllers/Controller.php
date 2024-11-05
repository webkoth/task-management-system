<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;

/**
 * @OA\Info(
 *      version="1.0.0",
 *      title="API для управления задачами",
 *      description="RESTful API для управления задачами и пользователями",
 *      @OA\Contact(
 *          email="your_email@example.com"
 *      ),
 *      @OA\License(
 *          name="Apache 2.0",
 *          url="http://www.apache.org/licenses/LICENSE-2.0.html"
 *      )
 * )
 */
abstract class Controller
{
    use AuthorizesRequests, ValidatesRequests;
}

<?php

namespace App\Http\Controllers;

use App\Models\Student;
use Illuminate\Http\JsonResponse;


class StudentController extends Controller
{

     public function index(): JsonResponse{
        // return response()->json(data:['students'=>Student::all()]);
        return response()->json(data:['students'=>'great']);
    }
}

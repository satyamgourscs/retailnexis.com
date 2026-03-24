<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;

class DemoAutoUpdateController extends Controller
{
    public function fetchDataGeneral(): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => 'Auto-update endpoint not configured on this installation.',
        ], 200);
    }

    public function fetchDataForAutoUpgrade(): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => 'Auto-upgrade endpoint not configured on this installation.',
        ], 200);
    }

    public function fetchDataForBugs(): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => 'Bug-fix feed endpoint not configured on this installation.',
        ], 200);
    }
}


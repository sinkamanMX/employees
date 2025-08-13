<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Employee;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function index()
    {
        return view('dashboard.index');
    }

    public function storeEmployee(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'education' => 'required|string|max:255',
            'joining_year' => 'required|integer|min:2000|max:2024',
            'city' => 'required|string|max:255',
            'payment_tier' => 'required|integer|min:1|max:3',
            'age' => 'required|integer|min:18|max:70',
            'gender' => 'required|in:Male,Female',
            'ever_benched' => 'required|in:Yes,No',
            'experience_in_current_domain' => 'required|integer|min:0|max:50',
            'leave_or_not' => 'boolean'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $employee = Employee::create([
                'education' => $request->education,
                'joining_year' => $request->joining_year,
                'city' => $request->city,
                'payment_tier' => $request->payment_tier,
                'age' => $request->age,
                'gender' => $request->gender,
                'ever_benched' => $request->ever_benched ?? false,
                'experience_in_current_domain' => $request->experience_in_current_domain,
                'leave_or_not' => $request->leave_or_not ?? false
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Empleado creado exitosamente',
                'data' => $employee
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al crear el empleado: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getGenderDistribution()
    {
        $data = Employee::select('gender', DB::raw('count(*) as count'))
            ->groupBy('gender')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }

    public function getAgeDistribution()
    {
        $data = Employee::select(
            DB::raw('CASE 
                WHEN age BETWEEN 18 AND 20 THEN "18-20"
                WHEN age BETWEEN 21 AND 25 THEN "21-20"
                WHEN age BETWEEN 26 AND 30 THEN "26-30"
                WHEN age BETWEEN 31 AND 35 THEN "31-35"
                WHEN age BETWEEN 36 AND 40 THEN "36-40"
                WHEN age BETWEEN 41 AND 45 THEN "41-45"
                ELSE "45+"
            END as age_range'),
            DB::raw('count(*) as count')
        )
        ->groupBy('age_range')
        ->get();

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }

    public function getCityDistribution()
    {
        $data = Employee::select('city', DB::raw('count(*) as count'))
            ->groupBy('city')
            ->orderBy('count', 'desc')
            ->limit(10)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }

    public function getEducationDistribution()
    {
        $data = Employee::select('education', DB::raw('count(*) as count'))
            ->groupBy('education')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }

    public function getExperiencePayCorrelation()
    {
        $data = Employee::select(
            'payment_tier',
            DB::raw('CASE 
                WHEN experience_in_current_domain = 0 THEN "0 años"
                WHEN experience_in_current_domain = 1 THEN "1 año"
                WHEN experience_in_current_domain = 2 THEN "2 años"
                WHEN experience_in_current_domain = 3 THEN "3 años"
                WHEN experience_in_current_domain = 4 THEN "4 años"
                WHEN experience_in_current_domain = 5 THEN "5 años"
                WHEN experience_in_current_domain = 6 THEN "6 años"
                WHEN experience_in_current_domain = 7 THEN "7 años"                
            END as experience_range'),
            DB::raw('count(*) as count')
        )
        ->groupBy('payment_tier', 'experience_range')
        ->orderBy('payment_tier')
        ->get();
        
        $result = [];
        $experienceRanges = ["0 años", "1 año", "2 años", "3 años", "4 años", "5 años", "6 años", "7 años"];
        
        foreach ($data as $item) {
            $tier = "Nivel " . $item->payment_tier;
            if (!isset($result[$tier])) {
                $result[$tier] = array_fill_keys($experienceRanges, 0);
            }
            $result[$tier][$item->experience_range] = $item->count;
        }

        return response()->json([
            'success' => true,
            'data' => $result,
            'experience_ranges' => $experienceRanges
        ]);
    }

    public function getBenchedHistory()
    {
        $data = Employee::select(
            DB::raw('CASE WHEN ever_benched = "Yes" THEN "SI" ELSE "NO" END as benched_status'),
            DB::raw('count(*) as count')
        )
        ->groupBy('ever_benched')
        ->get();

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }

    public function getLeaveOrNotPrediction()
    {
        $data = Employee::select(
            DB::raw('CASE WHEN leave_or_not = 1 THEN "En riesgo" ELSE "Estable" END as status'),
            DB::raw('count(*) as count')
        )
        ->groupBy('leave_or_not')
        ->get();

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }

    public function getEmployees(Request $request)
    {
        $query = Employee::query();

        // Aplicar filtros
        if ($request->filled('city')) {
            $query->where('city', $request->city);
        }

        if ($request->filled('age')) {
            $ageRange = $request->age;
            switch ($ageRange) {
                case '20-25':
                    $query->whereBetween('age', [20, 25]);
                    break;
                case '26-30':
                    $query->whereBetween('age', [26, 30]);
                    break;
                case '31-35':
                    $query->whereBetween('age', [31, 35]);
                    break;
                case '36-40':
                    $query->whereBetween('age', [36, 40]);
                    break;
                case '41+':
                    $query->where('age', '>=', 41);
                    break;
            }
        }

        if ($request->filled('gender')) {
            $query->where('gender', $request->gender);
        }

        if ($request->filled('education')) {
            $query->where('education', $request->education);
        }

        $employees = $query->orderBy('id')->get();

        return response()->json([
            'success' => true,
            'data' => $employees,
            'count' => $employees->count()
        ]);
    }

    public function getFilterOptions()
    {
        $cities = Employee::select('city')
            ->distinct()
            ->orderBy('city')
            ->pluck('city');

        $educations = Employee::select('education')
            ->distinct()
            ->orderBy('education')
            ->pluck('education');

        return response()->json([
            'success' => true,
            'data' => [
                'cities' => $cities,
                'educations' => $educations
            ]
        ]);
    }

    public function searchProfiles(Request $request)
    {
        $query = Employee::query();

        if ($request->has('experience_min')) {
            $query->where('experience_in_current_domain', '>=', $request->experience_min);
        }

        if ($request->has('experience_max')) {
            $query->where('experience_in_current_domain', '<=', $request->experience_max);
        }

        if ($request->has('city')) {
            $query->where('city', $request->city);
        }

        if ($request->has('education')) {
            $query->where('education', $request->education);
        }

        if ($request->has('payment_tier')) {
            $query->where('payment_tier', $request->payment_tier);
        }

        if ($request->has('gender')) {
            $query->where('gender', $request->gender);
        }

        if ($request->has('not_benched') && $request->not_benched) {
            $query->where('ever_benched', false);
        }

        if ($request->has('stable') && $request->stable) {
            $query->where('leave_or_not', false);
        }

        $employees = $query->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $employees
        ]);
    }
}

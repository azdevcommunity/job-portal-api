<?php
//
//namespace App\Http\Controllers\API;
//
//use App\Http\Controllers\Controller;
//use App\Models\JobSeeker;
//use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
//use Illuminate\Http\Request;
//use Illuminate\Support\Facades\Auth;
//
//class JobSeekerController extends Controller
//{
//
//    use AuthorizesRequests;
//
//    // List all job seekers
//    public function index()
//    {
//        return JobSeeker::with('user')->get();
//    }
//
//    // Create a new job seeker profile
//    public function store(Request $request)
//    {
//        $user = Auth::user();
//
//        // Ensure the user is a job seeker
//        if ($user->role !== 'job_seeker') {
//            return response()->json(['message' => 'Only job seekers can complete this profile'], 403);
//        }
//
//        // Check if the job seeker profile already exists in the database
//        if (JobSeeker::where('user_id', $user->id)->exists()) {
//            return response()->json(['message' => 'Job seeker profile already exists'], 400);
//        }
//
//        // Validate the profile details
//        $request->validate([
//            'first_name' => 'required|string',
//            'phone' => 'required|string',
//            'job_title' => 'nullable|string',
//            'linkedin_url' => 'nullable|url',
//            'cv_resume_link' => 'nullable|url',
//        ]);
//
//        // Create the job seeker profile
//        $jobSeeker = JobSeeker::create([
//            'user_id' => $user->id,
//            'first_name' => $request->first_name,
//            'phone' => $request->phone,
//            'job_title' => $request->job_title,
//            'linkedin_url' => $request->linkedin_url,
//            'cv_resume_link' => $request->cv_resume_link,
//        ]);
//
//        // Update the user with the job_seeker_id
//        $user->update(['job_seeker_id' => $jobSeeker->id]);
//
//        return response()->json(['message' => 'Job seeker profile created successfully', 'job_seeker' => $jobSeeker], 201);
//    }
//
//    // Get job seeker by ID
//    public function show($id)
//    {
//        return JobSeeker::with('user')->findOrFail($id);
//    }
//
//    // Update job seeker by ID
//    public function update(Request $request, $id)
//    {
//        $jobSeeker = JobSeeker::findOrFail($id);
//
//        $this->authorize('update', $jobSeeker);
//
//        $jobSeeker->update($request->only('first_name', 'phone', 'job_title', 'linkedin_url'));
//
//        return response()->json(['message' => 'Profile updated successfully']);
//    }
//
//    // Delete job seeker by ID
//    public function destroy($id)
//    {
//        $jobSeeker = JobSeeker::findOrFail($id);
//
//        $this->authorize('delete', $jobSeeker);
//
//        $jobSeeker->delete();
//
//        return response()->json(['message' => 'Profile deleted successfully']);
//    }
//
//    // Upload CV/Resume
//    public function uploadCv(Request $request, $id)
//    {
//        $request->validate([
//            'cv_resume' => 'required|file|mimes:pdf,doc,docx',
//        ]);
//
//        $jobSeeker = JobSeeker::findOrFail($id);
//
//        $this->authorize('update', $jobSeeker);
//
//        if ($request->hasFile('cv_resume')) {
//            $path = $request->file('cv_resume')->store('cv_resumes');
//
//            $jobSeeker->cv_resume_link = $path;
//            $jobSeeker->save();
//        }
//
//        return response()->json(['message' => 'CV/Resume uploaded successfully']);
//    }
//
//    // Jobs applied for
//    public function jobsAppliedFor($id)
//    {
//        $jobSeeker = JobSeeker::findOrFail($id);
//
//        $this->authorize('view', $jobSeeker);
//
//        $applications = $jobSeeker->applications()->with('vacancy')->get();
//
//        return response()->json($applications);
//    }
//
//    public function apply($vacancyId)
//    {
//        $user = Auth::user();
//        $job_seeker_id = $user->job_seeker_id;
//
//
//    }
//
//}

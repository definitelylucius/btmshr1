<?php
use Barryvdh\DomPDF\Facade\Pdf;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\JobController;
use App\Http\Controllers\ApplicantController;
use App\Http\Middleware\RoleMiddleware;
use App\Http\Controllers\SuperAdminController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ApplicationController;
use App\Http\Controllers\Staff\ApplicantsController;
use App\Http\Controllers\Staff\TaskController; // ✅ Make sure this is correct
use App\Http\Controllers\Staff\OnboardingController;
use App\Http\Controllers\Staff\RecruitmentController;
use App\Http\Controllers\Staff\ScheduleController;
use App\Http\Controllers\Staff\StaffDashboardController;
use App\Http\Controllers\Admin\AdminRecruitmentController;
use App\Http\Controllers\Admin\AdminReviewController;
use App\Mail\InterviewInvitation;
use Illuminate\Support\Facades\Mail;
use App\Models\JobApplication;
use Illuminate\Http\Request;
use App\Http\Controllers\Employee\EmployeeProfileController;

use App\Http\Controllers\Admin\AdminUpdateEmployeeController;
use App\Http\Controllers\Employee\VideoProgressController;
use App\Http\Controllers\EmployeeController;

use App\Http\Controllers\EmployeeNewhiredController;
use App\Http\Controllers\DocumentGenerateController;
use App\Http\Controllers\EmployeeOnboardingController;








Route::get('/', function () {
    return view('welcome');
})->name('welcome');

Route::get('/', [JobController::class, 'index'])->name('home');







Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';


// Protect routes with authentication middleware
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
});


// Show Registration Form
Route::get('/register', [RegisteredUserController::class, 'create'])->name('register');

// Handle Registration Form Submission
Route::post('/register', [RegisteredUserController::class, 'store']);


// Ensure the route is inside the auth middleware
Route::middleware(['auth'])->group(function () {
    Route::get('/applicant/dashboard', [ApplicantController::class, 'dashboard'])
        ->name('applicant.dashboard');
});

// Authentication Routes
Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
Route::post('/login', [AuthenticatedSessionController::class, 'store']);
Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

// Role-Based Dashboard Routes
Route::middleware(['auth'])->group(function () {
    Route::get('/superadmin/dashboard', function () {
        return view('superadmin.dashboard');
    })->name('superadmin.dashboard');

    Route::get('/admin/dashboard', function () {
        return view('admin.dashboard');
    })->name('admin.dashboard');

    Route::get('/staff/dashboard', function () {
        return view('staff.dashboard');
    })->name('staff.dashboard');

    Route::get('/employee/dashboard', function () {
        return view('employee.dashboard');
    })->name('employee.dashboard');

    Route::get('/applicant/dashboard', function () {
        return view('applicant.dashboard');
    })->name('applicant.dashboard');
});


//SUPER ADMIN


Route::middleware(['auth'])->group(function () {
    Route::get('/superadmin/dashboard', [SuperAdminController::class, 'index'])->name('superadmin.dashboard');

    // ✅ User & Role Management Routes
    Route::get('/superadmin/users', [SuperAdminController::class, 'userManagement'])->name('superadmin.userManagement');
    
    // ✅ Corrected Route for Create User
    Route::get('/superadmin/users/create', [SuperAdminController::class, 'create'])->name('superadmin.createUser');

    // ✅ Store User (POST)
    Route::post('/superadmin/users/store', [SuperAdminController::class, 'store'])->name('superadmin.storeUser');

    // ✅ Edit User (GET)
    Route::get('/superadmin/users/{id}/edit', [SuperAdminController::class, 'editUser'])->name('superadmin.editUser');

    // ✅ Delete User (DELETE)
    Route::delete('/superadmin/users/{id}', [SuperAdminController::class, 'destroy'])->name('superadmin.destroy');
});


//JOBS



Route::prefix('admin')->group(function () {
    Route::get('/jobs', [JobController::class, 'index'])->name('admin.jobs.index');
    Route::get('/jobs/create', [JobController::class, 'create'])->name('admin.jobs.create'); // Create Job Form
    Route::post('/jobs', [JobController::class, 'store'])->name('admin.jobs.store'); // Store Job
});

Route::get('/', [JobController::class, 'welcome']);


//ADMIN CONTROLLER

Route::get('/admin/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');



Route::prefix('admin')->group(function () {
    Route::get('/jobs', [JobController::class, 'index'])->name('admin.jobs.index');
   

});


Route::prefix('admin/jobs')->name('admin.jobs.')->group(function () {
    Route::get('/', [JobController::class, 'index'])->name('index'); // Show job listings
    Route::get('/create', [JobController::class, 'create'])->name('create'); // Show create job form
    Route::post('/store', [JobController::class, 'store'])->name('store'); // Store job

    Route::get('/jobs/{job}/edit', [JobController::class, 'edit'])->name('admin.jobs.edit');
    Route::put('/{job}', [JobController::class, 'update'])->name('admin.jobs.update'); // Update job (PUT request)
    Route::delete('/{job}', [JobController::class, 'destroy'])->name('admin.jobs.destroy'); // Delete job (DELETE request)

    Route::get('{job}/edit', [JobController::class, 'edit'])->name('admin.jobs.edit');
    Route::get('admin/jobs/{job}/edit', [JobController::class, 'edit'])->name('admin.jobs.edit');

});


Route::get('/admin/jobs/manage', [JobController::class, 'manage'])->name('admin.jobs.manage');
Route::prefix('admin')->name('admin.')->group(function () {
    Route::resource('jobs', JobController::class);

});
Route::prefix('admin/jobs')->name('admin.jobs.')->group(function () {
    Route::get('/manage', [JobController::class, 'manage'])->name('manage');
    Route::get('/{job}/edit', [JobController::class, 'edit'])->name('edit');
    Route::post('/admin/jobs/{id}', [JobController::class, 'update']);  // Use POST instead of PUT for FormData
Route::delete('/admin/jobs/{id}', [JobController::class, 'destroy']);
});

Route::put('/admin/job/{id}', [JobController::class, 'update'])->name('admin.job.update');




Route::middleware(['auth'])->group(function () {
    Route::get('/apply', [JobController::class, 'showApplicationForm'])->name('apply.form');
    Route::get('/apply/{job}', [JobController::class, 'showApplicationForm'])->name('apply.form');

    Route::get('/apply/{jobId}', [ApplicantController::class, 'showApplicationForm'])->name('apply.form');
    Route::post('/submit-application/{jobId}', [ApplicantController::class, 'submitApplication'])->name('submit.application');

    Route::post('/apply', [ApplicationController::class, 'store']);
    Route::post('/submit-application', [ApplicationController::class, 'store'])->name('submit.application');
});


//Staff



Route::get('/track', [ApplicantsController::class, 'trackApplications'])
    ->name('staff.applicants.track')
    ->middleware('auth');


    Route::get('/view', [ApplicantsController::class, 'view'])->name('staff.applicants.view');


Route::get('/staff/applicants/view', [ApplicantsController::class, 'view'])
    ->name('staff.applicants.view');

    Route::prefix('staff/onboarding')->group(function () {
        Route::get('/orientation', [OnboardingController::class, 'orientation'])->name('staff.onboarding.orientation');
        Route::get('/documents', [OnboardingController::class, 'documents'])->name('staff.onboarding.documents');
        Route::get('/training', [OnboardingController::class, 'training'])->name('staff.onboarding.training');
    });

    Route::prefix('staff/tasks')->group(function () {
        Route::get('/assigned', [TaskController::class, 'assignedTasks'])->name('staff.tasks.assigned');
        Route::get('/pending', [TaskController::class, 'pendingTasks'])->name('staff.tasks.pending');
        Route::get('/completed', [TaskController::class, 'completedTasks'])->name('staff.tasks.completed');

    });

    Route::prefix('staff/recruitment')->group(function () {
        Route::get('/interview', [RecruitmentController::class, 'interview'])->name('staff.recruitment.interview');
        Route::get('/documents', [RecruitmentController::class, 'documents'])->name('staff.recruitment.documents');
        Route::get('/feedback', [RecruitmentController::class, 'feedback'])->name('staff.recruitment.feedback');
    });

    Route::get('/staff/schedule', [ScheduleController::class, 'index'])->name('staff.schedule');


    Route::post('/apply/{job}', [ApplicantsController::class, 'submitApplication'])
    ->name('submit.application')
    ->middleware('auth');

  
    Route::middleware(['auth', RoleMiddleware::class . ':staff'])->group(function () {
        Route::prefix('staff/applicants')->group(function () {
            Route::get('/track', [ApplicantsController::class, 'trackApplications'])
                ->name('staff.applicants.track');
        
            Route::get('/view', [ApplicantsController::class, 'index'])
                ->name('staff.applicants.view');
        });
    });

    Route::middleware(['auth', RoleMiddleware::class . ':staff'])->group(function () {

        Route::get('/staff/dashboard', [StaffDashboardController::class, 'index'])->name('staff.dashboard');

        Route::get('/staff/dashboard', [StaffDashboardController::class, 'trackApplications'])
        ->name('staff.dashboard');
    });
    

    
// ✅ STAFF: Scan & Mark Applicants
Route::get('/staff/applicants/feedback', [ApplicationController::class, 'showScanPage'])->name('staff.applicants.scan');
Route::post('/staff/applicants/update-status', [ApplicationController::class, 'updateStatus'])->name('staff.updateStatus');

// ✅ ADMIN: Review Applicants
Route::get('/admin/applicants/review', [ApplicationController::class, 'viewApplications'])->name('admin.applicants.review');
Route::post('/admin/applicants/{id}/schedule-interview', [ApplicationController::class, 'scheduleInterview'])->name('admin.scheduleInterview');

// ✅ STAFF: Mark Interview as Completed
Route::get('/staff/applicants/interview', [ApplicationController::class, 'showInterviewPage'])->name('staff.applicants.interview');
Route::post('/staff/applicants/{id}/complete-interview', [ApplicationController::class, 'completeInterview'])->name('staff.completeInterview');

// ✅ STAFF: Mark Applicant as Hired
Route::get('/staff/applicants/finalize', [ApplicationController::class, 'showFinalizePage'])->name('staff.applicants.finalize');
Route::post('/staff/applicants/{id}/mark-hired', [ApplicationController::class, 'markHired'])->name('staff.markHired');

// ✅ ADMIN: Finalize Hiring
Route::get('/admin/applicants/finalize', [ApplicationController::class, 'showFinalizeHiringPage'])->name('admin.applicants.finalize');
Route::post('/admin/applicants/{id}/finalize-hiring', [ApplicationController::class, 'finalizeHiring'])->name('admin.finalizeHiring');

Route::post('/applications/{id}/send-interview', [ApplicationController::class, 'sendInterviewInvitation'])->name('applications.send-interview');

Route::post('/staff/applications/{id}/interview', 
    [RecruitmentController::class, 'completeInterview'])->name('staff.completeInterview');


    Route::get('/staff/interview', [RecruitmentController::class, 'interview'])->name('staff.interview');
 
    // Ensure the user is authenticated
    Route::get('/admin/applicants/{id}', [RecruitmentController::class, 'show'])->name('admin.applicants.show');


    Route::post('/staff/completeInterview/{id}', [RecruitmentController::class, 'completeInterview'])->name('staff.completeInterview');
    Route::get('/staff/recruitment/feedback', [RecruitmentController::class, 'feedback'])
    ->name('staff.recruitment.feedback');

    
   
    //ADMIN REVIEW
    Route::middleware(['auth', RoleMiddleware::class . ':admin'])->group(function () {
        // Route to review applications
        Route::get('/admin/applicants/review', [AdminReviewController::class, 'index'])
            ->name('admin.applicants.review');
        
        // Route to update a specific application status
        Route::put('/admin/applicants/review/{applicationId}', [AdminReviewController::class, 'updateApplicationReviewer'])
            ->name('admin.applicants.updateReview');
    });
    Route::middleware(['auth', ])->prefix('admin')->group(function () {
        Route::get('/review-applications', [AdminReviewController::class, 'index'])->name('admin.applications.review');
        Route::put('/applications/{id}/update-status', [AdminReviewController::class, 'updateStatus'])->name('admin.applications.updateStatus');
    });

    Route::put('/admin/applications/updateStatus/{id}', [AdminReviewController::class, 'updateStatus'])->name('admin.applications.updateStatus');
    Route::get('staff/recruitment/interview/{id}/send-email', [RecruitmentController::class, 'sendInterviewEmail'])
    ->name('staff.sendInterviewEmail');

    Route::post('/staff/applicant/{id}/recommend', [RecruitmentController::class, 'updateInterviewOutcome'])->name('staff.recommendApplicant');

    Route::get('/admin/recruitment/hired', [AdminUpdateEmployeeController::class, 'hired'])->name('admin.recruitment.hired');
    

    Route::put('/staff/recruitment/update-status/{applicationId}', [RecruitmentController::class, 'updateApplicationStatusByRequest'])
    ->name('staff.applications.update-status');

    Route::middleware(['auth'])->prefix('staff')->group(function () { 
        Route::post('/recruitment/interview/{applicantIds}}/store', [RecruitmentController::class, 'storeInterviewResult'])
             ->name('staff.recruitment.storeInterviewResult');
    
        Route::post('/recruitment/update-interview-outcome/{id}', [RecruitmentController::class, 'updateInterviewOutcome'])
             ->name('staff.updateInterviewOutcome');
    });;



    Route::get('/admin/review-applications', [AdminReviewController::class, 'index'])->name('admin.applications.review');

Route::get('/applications/{id}', [AdminRecruitmentController::class, 'showApplication'])
     ->name('applications.show');

     Route::middleware(['auth'])->group(function () {
        Route::get('/staff/dashboard', [StaffDashboardController::class, 'index'])->name('staff.dashboard');
    });
    Route::prefix('admin')->middleware('auth')->group(function () {
        Route::get('/review-applications', [AdminReviewController::class, 'index'])->name('admin.applications.review');
    });
    Route::post('/staff/updateInterviewOutcome/{applicationId}', [RecruitmentController::class, 'updateInterviewOutcome'])
    ->name('staff.updateInterviewOutcome');

//ADMIN UPDATE STATUSS






Route::get('/admin/applicants/review', [AdminRecruitmentController::class, 'index'])->name('admin.applicants.review');



Route::put('/admin/applications/{id}/status', [AdminRecruitmentController::class, 'updateApplicationStatus'])
    ->name('admin.applications.updateStatus');

    Route::put('/admin/applications/{id}/status', [AdminRecruitmentController::class, 'updateApplicationStatus'])->name('admin.applications.updateStatus');


  

    Route::get('/admin/applicants/review', [AdminReviewController::class, 'index'])->name('admin.applicants.review');

    Route::get('/admin/recruitment/hired', [AdminController::class, 'showHiredApplicants'])->name('admin.recruitment.hired');

    Route::get('admin/recruitment/hired', [AdminUpdateEmployeeController::class, 'showHiredApplicants'])->name('admin.recruitment.hired');
    Route::post('/admin/applicants/hire/{applicantIds}', [AdminUpdateEmployeeController::class, 'updateToEmployees'])->name('admin.updateToEmployees');


    //EMAIL

    Route::post('/staff/sendInterviewEmail/{id}', function (Request $request, $id) {
        $request->validate([
            'subject' => 'required|string',
            'message' => 'required|string',
        ]);
    
        $application = JobApplication::findOrFail($id);
    
        Mail::to($application->user->email)->send(new InterviewInvitation(
            $application,
            $request->input('subject'),
            $request->input('message')
        ));
    
        return back()->with('success', 'Interview invitation sent!');
    })->name('staff.sendInterviewEmail');

    
    

    Route::get('/staff/recruitment/interviews', [RecruitmentController::class, 'showApplicants'])->name('recruiter.applicants');
    Route::post('/staff/recruitment/interview/{applicationId}/store', [RecruitmentController::class, 'storeInterviewResult'])
    ->name('staff.recruitment.storeInterviewResult');

    Route::post('/recruitment/interview/{id}', [RecruitmentController::class, 'sendInterviewEmail']);


    // Define the route with the POST method

   // ✅ Fix duplicate "sendInterviewEmail" routes (keep only one)
Route::post('/staff/sendInterviewEmail/{id}', [RecruitmentController::class, 'sendInterviewEmail'])
->name('staff.sendInterviewEmail');

Route::post('/recruitment/interview/{id}', [RecruitmentController::class, 'sendInterviewEmail']);
Route::post('/staff/recruitment/interview/{id}', [RecruitmentController::class, 'sendInterviewEmail']);

// ✅ Fix duplicate "updateInterviewOutcome" routes (keep only one)
Route::post('/staff/updateInterviewOutcome/{applicationId}', [RecruitmentController::class, 'updateInterviewOutcome'])
->name('staff.updateInterviewOutcome');

// ✅ Hiring routes (ensure unique names)
Route::get('admin/recruitment/{id}/hire', [AdminRecruitmentController::class, 'showHireForm'])
->name('admin.recruitment.hire');

Route::post('admin/recruitment/{id}/hire', [AdminRecruitmentController::class, 'hire'])
->name('admin.recruitment.hire.submit');

// ✅ Fix conflicting "admin.applicants.hired" routes


Route::get('/admin/recruitment/hired', [AdminRecruitmentController::class, 'hiredApplicants'])
    ->name('admin.applicants.hired');
   


    Route::put('/admin/applications/{id}/updateEmployee', [AdminUpdateEmployeeController::class, 'updateEmployee'])
    ->name('admin.applications.updateEmployee');
    
    Route::put('/staff/applications/{applicationId}/update-status', [RecruitmentController::class, 'updateApplicationStatusByRequest'])
    ->name('staff.applications.update-status');

    Route::post('/admin/notifications/read', [AdminController::class, 'markNotificationsAsRead'])->name('admin.notifications.markAsRead');


//EMPLOYEE


Route::put('/staff/applications/{application}/update-status', [RecruitmentController::class, 'updateStatus'])->name('staff.applications.update-status');


Route::middleware('auth')->get('/employee/profile', [EmployeeProfileController::class, 'showProfile'])->name('employee.profile');

Route::put('employee/profile/{id}', [EmployeeProfileController::class, 'update'])->name('employee.profile.update');

Route::post('/admin/update-employee/{applicantId}', [AdminUpdateEmployeeController::class, 'updateToEmployees'])->name('admin.updateEmployee');

    Route::post('/staff/updateToEmployee/{applicationId}', [RecruitmentController::class, 'updateToEmployee'])
    ->name('staff.updateToEmployee');
   
    Route::get('/admin/hired-applicants', [AdminUpdateEmployeeController::class, 'showRecommendedApplicants'])
    ->name('admin.hiredApplicants');

Route::put('/admin/hire-applicant/{id}', [AdminUpdateEmployeeController::class, 'updateToEmployees'])
    ->name('employee.profile.update');

// Route to show the profile
Route::get('employee/{id}/profile', [EmployeeProfileController::class, 'showProfile'])->name('employee.profile.show');
// Route to update the profile
Route::put('employee/{id}/profile', [EmployeeProfileController::class, 'update'])->name('employee.profile.update');
Route::get('employee/{id}/profile', [EmployeeProfileController::class, 'showProfile'])->name('employee.profile.show');



//NOTIFICATION 
Route::get('/notifications', function (Request $request) {
    $user = auth()->user();
    return response()->json($user->unreadNotifications->map(function ($notification) {
        return [
            'id' => $notification->id,
            'message' => $notification->data['message'],
            'url' => $notification->data['url'],
        ];
    }));
})->middleware('auth');

Route::post('/notifications/mark-all-as-read', function () {
    Auth::user()->unreadNotifications->markAsRead();
    return response()->json(['message' => 'All notifications marked as read']);
})->middleware('auth');

// Clear all notifications
Route::delete('/notifications/clear-all', function () {
    Auth::user()->notifications()->delete();
    return response()->json(['message' => 'All notifications cleared']);
})->middleware('auth');



Route::get('/admin/applicants/{id}', [AdminController::class, 'show'])->name('admin.applicant.show');


Route::get('/admin/recruitment/hired', [AdminUpdateEmployeeController::class, 'showHiredApplicants'])
    ->name('admin.applicants.hired'); 


    Route::post('/admin/applicants/hire/{applicantId}', [AdminUpdateEmployeeController::class, 'updateToEmployees'])
    ->name('admin.updateToEmployees');


 // STAFF ONBOARDING
Route::prefix('staff')->middleware('auth')->group(function () {
    Route::get('/onboarding', [OnboardingController::class, 'index'])->name('staff.onboarding');
    Route::get('/onboarding/documents', [OnboardingController::class, 'documents'])->name('staff.onboarding.documents');
    
    // Changed route name here

    Route::get('/onboarding/training', [OnboardingController::class, 'training'])->name('staff.onboarding.training');

    // Upload Video Route
 
Route::get('/employee/orientation', [OnboardingController::class, 'employeeOrientation'])->name('staff.orientation');
Route::get('/upload-orientation-video', [OnboardingController::class, 'showForm'])->name('videos.uploadForm');


Route::post('/upload-orientation-video', [OnboardingController::class, 'upload'])->name('videos.upload'); // Handle form submission


Route::get('/new-hire-orientation', [OnboardingController::class, 'newHireOrientation'])->name('new.hire.orientation');
Route::get('/onboarding/upload', [OnboardingController::class, 'showForm']);
Route::get('/upload-video', [OnboardingController::class, 'showUploadForm'])->name('videos.upload.form');



// Video Upload
Route::get('/staff/upload-video', [OnboardingController::class, 'showUploadForm'])->name('videos.upload.form');
Route::post('/staff/upload-video', [OnboardingController::class, 'upload'])->name('videos.upload');

// Employee Orientation
Route::get('/employee/orientation/{employeeId?}', [OnboardingController::class, 'showEmployeeOrientation'])->name('employee.orientation');


});

Route::get('/new-hire-orientation', function () {
    return view('staff.onboarding.orientation'); // Correct path
})->name('new.hire.orientation');



Route::post('/upload-video', [EmployeeController::class, 'uploadVideo'])->name('staff.onboarding.upload-video');


//STAFF ONBOARDING DOCUMENTS


Route::middleware(['auth'])->group(function () {
    Route::get('/staff/onboarding/documents', [OnboardingController::class, 'documentCollectionView'])->name('staff.documents');
    Route::post('/staff/assign-document-task', [OnboardingController::class, 'assignDocumentTask'])->name('staff.assignDocumentTask');

    Route::get('/employee/onboarding', [OnboardingController::class, 'employeeView'])->name('employee.onboarding');
    Route::post('/employee/submit-document', [OnboardingController::class, 'submitDocument'])->name('employee.submitDocument');
    
});

Route::prefix('staff')->name('staff.')->group(function () {
    Route::prefix('onboarding')->name('onboarding.')->group(function () {
        Route::get('/documents', [OnboardingController::class, 'documentCollectionView'])->name('documents');
    });
});


Route::post('/onboarding/upload-documents', [OnboardingController::class, 'uploadDocuments'])
    ->name('onboarding.uploadDocuments');
    Route::post('/update-task-status', [OnboardingController::class, 'updateTaskStatus'])->name('updateTaskStatus');
    Route::post('/onboarding/update-progress', [OnboardingController::class, 'updateProgress'])->name('onboarding.updateProgress');
 
    Route::post('/assign-task', [OnboardingController::class, 'assignTask'])->name('staff.assignTask');



// EMPLOYEE ONBOARDING
Route::prefix('employee')->middleware('auth')->group(function () {
    Route::get('/onboarding/orientation/{employeeId?}', [EmployeeController::class, 'orientation'])->name('employee.onboarding.orientation');

    Route::post('/onboarding/update-video-progress', [EmployeeController::class, 'updateVideoProgress']);
    Route::post('/onboarding/progress', [OnboardingController::class, 'updateProgress'])
    ->name('onboarding.updateProgress');
    Route::post('/onboarding/update-progress', [EmployeeController::class, 'updateTaskProgress'])->name('onboarding.updateProgress');
    Route::post('/staff/onboarding/verify/{id}', [EmployeeController::class, 'verifyOnboarding'])->name('staff.verifyOnboarding');
    Route::post('/upload-documents', [EmployeeController::class, 'uploadDocuments'])->name('uploadDocuments');
    Route::post('/upload-documents', [EmployeeController::class, 'uploadDocuments'])->name('employee.uploadDocuments');
    Route::post('/onboarding/update-progress', [OnboardingController::class, 'updateProgress'])->name('onboarding.updateProgress');
    Route::get('/onboarding', [EmployeeController::class, 'showOnboarding'])->name('onboarding.view');
    Route::get('/onboarding', [EmployeeController::class, 'showOnboarding'])->name('onboarding.view');
    Route::post('/onboarding/orientation', [EmployeeController::class, 'updateProgress'])->name('onboarding.updateProgress');
    Route::get('/onboarding', [EmployeeController::class, 'showOnboarding'])->name('onboarding.show');
    Route::post('/onboarding/update-progress', [EmployeeController::class, 'updateProgress'])->name('onboarding.updateProgress');

    Route::post('/onboarding/update', [EmployeeNewhiredController::class, 'updateStatus'])->name('onboarding.update');
    Route::get('/onboarding/status', [EmployeeNewhiredController::class, 'getStatus'])->name('onboarding.status');

});

//ONBOARDING:

Route::middleware(['auth'])->group(function () {
    Route::get('/employee/onboarding', [OnboardingController::class, 'showForm'])->name('employee.onboarding.form');
    Route::post('/employee/onboarding/submit', [OnboardingController::class, 'submitForm'])->name('employee.onboarding.submit');

    Route::get('/staff/onboarding', [StaffController::class, 'viewOnboardingSummary'])->name('staff.onboarding.summary');
});

//DOCUMENT GENEERATE
Route::get('/generate-nda/{id}', [DocumentGenerateController::class, 'generateNDA'])->name('generate.nda');

Route::get('/generate/onboarding-letter/{id}', [DocumentGenerateController::class, 'generateOnboardingLetter'])
    ->name('generate.onboarding');
Route::get('/generate/hiring-contract/{id}', [DocumentGenerateController::class, 'generateHiringContract'])->name('generate.hiring');



//FILE


Route::middleware(['auth'])->group(function () {
    Route::get('/onboarding', [EmployeeOnboardingController::class, 'index'])->name('onboarding');
    Route::post('/onboarding/employee-details', [EmployeeOnboardingController::class, 'storeEmployeeDetails']);
    Route::post('/onboarding/personal-details', [EmployeeOnboardingController::class, 'storePersonalDetails']);
    Route::post('/onboarding/upload-document', [EmployeeOnboardingController::class, 'uploadDocument']);
    Route::post('/onboarding/bank-details', [EmployeeOnboardingController::class, 'storeBankDetails']);
});


Route::prefix('employee')->middleware('auth')->group(function () {
    Route::get('/onboarding', [EmployeeOnboardingController::class, 'index'])->name('employee.onboarding');

    Route::post('/onboarding/complete/{task}', [EmployeeOnboardingController::class, 'completeTask'])->name('onboarding.complete');
});;


Route::middleware(['auth', 'employee'])->group(function () {
    Route::get('/staff/onboarding', [OnboardingController::class, 'index'])->name('staff.onboarding');
    Route::post('/staff/onboarding/{taskId}/complete', [OnboardingController::class, 'completeTask'])->name('onboarding.complete');
});




<?php

namespace App\Http\Controllers;

use App\Http\Middleware\isEmployer;
use App\Http\Middleware\isPremiumUser;
use App\Http\Requests\JobEditFormRequest;
use App\Http\Requests\JobPostFormRequest;
use App\Models\Listing;
use Illuminate\Support\Str;
use App\Post\JobPost;
use Illuminate\Http\Request;

class PostJobController extends Controller
{
    protected $job;
    public function __construct(JobPost $job)
    {
        $this->job = $job;
        $this->middleware('auth');
        $this->middleware(isPremiumUser::class)->only(['create', 'store']);
        $this->middleware(isEmployer::class);
    }

    public function index()
    {
        $jobs = Listing::where('user_id', auth()->user()->id)->get();

        return view('job.index', compact('jobs'));
    }
    
    public function create()
    {
        return view('job.create');
    }

    public function store(JobPostFormRequest $request)
    {   
//           $this->validate($request, [
//         'title' => 'required|min:5',
//        'feature_image'=>'required|mimes:png,jpeg,jpg',
//         'descripton'=>'require|min:5',
//         'roles'=>'required|min:10',
//         'jop_type'=>'required',
//        'address'=>'required',
//         'salary'=>'required',
//         'date'=>'required'
// ]);
// $imagePath=$request->file('feature_image')->store('images','public');
// $post=new Listing;
// $post->feature_image=$imagePath;
// $post->user_id=auth()->user()->id;
// $post->title=$request->title;
// $post->descripton=$request->descripton;
// $post->roles=$request->roles;
// $post->jop_type=$request->jop_type;
// $post->address=$request->address;
// $post->application_close_date=$request->date;
// $post->salary=$request->salary;
// $post->jop_type=$request->jop_type;
// $post->slug = Str::slug($request('title')).'.'. Str::uuid();

// $post->save();
// return back();
//dd($request);
       $this->job->store($request);
        
        return redirect()->route('job.index')->with('success', 'Your job post has been posted');
    }

    public function edit(Listing $listing)
    {
        return view('job.edit',compact('listing'));
    }

    public function update($id, JobEditFormRequest $request)
    {  //dd($request);
        $this->job->updatePost($id, $request);

        return back()->with('success', 'Your job post has been successfully updated');
    }

    public function destroy($id)
    {
        Listing::find($id)->delete();
        
        return back()->with('success', 'Your job post has been successfully deleted');
    }

}

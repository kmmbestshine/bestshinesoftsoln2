<?php

namespace App\Http\Controllers\Backend;

use App\Forms\PostForm;
use App\Post;
use App\Video;
use App\Media;
use Illuminate\Http\Request;
use Kris\LaravelFormBuilder\FormBuilder;
use Kris\LaravelFormBuilder\FormBuilderTrait;
use Carbon\Carbon;
use Illuminate\Support\Facades\File;
use DB;

class BlogController extends BackendController
{
    use FormBuilderTrait;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $onlyTrashed = false;
        if(($status = $request->get('status')) && $status == 'trash')
        {
            $posts = Post::onlyTrashed()->with('category', 'author')->latest()->paginate($this->limit);
            $onlyTrashed = true;
        }
        elseif($status == 'published')
        {
            $posts = Post::published()->with('category', 'author')->latest()->paginate($this->limit);
        }
        elseif($status == 'scheduled')
        {
            $posts = Post::scheduled()->with('category', 'author')->latest()->paginate($this->limit);
        }
        elseif($status == 'draft')
        {
            $posts = Post::draft()->with('category', 'author')->latest()->paginate($this->limit);
        }
        elseif($status == 'own')
        {
            $posts = $request->user()->posts()->with('category', 'author')->latest()->paginate($this->limit);
        }
        else
        {
            $posts = Post::withTrashed()->with('category', 'author')->latest()->paginate($this->limit);
        }
        return view('backend.blog.index', compact('posts', 'onlyTrashed'));
    }
     public function approachcreate()
    {
        
        
        return view('backend.approach.create');
    }

    public function approachstore(Request $request)
    {
        $input= \Request::all();
        $title = $request->input('title');
       // $type = $request->input('type');
      // dd($input);
        // Upload the image
        if ($request->hasFile('image')) {
            $image = $request->image;
            $image->move('approach', $image->getClientOriginalName());
        }
    if ($request->hasFile('video')) {

          $randName = Carbon::now()->timestamp;
          $uploadDir = '/approach/';
          $location = $randName . '.mp4';
          $fullPath = public_path() . $uploadDir . $location;
          

          // check if 'uploads/' directory exists. If not, create it.
          ////if(!File::exists(public_path() . $uploadDir)) {
         //   File::makeDirectory(public_path() . $uploadDir, $mode = 0777, true, true);
        //  }

          $file = $request->file('video');
          $file->move(public_path() . $uploadDir, $location);

          $title = $request->input('title');

          // get video filesize
          $filesize = File::size($fullPath);
          $filesize = $this->formatBytes($filesize);

          // get video bitrate
        //  $bitrate = $ffprobe->format($fullPath)
           // ->get('bit_rate');
         // $bitrate = $this->formatBytes($bitrate);
          
            DB::table('approaches')->insert(
                array(
            'title' => $title,
           // 'type' =>$type,
           // 'image' => $request->image->getClientOriginalName(),
            'filename' => htmlspecialchars($file->getClientOriginalName()),
            'location' => $uploadDir . $location,
            'filesize' => $filesize,
           

        ));
           
      }else{
           // dd('in');
            DB::table('approaches')->insert(
                array(
            'title' => $title,
           // 'type' =>$type,
            'image' => $request->image->getClientOriginalName(),
          
        ));
           
        }

      return view('backend.approach.create');
    }
    public function approachindex()
    {
        $video= 'video';
        $image= 'image';
       // $meeting= 'meeting';
       // $others= 'others';
        $approach= \DB::table('approaches')->get();
       // $newspaper= \DB::table('medias')->where('type',$image)->get();
       // $meeting= \DB::table('medias')->where('type',$meeting)->get();
       // $others= \DB::table('medias')->where('type',$others)->get();
       // dd($meeting,$others,$newspaper);
        //dd($approach);
        return view('backend.approach.index', compact('approach'));
    }
    public function mediacreate()
    {
        //dd('kkkkkk');
       // $post = Post::where('id',$id)->first();
        
        return view('backend.media.create');
    }

    public function mediastore(Request $request)
    {
        $input= \Request::all();
        $title = $request->input('title');
        $type = $request->input('type');
      // dd($input);
        // Upload the image
        if ($request->hasFile('image')) {
            $image = $request->image;
            $image->move('media', $image->getClientOriginalName());
        }
    if ($request->hasFile('video')) {

          $randName = Carbon::now()->timestamp;
          $uploadDir = '/media/';
          $location = $randName . '.mp4';
          $fullPath = public_path() . $uploadDir . $location;
          

          // check if 'uploads/' directory exists. If not, create it.
          ////if(!File::exists(public_path() . $uploadDir)) {
         //   File::makeDirectory(public_path() . $uploadDir, $mode = 0777, true, true);
        //  }

          $file = $request->file('video');
          $file->move(public_path() . $uploadDir, $location);

          $title = $request->input('title');

          // get video filesize
          $filesize = File::size($fullPath);
          $filesize = $this->formatBytes($filesize);

          // get video bitrate
        //  $bitrate = $ffprobe->format($fullPath)
           // ->get('bit_rate');
         // $bitrate = $this->formatBytes($bitrate);
          
            DB::table('medias')->insert(
                array(
            'title' => $title,
            'type' =>$type,
           // 'image' => $request->image->getClientOriginalName(),
            'filename' => htmlspecialchars($file->getClientOriginalName()),
            'location' => $uploadDir . $location,
            'filesize' => $filesize,
           

        ));
           
      }else{
           // dd('in');
            DB::table('medias')->insert(
                array(
            'title' => $title,
            'type' =>$type,
            'image' => $request->image->getClientOriginalName(),
          
        ));
           
        }

      return view('backend.media.create');
    }
    public function mediaindex()
    {
        
        $medias= DB::table('medias')->get();
        //dd($medias);
        return view('backend.media.index',compact('medias'));
    }

    public function mediadelete( $id)
    {
       // $user = User::findOrFail($id);
        $medias1= DB::table('medias')->where('id',$id)->delete();
        $medias= DB::table('medias')->get();
       // dd($medias);
        return redirect()->route('backend.media.index')->with('success', 'Your post was deleted successfully!');
    }

public function videocreate($id)
    {
        
        $post = Post::where('id',$id)->first();
        
        return view('backend.videos.create', compact('post'));
    }


    public function videostore(Request $request,$id)
    {
        $input= \Request::all();
       
        $post = Post::where('id',$id)->first();

        

    if ($request->hasFile('video')) {

          $randName = Carbon::now()->timestamp;
          $uploadDir = '/videouploads/';
          $location = $randName . '.mp4';
          $fullPath = public_path() . $uploadDir . $location;
          

          // check if 'uploads/' directory exists. If not, create it.
          ////if(!File::exists(public_path() . $uploadDir)) {
         //   File::makeDirectory(public_path() . $uploadDir, $mode = 0777, true, true);
        //  }

          $file = $request->file('video');
          $file->move(public_path() . $uploadDir, $location);

          $title = $request->input('title');

          // get video filesize
          $filesize = File::size($fullPath);
          $filesize = $this->formatBytes($filesize);

          // get video bitrate
        //  $bitrate = $ffprobe->format($fullPath)
           // ->get('bit_rate');
         // $bitrate = $this->formatBytes($bitrate);

          $video = new Video();
          $video->title = $title;
          $video->category_id = $post->category;
          $video->post_id = $post->id;
          $video->filename = htmlspecialchars($file->getClientOriginalName());
          $video->location = $uploadDir . $location;
         // $video->thumbnail = $uploadDir . $randName . '.png';
         // $video->duration = $duration;
          $video->filesize = $filesize;
         // $video->bitrate = $bitrate;
          $video->save();
        //  dd('inserted');
         // return redirect('site.product.uploadvideo')
           // ->withSuccess('Successfully uploaded!')
           // ->with([
            //  'title' => $title
           // ]);
         
          
         // return redirect()->route('backend.video.create')->with('success', 'Your post was created successfully!');
           return view('backend.videos.create', compact('post'));
            //return redirect()->route('site.product.uploadvideo')->with('success_message', 'successfully Uploaded');
      }

      
    }

    private function formatBytes($size, $precision = 2)
  {
      $base = log($size, 1024);
      $suffixes = array('', 'K', 'M', 'G', 'T');

      return round(pow(1024, $base - floor($base)), $precision) .' '. $suffixes[floor($base)];
  }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(FormBuilder $formBuilder)
    {
        $form = $formBuilder->create(PostForm::class, [
            'method' => 'POST',
            'url' => route('backend.blog.store')
        ]);
        return view('backend.blog.edit', compact('form'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $form = $this->form(PostForm::class);

        // It will automatically use current request, get the rules, and do the validation
        if (!$form->isValid()) {
            return redirect()->back()->withErrors($form->getErrors())->withInput();
        }

        // Or automatically redirect on error. This will throw an HttpResponseException with redirect
        $form->redirectIfNotValid();

        $data = $form->getFieldValues();

        $post = $request->user()->posts()->create($data);
        $post->attachTags($data['post_tags']);

        return redirect()->route('backend.blog.index')->with('success', 'Your post was created successfully!');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     *
     * @todo the controller does not receive Post object
     */
    public function edit($id, FormBuilder $formBuilder)
    {
        $post = Post::findOrFail($id);

        $form = $formBuilder->create(PostForm::class, [
            'method' => 'PUT',
            'url' => route('backend.blog.update', ['id' => $id]),
            'model' => $post
        ]);
        return view('backend.blog.edit', compact('form'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $form = $this->form(PostForm::class);

        // It will automatically use current request, get the rules, and do the validation
        if (!$form->isValid()) {
            return redirect()->back()->withErrors($form->getErrors())->withInput();
        }

        // Or automatically redirect on error. This will throw an HttpResponseException with redirect
        $form->redirectIfNotValid();

        $data = $form->getFieldValues();

        $post = Post::findOrFail($id);
        $post->update($data);
        $post->attachTags($data['post_tags']);

        return redirect()->route('backend.blog.index')->with('success', 'Your post was updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Post::destroy($id);
        return redirect()->back()->with('trash-message', ['Your post was moved to trash!', $id]);
    }

    /**
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function forceDestroy($id)
    {
        Post::onlyTrashed()->findOrFail($id)->forceDelete();
        return redirect()->back()->with('message', 'Your post was deleted permanently!');
    }

    /**
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function restore($id)
    {
        Post::onlyTrashed()->findOrFail($id)->restore();
        return redirect()->back()->with('message', 'Your post was restored!');
    }
}

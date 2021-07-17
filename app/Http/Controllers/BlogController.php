<?php

namespace App\Http\Controllers;

use App\Category;
use App\Forms\CommentForm;
use App\Tag;
use App\User;
use App\Post;
use App\Video;
use App\Media;
use Kris\LaravelFormBuilder\FormBuilder;
use Kris\LaravelFormBuilder\FormBuilderTrait;

class BlogController extends Controller
{
    use FormBuilderTrait;

    protected $limit = 2;

    public function index(){

       // $posts = Post::with('author', 'category', 'tags', 'comments')->latestFirst()->published()->filter(request()->only(['term', 'month', 'year']))->simplePaginate($this->limit);
      $posts = Post::with('author', 'category', 'tags', 'comments')->published()->filter(request()->only(['term', 'month', 'year']))->simplePaginate($this->limit);

       foreach ($posts as  $value) {
                
                    $post_id=$value->id;
                $videos1= \DB::table('videos')->where('post_id',$post_id)
                    ->get();
                  
                    if($videos1){
                      $value->videos=$videos1;
                    }
                }
              
        return view('blog.index', compact('posts'));
    }

    public function category(Category $category){

        $categoryName = $category->title;

        $posts = $category->posts()->with('author', 'category', 'tags', 'comments')->latestFirst()->published()->simplePaginate($this->limit);
       // dd($posts);
        foreach ($posts as  $value) {
                
                    $post_id=$value->id;
                $videos1= \DB::table('videos')->where('post_id',$post_id)
                    ->get();
                  

                    $value->videos=$videos1;
                    
                }
                
        return view('blog.index', compact('posts', 'categoryName'));
    }
    public function about()
    {
       
        return view('blog.about');
    }

    public function author(User $author)
    {
        $authorName = $author->name;

        $posts = $author->posts()->with('author', 'category', 'tags', 'comments')->latestFirst()->published()->simplePaginate($this->limit);
       // dd($posts);
        return view('blog.index', compact('posts', 'authorName'));
    }

    public function tag(Tag $tag)
    {
        $tagName = $tag->name;

        $posts = $tag->posts()->with('author', 'category', 'tags', 'comments')->latestFirst()->published()->simplePaginate($this->limit);

        return view('blog.index', compact('posts', 'tagName'));
    }

    public function show(FormBuilder $formBuilder, Post $post)
    {
        $post->increment('view_count');

        $postComments = $post->comments()->orderBy('created_at', 'desc')->simplePaginate(5);

        $form = $formBuilder->create(CommentForm::class, [
            'method' => 'POST',
            'url' => route('blog.show', $post->slug)
        ]);

        return view('blog.show', compact('post', 'postComments', 'form'));
    }

    public function comment(Post $post)
    {
        $form = $this->form(CommentForm::class);

        // It will automatically use current request, get the rules, and do the validation
        if (!$form->isValid()) {
            return redirect()->back()->withErrors($form->getErrors())->withInput();
        }

        // Or automatically redirect on error. This will throw an HttpResponseException with redirect
        $form->redirectIfNotValid();

        $post->comments()->create($form->getFieldValues());
        return redirect()->route('blog.show', $post->slug . '#comments')->with('success', 'Your comment was created successfully!');
    }

    public function mediashow()
    {
        $video= 'video';
        $image= 'image';
        $meeting= 'meeting';
        $others= 'others';
        $tv= \DB::table('medias')->where('type',$video)->get();
        $newspaper= \DB::table('medias')->where('type',$image)->get();
        $meeting= \DB::table('medias')->where('type',$meeting)->get();
        $others= \DB::table('medias')->where('type',$others)->get();
       // dd($meeting,$others,$newspaper);

        return view('blog.media', compact('tv', 'newspaper', 'meeting','others'));
    }
}

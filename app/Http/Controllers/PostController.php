<?php

namespace App\Http\Controllers;

use App\Http\Resources\PostResource;
use App\Models\Post;
use App\Traits\ApiResponser;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PostController extends Controller
{

    use ApiResponser;


    public function index()
    {
        
        $posts = Post::paginate(2);
        // return $this->successResponse(PostResource::collection($posts),210);
        // return PostResource::collection($posts);
        // return $this->errorResponse('error',405);
        return $this->successResponse([
            'posts'=>PostResource::collection($posts),
            'links'=>PostResource::collection($posts)->response()->getData()->links,
            'meta'=>PostResource::collection($posts)->response()->getData()->meta
        ],200);
    }

    public function show(Post $post)
    {
        return $this->successResponse(new PostResource($post),200);

        // return new PostResource($post);
    }

    public function store(Request $request)
    {   
        $validator=Validator::make($request->all(),[
            'title'=>'required|string',
            'body'=>'required|string',
            'image'=>'required|image',
            'user_id'=>'required|exists:users,id'
        ]);

        if($validator->fails())
        {
            return $this->errorResponse($validator->messages(),422);
        }

        
        $imageName=Carbon::now()->microsecond. '.' . $request->image->extension();
        
        $request->image->storeAs('images/posts',$imageName,'public');
       
        $post=Post::create([
            'title'=>$request->title,
            'body'=>$request->body,
            'image'=>$imageName,
            'user_id'=>$request->user_id,
        ]);

        return $this->successResponse($post,201);
    }


    public function update(Request $request,Post $post)
    {
        
        $validator=Validator::make($request->all(),[
            'title'=>'required|string',
            'body'=>'required|string',
            'image'=>'image',
            'user_id'=>'required|exists:users,id',
        ]);

        if($validator->fails())
        {
            return $this->errorResponse($validator->messages(),422);
        }

        if ($request->has('image')) {
            $imageName=Carbon::now()->microsecond . '.' . $request->image->extension();
            $request->image->storeAs('images/posts',$imageName,'public');
        }

        $post->update(
            [
                'title'=>$request->title,
                'body'=>$request->body,
                'image'=>$request->has('image') ? $imageName : $post->image,
                'user_id'=>$request->user_id,
            ]
        );
        return $this->successResponse($post,200);
    }

    public function destroy(Post $post)
    {
        $post->delete();
        return $this->successResponse($post,200);
    }




   
}

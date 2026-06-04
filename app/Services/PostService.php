<?php

namespace App\Services;



use App\Http\Controllers\Traits\ApiResponseTrait;
use App\Http\Resources\PostResource;
use App\Models\News;
use App\Models\Post;
use Illuminate\Http\Request;



class PostService
{
    use ApiResponseTrait;


    public function getPosts(Request $request)
    {
    $cities      = $request->input('cities', []);
    $governorates = $request->input('governorates', []);

    $posts = Post::select('news_id', 'title', 'created_at')
        ->with([
            'news.newsType',
            'news.address.city.governorate',
            'news.media' => function ($query) {
                $query->where('media_type_id', 1);
            },
            'news.report' => function ($q) {
                $q->selectRaw(
                    'news_id, ST_X(location) as longitude, ST_Y(location) as latitude'
                );
            },
            //'news.report:news_id,location',
        ])
        ->whereHas('news.address.city', function ($query) use ($cities, $governorates) {
            if (!empty($cities)) {
                $query->whereIn('name', $cities);
            }
            if (!empty($governorates)) {
                $query->orWhereHas('governorate', function ($q) use ($governorates) {
                    $q->whereIn('name', $governorates);
                });
            }
        })
        ->latest()
        ->paginate(2);
 
    return $this->apiResponse(PostResource::collection($posts), 'posts fetched successfully', 200);
    }
    public function hetPosts(Request $request)
    {
        // $post = Post::first();

        // return (Post::with('news')->first());
        return(    News::with('media')->find(1)->media
);
    }
}
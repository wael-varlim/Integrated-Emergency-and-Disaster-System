<?php

namespace App\Services;



use App\Http\Controllers\Traits\ApiResponseTrait;
use App\Http\Resources\AdminPostResource;
use App\Http\Resources\NormalPostResource;
use App\Http\Resources\PostCollection;
use App\Models\News;
use App\Models\Post;
use Illuminate\Http\Request;



class PostService
{
    use ApiResponseTrait;


    public function getNormalPosts(Request $request)
    {
        $cities      = $request->input('cities', []);
        $governorates = $request->input('governorates', []);

        $posts = Post::select('news_id', 'created_at')
            ->where('by_admin', false)
            ->with([
                'news:id,address_id',
                'news.newsType:id,type_name',
                'news.newsType.currentTranslation:id,news_type_id,translation',
                'news.address:id,street,city_id',
                'news.address.currentTranslation:id,address_id,translation',
                'news.address.city:id,governorate_id',
                'news.address.city.currentTranslation:id,city_id,translation',
                'news.address.city.governorate.currentTranslation:id,governorate_id,translation',
                'news.media' => function ($query) {
                    $query->select('id', 'model_id', 'media_url')
                        ->where('media_type_id', 1);
                },
                'news.report' => function ($q) {
                    $q->selectRaw(
                        'news_id,
                        ST_X(location) as longitude,
                        ST_Y(location) as latitude'
                    );
                },
            ])
            ->whereHas('news.address.city', function ($query) use ($cities, $governorates) {
                $query->where(function ($q) use ($cities, $governorates) {
                    if (!empty($cities)) {
                        $q->whereIn('name', $cities);
                    }
                    if (!empty($governorates)) {
                        $q->orWhereHas('governorate', function ($inner) use ($governorates) {
                            $inner->whereIn('name', $governorates);
                        });
                    }
                });
            })
            ->latest()
            ->paginate(10);

        return (new PostCollection($posts, NormalPostResource::class))
            ->additional([
                'message' => 'posts fetched successfully',
                'status' => 200,
            ]);
    }


    public function getAdminPosts(Request $request)
    {
        $posts = Post::select('id', 'news_id', 'title', 'created_at')
            ->where('by_admin', true)
            ->with([
                'currentTranslation:id,post_id,translation',
                'news:id,address_id,body',
                'news.newsType:id,type_name',
                'news.newsType.currentTranslation:id,news_type_id,translation',
                'news.address:id,street,city_id',
                'news.address.currentTranslation:id,address_id,translation',
                'news.address.city:id,governorate_id',
                'news.address.city.currentTranslation:id,city_id,translation',
                'news.address.city.governorate.currentTranslation:id,governorate_id,translation',
                'news.media' => function ($query) {
                    $query->select('id', 'model_id', 'media_url')
                        ->where('media_type_id', 1);
                },
            ])
            ->latest()
            ->paginate(10);

        return (new PostCollection($posts, AdminPostResource::class))
            ->additional([
                'message' => 'posts fetched successfully',
                'status' => 200,
            ]);
    }


    public function getPostsLocation(Request $request)
    {
        $locations = Post::join('news', 'posts.news_id', '=', 'news.id')
                        ->join('reports', 'news.id', '=', 'reports.news_id')
                        ->whereDate('posts.created_at', now()->timezone('Asia/Damascus')->toDateString())
                        ->where('posts.by_admin', false)
                        ->selectRaw('ST_X(reports.location) as longitude, ST_Y(reports.location) as latitude')
                        ->get();

        return $this->apiResponse($locations, 'Today\'s post locations retrieved successfully', 200);
                        //return "hi";
    }
    
}
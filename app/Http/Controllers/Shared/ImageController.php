<?php

namespace App\Http\Controllers\Shared;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;

class ImageController extends Controller
{
    private $image;

    public function __construct(\App\Model\Image $image)
    {
        $this->image = $image;
    }

    /*
     * $request->image
     * image: object
     * -----------------
     * url: domain/api/shared/upload-single-image?folder=slider&height=400&with=400
     * */
    public function uploadSingleImage(Request $request)
    {
        $imageName = '';
        $imagePath = '';
        $folder = '';
        if ($request->hasFile('image')) {
            $image_tmp = $request->file('image');
            // Get image extension
            $imageExtension = $image_tmp->getClientOriginalExtension();
            // Generate new image name
            $imageName = Str::random(20) . '.' . $imageExtension;
            // Generate image path
            if (!empty($request->folder)) {
                $imagePath = 'public/image/' . $request->folder . '/' . $imageName;
            } else {
                $imagePath = 'public/image/' . $imageName;
            }
            // Upload the image
            if (!empty($request->height) && !empty($request->with)) {
                Image::make($image_tmp)->resize($request->with, $request->height)->save(storage_path('app/' . $imagePath));
            } else {
                Image::make($image_tmp)->save(storage_path('app/' . $imagePath));
            }
        }

        if (!empty($request->folder)) {
            $imagePath = 'storage/image/' . $request->folder . '/' . $imageName;
            $folder = 'storage/image' . '/' . $request->folder;
        } else {
            $imagePath = 'storage/image' . $imageName;
            $folder = 'storage/image';
        }

        $this->image->create([
            'image' => $imageName,
            'image_path' => $imagePath,
            'folder' => $folder
        ]);

        $response['data'] = [
            'image' => $imageName,
            'image_path' => $imagePath,
            'folder' => $folder
        ];
        $response['status'] = 1;
        $response['code'] = 200;
        $response['message'] = 'Single Image Uploaded Successfully';
        return response()->json($response);
    }

    /*
     * $request->folder
     * folder: folder name
     * $request->image_id
     * image_id: image id
     * */
    public function getAllImage(Request $request)
    {
        if (!empty($request->image_id)) {
            $images = $this->image->where(['folder' => $request->folder, 'id' => $request->image_id])->get();
            if ($images->count() > 0) {
                $image_array2 = $this->image->where('folder', $request->folder)->get();
                foreach ($image_array2 as $item) {
                    if ($item->id == $images[0]->id) {
                        continue;
                    } else {
                        $images[] = $item;
                    }
                }
            } else {
                $images = $this->image->where('folder', $request->folder)->get();
            }
        } else {
            $images = $this->image->where('folder', $request->folder)->get();
        }

        $response['data'] = $images;
        $response['status'] = 1;
        $response['code'] = 200;
        $response['message'] = 'Get All Image Successfully';
        return response()->json($response);
    }

//    public function getSingleImage($image)
//    {
//        $image = $this->image->where('image', $image)->first();
//        if (!empty($image)) {
//            $response['data'] = $image;
//            $response['status'] = 1;
//            $response['code'] = 200;
//            $response['message'] = 'Get Single Image Successfully';
//        }
//        else {
//            $response['data'] = '';
//            $response['status'] = 0;
//            $response['code'] = 404;
//            $response['message'] = 'Get Single Image Failed';
//        }
//        return response()->json($response);
//    }

    /*
     * $id: image id
     * */
    public function deleteSingleImage($id)
    {
        $image = $this->image->find($id);
        $image->delete();

        if (!empty($image) && file_exists($image->image_path)) {
            unlink($image->image_path);
        }

        $response['status'] = 1;
        $response['code'] = 200;
        $response['message'] = 'Deleted Image Successfully';
        return response()->json($response);
    }
}

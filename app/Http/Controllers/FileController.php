<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\File;
use App\Transformers\FileTransformer;
use Storage;
use Auth;
use Validator;

class FileController extends Controller
{
	/**
	 * Upload a new file.
	 *
	 * @param Request $request
	 * @return \Illuminate\Http\Response
	 */
	public function upload(Request $request)
	{
		$user = Auth::user();
		$uploadedFile = $request->file('file');

		// Stope if file is too big, and force json response, since it is most
		// probably a normal request
		$validator = Validator::make($request->all(), [
			'file' => 'bail|required|file|max:5192'
		]);
		if ($validator->fails()) {
			return response()->json($validator->messages(), 400);
		}

		// Stop if something else is wrong
		abort_if(!$uploadedFile->isValid(), 500, 'File upload error.');

		// Store the uploaded file as a File record
		$file = File::createFromUpload($uploadedFile);
		$file->user()->associate($user);
		$file->save();

		$file = fractal($file, new FileTransformer());
		
		return $file;
	}

	/**
	 * View/download a file.
	 *
	 * @param File $file
	 * @param Request $request
	 * @param string $size The size from File's size constants
	 * @param string $token The file token (for security reasons)
	 * @return \Illuminate\Http\Response
	 */
	public function view(Request $request, File $file, $size = File::SIZE_RESIZED, $token)
	{
		// Stop if tokens dont' match
		abort_if(!$token || $token != $file->token, 404);

		// Prepare the internal path with the specified size
		$path = $file->pathForSize($size);

		// Stop if file can't be found
		abort_if(!Storage::exists($path), 404);

		// Get the file path from root, as Storage::class doesn't provide it
		$rootPath = $file->rootPathForSize($size);

		if ($request->download) {
			return response()->download($rootPath, $file->name);
		} else {
			return response()->file($rootPath);
		}
	}
}
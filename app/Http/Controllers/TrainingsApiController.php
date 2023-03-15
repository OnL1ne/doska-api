<?php

namespace App\Http\Controllers;
use App\Role;
use App\Event;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Http\Request;
use App\Training;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class TrainingsApiController extends Controller
{
    public function __construct()
    {
        parent::__construct(Training::TABLE_NAME);
    }

    /**
     * @return Training[]|\Illuminate\Database\Eloquent\Collection
     */
    public function index()
    {
        $user = parent::getAuthUser();
        $role_name = $user->role->name;
        $params = array(Training::ACTIVE => 1);
        if ($role_name === Role::ADMIN) {
            return Training::where($params)->get();
        } else {
            return $user->company->trainings()->where($params)->get();
        }
    }

    /**
     * @param $id
     * @return mixed
     */
    public function show($id)
    {
        return Training::find($id);
    }

    /**
     * @param Request $request
     * @return string
     * @throws \Exception
     */
    public function create(Request $request)
    {
        $user = parent::getAuthUser();
        $data = $request->all();
        $file = $request->file('file');
        $fileName = strtotime(date('d-m-Y H:i:s')).random_int(1, 1000).'_'.$file->getClientOriginalName();
        $mediaPath = '/files/company_'.$user->company->id.'/'.$fileName;
        $result = Storage::disk('public')->put($mediaPath, File::get($file));
        if (!$result) {
            return response()->json(false);
        }

        $training = Training::create([
            Training::TRAINING_TITLE => isset($data['title']) ? $data['title'] : '',
            Training::TRAINING_DESCRIPTION => isset($data['description']) ? $data['description'] : '',
            Training::TRAINING_FILE_NAME => $fileName,
            Training::TRAINING_FILE_SRC => $mediaPath
        ]);

        $company = $user->company;
        $company->trainings()->attach($training->id);

        parent::createEvent(Event::EVENT_CREATE_TRAINING, $training->title, $training->id);

        return response()->json($training);
    }

    /**
     * @param Request $request
     * @param $id
     * @return mixed
     */
    public function update(Request $request, $id)
    {
        $training = Training::findOrFail($id);
        $training->update($request->all());

        parent::createEvent(Event::EVENT_UPDATE_TRAINING, $training->title, $training->id);

        return  response()->json($training);
    }

    /**
     * @param $id
     * @return int
     */
    public function delete($id)
    {
        $userAuth = $this->getAuthUser();
        $training = Training::findOrFail($id);
        $checkTrainingCompany = false;
        foreach ($training->companies as $company) {
            if ($userAuth->company_id === $company->id) {
                $checkTrainingCompany = true;
            }
        }
        if (!$checkTrainingCompany) {
            return response()->json(['error' => 'Wrong company'], 400);
        }

        Storage::disk('public')->delete($training->{Training::TRAINING_FILE_SRC});
        $training->delete();

        parent::createEvent(Event::EVENT_DELETE_TRAINING, $training->title, $training->id);

        return 204;
    }

    /**
     * @param $id
     * @return BinaryFileResponse
     */
    public function downloadFile($id)
    {
        $user = parent::getAuthUser();
        $training = Training::findOrFail($id);
        $headers = array(
            'Content-Description: File Transfer',
            'Content-Type: application/octet-stream',
            'Content-Disposition: attachment; filename="' . $training->file_name . '"',
        );

        Event::create([
            Event::USER_ID => $user->id,
            Event::TITLE => Event::EVENT_DOWNLOAD_TRAINING,
            Event::DESCRIPTION => Event::EVENT_DOWNLOAD_TRAINING.' - '.$training->title.'('.$training->id.')'
        ]);

        return response()->download(Storage::disk('public')->path($training->file_src), $training->file_name, $headers);
    }
}

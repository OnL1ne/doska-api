<?php

namespace App\Http\Controllers;

use App\Event;
use App\License;
use Illuminate\Http\Request;

class LicensesApiController extends Controller
{
    public function __construct()
    {
        parent::__construct(License::TABLE_NAME);
    }

    /**
     * @return mixed
     */
    public function index()
    {
        return License::where(License::ACTIVE, 1)->get();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function create(Request $request)
    {
        $data = $request->all();
        if ($data) {
            $license = License::create($data);
            if ($license && isset($license->id)) {
                parent::createEvent(Event::EVENT_CREATE_LICENSE, $license->title, $license->id);

                return response()->json(License::where(License::LICENSE_ID, $license->id)->first());
            }
        }

        return response()->json(false);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return License::find($id);
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
        $license = License::findOrFail($id);
        $license->update($request->all());

        parent::createEvent(Event::EVENT_UPDATE_LICENSE, $license->title, $license->id);

        return License::where(License::LICENSE_ID, $license->id)->first();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return int
     */
    public function delete($id)
    {
        $license = License::findOrFail($id);
        $license->delete();

        parent::createEvent(Event::EVENT_DELETE_LICENSE, $license->title, $license->id);

        return 204;
    }
}

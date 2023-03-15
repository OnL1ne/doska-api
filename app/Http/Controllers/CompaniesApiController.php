<?php

namespace App\Http\Controllers;

use App\Company;
use App\Event;
use App\License;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;

class CompaniesApiController extends Controller
{
    public function __construct()
    {
        parent::__construct(Company::TABLE_NAME);
    }

    /**
     * Display a listing of the resource.
     *
     * @return Company[]|Collection
     */
    public function index()
    {
        $companies = array();
        $companiesTemp = Company::where(Company::ACTIVE, 1)->with('license')->get();
        if (array($companiesTemp)) foreach ($companiesTemp as $company) {
            $company['license_finish'] = $this->getLicenseFinish($company);
            $companies[] = $company;
        }
        return $companies;
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
            $company = Company::create($data);
            if ($company && isset($company->id)) {
                parent::createEvent(Event::EVENT_CREATE_COMPANY, $company->name, $company->id);

                return response()->json(Company::where(Company::COMPANY_ID, $company->id)->with('license')->first());
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
        return Company::find($id);
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
        $company = Company::findOrFail($id);
        $company->update($request->all());

        parent::createEvent(Event::EVENT_UPDATE_COMPANY, $company->name, $company->id);

        $company = Company::where(Company::COMPANY_ID, $company->id)->with('license')->first();
        $company['license_finish'] = $this->getLicenseFinish($company);
        return $company;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return int
     */
    public function delete($id)
    {
        $company = Company::findOrFail($id);
        $company->delete();

        parent::createEvent(Event::EVENT_DELETE_COMPANY, $company->name, $company->id);

        return 204;
    }

    /**
     * @param $company
     * @return bool|false|string
     */
    private function getLicenseFinish($company) {
        if (!$company) return false;
        return date('Y-m-d', strtotime('+'.$company['license']->validity.' day', strtotime($company['license_start'])));
    }
}

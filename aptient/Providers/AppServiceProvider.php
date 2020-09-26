<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use App\needs;
use App\status_desc;
use App\place;
use App\patient_personal;
use App\Service;
use App\City;
use App\Area;
use App\status_class;
use App\Committee;
use App\CommitteeClass;
use App\CommitteePatient;
use App\diagnosis;
use App\committee_decision;
use App\app_type;
class AppServiceProvider extends ServiceProvider {

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot() {
        Schema::defaultStringLength(191);

        $services = Service::select('id', 'service_name')->get();
        $patient = patient_personal::select('id', 'name', 'identity')->get();
        $needs = needs::select('id', 'need_name')->get();
        $city = City::select('id', 'city_name')->get();
        $area = Area::select('id', 'area_name')->get();
        $place = place::select('id', 'place_name')->get();
        $status_class = status_class::select('id', 'status_class_name')->get();
        $status_desc = status_desc::select('id', 'status_desc_name')->get();
        $committee = Committee::select('id', 'comit_date')->get();
        $committeeClass = CommitteeClass::select('id', 'committee_class_name')->get();
        $committee_decision = committee_decision::select('id', 'committee_decision_name')->get();

        $app_type = app_type::select('id', 'app_type_name')->get();
        $treatment_duration = CommitteePatient::where('treatment_duration','!=','')->select('treatment_duration')->distinct('treatment_duration')->get();
        $travel_place = \App\patient_travel::select('travel_place')->distinct('travel_place')->get();

        View::share(['travel_place'=>$travel_place,'app_type' => $app_type,'committee_decision' => $committee_decision,'treatment_duration' => $treatment_duration,'committee' => $committee, 'committeeClass' => $committeeClass, 'status_class' => $status_class, 'area' => $area, 'city' => $city, 'services' => $services, 'patient' => $patient, 'needs' => $needs, 'place' => $place, 'status_desc' => $status_desc,]);

    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register() {
        //
    }

}

<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\AdministerUser;
use App\Models\AdministerRole;
use App\Models\Report;
use App\Models\ReportHeading;
use App\Models\Employer;
use App\Observers\AdministerUserObserver;
use App\Observers\AdministerRoleObserver;
use App\Observers\ReportObserver;
use App\Observers\ReportHeadingObserver;
use App\Observers\EmployerObserver;

class AppServiceProvider extends ServiceProvider {

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register() {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot() {
        AdministerUser::observe(AdministerUserObserver::class);
        AdministerRole::observe(AdministerRoleObserver::class);
        ReportHeading::observe(ReportHeadingObserver::class);
        Report::observe(ReportObserver::class);
        Employer::observe(EmployerObserver::class);
    }
}

<?php

namespace App\Providers;

use App\Admin;
use App\Certificate;
use App\ComputeInstance;
use App\ComputeInstancePackage;
use App\Currency;
use App\Image;
use App\IPPool\IPv4;
use App\IPPool\IPv4Assignment;
use App\IPPool\IPv6;
use App\IPPool\IPv6Assignment;
use App\Node\ComputeNode;
use App\PaymentModule;
use App\PaymentTrade;
use App\PaymentTradeRefund;
use App\Policies\AdminPolicy;
use App\Policies\CertificatePolicy;
use App\Policies\ComputeInstancePackagePolicy;
use App\Policies\ComputeInstancePolicy;
use App\Policies\ComputeNodePolicy;
use App\Policies\CurrencyPolicy;
use App\Policies\IPAssignmentPolicy;
use App\Policies\IPPoolPolicy;
use App\Policies\LocalVolumePolicy;
use App\Policies\NetworkInterfacePolicy;
use App\Policies\PaymentModuleConfigurationPolicy;
use App\Policies\PaymentTradePolicy;
use App\Policies\PaymentTradeRefundPolicy;
use App\Policies\PublicFloppyPolicy;
use App\Policies\PublicImagePolicy;
use App\Policies\PublicISOPolicy;
use App\Policies\RegionPolicy;
use App\Policies\TicketDepartmentPolicy;
use App\Policies\TicketPolicy;
use App\Policies\TrafficShareGroupPolicy;
use App\Policies\UserPolicy;
use App\Policies\UserQuotaPolicy;
use App\Policies\ZonePolicy;
use App\PublicFloppy;
use App\PublicISO;
use App\Region;
use App\Ticket\Department;
use App\Ticket\Ticket;
use App\TrafficShareGroup;
use App\User;
use App\UserQuota;
use App\Zone;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        Certificate::class => CertificatePolicy::class,
        Region::class => RegionPolicy::class,
        TrafficShareGroup::class => TrafficShareGroupPolicy::class,
        Zone::class => ZonePolicy::class,
        ComputeNode::class => ComputeNodePolicy::class,
        Image::class => PublicImagePolicy::class,
        PublicISO::class => PublicISOPolicy::class,
        PublicFloppy::class => PublicFloppyPolicy::class,
        ComputeInstancePackage::class => ComputeInstancePackagePolicy::class,
        IPv4::class => IPPoolPolicy::class,
        IPv6::class => IPPoolPolicy::class,
        UserQuota::class => UserQuotaPolicy::class,
        User::class => UserPolicy::class,
        ComputeInstance::class => ComputeInstancePolicy::class,
        ComputeInstance\LocalVolume::class => LocalVolumePolicy::class,
        ComputeInstance\Device\NetworkInterface::class => NetworkInterfacePolicy::class,
        IPv4Assignment::class => IPAssignmentPolicy::class,
        IPv6Assignment::class => IPAssignmentPolicy::class,
        Currency::class => CurrencyPolicy::class,
        PaymentModule::class => PaymentModuleConfigurationPolicy::class,
        PaymentTrade::class => PaymentTradePolicy::class,
        PaymentTradeRefund::class => PaymentTradeRefundPolicy::class,
        Department::class => TicketDepartmentPolicy::class,
        Ticket::class => TicketPolicy::class,
        Admin::class => AdminPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        //
    }
}

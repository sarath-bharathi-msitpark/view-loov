<?php

namespace App\Models;

use DB;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Models\Permission;

class Plan extends Model
{
    protected $fillable = [
        'name',
        'price',
        'duration',
        'max_users',
        'max_customers',
        'max_venders',
        'max_clients',
        'trial',
        'trial_days',
        'description',
        'image',
        'crm',
        'hrm',
        'account',
        'project',
        'pos',
        'chatgpt',
        'storage_limit',
        'company_id',
        'total_amount',
        'tax',
        'description',
        'plan_type',
        'features',
        'backup_duration',
        'cashfree_plan_id'
    ];

    private static $getplans = NULL;

    public static $arrDuration = [
        'lifetime' => 'Lifetime',
        'month'    => 'Per Month',
        '3month'   => '3 Months',
        'year'     => 'Per Year',
        'day'     => 'Per Day',
    ];

    
    public function company()
    {
        return $this->belongsTo(User::class, 'company_id');
    }
    
    public function features()
    {
        return $this->hasMany(PlanFeature::class);
    }

    
    public function planPermissions()
    {
        return $this->hasMany(PlanPermission::class, 'plan_id');
    }
    
    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'plan_permissions', 'plan_id', 'permission_id');
    }

    public function permissionHas($permissionId)
    {
        return $this->permissions()->where('permissions.id', $permissionId)->exists();
    }

    public function status()
    {
        return [
            __('lifetime'),
            __('Per Month'),
            __('Per Year'),
        ];
    }

    public static function total_plan()
    {
        return Plan::count();
    }

    public static function most_purchese_plan()
    {
        $free_plan = Plan::where('price', '<=', 0)->first()->id;
        $plan =  User::select(DB::raw('count(*) as total') , 'plan')->where('type', '=', 'company')->where('plan', '!=', $free_plan)->groupBy('plan')->first();

        return $plan;
    }

    public static function getPlan($id)
    {
        if(self::$getplans == null)
        {
            $plan = Plan::find($id);
            self::$getplans = $plan;
        }

        return self::$getplans;
    }
    
    public static function getTotalPlanAmount($planId)
    {
       $plan = Plan::find($planId);
    
        if (!$plan) {
            return null;
        }
        
        if(empty($plan->company_id) && $plan->plan_type == "common") {
            $userCount = (int) 1;
        } else {
            $userCount = (int) $plan->max_users;
        }
    
        $amount = (float) $plan->price;
        $tax = (float) $plan->tax;
    
        $taxPercentage = $tax / 100;
        $totalAmount = ($amount * $userCount) + ($amount * $userCount * $taxPercentage);
    
        return round($totalAmount);
    }
    
    public function getSingleUserPlanAmount()
    {
        $amount = (float) $this->price;
        $tax = (float) $this->tax;
    
        $taxPercentage = $tax / 100;
        $totalAmount = $amount + ($amount * $taxPercentage);
    
        return round($totalAmount);
    }
    
    public function getTotalPlanAmountWithAdditionalUsers(int $additionalUsers = 0)
    {
        $currentUsers = (int) $this->max_users ?: 1;
        $totalUsers = $currentUsers + max(0, $additionalUsers);
    
        $pricePerUser = (float) $this->price;
        $taxPercentage = (float) $this->tax / 100;
    
        $amountPerUserWithTax = $pricePerUser + ($pricePerUser * $taxPercentage);
    
        return round($totalUsers * $amountPerUserWithTax);
    }
    
    public function getAdditionalUsersAmount(int $additionalUsers = 0)
    {
        if ($additionalUsers <= 0) {
            return 0;
        }
    
        $pricePerUser = (float) $this->price;
        $taxPercentage = (float) $this->tax / 100;
    
        $amountPerUserWithTax = $pricePerUser + ($pricePerUser * $taxPercentage);
    
        return round($additionalUsers * $amountPerUserWithTax);
    }




}

<?php

namespace Database\Factories;

use App\Models\Organization;
use App\Models\OrganizationSetting;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\OrganizationSetting>
 */
class OrganizationSettingFactory extends Factory
{
    protected $model = OrganizationSetting::class;

    public function definition(): array
    {
        return [
            'organization_id' => Organization::factory(),
            'company_name'    => null,
            'company_email'   => null,
            'company_phone'   => null,
            'company_address' => null,
            'company_city'    => null,
            'company_state'   => null,
            'company_zip'     => null,
            'company_website' => null,
            'logo_path'       => null,
            'default_tax_rate' => 0,
        ];
    }
}

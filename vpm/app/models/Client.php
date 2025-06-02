<?php

namespace App\models;

class Client extends BaseModel
{
    public $id;
    public $name;
    public $alias;
    public $number;
    public $rfc;
    public $email;
    public $business_name;
    public $tax_address;
    public $contact_data;
    public $active;
    public $region_id;
    public $sector_id;
    public $currency_molecule_id;
    public $currency_service_id;
    public $molecule_unit_id;
    public $service_unit_id;
    public $billing_unit_id;
    public $billing_period_id;
    public $gas_cfdi_use_id;
    public $service_cfdi_use_id;
    public $gn_molecule_delivery_perm;
    public $gn_service_description;
    public $hsc_rate;
    public $apply_rate_1_025;
    public $fuel_over_hsc;
    public $gnc_service_rate;
    public $transport_bf_rate;
    public $transport_bi_rate;
}

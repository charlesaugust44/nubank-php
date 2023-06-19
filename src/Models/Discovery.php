<?php

namespace CharlesAugust44\NubankPHP\Models;

class Discovery extends Base
{
    public string $register_prospect_savings_web;
    public string $register_prospect_savings_mgm;
    public string $pusher_auth_channel;
    public string $register_prospect_debit;
    public string $reset_password;
    public string $register_prospect_ultraviolet_web;
    public string $business_card_waitlist;
    public string $register_prospect;
    public string $register_prospect_savings_request_money;
    public string $register_prospect_global_web;
    public string $register_prospect_c;
    public string $request_password_reset;
    public string $auth_gen_certificates;
    public string $login;
    public string $email_verify;
    public string $ultraviolet_waitlist;
    public string $auth_device_resend_code;
    public string $msat;
    public string $company_social_invite_by_slug;
    public string $application_status_by_tax_id;
    public string $lobby_offers;
    public string $application_status_by_prospect_id;
    public string $register_prospect_company;
    public string $get_customer_sessions;

    protected function getClassName(): string
    {
        return self::class;
    }
}

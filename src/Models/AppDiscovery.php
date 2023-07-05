<?php

namespace CharlesAugust44\NubankPHP\Models;
/** @property string[] $faq */
class AppDiscovery extends Base
{
    public string $scopes;
    public string $creation;
    public string $rosetta_images;
    public string $change_password;
    public string $smokejumper;
    public string $block;
    public string $lift;
    public string $shard_mapping_id;
    public string $force_reset_password;
    public string $rosetta_localization;
    public string $revoke_token;
    public string $userinfo;
    public string $reset_password;
    public string $unblock;
    public string $shard_mapping_cnpj;
    public string $shard_mapping_cpf;
    public string $register_prospect;
    public string $engage;
    public string $creation_with_credentials;
    public string $magnitude;
    public string $revoke_all;
    public string $user_update_credential;
    public string $user_hypermedia;
    public string $gen_certificate;
    public string $email_verify;
    public string $token;
    public string $account_recovery;
    public string $start_screen_v2;
    public string $scopes_remove;
    public string $approved_products;
    public string $admin_revoke_all;
    public array $faq;  // Nested object
    public string $ultraviolet_product_interest_screen;
    public string $ultraviolet_register_product_interest;
    public string $scopes_add;
    public string $registration;
    public string $global_services;
    public string $start_screen;
    public string $user_change_password;
    public string $rosetta_localizations_by_locale;
    public string $remote_config;
    public string $fog_wall_discovery;
    public string $account_recovery_token;
    public string $user_status;
    public string $engage_and_create_credentials;
    public string $unlogged_challenge_platform;
    public string $foundation_tokens;
    public string $application_status_by_tax_id;
    public string $lobby_offers;
    public string $account_recovery_v2;
    public string $send_data_to_etl;
    public string $register_prospect_mobile_mgm_social;
    public string $deferred_deeplink_application;
    public string $application_status_by_prospect_id;
    public string $produce_marketing_event;
    public string $force_reset_password_without_revoking_token;
    public string $unlogged_area;
    public string $start_screen_v4;

    protected function getClassName(): string
    {
        return self::class;
    }

    protected function getArrayType(string $key): ?string
    {
        return match ($key) {
            'faq' => 'string',
            default => null
        };
    }

}

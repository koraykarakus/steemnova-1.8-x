<?php

class Login
{
    public const unset_id = 0;
    public const success = 1;
    public const csrf_wrong = 2;
    public const password_empty = 3;
    public const mail_empty = 4;
    public const form_is_valid = 5;
    public const login_data_not_found = 6;
    public const verify_st_wrong_data = 7;
    public const verify_token_wrong_data = 8;
    public const verify_token_not_match = 9;
    public const verify_token_no_email = 10;
    public const verify_token_email_not_match = 11;
    public const verify_success_token = 12;
    public const verify_success_st = 13;
    public const verify_success_external = 14;
    public const login_captcha_wrong = 15;
}

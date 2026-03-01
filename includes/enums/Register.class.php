<?php

class Register
{
    public const success = 1;
    public const game_disabled = 2;
    public const register_disabled = 3;
    public const wrong_secret_question = 4;
    public const empty_secret_question_ans = 5;
    public const too_long_secret_quest_ans = 6;
    public const secret_question_success = 7;
    public const user_name_empty = 8;
    public const user_name_not_valid = 9;
    public const pass_too_short = 10;
    public const pass_too_long = 11;
    public const mail_not_valid = 12;
    public const mail_empty = 13;
    public const mail_too_long = 14;
    public const rules_not_checked = 15;
    public const form_is_valid = 16;
    public const csrf_wrong = 17;
    public const username_exists_in_db = 18;
    public const email_exists_in_db = 19;
    public const register_success_verify_with_mail_existing = 20;
    public const recaptcha_error = 21;
    public const register_success_verify_with_mail = 22;
    public const register_success_no_verify = 23;
}

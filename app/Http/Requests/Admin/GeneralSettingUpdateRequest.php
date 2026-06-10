<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class GeneralSettingUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'site_name' => ['required', 'string', 'max:120'],
            'site_slogan' => ['nullable', 'string', 'max:200'],
            'copyright' => ['nullable', 'string', 'max:255'],
            'logo' => ['nullable', 'file', 'extensions:png,jpg,jpeg,webp,svg,gif,avif,bmp', 'max:4096'],
            'favicon' => ['nullable', 'file', 'extensions:png,jpg,jpeg,webp,svg,ico,gif', 'max:1024'],
            'mail_mailer' => ['nullable', 'in:log,smtp'],
            'mail_scheme' => ['nullable', 'in:tls,ssl'],
            'mail_host' => ['nullable', 'required_if:mail_mailer,smtp', 'string', 'max:255'],
            'mail_port' => ['nullable', 'required_if:mail_mailer,smtp', 'integer', 'between:1,65535'],
            'mail_username' => ['nullable', 'string', 'max:255'],
            'mail_password' => ['nullable', 'string', 'max:255'],
            'mail_from_address' => ['nullable', 'required_if:mail_mailer,smtp', 'email', 'max:255'],
            'mail_from_name' => ['nullable', 'string', 'max:120'],
        ];
    }

    public function attributes(): array
    {
        return [
            'site_name' => 'nombre del sitio',
            'site_slogan' => 'slogan',
            'copyright' => 'texto de copyright',
            'logo' => 'logo',
            'favicon' => 'favicon',
            'mail_mailer' => 'mailer de correo',
            'mail_scheme' => 'esquema SMTP',
            'mail_host' => 'host SMTP',
            'mail_port' => 'puerto SMTP',
            'mail_username' => 'usuario SMTP',
            'mail_password' => 'contraseña SMTP',
            'mail_from_address' => 'correo remitente',
            'mail_from_name' => 'nombre remitente',
        ];
    }
}

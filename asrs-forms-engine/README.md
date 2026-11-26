# ASRS Forms Engine

A clean, production-ready WordPress forms platform built around **WordPress lead storage + Zoho CRM API v7 sync**, with all forms stored cleanly inside the plugin's `forms/` folder and all client/customer emails & automations handled natively by Zoho CRM.

---

## Key Features

- **Forms Live Inside the Plugin (`forms/*.php`)**: Add unlimited forms by dropping PHP config files into `asrs-forms-engine/forms/` — zero `functions.php` edits required.
- **Automatic Shortcodes**: Every form gets `[asrs_form id="<id>"]` and `[asrs_form_<slug>]`.
- **Built-In Form (`forms/contact-product-enquiry.php`)**: Contact / Product Enquiry Form with Full Name, Business Email, Company Name, Enquiry Type (16 options), and How Can We Help.
- **WordPress Backup + Zoho CRM API v7**:
  - Saves every lead in WordPress (`wp_asrs_submissions`) first.
  - Pushes to Zoho CRM v7 (`POST /crm/v7/{module}`) with `"trigger": ["approval", "workflow", "blueprint"]` so **Zoho CRM sends all client & customer emails and runs all workflows natively**.
  - Automatic WP-Cron retry + 1-click manual retry if Zoho ever fails temporarily.
- **4-Screen Admin UI**: `Entries`, `Forms`, `Zoho CRM`, `Settings`.
- **Security**: Nonces, Honeypot, Minimum Submit Time, Cloudflare Turnstile, AES-256-CBC encrypted OAuth secrets, Prepared SQL, CSV Formula-Injection protection.

---

## Adding a New Form Inside the Plugin

Create a new file such as `asrs-forms-engine/forms/sample-request.php`:

```php
<?php
return [
    'id'          => 'sample-request',
    'title'       => 'Polymer Sample Request',
    'zoho'        => [
        'enabled'          => true,
        'module'           => 'Leads',
        'trigger_workflow' => true,
        'field_map'        => [
            'full_name'      => 'Last_Name',
            'business_email' => 'Email',
            'company_name'   => 'Company',
            'grade'          => 'Description',
        ],
        'static_values'    => [
            'Lead_Source' => 'Sample Request Form',
        ],
    ],
    'fields'      => [
        'full_name'      => [ 'label' => 'Full Name', 'type' => 'text', 'required' => true ],
        'business_email' => [ 'label' => 'Business Email', 'type' => 'email', 'required' => true ],
        'company_name'   => [ 'label' => 'Company Name', 'type' => 'text', 'required' => false ],
        'grade'          => [ 'label' => 'Material Grade', 'type' => 'select', 'required' => true, 'options' => [ 'PP', 'HDPE', 'ABS' ] ],
    ],
];
```

Embed anywhere with `[asrs_form id="sample-request"]` or `[asrs_form_sample_request]`.

---

## Zoho CRM OAuth Scopes

- `ZohoCRM.modules.ALL`
- `ZohoCRM.settings.ALL`
- `ZohoCRM.org.READ`

Reference: [Resources | Zoho Developer](https://www.zoho.com/developer/help/)

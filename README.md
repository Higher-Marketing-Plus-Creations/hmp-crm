# Laravel Lead Management CRM

This project is now structured as a client-wise lead CRM and website monitoring workspace. Each client can have separate websites, forms, leads, email delivery history, and monitoring snapshots so data does not stay mixed across all customers.

## Current build status

### Monitoring CRM checklist

1. Website online/offline: Implemented
2. Email delivery status: Implemented
3. Forms submitted this month: Implemented
4. Last successful form: Implemented
5. Failed form count: Implemented
6. Site load time: Implemented
7. Current issues: Implemented
8. Run Test button: Implemented
9. SSL status: Implemented
10. Uptime percentage: Implemented

### New pages added

- `/dashboard`
  Client-wise dashboard with workspace cards instead of only mixed global lead stats.
- `/admin/monitoring`
  Build checklist page to confirm what is already completed.
- `/admin/clients/{client}/workspace`
  Dedicated per-client workspace showing each website and all 10 monitoring items.
- `POST /admin/websites/{website}/run-test`
  Runs a website monitoring snapshot and stores the result in the CRM.

## What it does

- Accepts lead submissions through `POST /api/leads/submit`
- Validates `X-API-KEY` and allowed origin/domain
- Stores website, form, visitor, and raw field payload details
- Queues email notifications and logs sent/failed attempts
- Gives admins a dashboard for clients, websites, forms, leads, email logs, and monitoring workspaces
- Exports filtered leads to CSV
- Includes a reusable frontend JavaScript snippet for any website
- Saves website monitoring snapshots with SSL, uptime, load time, and issue summaries
- Runs automatic monitoring every 5 minutes through Laravel scheduler when server cron is configured

## CRM structure

1. Create a client first
2. Connect one or more websites to that client
3. Add notification emails and allowed domains on the website
4. Connect forms from the website to the CRM API
5. Open the client workspace to review form counts, last success, failures, SSL, uptime, and current issues
6. Use `Run Test` whenever you want a fresh website health snapshot, or let the scheduler run automatically

## Full setup steps

1. Install dependencies:

```bash
composer install
npm install
```

2. Copy environment file and generate app key:

```bash
copy .env.example .env
php artisan key:generate
```

3. Configure database in `.env`:

For SQLite:

```env
DB_CONNECTION=sqlite
```

For MySQL:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=lead_crm
DB_USERNAME=root
DB_PASSWORD=
```

4. Configure mail in `.env`:

```env
MAIL_MAILER=smtp
MAIL_HOST=mail.yourserver.com
MAIL_PORT=587
MAIL_USERNAME=your_smtp_username
MAIL_PASSWORD=your_smtp_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=crm@yourdomain.com
MAIL_FROM_NAME="Lead CRM"
```

5. Optional admin defaults in `.env`:

```env
ADMIN_NAME="Admin User"
ADMIN_EMAIL=admin@example.com
ADMIN_PASSWORD=password123
```

6. Optional reCAPTCHA in `.env`:

```env
RECAPTCHA_ENABLED=true
RECAPTCHA_SECRET=your_google_secret_key
```

7. Run migrations and seed admin user:

```bash
php artisan migrate
php artisan db:seed
```

8. Or create admin manually:

```bash
php artisan app:create-admin-user
```

9. Build frontend assets:

```bash
npm run build
```

10. Run the application and queue worker:

```bash
php artisan serve
php artisan queue:work
```

11. For automatic monitoring in production, run Laravel scheduler every minute:

```bash
* * * * * cd /path-to-project && php artisan schedule:run >> /dev/null 2>&1
```

On Windows Task Scheduler, run this every minute:

```powershell
php C:\path\to\project\artisan schedule:run
```

## Monitoring model

Each website monitoring test stores a snapshot with:

- website online/offline result
- last checked time
- email delivery health status
- forms submitted this month
- latest successful form date
- failed form count
- site load time in milliseconds
- response time in milliseconds
- current issue list
- SSL certificate status
- SSL expiry date
- SSL days left
- uptime percentage based on saved checks
- last error message
- test run timestamp

## API request example

```http
POST /api/leads/submit
X-API-KEY: your_website_api_key
Content-Type: application/json
```

```json
{
  "website_name": "Website Name",
  "page_url": "https://clientsite.com/contact",
  "form_name": "Contact Form",
  "form_identifier": "contact-form",
  "fields": {
    "name": "John Doe",
    "email": "john@email.com",
    "phone": "123456789",
    "message": "Need quote"
  }
}
```

## Frontend JavaScript snippet

```html
<form id="contact-form" data-lead-form data-form-name="Contact Form">
  <input type="text" name="name" required>
  <input type="email" name="email" required>
  <input type="text" name="phone">
  <textarea name="message"></textarea>
  <input type="text" name="website" style="display:none">
  <button type="submit">Send</button>
  <div data-lead-message style="display:none;margin-top:10px;"></div>
</form>

<script src="https://your-domain.com/js/lead-form.js"></script>
<script>
  LeadFormTracker.init({
    selector: '#contact-form',
    endpoint: 'https://your-domain.com/api/leads/submit',
    apiKey: 'your_website_api_key',
    websiteName: 'Client Website',
    formName: 'Contact Form',
    honeypotField: 'website'
  });
</script>
```

## Queue and email retry steps

1. Lead submission is saved immediately
2. Notification email is queued in `jobs`
3. Queue worker sends the email
4. Result is stored in `email_logs`
5. `leads.email_status` becomes `sent` or `failed`
6. Admin can open a failed lead and click `Retry Email`
7. Website monitoring alerts are also queued and duplicate alerts are suppressed until status changes

## Notes

- This project handles lead management, client workspaces, and website monitoring.
- Payment processing is not part of this CRM flow.
- For production, change default admin credentials immediately.
- Automatic monitoring depends on both `php artisan schedule:run` and a running queue worker in production.

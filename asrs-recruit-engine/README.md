# ASRS Recruit Engine

White-label WordPress Careers Portal & Zoho Recruit API v2 integration plugin.

## Features
- **Zero Zoho Branding**: Visitors browse jobs and apply directly on your WordPress domain using `[asrs_careers]`.
- **Direct Job URLs**: Every job opening has a shareable URL (`/careers/?job={id}`) plus `[asrs_careers_apply job_id="..."]`.
- **Idempotent 3-Step Zoho Recruit Sync**:
  1. `POST /recruit/v2/Candidates` (creates candidate)
  2. `POST /recruit/v2/Candidates/{id}/Attachments?attachments_category=Resume` (uploads PDF/DOC/DOCX resume)
  3. `PUT /recruit/v2/Candidates/actions/associate` (links candidate directly to the Job Opening)
- **Local Safety Backup & Auto-Retry**: Every application and resume is backed up locally in `wp_asrs_recruit_applications` and `wp-content/uploads/asrs-recruit-resumes/` (protected with `.htaccess`), with automatic 5-minute WP-Cron retry and 1-click manual retry.

## Quick Setup
1. Upload & activate `asrs-recruit-engine.zip` in WordPress.
2. Create a **Self Client** in Zoho API Console (`https://api-console.zoho.in/` for India or `.com` for US).
3. Generate a Grant Token with scopes:
   `ZohoRecruit.modules.ALL,ZohoRecruit.settings.ALL,ZohoRecruit.org.ALL`
4. Go to **ASRS Careers -> Zoho Recruit & Settings**, save **Client ID** + **Client Secret**, paste the Grant Token, and click **Exchange & Connect**.
5. Go to **ASRS Careers -> Job Openings** and click **Sync Jobs Now**.
6. Create a WordPress page `/careers/` and add `[asrs_careers]`.

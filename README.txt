E-VACCINE  -  SETUP
===================

1. XAMPP / WAMP mein Apache aur MySQL start karo.
2. Ye poora folder htdocs (XAMPP) ya www (WAMP) mein rakho.
3. Fresh install ke liye phpMyAdmin kholo -> "Import" tab -> ye single SQL file import karo:
       database/vaccine_database.sql
   (It creates vaccine_database and all required portal, contact, and feedback tables.)
   Existing installs mein database ko dobara import na karein. Preserve your data
   and run these one-time queries in phpMyAdmin's SQL tab for the new profile,
   vaccination report, and appointment vaccine fields:
       ALTER TABLE bookings ADD COLUMN vaccination_date DATE DEFAULT NULL;
       ALTER TABLE bookings ADD COLUMN vaccine_id INT(11) DEFAULT NULL,
           ADD KEY appointments_vaccine_idx (vaccine_id),
           ADD CONSTRAINT appointments_vaccine FOREIGN KEY (vaccine_id)
               REFERENCES vaccines (Vaccine_ID) ON DELETE SET NULL ON UPDATE CASCADE;
       ALTER TABLE parents ADD COLUMN profile_image VARCHAR(255) DEFAULT NULL;
       ALTER TABLE hospitals ADD COLUMN profile_image VARCHAR(255) DEFAULT NULL;
4. Browser mein kholo:   http://localhost:82/<folder ka naam>/
   (XAMPP Apache is configured to use port 82.)
   The E-Vaccine overview is index.php; about.php, awareness.php, contact.php,
   sitemap.php and terms.php provide the other information pages.

Agar MySQL ka password set hai ya database ka naam alag hai to sirf
config.php ki upar wali 4 lines badalni hain. Poore project ka connection wahin se aata hai;
contact messages and submitted feedback are saved in vaccine_database.
Feedback remains unpublished until an administrator sets is_approved=1 in
customer_feedback after review. Homepage counters/charts use actual database totals.
The WhatsApp business number is intentionally left blank in includes/site-footer.php;
add a verified international number there to enable direct team chat.

ADMIN LOGIN (config.php mein badal sakte ho)
    username: admin
    password: admin123

KAAM KAISE CHALTA HAI
    1. Hospital "Apply to join" se register karta hai  -> status "pending"
    2. Admin  ->  Hospitals  ->  Accept  (tab hi hospital login kar sakta hai)
    3. Hospital login karke apna vaccine stock add karta hai
    4. Parent register karke login karta hai -> bacha register karta hai
       -> accepted hospital mein appointment book karta hai
    5. Hospital approves the appointment, records vaccination, and enters its date
    6. Admin reviews and approves the vaccination record
    7. Parents can view or print their child's vaccination report from the
       dashboard; only admin-approved entries are marked as approved.
    8. Hospital can print a completion certificate for admin-approved records;
       the admin report counts approved vaccinated children

Patient and hospital users can edit their profile details and replace or remove
their profile image from their dashboard. Images are limited to JPEG, PNG, or
WebP up to 2 MB and are stored under uploads/profile-images.

DATABASE FUNCTIONS
    Website contact and feedback forms use prepared statements. Homepage
    dashboard counts and chart totals are read from vaccine_database. The main
    SQL file includes all tables so no second database import is required.

FOLDERS
    index.php    overview page      about.php    platform information
    awareness.php vaccine education contact.php  contact form
    sitemap.php  pages and portal links
    config.php   database connection + helpers
    admin/       admin panel        parents/     parent portal
    hospital/    hospital portal    database/    single SQL schema
    images/      site logo and illustrations
    Extra/       legacy pages, backups and reference assets

THEME COLORS
    Navy blue #12334a for primary navigation and actions, teal #078f87 for
    positive actions, wine #7a2846 for emphasis and errors, and light grey
    #f1f3f5 for page backgrounds. The public theme preference is shared with
    the parent, hospital, and admin pages.

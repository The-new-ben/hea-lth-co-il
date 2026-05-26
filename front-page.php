<?php
/**
 * Front page template.
 */
get_header();
?>
<main id="main" class="site-main">
    <section class="hero">
        <div class="container hero-grid">
            <div>
                <p class="eyebrow">Hea-lth: תיאום שירותי בריאות פרטיים בלי לתת ייעוץ רפואי באתר</p>
                <h1>לפני שמחפשים רופא פרטי, חוות דעת שנייה או בדיקה מהירה, ממיינים צורך, דחיפות וכיסוי ביטוחי</h1>
                <p class="hero-copy">האתר נבנה כנכס לידים לשירותי בריאות בתשלום: רופא פרטי, חוות דעת שנייה, MRI/CT, החזר ביטוח, ביקור רופא בבית ושירותי פרימיום. המטרה היא לתאם ולנתב, לא לאבחן ולא להמליץ על טיפול.</p>
                <div class="hero-actions">
                    <a class="button accent" href="#lead">בדיקת התאמה לשירות</a>
                    <a class="button secondary" href="#money">לתחומי הכסף</a>
                </div>
                <div class="proof-row" aria-label="גבולות רפואיים">
                    <div class="proof-item"><strong>לא חירום</strong><span>במצב מסכן חיים פונים למד״א 101 או למיון, לא לטופס באתר.</span></div>
                    <div class="proof-item"><strong>לא אבחון</strong><span>הטופס מתאם שירותים ומרכז פרטים, אך החלטות רפואיות נשארות אצל רופא מורשה.</span></div>
                    <div class="proof-item"><strong>כיסוי והחזר</strong><span>איסוף ראשוני של קופה, שב״ן או ביטוח פרטי כדי להבין מסלול תשלום אפשרי.</span></div>
                </div>
            </div>
            <aside id="lead" class="lead-card" aria-label="טופס ליד בריאות">
                <h2>איזה שירות בריאות צריך לתאם?</h2>
                <p class="emergency-notice">במקרה חירום רפואי, כאב חזה, קוצר נשימה, סימני שבץ, דימום חמור או סכנת חיים: פנו מיד למד״א 101 או לחדר מיון.</p>
                <p>הפנייה נשמרת ב-CRM פרטי ומנותבת לפי שירות, דחיפות, אזור, מסלול תשלום ובדיקת התאמה.</p>
                <?php if (isset($_GET['lead']) && $_GET['lead'] === 'received') : ?>
                    <p class="notice success">הפנייה התקבלה. נחזור עם כיוון תיאום רלוונטי, בלי לתת אבחון באתר.</p>
                <?php endif; ?>
                <?php if (isset($_GET['lead']) && $_GET['lead'] === 'missing_required') : ?>
                    <p class="notice error">שם, טלפון, שירות, אישור פרטיות ואישור יצירת קשר הם שדות חובה.</p>
                <?php endif; ?>
                <form class="form-grid" method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
                    <input type="hidden" name="action" value="health_lead">
                    <?php wp_nonce_field('health_lead', 'health_nonce'); ?>
                    <input type="hidden" name="landing_url" value="">
                    <input type="hidden" name="referrer_url" value="">
                    <input type="hidden" name="utm_source" value="">
                    <input type="hidden" name="utm_medium" value="">
                    <input type="hidden" name="utm_campaign" value="">
                    <input type="hidden" name="utm_term" value="">
                    <input type="hidden" name="utm_content" value="">
                    <div class="field hp-field" aria-hidden="true"><label for="company_website">אתר חברה</label><input id="company_website" name="company_website" tabindex="-1" autocomplete="off"></div>
                    <div class="field"><label for="lead_name">שם מלא</label><input id="lead_name" name="lead_name" autocomplete="name" required></div>
                    <div class="field"><label for="lead_phone">טלפון</label><input id="lead_phone" name="lead_phone" autocomplete="tel" required></div>
                    <div class="field"><label for="lead_email">אימייל</label><input id="lead_email" name="lead_email" type="email" autocomplete="email"></div>
                    <div class="field"><label for="service_category">שירות מבוקש</label><select id="service_category" name="service_category" required><option value="">בחירת שירות</option><option>רופא פרטי</option><option>חוות דעת שנייה</option><option>MRI / CT</option><option>החזר ביטוח בריאות</option><option>ביקור רופא בבית</option><option>שירותי בריאות פרימיום</option></select></div>
                    <div class="field"><label for="specialty_needed">התמחות / תחום</label><input id="specialty_needed" name="specialty_needed" placeholder="לדוגמה: אורתופדיה, קרדיולוגיה, אונקולוגיה, לא בטוח"></div>
                    <div class="field"><label for="lead_city">עיר / אזור</label><input id="lead_city" name="lead_city" autocomplete="address-level2"></div>
                    <div class="field"><label for="lead_urgency">דחיפות</label><select id="lead_urgency" name="lead_urgency"><option>היום / מחר</option><option>השבוע</option><option>30 יום</option><option>בדיקה ראשונית</option></select></div>
                    <div class="field"><label for="payer_type">מסלול תשלום</label><select id="payer_type" name="payer_type"><option>פרטי</option><option>שב״ן / קופת חולים</option><option>ביטוח בריאות פרטי</option><option>לא יודע/ת</option></select></div>
                    <div class="field"><label for="insurance_provider">קופה / ביטוח</label><input id="insurance_provider" name="insurance_provider" placeholder="מכבי, כללית, הראל, הפניקס, לא ידוע"></div>
                    <div class="field"><label for="preferred_route">מה חשוב עכשיו?</label><select id="preferred_route" name="preferred_route"><option>תור מהיר</option><option>מומחה מסוים</option><option>בדיקת זכאות להחזר</option><option>השוואת אפשרויות</option><option>שירות עד הבית</option></select></div>
                    <div class="field"><label for="age_group">קבוצת גיל</label><select id="age_group" name="age_group"><option>מבוגר/ת</option><option>ילד/ה</option><option>גיל שלישי</option><option>מעדיף/ה לא לציין</option></select></div>
                    <div class="field"><label for="lead_message">מידע כללי לתיאום בלבד</label><textarea id="lead_message" name="lead_message" placeholder="לא לשלוח מידע רגיש או מסמכים רפואיים בשלב זה."></textarea></div>
                    <label class="consent-field"><input type="checkbox" name="privacy_ack" value="yes" required> אני מבין/ה שהאתר אינו מספק אבחון, טיפול או ייעוץ רפואי ושאין לשלוח מידע רפואי רגיש שאינו נדרש לתיאום ראשוני.</label>
                    <label class="consent-field"><input type="checkbox" name="lead_consent" value="yes" required> אני מאשר/ת שמירת פרטי הפנייה ויצירת קשר לצורך תיאום שירות או בדיקת התאמה.</label>
                    <button class="button accent" type="submit">שליחת פנייה לתיאום</button>
                    <p class="notice">הטופס אינו מיועד למצבי חירום ואינו מחליף פנייה לרופא מטפל, מוקד רפואי או מיון.</p>
                </form>
            </aside>
        </div>
    </section>

    <section id="money" class="section money-band">
        <div class="container">
            <div class="section-head">
                <h2>תחומי כסף ראשונים</h2>
                <p>עמודי השירות הראשונים מכוונים לכוונת רכישה גבוהה: תור פרטי, חוות דעת שנייה, בדיקות דימות, החזר ביטוח ושירותי בית.</p>
            </div>
            <div class="cards">
                <article class="card"><h3>רופא פרטי</h3><p>תיאום עם מומחה לפי תחום, מיקום, דחיפות ומסלול תשלום.</p><a href="/private-doctor-appointment/">לטיוטת העמוד</a></article>
                <article class="card"><h3>חוות דעת שנייה</h3><p>מסלול למטופלים שרוצים בדיקה נוספת לאחר ייעוץ ראשוני או לפני החלטה משמעותית.</p><a href="/medical-second-opinion/">לטיוטת העמוד</a></article>
                <article class="card"><h3>MRI / CT</h3><p>תיאום בדיקה פרטית או מסלול החזר לפי צורך, אזור וזמינות.</p><a href="/mri-ct-appointment/">לטיוטת העמוד</a></article>
            </div>
        </div>
    </section>

    <section id="process" class="section">
        <div class="container">
            <div class="section-head">
                <h2>מסלול תיאום שלא מחליף רופא</h2>
                <p>האתר אוסף פרטי תיאום ותשלום כדי למצוא מסלול שירות, ומפריד בבירור בין תיאום לבין החלטה רפואית.</p>
            </div>
            <div class="cards">
                <article class="card"><h3>החזר ביטוח בריאות</h3><p>בדיקת מסלול אפשרי לפי קופה, שב״ן או פוליסה פרטית.</p><a href="/health-insurance-refund/">למבנה העמוד</a></article>
                <article class="card"><h3>ביקור רופא בבית</h3><p>תיאום שירות לבית במקרים שאינם חירום, לפי אזור וזמינות.</p><a href="/doctor-home-visit/">למבנה העמוד</a></article>
                <article class="card"><h3>שירותי בריאות פרימיום</h3><p>ליווי תיאום, רופאים פרטיים, בדיקות, החזרים ושירותי ניהול תהליך.</p><a href="/premium-health-services/">למבנה העמוד</a></article>
            </div>
            <?php echo do_shortcode('[health_medical_disclosure]'); ?>
        </div>
    </section>
</main>
<?php
get_footer();

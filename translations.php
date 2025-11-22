<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Set default language to English if not set
if (!isset($_SESSION['lang'])) {
    $_SESSION['lang'] = 'en';
}

// Handle language switch
if (isset($_GET['lang']) && in_array($_GET['lang'], ['ar', 'en'])) {
    $_SESSION['lang'] = $_GET['lang'];
}

$current_lang = $_SESSION['lang'];

// Translation arrays
$translations = [
    'en' => [
        // Navigation
        'home' => 'Home',
        'specialties' => 'Specialties',
        'doctors' => 'Doctors',
        'about' => 'About',
        'login' => 'Login',
        'signup' => 'Signup',
        'logout' => 'Log out',
        'dashboard' => 'Dashboard',
        'schedule' => 'Schedule',
        'appointment' => 'Appointment',
        'patients' => 'Patients',
        'settings' => 'Settings',
        
        // Hero Section
        'hero_title' => 'Your Health, Our Priority',
        'hero_subtitle' => 'Experience world-class healthcare with our expert medical team',
        'book_appointment' => 'Book Appointment',
        'view_specialties' => 'View Specialties',
        
        // Statistics
        'expert_doctors' => 'Expert Doctors',
        'medical_specialties' => 'Medical Specialties',
        'happy_patients' => 'Happy Patients',
        'appointments' => 'Appointments',
        
        // Sections
        'our_medical_specialties' => 'Our Medical Specialties',
        'our_expert_doctors' => 'Our Expert Doctors',
        'why_choose_us' => 'Why Choose Smart Clinic?',
        'select_a_specialty' => 'Select a Specialty',
        'view_doctors' => 'View Doctors',
        
        // Features
        'quick_appointments' => 'Quick Appointments',
        'quick_appointments_desc' => 'Book your appointment online in minutes, no waiting in queues.',
        'expert_doctors_feature' => 'Expert Doctors',
        'expert_doctors_desc' => 'Our team consists of highly qualified and experienced medical professionals.',
        'secure_private' => 'Secure & Private',
        'secure_private_desc' => 'Your medical records and personal information are kept completely confidential.',
        'easy_management' => 'Easy Management',
        'easy_management_desc' => 'Manage your appointments, view medical records, and chat with doctors all in one place.',
        'direct_communication' => 'Direct Communication',
        'direct_communication_desc' => 'Chat directly with your doctor for consultations and follow-ups.',
        
        // Footer
        'copyright' => '© 2024 Smart Clinic. All rights reserved.',
        
        // Common
        'search' => 'Search',
        'search_doctor' => 'Search Doctor name or Email',
        'search_patient' => 'Search Patient name or Email',
        'search_doctor_or_date' => 'Search Doctor name or Email or Date (YYYY-MM-DD)',
        'no_results' => 'No results found',
        'search_result' => 'Search Result : ',
        'all' => 'All',
        'show_all_sessions' => 'Show all Sessions',
        'starts' => 'Starts:',
        'book_now' => 'Book Now',
        'no_specialties' => 'No specialties available at the moment.',
        'no_doctors' => 'No doctors available at the moment.',
        
        // Admin Dashboard
        'administrator' => 'Administrator',
        'todays_date' => "Today's Date",
        'status' => 'Status',
        'total_doctors' => 'Total Doctors',
        'total_patients' => 'Total Patients',
        'total_appointments' => 'Total Appointments',
        'today_sessions' => "Today's Sessions",
        'upcoming_sessions' => 'Upcoming Sessions',
        'upcoming_sessions_title' => 'Upcoming Sessions (This Week)',
        'no_sessions_found' => "We couldn't find anything related to your keywords !",
        'show_all_sessions' => 'Show all Sessions',
        'back' => 'Back',
        
        // Admin Pages
        'appointment_manager' => 'Appointment Manager',
        'all_patients' => 'All Patients',
        'all_doctors' => 'All Doctors',
        'all_appointments' => 'All Appointments',
        'choose_doctor' => 'Choose Doctor Name from the list',
        'filter' => 'Filter',
        'date' => 'Date',
        'name' => 'Name',
        'email' => 'Email',
        'date_of_birth' => 'Date of Birth',
        'view' => 'View',
        'edit' => 'Edit',
        'delete' => 'Delete',
        'add_new' => 'Add New',
        'specialty' => 'Specialty',
        'telephone' => 'Telephone',
        'patient_id' => 'Patient ID',
        'patient_telephone' => 'Patient Telephone',
        'address' => 'Address',
        'patient_name' => 'Patient name',
        'appointment_number' => 'Appointment number',
        'session_date_time' => 'Session Date & Time',
        'appointment_date' => 'Appointment Date',
        'actions' => 'Actions',
        'events' => 'Events',
        'view_details' => 'View Details.',
        'gender' => 'Gender',
        'male' => 'Male',
        'female' => 'Female',
        'yes' => 'Yes',
        'no' => 'No',
        'are_you_sure' => 'Are you sure?',
        'delete_record' => 'You want to delete this record',
        'next' => 'Next',
        'save' => 'Save',
        'cancel' => 'Cancel',
        'close' => 'Close',
        'title' => 'Title',
        'time' => 'Time',
        'number_of_patients' => 'Number of Patients',
        'session' => 'Session',
        'sessions' => 'Sessions',
        'select' => 'Select',
        'schedule_manager' => 'Schedule Manager',
        'schedule_a_session' => 'Schedule a Session',
        'add_session' => 'Add a Session',
        'session_title' => 'Session Title',
        'scheduled_date_time' => 'Scheduled Date & Time',
        'allowed_1_to_5_patients' => 'Allowed: 1 — 5 patients',
        'appointment_number_hint' => 'The final appointment number for this session depends on this number',
        'session_date' => 'Session Date',
        'confirm_add_session' => 'Confirm & Add Session',
        'add_new_session' => 'Add New Session',
        'session_name_placeholder' => 'Name of this Session',
        'session_placed' => 'Session Placed.',
        'was_scheduled' => 'was scheduled.',
        
        // Patient Pages
        'my_appointments' => 'My Appointments',
        'book_appointment_now' => 'Book Appointment Now',
        'my_profile' => 'My Profile',
        'chat' => 'Chat',
        'live_chat' => 'Live Chat',
        'write_your_message' => 'Write your message...',
        'send' => 'Send',
        'select_doctor' => 'Select Doctor',
        'select_patient' => 'Select Patient',
        'please_select_doctor_to_chat' => 'Please select a doctor to start chatting',
        'please_select_patient_to_chat' => 'Please select a patient to view messages',
        'please_select_patient_first' => 'Please select a patient first',
        'you_must_book_to_chat' => 'You must book an appointment first to communicate with doctors',
        'no_patients_booked' => 'No patients have booked appointments with you yet',
        'book_to_start_chatting' => 'Book an appointment with your doctor first to start chatting',
        'patients_will_appear_here' => 'No patients have booked appointments with you yet. Patients will appear here after booking appointments with you.',
        'please_select_doctor_first' => 'Please select a doctor first',
        'redirect_to_doctors_sessions' => 'Redirect to Doctors sessions?',
        'you_want_to_view_all_sessions_by' => 'You want to view All sessions by',
        'doctors_in' => 'Doctors in',
        'my_bookings_history' => 'My Bookings history',
        'my_sessions' => 'My Sessions',
        'my_patients' => 'My Patients',
        'medical_record_for_patient' => 'Medical Record for patient',
        'medical_records' => 'Medical Records',
        'medical_record' => 'Medical Record',
        'welcome' => 'Welcome!',
        'thanks_for_joining' => 'Thanks for joining with us.',
        'view_my_appointments' => 'View My Appointments',
        'chat_with_patients' => 'Chat With Patients',
        'live_chat_with_patients' => 'Live Chat with Patients',
        'select_patient' => 'Select Patient',
        'select_the_patient' => '-- Select the patient --',
        'weight_kg' => 'Weight (kg):',
        'enter_weight' => 'Enter weight',
        'height_cm' => 'Height (cm):',
        'enter_height' => 'Enter height',
        'allergy' => 'Allergy:',
        'enter_any_allergies' => 'Enter any allergies',
        'surgical_history' => 'Surgical History:',
        'enter_surgical_history' => 'Enter any surgical history',
        'diabetes' => 'Diabetes:',
        'hypertension' => 'Hypertension:',
        'diagnosis' => 'Diagnosis:',
        'enter_diagnosis' => 'Enter diagnosis',
        'treatment' => 'Treatment:',
        'enter_treatment' => 'Enter treatment',
        'additional_notes' => 'Additional Notes:',
        'enter_additional_notes' => 'Enter additional notes',
        'save_record' => 'Save Record',
        'medical_record_saved_successfully' => 'Medical record saved successfully!',
        'my_patients_only' => 'My Patients Only',
        'all_patients' => 'All Patients',
        'show_details_about' => 'Show Details About',
        'filter_button' => 'Filter',
        'show_all_patients' => 'Show all Patients',
        'notes' => 'Notes:',
        'appointment_manager' => 'Appointment Manager',
        'session_title' => 'Session Title',
        'session_date_time' => 'Session Date & Time',
        'you_want_to_delete_this_record' => 'You want to delete this record',
        'patient_name_label' => 'Patient Name:',
        'scheduled_date_time' => 'Scheduled Date & Time',
        'max_num_can_be_booked' => 'Max num that can be booked',
        'show_all_sessions' => 'Show all Sessions',
        'cancel_session' => 'Cancel Session',
        'view_details' => 'View Details.',
        'patient_id' => 'Patient ID',
        'date_label' => 'Date:',
        'show_all_appointments' => 'Show all Appointments',
        'booking_date' => 'Booking Date:',
        'reference_number' => 'Reference Number:',
        'appointment_number_label' => 'Appointment Number:',
        'scheduled_date_label' => 'Scheduled Date:',
        'cancel_booking' => 'Cancel Booking',
        'booking_successfully' => 'Booking Successfully.',
        'your_appointment_number_is' => 'Your Appointment number is',
        'you_want_to_cancel_this_appointment' => 'You want to Cancel this Appointment?',
        'session_name_label' => 'Session Name:',
        'doctor_name_label' => 'Doctor name',
        
        // Settings Pages
        'account_settings' => 'Account Settings',
        'edit_account_details' => 'Edit your Account Details & Change Password',
        'view_account_details' => 'View Account Details',
        'view_personal_information' => 'View Personal information About Your Account',
        'delete_account' => 'Delete Account',
        'will_permanently_remove_account' => 'Will Permanently Remove your Account',
        'edit_user_account_details' => 'Edit User Account Details.',
        'edit_doctor_details_title' => 'Edit Doctor Details.',
        'you_want_to_delete_your_account' => 'You want to delete Your Account',
        'reset' => 'Reset',
        'ok' => 'OK',
        
        // Error Messages
        'error_email_exists' => 'Already have an account for this Email address.',
        'error_password_mismatch' => 'Password Confirmation Error! Reconfirm Password',
        'error_phone_exists' => 'The phone number is already in use.',
        'error_nic_exists' => 'The Identification Number is already in use.',
        'error_invalid_email' => 'Please enter a valid email address.',
        'success_record_added' => 'New Record Added Successfully!',
        'success_record_edited' => 'Edit Successfully!',
        
        // Form Labels
        'add_new_doctor' => 'Add New Doctor',
        'edit_doctor_details' => 'Edit Doctor Details',
        'doctor_id' => 'Doctor ID',
        'auto_generated' => '(Auto Generated)',
        'doctor_name' => 'Doctor Name',
        'doctor_details' => 'Doctor Details',
        'name_label' => 'Name:',
        'email_label' => 'Email:',
        'nic_label' => 'NIC:',
        'telephone_label' => 'Telephone:',
        'specialty_label' => 'Specialty:',
        'identification_number' => 'Identification Number',
        'nic_number' => 'NIC Number',
        'telephone' => 'Telephone',
        'telephone_number' => 'Telephone Number',
        'choose_specialties' => 'Choose specialties',
        'current' => 'Current',
        'define_password' => 'Define a Password',
        'confirm_password' => 'Confirm Password',
        'identification_must_be_10_digits' => 'Identification Number must be exactly 10 digits',
        'example_phone' => 'ex: 0712345678',
        
        // Schedule View
        'doctor_of_this_session' => 'Doctor of this session',
        'scheduled_date' => 'Scheduled Date',
        'scheduled_time' => 'Scheduled Time',
        'patients_already_registered' => 'Patients that Already registered for this session',
    ],
    
    'ar' => [
        // Navigation
        'home' => 'الرئيسية',
        'specialties' => 'التخصصات',
        'doctors' => 'الأطباء',
        'about' => 'من نحن',
        'login' => 'تسجيل الدخول',
        'signup' => 'إنشاء حساب',
        'logout' => 'تسجيل الخروج',
        'dashboard' => 'لوحة التحكم',
        'schedule' => 'الجدول',
        'appointment' => 'المواعيد',
        'patients' => 'المرضى',
        'settings' => 'الإعدادات',
        
        // Hero Section
        'hero_title' => 'صحتك، أولويتنا',
        'hero_subtitle' => 'استمتع برعاية صحية عالمية المستوى مع فريقنا الطبي المختص',
        'book_appointment' => 'احجز موعد',
        'view_specialties' => 'عرض التخصصات',
        
        // Statistics
        'expert_doctors' => 'أطباء متخصصون',
        'medical_specialties' => 'تخصصات طبية',
        'happy_patients' => 'مرضى راضون',
        'appointments' => 'المواعيد',
        
        // Sections
        'our_medical_specialties' => 'التخصصات الطبية لدينا',
        'our_expert_doctors' => 'أطباؤنا المتخصصون',
        'why_choose_us' => 'لماذا تختار العيادة الذكية؟',
        'select_a_specialty' => 'اختر تخصصاً',
        'view_doctors' => 'عرض الأطباء',
        
        // Features
        'quick_appointments' => 'مواعيد سريعة',
        'quick_appointments_desc' => 'احجز موعدك عبر الإنترنت في دقائق، بدون انتظار في الطوابير.',
        'expert_doctors_feature' => 'أطباء متخصصون',
        'expert_doctors_desc' => 'يتكون فريقنا من متخصصين طبيين مؤهلين وذوي خبرة عالية.',
        'secure_private' => 'آمن وخاص',
        'secure_private_desc' => 'تُحفظ سجلاتك الطبية ومعلوماتك الشخصية بسرية تامة.',
        'easy_management' => 'إدارة سهلة',
        'easy_management_desc' => 'قم بإدارة مواعيدك وعرض السجلات الطبية والدردشة مع الأطباء في مكان واحد.',
        'direct_communication' => 'تواصل مباشر',
        'direct_communication_desc' => 'تحدث مباشرة مع طبيبك للاستشارات والمتابعات.',
        
        // Footer
        'copyright' => '© 2024 العيادة الذكية. جميع الحقوق محفوظة.',
        
        // Common
        'search' => 'بحث',
        'search_doctor' => 'ابحث عن اسم الطبيب أو البريد الإلكتروني',
        'search_patient' => 'ابحث عن اسم المريض أو البريد الإلكتروني',
        'search_doctor_or_date' => 'ابحث عن اسم الطبيب أو البريد الإلكتروني أو التاريخ (YYYY-MM-DD)',
        'no_results' => 'لم يتم العثور على نتائج',
        'search_result' => 'نتائج البحث: ',
        'all' => 'الكل',
        'show_all_sessions' => 'عرض جميع الجلسات',
        'starts' => 'يبدأ:',
        'book_now' => 'احجز الآن',
        'no_specialties' => 'لا توجد تخصصات متاحة حالياً.',
        'no_doctors' => 'لا يوجد أطباء متاحون حالياً.',
        
        // Admin Dashboard
        'administrator' => 'المدير',
        'todays_date' => 'تاريخ اليوم',
        'status' => 'الحالة',
        'total_doctors' => 'إجمالي الأطباء',
        'total_patients' => 'إجمالي المرضى',
        'total_appointments' => 'إجمالي المواعيد',
        'today_sessions' => 'جلسات اليوم',
        'upcoming_sessions' => 'الجلسات القادمة',
        'upcoming_sessions_title' => 'الجلسات القادمة (هذا الأسبوع)',
        'no_sessions_found' => 'لم نتمكن من العثور على أي شيء متعلق بكلماتك البحثية!',
        'show_all_sessions' => 'عرض جميع الجلسات',
        'back' => 'رجوع',
        
        // Admin Pages
        'appointment_manager' => 'إدارة المواعيد',
        'all_patients' => 'جميع المرضى',
        'all_doctors' => 'جميع الأطباء',
        'all_appointments' => 'جميع المواعيد',
        'choose_doctor' => 'اختر اسم الطبيب من القائمة',
        'filter' => 'تصفية',
        'date' => 'التاريخ',
        'name' => 'الاسم',
        'email' => 'البريد الإلكتروني',
        'date_of_birth' => 'تاريخ الميلاد',
        'view' => 'عرض',
        'edit' => 'تعديل',
        'delete' => 'حذف',
        'add_new' => 'إضافة جديد',
        'specialty' => 'التخصص',
        'telephone' => 'الهاتف',
        'patient_id' => 'رقم المريض',
        'patient_telephone' => 'هاتف المريض',
        'address' => 'العنوان',
        'patient_name' => 'اسم المريض',
        'appointment_number' => 'رقم الموعد',
        'session_date_time' => 'تاريخ ووقت الجلسة',
        'appointment_date' => 'تاريخ الموعد',
        'actions' => 'الإجراءات',
        'events' => 'الأحداث',
        'view_details' => 'عرض التفاصيل.',
        'gender' => 'الجنس',
        'male' => 'ذكر',
        'female' => 'أنثى',
        'yes' => 'نعم',
        'no' => 'لا',
        'are_you_sure' => 'هل أنت متأكد؟',
        'delete_record' => 'أنت تريد حذف هذا السجل',
        'next' => 'التالي',
        'save' => 'حفظ',
        'cancel' => 'إلغاء',
        'close' => 'إغلاق',
        'title' => 'العنوان',
        'time' => 'الوقت',
        'number_of_patients' => 'عدد المرضى',
        'session' => 'جلسة',
        'sessions' => 'جلسات',
        'select' => 'اختر',
        'doctor_of_this_session' => 'طبيب هذه الجلسة',
        'scheduled_date' => 'التاريخ المحدد',
        'schedule_manager' => 'مدير الجدولة',
        'schedule_a_session' => 'جدولة جلسة',
        'add_session' => 'إضافة جلسة',
        'session_title' => 'عنوان الجلسة',
        'scheduled_date_time' => 'التاريخ والوقت المحدد',
        'allowed_1_to_5_patients' => 'المسموح: 1 — 5 مرضى',
        'appointment_number_hint' => 'رقم الموعد النهائي لهذه الجلسة يعتمد على هذا الرقم',
        'session_date' => 'تاريخ الجلسة',
        'confirm_add_session' => 'تأكيد وإضافة الجلسة',
        'add_new_session' => 'إضافة جلسة جديدة',
        'session_name_placeholder' => 'اسم هذه الجلسة',
        'session_placed' => 'تم جدولة الجلسة.',
        'was_scheduled' => 'تم جدولته.',
        
        // Patient Pages
        'my_appointments' => 'مواعيدي',
        'book_appointment_now' => 'احجز موعد الآن',
        'my_profile' => 'ملفي الشخصي',
        'chat' => 'الدردشة',
        'live_chat' => 'المحادثة المباشرة',
        'write_your_message' => 'اكتب رسالتك...',
        'send' => 'إرسال',
        'select_doctor' => 'اختر الطبيب',
        'select_patient' => 'اختر المريض',
        'please_select_doctor_to_chat' => 'الرجاء اختيار طبيب للبدء في المحادثة',
        'please_select_patient_to_chat' => 'الرجاء اختيار مريض لعرض الرسائل',
        'please_select_patient_first' => 'الرجاء اختيار مريض أولاً',
        'you_must_book_to_chat' => 'يجب عليك الحجز أولاً لتتمكن من التواصل مع الأطباء',
        'no_patients_booked' => 'لا يوجد مرضى محجوزين لديك بعد',
        'book_to_start_chatting' => 'احجز موعداً مع طبيبك أولاً للبدء في المحادثة',
        'patients_will_appear_here' => 'لا يوجد مرضى محجوزين لديك بعد. سيظهر المرضى هنا بعد حجز مواعيد معك.',
        'please_select_doctor_first' => 'الرجاء اختيار طبيب أولاً',
        'redirect_to_doctors_sessions' => 'إعادة التوجيه إلى جلسات الطبيب؟',
        'you_want_to_view_all_sessions_by' => 'أنت تريد عرض جميع الجلسات لـ',
        'doctors_in' => 'الأطباء في',
        'my_bookings_history' => 'تاريخ حجوزاتي',
        'my_sessions' => 'جلساتي',
        'my_patients' => 'مرضاي',
        'medical_record_for_patient' => 'السجل الطبي للمريض',
        'medical_records' => 'السجلات الطبية',
        'medical_record' => 'السجل الطبي',
        'welcome' => 'مرحباً!',
        'thanks_for_joining' => 'شكراً لانضمامك إلينا.',
        'view_my_appointments' => 'عرض مواعيدي',
        'chat_with_patients' => 'الدردشة مع المرضى',
        'live_chat_with_patients' => 'الدردشة المباشرة مع المرضى',
        'select_patient' => 'اختر المريض',
        'select_the_patient' => '-- اختر المريض --',
        'weight_kg' => 'الوزن (كجم):',
        'enter_weight' => 'أدخل الوزن',
        'height_cm' => 'الطول (سم):',
        'enter_height' => 'أدخل الطول',
        'allergy' => 'الحساسية:',
        'enter_any_allergies' => 'أدخل أي حساسيات',
        'surgical_history' => 'التاريخ الجراحي:',
        'enter_surgical_history' => 'أدخل التاريخ الجراحي',
        'diabetes' => 'السكري:',
        'hypertension' => 'ارتفاع ضغط الدم:',
        'diagnosis' => 'التشخيص:',
        'enter_diagnosis' => 'أدخل التشخيص',
        'treatment' => 'العلاج:',
        'enter_treatment' => 'أدخل العلاج',
        'additional_notes' => 'ملاحظات إضافية:',
        'enter_additional_notes' => 'أدخل ملاحظات إضافية',
        'save_record' => 'حفظ السجل',
        'medical_record_saved_successfully' => 'تم حفظ السجل الطبي بنجاح!',
        'my_patients_only' => 'مرضاي فقط',
        'all_patients' => 'جميع المرضى',
        'show_details_about' => 'عرض التفاصيل حول:',
        'filter_button' => 'تصفية',
        'show_all_patients' => 'عرض جميع المرضى',
        'notes' => 'ملاحظات:',
        'appointment_manager' => 'مدير المواعيد',
        'session_title' => 'عنوان الجلسة',
        'session_date_time' => 'تاريخ ووقت الجلسة',
        'you_want_to_delete_this_record' => 'أنت تريد حذف هذا السجل',
        'patient_name_label' => 'اسم المريض:',
        'scheduled_date_time' => 'التاريخ والوقت المحدد',
        'max_num_can_be_booked' => 'الحد الأقصى لعدد الحجوزات',
        'show_all_sessions' => 'عرض جميع الجلسات',
        'cancel_session' => 'إلغاء الجلسة',
        'view_details' => 'عرض التفاصيل.',
        'patient_id' => 'رقم المريض',
        'date_label' => 'التاريخ:',
        'show_all_appointments' => 'عرض جميع المواعيد',
        'booking_date' => 'تاريخ الحجز:',
        'reference_number' => 'رقم المرجع:',
        'appointment_number_label' => 'رقم الموعد:',
        'scheduled_date_label' => 'التاريخ المحدد:',
        'cancel_booking' => 'إلغاء الحجز',
        'booking_successfully' => 'تم الحجز بنجاح.',
        'your_appointment_number_is' => 'رقم موعدك هو',
        'you_want_to_cancel_this_appointment' => 'هل تريد إلغاء هذا الموعد؟',
        'session_name_label' => 'اسم الجلسة:',
        'doctor_name_label' => 'اسم الطبيب',
        
        // Settings Pages
        'account_settings' => 'إعدادات الحساب',
        'edit_account_details' => 'تعديل بيانات حسابك وتغيير كلمة المرور',
        'view_account_details' => 'عرض تفاصيل الحساب',
        'view_personal_information' => 'عرض المعلومات الشخصية حول حسابك',
        'delete_account' => 'حذف الحساب',
        'will_permanently_remove_account' => 'سيتم حذف حسابك بشكل دائم',
        'edit_user_account_details' => 'تعديل بيانات حساب المستخدم.',
        'edit_doctor_details_title' => 'تعديل بيانات الطبيب.',
        'you_want_to_delete_your_account' => 'أنت تريد حذف حسابك',
        'reset' => 'إعادة تعيين',
        'ok' => 'موافق',
        
        // Error Messages
        'error_email_exists' => 'يوجد بالفعل حساب لهذا البريد الإلكتروني.',
        'error_password_mismatch' => 'خطأ في تأكيد كلمة المرور! يرجى إعادة تأكيد كلمة المرور',
        'error_phone_exists' => 'رقم الهاتف مستخدم بالفعل.',
        'error_nic_exists' => 'رقم الهوية مستخدم بالفعل.',
        'error_invalid_email' => 'الرجاء إدخال عنوان بريد إلكتروني صحيح.',
        'success_record_added' => 'تمت إضافة السجل الجديد بنجاح!',
        'success_record_edited' => 'تم التعديل بنجاح!',
        
        // Form Labels
        'add_new_doctor' => 'إضافة طبيب جديد',
        'edit_doctor_details' => 'تعديل بيانات الطبيب',
        'doctor_id' => 'رقم الطبيب',
        'auto_generated' => '(يتم توليده تلقائياً)',
        'doctor_name' => 'اسم الطبيب',
        'doctor_details' => 'تفاصيل الطبيب',
        'name_label' => 'الاسم:',
        'email_label' => 'البريد الإلكتروني:',
        'nic_label' => 'رقم الهوية:',
        'telephone_label' => 'الهاتف:',
        'specialty_label' => 'التخصص:',
        'identification_number' => 'رقم الهوية',
        'nic_number' => 'رقم الهوية',
        'telephone' => 'الهاتف',
        'telephone_number' => 'رقم الهاتف',
        'choose_specialties' => 'اختر التخصص',
        'current' => 'الحالي',
        'define_password' => 'تعريف كلمة المرور',
        'confirm_password' => 'تأكيد كلمة المرور',
        'identification_must_be_10_digits' => 'يجب أن يكون رقم الهوية 10 أرقام بالضبط',
        'example_phone' => 'مثال: 0712345678',
        
        // Schedule View
        'doctor_of_this_session' => 'طبيب هذه الجلسة',
        'scheduled_date' => 'التاريخ المحدد',
        'scheduled_time' => 'الوقت المحدد',
        'patients_already_registered' => 'المرضى المسجلون بالفعل لهذه الجلسة',
    ]
];

// Specialty names translations
$specialty_translations = [
    'en' => [
        'Accident and emergency medicine' => 'Accident and emergency medicine',
        'Paediatrics' => 'Paediatrics',
        'Clinical radiology' => 'Clinical radiology',
        'Dental, oral and maxillo-facial surgery' => 'Dental, oral and maxillo-facial surgery',
        'Cardiology' => 'Cardiology',
        'Internal medicine' => 'Internal medicine',
        'General surgery' => 'General surgery',
        'Gastroenterology' => 'Gastroenterology',
        'Endocrinology' => 'Endocrinology',
        'Nephrology' => 'Nephrology',
        'Neuro-psychiatry' => 'Neuro-psychiatry',
        'Neurosurgery' => 'Neurosurgery',
        'Obstetrics and gynecology' => 'Obstetrics and gynecology',
        'Ophthalmology' => 'Ophthalmology',
        'Orthopaedics' => 'Orthopaedics',
        'Otorhinolaryngology' => 'Otorhinolaryngology',
        // Additional common specialties
        'Dermatology' => 'Dermatology',
        'Neurology' => 'Neurology',
        'Psychiatry' => 'Psychiatry',
        'General Medicine' => 'General Medicine',
        'Radiology' => 'Radiology',
        'Urology' => 'Urology',
        'Oncology' => 'Oncology',
    ],
    'ar' => [
        'Accident and emergency medicine' => 'طب الحوادث والطوارئ',
        'Paediatrics' => 'طب الأطفال',
        'Clinical radiology' => 'الأشعة السريرية',
        'Dental, oral and maxillo-facial surgery' => 'جراحة الفم والوجه والفكين',
        'Cardiology' => 'أمراض القلب',
        'Internal medicine' => 'الطب الباطني',
        'General surgery' => 'الجراحة العامة',
        'Gastroenterology' => 'أمراض الجهاز الهضمي',
        'Endocrinology' => 'الغدد الصماء',
        'Nephrology' => 'أمراض الكلى',
        'Neuro-psychiatry' => 'الطب النفسي العصبي',
        'Neurosurgery' => 'جراحة الأعصاب',
        'Obstetrics and gynecology' => 'طب النساء والتوليد',
        'Ophthalmology' => 'طب العيون',
        'Orthopaedics' => 'جراحة العظام',
        'Otorhinolaryngology' => 'طب الأنف والأذن والحنجرة',
        // Additional common specialties
        'Dermatology' => 'الأمراض الجلدية',
        'Neurology' => 'طب الأعصاب',
        'Psychiatry' => 'الطب النفسي',
        'General Medicine' => 'الطب العام',
        'Radiology' => 'الأشعة',
        'Urology' => 'جراحة المسالك البولية',
        'Oncology' => 'الأورام',
    ]
];

// Function to translate specialty name
function translateSpecialty($specialty_name) {
    global $specialty_translations, $current_lang;
    if (isset($specialty_translations[$current_lang][$specialty_name])) {
        return $specialty_translations[$current_lang][$specialty_name];
    }
    // If translation not found, return original name
    return $specialty_name;
}

// Function to get icon for specialty based on name
function getSpecialtyIcon($specialty_name) {
    $specialty_icons = [
        'Accident and emergency medicine' => '🚑',
        'Paediatrics' => '👶',
        'Clinical radiology' => '🔬',
        'Dental, oral and maxillo-facial surgery' => '🦷',
        'Cardiology' => '🫀',
        'Internal medicine' => '🩺',
        'General surgery' => '⚕️',
        'Gastroenterology' => '🫁',
        'Endocrinology' => '🧬',
        'Nephrology' => '💊',
        'Neuro-psychiatry' => '🧠',
        'Neurosurgery' => '🧠',
        'Obstetrics and gynecology' => '👩',
        'Ophthalmology' => '👁️',
        'Orthopaedics' => '🦴',
        'Otorhinolaryngology' => '👂',
        // Additional mappings
        'Dermatology' => '💉',
        'Neurology' => '🧠',
        'Psychiatry' => '🧠',
        'General Medicine' => '🩺',
        'Radiology' => '🔬',
        'Urology' => '💊',
        'Oncology' => '🔬',
        'Surgery' => '⚕️',
        'Emergency Medicine' => '🚑',
        'Dentistry' => '🦷',
        'Gynecology' => '👩',
        'Orthopedics' => '🦴',
    ];
    
    // Check for exact match first
    if (isset($specialty_icons[$specialty_name])) {
        return $specialty_icons[$specialty_name];
    }
    
    // Check for partial matches (case insensitive)
    $specialty_lower = strtolower($specialty_name);
    foreach ($specialty_icons as $key => $icon) {
        if (stripos($specialty_lower, strtolower($key)) !== false || stripos(strtolower($key), $specialty_lower) !== false) {
            return $icon;
        }
    }
    
    // Default icon if no match found
    return '🩺';
}

// Function to get translation
function t($key, $default = '') {
    global $translations, $current_lang;
    if (isset($translations[$current_lang][$key])) {
        return $translations[$current_lang][$key];
    }
    return $default !== '' ? $default : $key;
}

// Function to get current language
function getLang() {
    global $current_lang;
    return $current_lang;
}

// Function to check if current language is Arabic
function isArabic() {
    return getLang() === 'ar';
}
?>


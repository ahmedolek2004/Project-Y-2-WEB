# نظام قاعدة البيانات الصحية الوطنية

## National Health Database System

### 🎯 المميزات الرئيسية

- ✅ نظام تسجيل دخول آمن مع تشفير كلمات المرور
- ✅ ثلاث أنواع مستخدمين: مسؤول، طبيب، مريض
- ✅ لوحات تحكم منفصلة لكل نوع مستخدم
- ✅ إدارة السجلات الطبية
- ✅ إدارة الروشتات الطبية
- ✅ إحصائيات شاملة للنظام
- ✅ حماية آمنة ضد هجمات SQL Injection
- ✅ التحقق من صلاحيات المستخدمين

---

## 🚀 خطوات البدء السريع

### 1️⃣ متطلبات النظام

- PHP 7.4+
- MySQL 5.7+
- Apache/Nginx Web Server
- Composer (اختياري)

### 2️⃣ إعداد قاعدة البيانات

#### الطريقة الأولى: استخدام ملف SQL

```bash
# في MySQL
mysql -u root -p < src/config/DB.sql
```

أو انسخ والصق محتوى `src/config/DB.sql` في MySQL مباشرة

#### الطريقة الثانية: إنشاء الجداول يدويا

```sql
CREATE DATABASE national_health_db;
USE national_health_db;

-- يمكنك نسخ جميع جداول من ملف DB.sql
```

### 3️⃣ تعديل بيانات الاتصال بالقاعدة

في ملف `src/config/Database.php`:

```php
$this->conn = new PDO(
    "mysql:host=localhost;dbname=national_health_db",
    "root",           // اسم المستخدم (غير رقم المرور إذا لزم الحال)
    ""                // كلمة المرور (اتركها فارغة إذا لم تكن موجودة)
);
```

### 4️⃣ إضافة بيانات الاختبار

```bash
# قم بتشغيل هذا الملف مرة واحدة
php src/setup/create_test_data.php
```

أو أضف المستخدمين يدويا في قاعدة البيانات.

### 5️⃣ تشغيل الخادم

```bash
# طريقة 1: استخدام PHP المدمج
php -S localhost:8000

# ثم افتح في المتصفح:
# http://localhost:8000/src/
```

---

## 👤 حسابات الاختبار

| الدور      | البريد الإلكتروني | كلمة المرور |
| ---------- | ----------------- | ----------- |
| **مسؤول**  | admin@test.com    | password123 |
| **طبيب**   | doctor@test.com   | password123 |
| **طبيب 2** | doctor2@test.com  | password123 |
| **مريض**   | patient1@test.com | password123 |
| **مريض 2** | patient2@test.com | password123 |
| **مريض 3** | patient3@test.com | password123 |

---

## 📁 هيكل المشروع

```
src/
├── index.php                          # صفحة تسجيل الدخول الرئيسية
├── assets/
│   └── style.css                      # أنماط النظام
├── classes/
│   ├── User.php                       # فئة المستخدم الأساسية
│   ├── Admin.php                      # فئة المسؤول
│   ├── Doctor.php                     # فئة الطبيب
│   ├── Patient.php                    # فئة المريض
│   └── AuthMiddleware.php             # التحقق من الصلاحيات
├── config/
│   ├── Database.php                   # اتصال قاعدة البيانات
│   └── DB.sql                         # ملف إنشاء الجداول
├── includes/
│   ├── login_process.php              # معالجة تسجيل الدخول
│   ├── logout_process.php             # معالجة تسجيل الخروج
│   ├── auth_check.php                 # دوال مساعدة للتحقق
│   ├── add_user.php                   # إضافة مستخدم جديد
│   ├── delete_user.php                # حذف مستخدم
│   ├── add_medical_record.php         # إضافة سجل طبي
│   └── add_prescription.php           # إضافة دواء
├── views/
│   ├── admin_dash.php                 # لوحة تحكم المسؤول
│   ├── doctor_dash.php                # لوحة تحكم الطبيب
│   ├── Patient_dash.php               # لوحة تحكم المريض
│   ├── user_dash.php                  # لوحة تحكم عامة
│   └── patient_record.php             # عرض سجلات المريض
└── setup/
    └── create_test_data.php           # إضافة بيانات اختبار
```

---

## 🔐 الميزات الأمنية

### 🛡️ حماية من هجمات SQL Injection

```php
// استخدام Prepared Statements
$stmt = $this->db->prepare("SELECT * FROM users WHERE email = ?");
$stmt->execute([$email]);
```

### 🔒 تشفير كلمات المرور

```php
// استخدام BCRYPT لتشفير آمن
$hashedPassword = password_hash($password, PASSWORD_BCRYPT);
password_verify($password, $hashedPassword);
```

### 🔐 نظام جلسات آمن

```php
// تخزين بيانات المستخدم في الجلسة
$_SESSION['user_id'] = $user['id'];
$_SESSION['role'] = $user['role'];
```

### 🚫 فحص الصلاحيات

```php
// التحقق من أن المستخدم له الحق في الوصول
AuthMiddleware::requireRole(['doctor', 'admin']);
```

---

## 📋 المهام المتاحة

### لمسؤول النظام:

- ✅ عرض جميع المستخدمين والإحصائيات
- ✅ إضافة مستخدمين جدد (طبيب أو مريض)
- ✅ حذف المستخدمين
- ✅ عرض إحصائيات شاملة

### للطبيب:

- ✅ عرض قائمة المرضى
- ✅ إضافة سجلات طبية جديدة
- ✅ إضافة روشتات (أدوية) للمرضى
- ✅ البحث عن المرضى
- ✅ عرض السجلات الطبية للمريض

### للمريض:

- ✅ عرض سجلاته الطبية الشخصية
- ✅ عرض الأدوية الموصوفة له
- ✅ عرض ملاحظات الأطباء

---

## 🧪 اختبار النظام

### اختبار تسجيل الدخول:

1. افتح المتصفح وذهب إلى `http://localhost:8000/src/`
2. أدخل بيانات الاختبار:
   - البريد: `admin@test.com`
   - كلمة المرور: `password123`
3. اضغط "دخول"

### اختبار لوحة تحكم المسؤول:

1. سجل دخول كـ admin
2. يجب أن ترى:
   - إحصائيات المستخدمين
   - قائمة بجميع المستخدمين
   - خيار إضافة مستخدم جديد

### اختبار لوحة تحكم الطبيب:

1. سجل دخول كـ doctor@test.com
2. يجب أن ترى:
   - قائمة المرضى
   - نموذج إضافة سجل طبي

### اختبار لوحة تحكم المريض:

1. سجل دخول كـ patient1@test.com
2. يجب أن ترى سجلاته الطبية (إن وجدت)

---

## ⚙️ تكوين إضافي

### تغيير جودة التشفير:

في `src/classes/User.php`:

```php
// يمكنك تغيير كود التشفير
$hashedPassword = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
```

### تعديل رسائل الخطأ:

كل رسائل الخطأ تنبيهات موجودة في الملفات الخاصة بها.

---

## 🐛 استكشاف الأخطاء

### خطأ في الاتصال بقاعدة البيانات:

- تأكد من أن MySQL يعمل
- تأكد من بيانات الاتصال في `src/config/Database.php`
- تأكد من أن قاعدة البيانات موجودة

### خطأ في تسجيل الدخول:

- تأكد من أن البريد الإلكتروني موجود في الجدول
- تأكد من أن كلمة المرور صحيحة
- جرب مع حسابات الاختبار أولا

### خطأ في الصلاحيات:

- تأكد من أن دورك صحيح في قاعدة البيانات
- جرب تسجيل الخروج وإعادة تسجيل الدخول

---

## 📚 مراجع تقنية

- [PHP PDO Documentation](https://www.php.net/manual/en/pdo.prepared-statements.php)
- [Password Hashing](https://www.php.net/manual/en/function.password-hash.php)
- [Session Management](https://www.php.net/manual/en/session.examples.basic.php)

---

## 🤝 المساهمة

هذا المشروع مفتوح للتطوير والتحسينات. يمكنك:

- إضافة ميزات جديدة
- تحسين الواجهة
- إضافة اختبارات

---

## 📝 الملاحظات

- تأكد من استخدام SSL/HTTPS في الإنتاج
- لا تستخدم كلمات المرور البسيطة في الإنتاج
- نظف بيانات المدخلات دائما
- قم بنسخ احتياطية منتظمة لقاعدة البيانات

---

**آخر تحديث:** 24 مارس 2026
**الإصدار:** 1.0.0


git init
git add .
git commit -m "Initial clean commit"
git branch -M main
git remote add origin https://github.com/USER_NAME/REPO_NAME.git
git push -u origin main --force


password = Admin@123
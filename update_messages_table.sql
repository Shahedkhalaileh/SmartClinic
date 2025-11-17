-- تحديث جدول messages لإضافة حقول الإشعارات وأنواع المرسل/المستقبل

ALTER TABLE messages 
ADD COLUMN IF NOT EXISTS is_read TINYINT(1) DEFAULT 0,
ADD COLUMN IF NOT EXISTS sender_type VARCHAR(10) DEFAULT 'patient',
ADD COLUMN IF NOT EXISTS receiver_type VARCHAR(10) DEFAULT 'doctor';





# 📋 Ứng Dụng Quản Lý Công Việc Cá Nhân (Todo App)

**Bài toán:** Xây dựng ứng dụng web quản lý công việc cá nhân với PHP thuần và MySQL.

---

## 🎯 MỤC TIÊU BÀI TOÁN

1. ✅ Áp dụng kiến thức **PHP thuần** để xây dựng web động
2. ✅ Thực hành làm việc với **MySQL** để lưu trữ dữ liệu
3. ✅ Triển khai tính năng **Xác thực người dùng** (Authentication)
4. ✅ Thực hành tổ chức code và **bảo mật cơ bản**

---

## 🏗️ KIẾN TRÚC NGHIỆP VỤ

### 1. Quản Lý Người Dùng (User Management)

#### Đăng ký (Registration)
- **Input:** Username, Email, Password, Confirm Password
- **Xử lý:**
  - Validate đầu vào (username ≥3 ký tự, email hợp lệ, password ≥6 ký tự)
  - Kiểm tra username/email đã tồn tại chưa
  - **Băm mật khẩu** bằng `password_hash()` trước khi lưu
  - Lưu user vào database
- **Output:** Redirect về trang login với thông báo thành công

#### Đăng nhập (Login)
- **Input:** Username/Email và Password
- **Xử lý:**
  - Query database lấy user theo username/email
  - **Xác thực mật khẩu** bằng `password_verify()`
  - Lưu thông tin user vào `$_SESSION`
- **Output:** 
  - Thành công → Dashboard
  - Thất bại → Thông báo lỗi

#### Đăng xuất (Logout)
- **Xử lý:** Hủy session (`session_destroy()`)
- **Output:** Redirect về trang login

### 2. Quản Lý Công Việc (Task Management)

#### 2.1. Tạo Công Việc (CREATE)

**Thông tin công việc:**
- **Bắt buộc:**
  - `title` - Tiêu đề công việc
- **Tùy chọn:**
  - `description` - Mô tả chi tiết
  - `due_date` - Ngày hết hạn
  - `due_time` - Giờ hết hạn
  - `category_id` - Danh mục
  - `priority` - Độ ưu tiên (low/medium/high/urgent)
  - `status` - Trạng thái (pending/in_progress/completed)
  - `is_important` - Đánh dấu quan trọng
  - `tags[]` - Nhiều tags

**Nghiệp vụ:**
```
1. User nhập thông tin task
2. Validate: title không được rỗng
3. Lưu task vào database với user_id = current user
4. Gán tags cho task (nếu có)
5. Redirect về dashboard với thông báo thành công
```

#### 2.2. Xem Danh Sách (READ)

**Yêu cầu nghiệp vụ:**
- User CHỈ thấy tasks của chính mình
- Hỗ trợ **Filter** (Lọc):
  - Theo Status (Chờ xử lý, Đang làm, Hoàn thành, Đã hủy)
  - Theo Category (Danh mục)
  - Theo Priority (Độ ưu tiên)
- Hỗ trợ **Sort** (Sắp xếp):
  - Theo Ngày hết hạn
  - Theo Độ ưu tiên
  - Theo Ngày tạo
  - Theo Tiêu đề
  - Theo Trạng thái

**Query mẫu:**
```sql
SELECT t.*, c.name AS category_name, c.color AS category_color 
FROM tasks t 
LEFT JOIN categories c ON t.category_id = c.id 
WHERE t.user_id = ? 
ORDER BY t.is_important DESC, t.due_date ASC
```

**Hiển thị:**
- Dạng **Card View** responsive
- Thể hiện:
  - Icon + tên category (với màu sắc)
  - Badge priority (màu theo mức độ)
  - Tags (nhiều tags với màu riêng)
  - Trạng thái (badge màu)
  - Ngày hết hạn (đỏ nếu quá hạn)
  - Icon ⭐ nếu important

#### 2.3. Cập Nhật Công Việc (UPDATE)

**Nghiệp vụ:**
```
1. Load task hiện tại theo id
2. Kiểm tra task thuộc về user (security check)
3. User chỉnh sửa thông tin
4. Validate dữ liệu
5. Update database
6. Update tags (xóa tags cũ, thêm tags mới)
7. Redirect về dashboard
```

**Bảo mật quan trọng:**
```sql
UPDATE tasks 
SET title = ?, description = ?, ...
WHERE id = ? AND user_id = ?  -- Phải check user_id!
```

#### 2.4. Xóa Công Việc (DELETE)

**Nghiệp vụ:**
```
1. Kiểm tra task thuộc về user
2. Confirm trước khi xóa (JavaScript)
3. Xóa VĨNH VIỄN khỏi database
4. Redirect về dashboard
```

**Query:**
```sql
DELETE FROM tasks 
WHERE id = ? AND user_id = ?  -- Phải check user_id!
```

#### 2.5. Đánh Dấu Hoàn Thành

**Nghiệp vụ:**
```
1. Kiểm tra task thuộc về user
2. Update: status = 'completed', completed_at = NOW()
3. Đếm "Hoàn thành" tăng lên
```

#### 2.6. Hủy Công Việc (Cancel)

**Khác với DELETE:**
- DELETE: Xóa hẳn khỏi database
- CANCEL: Chỉ đổi status = 'cancelled', task vẫn còn

**Nghiệp vụ:**
```
1. Kiểm tra task thuộc về user
2. Update: status = 'cancelled'
3. Đếm "Đã hủy" tăng lên
4. Task vẫn hiển thị trong danh sách (có filter)
```

#### 2.7. Đánh Dấu Quan Trọng (Toggle Important)

**Nghiệp vụ:**
```
1. Lấy giá trị is_important hiện tại
2. Toggle: 0 → 1 hoặc 1 → 0
3. Tasks quan trọng luôn hiển thị đầu tiên
```

### 3. Quản Lý Danh Mục (Categories)

**Mục đích:** Phân loại tasks (Công việc, Cá nhân, Học tập...)

**Cấu trúc:**
- `name` - Tên danh mục
- `color` - Màu sắc (hex color)
- `icon` - Icon emoji
- `user_id` - Thuộc về user nào

**Nghiệp vụ:**
- Mỗi user có danh mục riêng
- Tên danh mục UNIQUE trong phạm vi 1 user
- Khi xóa category, tasks không bị xóa (SET NULL)

**Hiển thị:**
```
┌─────────────────────┐
│ 💼 [#3B82F6]       │
│    Công việc       │
└─────────────────────┘
```

### 4. Quản Lý Thẻ (Tags)

**Mục đích:** Gắn nhãn chi tiết cho tasks (Khẩn cấp, Dự án, Họp...)

**Đặc điểm:**
- Quan hệ **nhiều-nhiều** với tasks (1 task có nhiều tags, 1 tag gắn cho nhiều tasks)
- Mỗi user có tags riêng
- Tên tag UNIQUE trong phạm vi 1 user

**Bảng trung gian:**
```sql
task_tags (
    task_id,
    tag_id
)
```

**Hiển thị:**
```
🏷️ Khẩn cấp  🏷️ Dự án  🏷️ Họp
```

---

## 📊 THỐNG KÊ DASHBOARD

**Các chỉ số hiển thị:**

1. **Tổng số** - Tổng tasks của user
2. **Chờ xử lý** - status = 'pending'
3. **Đang làm** - status = 'in_progress'
4. **Hoàn thành** - status = 'completed'
5. **Đã hủy** - status = 'cancelled'
6. **Quá hạn** - due_date < today AND status NOT IN ('completed', 'cancelled')

**Query thống kê:**
```sql
-- Đếm tasks quá hạn
SELECT COUNT(*) FROM tasks 
WHERE user_id = ? 
AND due_date < CURDATE() 
AND status NOT IN ('completed', 'cancelled')
```

---

## 🔐 BẢO MẬT NGHIỆP VỤ

### 1. SQL Injection Prevention

**❌ SAI:**
```php
$query = "SELECT * FROM users WHERE username = '$username'";
```

**✅ ĐÚNG:**
```php
$stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
$stmt->execute([$username]);
```

### 2. Password Security

**Đăng ký:**
```php
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);
// Lưu $hashedPassword vào database
```

**Đăng nhập:**
```php
if (password_verify($inputPassword, $hashedFromDB)) {
    // Login success
}
```

### 3. Access Control

**Nguyên tắc:** User chỉ thấy và thao tác với dữ liệu của mình

```php
// Middleware kiểm tra đăng nhập
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Mọi query phải có điều kiện user_id
WHERE user_id = ?

// Mọi UPDATE/DELETE phải check user_id
UPDATE tasks SET ... WHERE id = ? AND user_id = ?
DELETE FROM tasks WHERE id = ? AND user_id = ?
```

---

## 🔄 WORKFLOW TỔNG THỂ

```
┌─────────────────────────────────────────────────────┐
│  User vào trang chủ (index.php)                     │
└─────────────────┬───────────────────────────────────┘
                  │
                  ▼
          ┌───────────────┐
          │ Đã login?     │
          └───┬───────┬───┘
              │       │
         NO   │       │   YES
              ▼       ▼
      ┌──────────┐  ┌─────────────────────────┐
      │  LOGIN   │  │   DASHBOARD             │
      │  PAGE    │  │                         │
      └────┬─────┘  │  Thống kê 6 chỉ số      │
           │        │  Filter (status/cat/pri)│
           │        │  Sort (5 tiêu chí)      │
      ┌────┴─────┐  │  List tasks (card view) │
      │ REGISTER │  │                         │
      │  PAGE    │  │  Actions:               │
      └──────────┘  │  - Thêm task            │
                    │  - Sửa task             │
                    │  - Hoàn thành           │
                    │  - Hủy                  │
                    │  - Đánh dấu quan trọng  │
                    │  - Xóa                  │
                    │                         │
                    │  Quản lý Categories     │
                    │  Quản lý Tags           │
                    │                         │
                    │  Logout                 │
                    └─────────────────────────┘
```

---

## 📋 CÁC TRẠNG THÁI TASK

| Status | Ý nghĩa | Màu badge | Chuyển đổi |
|--------|---------|-----------|------------|
| `pending` | Chờ xử lý | Vàng | → in_progress, cancelled |
| `in_progress` | Đang làm | Xanh dương | → completed, cancelled |
| `completed` | Hoàn thành | Xanh lá | (không đổi) |
| `cancelled` | Đã hủy | Xám | (không đổi) |

**State Diagram:**
```
pending ─────→ in_progress ─────→ completed
   │                 │
   └─────────┬───────┘
             │
             ↓
         cancelled
```

---

## 🎯 ĐỘ ƯU TIÊN (PRIORITY)

| Priority | Ý nghĩa | Màu | Sắp xếp |
|----------|---------|-----|---------|
| `urgent` | Khẩn cấp | Đỏ | 1 |
| `high` | Cao | Cam | 2 |
| `medium` | Trung bình | Xanh | 3 |
| `low` | Thấp | Xám | 4 |

**Sort by priority:**
```sql
ORDER BY FIELD(priority, 'urgent', 'high', 'medium', 'low')
```

---

## 📁 CẤU TRÚC DATABASE

### Quan hệ giữa các bảng:

```
users (1) ─────< (N) tasks
users (1) ─────< (N) categories
users (1) ─────< (N) tags

categories (1) ─────< (N) tasks

tasks (N) ─────< (M) tags  (qua task_tags)
```

### Các ràng buộc quan trọng:

1. **users.username** - UNIQUE
2. **users.email** - UNIQUE
3. **categories (user_id, name)** - UNIQUE (composite)
4. **tags (user_id, name)** - UNIQUE (composite)
5. **task_tags (task_id, tag_id)** - UNIQUE (composite)

### Xóa cascade:

- Xóa user → Xóa tất cả tasks, categories, tags của user
- Xóa task → Xóa tất cả task_tags liên quan
- Xóa category → Tasks.category_id = NULL (SET NULL)

---

## 🧪 TÍNH NĂNG ĐẶC BIỆT

### 1. Important Flag
- Tasks quan trọng có border vàng
- Luôn hiển thị đầu tiên trong list
- Icon ⭐ nổi bật

### 2. Overdue Detection
- Tasks quá hạn hiển thị ngày đỏ
- Text "(Quá hạn)"
- Đếm riêng trong thống kê

### 3. Icon Emoji cho Categories
- Dropdown 20+ emoji có sẵn
- Hiển thị đẹp mắt, dễ nhận diện
- Tự động convert text cũ sang emoji

### 4. Multi-filter
- Có thể filter đồng thời: Status + Category + Priority
- Sort kết hợp với filter

---

## ✅ CHECKLIST NGHIỆP VỤ HOÀN THIỆN

### Authentication
- [x] Đăng ký với validation đầy đủ
- [x] Password hashing
- [x] Login với password verify
- [x] Session management
- [x] Logout
- [x] Middleware kiểm tra login

### Tasks CRUD
- [x] Tạo task với đầy đủ thông tin
- [x] Xem danh sách tasks
- [x] Sửa task
- [x] Xóa task (với confirm)
- [x] Hoàn thành task
- [x] Hủy task (khác với xóa)
- [x] Toggle important

### Filter & Sort
- [x] Filter theo status
- [x] Filter theo category
- [x] Filter theo priority
- [x] Sort theo 5 tiêu chí
- [x] Combine filters

### Categories & Tags
- [x] Quản lý categories
- [x] Quản lý tags
- [x] Gán category cho task
- [x] Gán nhiều tags cho task
- [x] Icon emoji cho categories

### Thống kê
- [x] Đếm tổng số tasks
- [x] Đếm theo status (4 loại)
- [x] Đếm tasks quá hạn
- [x] Hiển thị dashboard

### Bảo mật
- [x] Prepared statements
- [x] Password hashing
- [x] User_id check trong mọi query
- [x] Session management
- [x] Input validation

---

**Dự án hoàn thành đầy đủ yêu cầu bài toán! 🎉**


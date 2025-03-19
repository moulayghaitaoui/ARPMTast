
# ARPM

## Task 5: Q&A - Answers

### A) Explain this code:
_(Provide the code snippet so I can explain it in detail.)_

---

### B) Difference Between Context and Cache Facades in Laravel

#### 🔹 1. Cache Facade
The `Cache` facade is used to store and retrieve data temporarily to improve performance.
It supports multiple drivers such as **file, database, Redis, and Memcached**.

✅ **Example:**
```php
use Illuminate\Support\Facades\Cache;

// Store data for 60 minutes
Cache::put('user_123', ['name' => 'John Doe', 'email' => 'john@example.com'], 60);

// Retrieve cached data
$user = Cache::get('user_123');

// Remove from cache
Cache::forget('user_123');
```
📌 **Use Case:** Storing frequently accessed data such as **user profiles, settings, or API responses** to improve performance.

---

#### 🔹 2. Context Facade
Laravel **does not have** a built-in `Context` facade.
If this is a **custom facade**, it likely provides **a way to store and retrieve contextual data during a request lifecycle** (e.g., request metadata, user session, or dynamic configurations).

✅ **Example (Custom `Context` Implementation)**
```php
class Context
{
    protected static $data = [];

    public static function set($key, $value)
    {
        self::$data[$key] = $value;
    }

    public static function get($key, $default = null)
    {
        return self::$data[$key] ?? $default;
    }
}

// Usage
Context::set('current_user', auth()->user());
$user = Context::get('current_user');
```
📌 **Use Case:**
- In a **multi-tenant system**, `Context` can store the current tenant.
- Storing temporary **request-specific** data that should not persist between requests.

---

### C) Difference Between `$query->update()`, `$model->update()`, and `$model->updateQuietly()`

#### **1️⃣ `$query->update()`**
- Updates **multiple records** at once using **mass update**.
- **Bypasses Eloquent events (`saving`, `updating`, `updated`)**.

✅ **Example:**
```php
Order::where('status', 'pending')->update(['status' => 'processed']);
```
📌 **Use Case:** When updating multiple rows at once **without** triggering model events.

---

#### **2️⃣ `$model->update()`**
- Updates **a single model instance**.
- **Triggers Eloquent events** such as `updating`, `updated`, `saving`, and `saved`.

✅ **Example:**
```php
$order = Order::find(1);
$order->update(['status' => 'shipped']);
```
📌 **Use Case:** When updating **a single record** and you want to trigger Eloquent events.

---

#### **3️⃣ `$model->updateQuietly()`**
- Similar to `$model->update()`, but **does not trigger Eloquent events**.

✅ **Example:**
```php
$order = Order::find(1);
$order->updateQuietly(['status' => 'shipped']);
```


---

### ✅ **Final Thoughts**
- **Use `$query->update()`** when updating multiple records quickly.
- **Use `$model->update()`** when updating a single record and need event listeners.
- **Use `$model->updateQuietly()`** when you want to **skip** triggering model events.

🚀 Let me know if you need further clarifications!

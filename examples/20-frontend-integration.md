# 前端框架整合指南

本文說明如何在 Vue、React 等前端框架中整合藍新金流支付。

## 整合架構

由於藍新金流 MPG 使用**表單 POST 跳轉**方式進行支付，前端框架需要透過後端 API 取得加密後的參數，再組裝表單送出。

```
┌─────────────┐     1. 建立訂單      ┌─────────────┐
│   Frontend  │ ──────────────────▶ │   Backend   │
│  (Vue/React)│                      │    (PHP)    │
└─────────────┘                      └─────────────┘
       │                                    │
       │                                    │ 2. 產生加密參數
       │                                    ▼
       │         3. 回傳表單參數      ┌─────────────┐
       │ ◀─────────────────────────── │  NewebPay   │
       │                              │    SDK      │
       ▼                              └─────────────┘
┌─────────────┐
│  建立表單    │
│  POST 跳轉  │
└─────────────┘
       │
       │ 4. 跳轉至藍新金流
       ▼
┌─────────────┐
│  藍新金流   │
│  支付頁面   │
└─────────────┘
```

## 後端 API 端點設計

### Laravel 範例

```php
<?php
// app/Http/Controllers/PaymentController.php

namespace App\Http\Controllers;

use CarlLee\NewebPay\Laravel\Facades\NewebPay;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    /**
     * 建立支付請求，回傳表單參數給前端。
     */
    public function create(Request $request)
    {
        $validated = $request->validate([
            'order_id' => 'required|string|max:30',
            'amount' => 'required|integer|min:1',
            'item_desc' => 'required|string|max:50',
            'email' => 'required|email|max:50',
        ]);

        // 建立信用卡付款
        $payment = NewebPay::credit()
            ->setMerchantOrderNo($validated['order_id'])
            ->setAmt($validated['amount'])
            ->setItemDesc($validated['item_desc'])
            ->setEmail($validated['email'])
            ->setReturnURL(config('newebpay.return_url'))
            ->setNotifyURL(config('newebpay.notify_url'));

        // 取得表單內容（加密後的參數）
        $content = $payment->getContent();

        return response()->json([
            'success' => true,
            'data' => [
                'action' => $payment->getApiUrl(),
                'method' => 'POST',
                'fields' => $content,
            ],
        ]);
    }

    /**
     * 建立支付請求，直接回傳完整 HTML 表單。
     */
    public function createWithHtml(Request $request)
    {
        $validated = $request->validate([
            'order_id' => 'required|string|max:30',
            'amount' => 'required|integer|min:1',
            'item_desc' => 'required|string|max:50',
            'email' => 'required|email|max:50',
        ]);

        $payment = NewebPay::credit()
            ->setMerchantOrderNo($validated['order_id'])
            ->setAmt($validated['amount'])
            ->setItemDesc($validated['item_desc'])
            ->setEmail($validated['email'])
            ->setReturnURL(config('newebpay.return_url'))
            ->setNotifyURL(config('newebpay.notify_url'));

        $formHtml = NewebPay::form($payment)
            ->setAutoSubmit(true)
            ->build();

        return response()->json([
            'success' => true,
            'data' => [
                'html' => $formHtml,
            ],
        ]);
    }
}
```

### 原生 PHP 範例

```php
<?php
// api/payment/create.php

require_once __DIR__ . '/../../vendor/autoload.php';

use CarlLee\NewebPay\Operations\CreditPayment;

header('Content-Type: application/json');

// 取得 POST 資料
$input = json_decode(file_get_contents('php://input'), true);

// 驗證參數
if (empty($input['order_id']) || empty($input['amount'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => '缺少必要參數']);
    exit;
}

// 設定商店資訊
$merchantId = 'MS12345678';
$hashKey = 'your-hash-key';
$hashIV = 'your-hash-iv';

// 建立付款
$payment = new CreditPayment($merchantId, $hashKey, $hashIV);

$payment
    ->setTestMode(true)
    ->setMerchantOrderNo($input['order_id'])
    ->setAmt((int) $input['amount'])
    ->setItemDesc(isset($input['item_desc']) ? $input['item_desc'] : '商品')
    ->setEmail(isset($input['email']) ? $input['email'] : '')
    ->setReturnURL('https://your-site.com/payment/return')
    ->setNotifyURL('https://your-site.com/payment/notify');

// 回傳表單參數
echo json_encode([
    'success' => true,
    'data' => [
        'action' => $payment->getApiUrl(),
        'method' => 'POST',
        'fields' => $payment->getContent(),
    ],
]);
```

---

## Vue 3 整合範例

### 使用 Composition API

```vue
<!-- components/PaymentButton.vue -->
<template>
  <button 
    @click="handlePayment" 
    :disabled="loading"
    class="payment-btn"
  >
    <span v-if="loading">處理中...</span>
    <span v-else>前往付款</span>
  </button>

  <!-- 隱藏的表單容器 -->
  <div ref="formContainer" style="display: none;"></div>
</template>

<script setup>
import { ref } from 'vue';

const props = defineProps({
  orderId: { type: String, required: true },
  amount: { type: Number, required: true },
  itemDesc: { type: String, default: '商品' },
  email: { type: String, default: '' },
});

const loading = ref(false);
const formContainer = ref(null);

async function handlePayment() {
  loading.value = true;

  try {
    // 1. 呼叫後端 API 取得表單參數
    const response = await fetch('/api/payment/create', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({
        order_id: props.orderId,
        amount: props.amount,
        item_desc: props.itemDesc,
        email: props.email,
      }),
    });

    const result = await response.json();

    if (!result.success) {
      throw new Error(result.message || '建立付款失敗');
    }

    // 2. 建立表單並送出
    submitPaymentForm(result.data);
  } catch (error) {
    console.error('Payment error:', error);
    alert('付款發生錯誤：' + error.message);
    loading.value = false;
  }
}

function submitPaymentForm({ action, method, fields }) {
  // 建立表單元素
  const form = document.createElement('form');
  form.method = method;
  form.action = action;

  // 添加隱藏欄位
  Object.entries(fields).forEach(([name, value]) => {
    const input = document.createElement('input');
    input.type = 'hidden';
    input.name = name;
    input.value = value;
    form.appendChild(input);
  });

  // 添加到 DOM 並送出
  formContainer.value.appendChild(form);
  form.submit();
}
</script>

<style scoped>
.payment-btn {
  padding: 12px 24px;
  background: #4CAF50;
  color: white;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  font-size: 16px;
}

.payment-btn:disabled {
  background: #ccc;
  cursor: not-allowed;
}

.payment-btn:hover:not(:disabled) {
  background: #45a049;
}
</style>
```

### Vue 2 Options API 範例

```vue
<!-- components/PaymentButton.vue -->
<template>
  <div>
    <button 
      @click="handlePayment" 
      :disabled="loading"
      class="payment-btn"
    >
      {{ loading ? '處理中...' : '前往付款' }}
    </button>
    <div ref="formContainer" style="display: none;"></div>
  </div>
</template>

<script>
export default {
  name: 'PaymentButton',
  props: {
    orderId: { type: String, required: true },
    amount: { type: Number, required: true },
    itemDesc: { type: String, default: '商品' },
    email: { type: String, default: '' },
  },
  data() {
    return {
      loading: false,
    };
  },
  methods: {
    async handlePayment() {
      this.loading = true;

      try {
        const response = await fetch('/api/payment/create', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({
            order_id: this.orderId,
            amount: this.amount,
            item_desc: this.itemDesc,
            email: this.email,
          }),
        });

        const result = await response.json();

        if (!result.success) {
          throw new Error(result.message || '建立付款失敗');
        }

        this.submitPaymentForm(result.data);
      } catch (error) {
        console.error('Payment error:', error);
        alert('付款發生錯誤：' + error.message);
        this.loading = false;
      }
    },

    submitPaymentForm({ action, method, fields }) {
      const form = document.createElement('form');
      form.method = method;
      form.action = action;

      Object.keys(fields).forEach(function(name) {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = name;
        input.value = fields[name];
        form.appendChild(input);
      });

      this.$refs.formContainer.appendChild(form);
      form.submit();
    },
  },
};
</script>
```

### 使用 Composable（可複用邏輯）

```javascript
// composables/useNewebPay.js
import { ref } from 'vue';

export function useNewebPay() {
  const loading = ref(false);
  const error = ref(null);

  async function createPayment(params) {
    loading.value = true;
    error.value = null;

    try {
      const response = await fetch('/api/payment/create', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
          order_id: params.orderId,
          amount: params.amount,
          item_desc: params.itemDesc || '商品',
          email: params.email || '',
        }),
      });

      const result = await response.json();

      if (!result.success) {
        throw new Error(result.message);
      }

      return result.data;
    } catch (e) {
      error.value = e.message || '未知錯誤';
      throw e;
    } finally {
      loading.value = false;
    }
  }

  function submitForm(formData) {
    const form = document.createElement('form');
    form.method = formData.method;
    form.action = formData.action;

    Object.keys(formData.fields).forEach(function(name) {
      const input = document.createElement('input');
      input.type = 'hidden';
      input.name = name;
      input.value = formData.fields[name];
      form.appendChild(input);
    });

    document.body.appendChild(form);
    form.submit();
  }

  async function checkout(params) {
    const formData = await createPayment(params);
    submitForm(formData);
  }

  return {
    loading: loading,
    error: error,
    createPayment: createPayment,
    submitForm: submitForm,
    checkout: checkout,
  };
}
```

---

## React 整合範例

### 使用 Hooks

```jsx
// hooks/useNewebPay.js
import { useState, useCallback } from 'react';

export function useNewebPay() {
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState(null);

  const createPayment = useCallback(async function(params) {
    setLoading(true);
    setError(null);

    try {
      const response = await fetch('/api/payment/create', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
          order_id: params.orderId,
          amount: params.amount,
          item_desc: params.itemDesc || '商品',
          email: params.email || '',
        }),
      });

      const result = await response.json();

      if (!result.success) {
        throw new Error(result.message);
      }

      return result.data;
    } catch (e) {
      var message = e.message || '未知錯誤';
      setError(message);
      throw e;
    } finally {
      setLoading(false);
    }
  }, []);

  const submitForm = useCallback(function(formData) {
    const form = document.createElement('form');
    form.method = formData.method;
    form.action = formData.action;

    Object.keys(formData.fields).forEach(function(name) {
      const input = document.createElement('input');
      input.type = 'hidden';
      input.name = name;
      input.value = formData.fields[name];
      form.appendChild(input);
    });

    document.body.appendChild(form);
    form.submit();
  }, []);

  const checkout = useCallback(async function(params) {
    const formData = await createPayment(params);
    submitForm(formData);
  }, [createPayment, submitForm]);

  return {
    loading: loading,
    error: error,
    createPayment: createPayment,
    submitForm: submitForm,
    checkout: checkout,
  };
}
```

### React 元件

```jsx
// components/PaymentButton.jsx
import React from 'react';
import { useNewebPay } from '../hooks/useNewebPay';

export function PaymentButton(props) {
  const { orderId, amount, itemDesc, email, children } = props;
  const { loading, error, checkout } = useNewebPay();

  var buttonText = children || '前往付款';

  async function handleClick() {
    try {
      await checkout({ 
        orderId: orderId, 
        amount: amount, 
        itemDesc: itemDesc || '商品', 
        email: email || '' 
      });
    } catch (e) {
      console.error('Payment failed:', e);
    }
  }

  return (
    <div>
      <button
        onClick={handleClick}
        disabled={loading}
        style={{
          padding: '12px 24px',
          backgroundColor: loading ? '#ccc' : '#4CAF50',
          color: 'white',
          border: 'none',
          borderRadius: '4px',
          cursor: loading ? 'not-allowed' : 'pointer',
          fontSize: '16px',
        }}
      >
        {loading ? '處理中...' : buttonText}
      </button>
      {error && (
        <p style={{ color: 'red', marginTop: '8px' }}>{error}</p>
      )}
    </div>
  );
}
```

### 使用範例

```jsx
// pages/Checkout.jsx
import React from 'react';
import { PaymentButton } from '../components/PaymentButton';

export function CheckoutPage() {
  var order = {
    id: 'ORDER' + Date.now(),
    amount: 1000,
    items: '測試商品',
  };

  return (
    <div style={{ padding: '20px' }}>
      <h1>訂單確認</h1>
      
      <div style={{ marginBottom: '20px' }}>
        <p>訂單編號：{order.id}</p>
        <p>金額：NT$ {order.amount}</p>
        <p>商品：{order.items}</p>
      </div>

      <PaymentButton
        orderId={order.id}
        amount={order.amount}
        itemDesc={order.items}
        email="customer@example.com"
      >
        確認付款 NT$ {order.amount}
      </PaymentButton>
    </div>
  );
}
```

---

## Next.js 整合範例

### API Route

```javascript
// pages/api/payment/create.js
export default async function handler(req, res) {
  if (req.method !== 'POST') {
    return res.status(405).json({ message: 'Method not allowed' });
  }

  var body = req.body;

  // 呼叫 PHP 後端 API
  var response = await fetch(process.env.PHP_API_URL + '/payment/create', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify(body),
  });

  var data = await response.json();

  res.status(200).json(data);
}
```

---

## Nuxt 2 整合範例

### asyncData / fetch

```vue
<!-- pages/checkout.vue -->
<template>
  <div class="checkout">
    <h1>訂單確認</h1>
    
    <div class="order-info">
      <p>訂單編號：{{ order.id }}</p>
      <p>金額：NT$ {{ order.amount }}</p>
    </div>

    <button @click="checkout" :disabled="loading">
      {{ loading ? '處理中...' : '確認付款' }}
    </button>
  </div>
</template>

<script>
export default {
  data() {
    return {
      loading: false,
      order: {
        id: 'ORDER' + Date.now(),
        amount: 1000,
      },
    };
  },
  methods: {
    async checkout() {
      this.loading = true;

      try {
        var response = await this.$axios.$post('/api/payment/create', {
          order_id: this.order.id,
          amount: this.order.amount,
          item_desc: '測試商品',
        });

        if (!response.success) {
          throw new Error(response.message);
        }

        this.submitForm(response.data);
      } catch (error) {
        alert('付款失敗：' + error.message);
        this.loading = false;
      }
    },

    submitForm(data) {
      var form = document.createElement('form');
      form.method = data.method;
      form.action = data.action;

      Object.keys(data.fields).forEach(function(name) {
        var input = document.createElement('input');
        input.type = 'hidden';
        input.name = name;
        input.value = data.fields[name];
        form.appendChild(input);
      });

      document.body.appendChild(form);
      form.submit();
    },
  },
};
</script>
```

---

## 注意事項

### 1. CORS 設定

後端 API 需要正確設定 CORS，允許前端網域存取：

```php
// Laravel
// config/cors.php
'paths' => ['api/*'],
'allowed_origins' => ['http://localhost:3000', 'https://your-frontend.com'],
```

### 2. CSRF 保護

如果使用 Laravel，API 路由需要排除 CSRF 驗證或使用 Sanctum：

```php
// app/Http/Middleware/VerifyCsrfToken.php
protected $except = [
    'api/payment/*',
];
```

### 3. 安全考量

- **不要**將 HashKey、HashIV 暴露給前端
- 所有加密操作必須在後端完成
- 驗證所有前端傳入的參數
- 使用 HTTPS

### 4. 付款完成後的處理

前端跳轉回 ReturnURL 時，建議：

```javascript
// 付款完成頁面
async function checkPaymentStatus() {
  var urlParams = new URLSearchParams(window.location.search);
  var orderId = urlParams.get('order_id');

  // 向後端確認付款狀態
  var response = await fetch('/api/orders/' + orderId + '/status');
  var result = await response.json();

  if (result.paid) {
    // 顯示付款成功
  } else {
    // 顯示付款失敗或等待中
  }
}
```

---

## API 回應格式建議

### 成功回應

```json
{
  "success": true,
  "data": {
    "action": "https://ccore.newebpay.com/MPG/mpg_gateway",
    "method": "POST",
    "fields": {
      "MerchantID": "MS12345678",
      "TradeInfo": "加密後的交易資料",
      "TradeSha": "SHA256 驗證碼",
      "Version": "2.0"
    }
  }
}
```

### 錯誤回應

```json
{
  "success": false,
  "message": "金額必須大於 0",
  "errors": {
    "amount": ["金額必須大於 0"]
  }
}
```


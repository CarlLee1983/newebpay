# 貢獻指南

感謝您有興趣為此專案做出貢獻！我們非常歡迎任何形式的貢獻，包括回報 Bug、建議新功能、撰寫文件或提交程式碼修補。

為了確保協作順利，請在參與貢獻前閱讀以下指南。

## 🤝 參與貢獻的方式

### 回報 Bug (Bug Reports)

如果您發現了 Bug，請提交 Issue 並包含以下資訊：
- 發生問題的 SDK 版本。
- 您使用的 PHP 版本與 Laravel 版本 (若有)。
- 重現問題的最小程式碼範例。
- 預期結果與實際結果。
- 錯誤訊息截圖或 Log。

### 建議功能 (Feature Requests)

有好的點子嗎？歡迎提交 Issue 討論！
請清楚描述您希望新增的功能，以及它能解決什麼問題。

### 提交 Pull Request (PR)

1. **Fork** 此專案到您的 GitHub 帳號。
2. **Clone** 您 Fork 的專案到本地端。
3. 建立一個新的分支 (Branch) 進行開發：
   ```bash
   git checkout -b feature/your-feature-name
   # 或
   git checkout -b fix/your-bug-fix
   ```
4. 進行程式碼修改。
5. **確保通過測試** (這是最重要的一步)：
   ```bash
   composer test
   ```
   如果可以，請為您的修改新增對應的單元測試。
6. 確保程式碼風格符合 PSR-12 標準：
   ```bash
   composer cs-fix
   ```
7. 提交 (Commit) 並 Push 到您的遠端分支。
8. 到 GitHub 上發送 Pull Request。

## 🧑‍💻 開發環境設定

我們提供了 Docker 環境，讓您無需擔心本地環境差異。

```bash
# 啟動並進入開發容器
make shell

# 安裝依賴
composer install

# 執行測試
composer test
```

## 📝 程式碼風格 (Coding Style)

本專案遵循 PSR-12 編碼標準。在提交 PR 前，請務必執行以下指令檢查並自動修復格式：

```bash
composer cs-fix
```

## 📜 授權

貢獻的程式碼將沿用本專案的 MIT 授權條款。

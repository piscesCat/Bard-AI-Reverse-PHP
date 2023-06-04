## Bard-AI-Reverse

Bard-AI-Reverse là một gói Composer cho phép bạn tương tác với Google Bard AI bằng mã PHP.

## Ngôn ngữ

- [English](README.md)
- [Tiếng Việt](README_vi.md)

## Cài đặt

Phiên bản PHP tối thiểu yêu cầu là 7.0

Sử dụng [Composer](https://getcomposer.org) để cài đặt gói.

Chạy lệnh sau trong terminal:

```
composer require khaiphan/bard-reverse:dev-main
```

## Sử dụng

1. Đầu tiên, bạn cần include autoloader trong mã PHP của bạn:

```php
require 'vendor/autoload.php';
```

2. Tiếp theo, tạo một instance của lớp `Bard` và cung cấp Cookie đăng nhập Google Bard AI:

```php
use KhaiPhan\Google\Bard;

$bard = new Bard('__Secure-1PSID');
```

Hãy chắc chắn thay `'__Secure-1PSID'` bằng giá trị của cookie __Secure-1PSID được lấy từ [trang web Google Bard AI](https://bard.google.com).

3. Sau đó, gọi phương thức `getAnswer()` để lấy kết quả phản hồi từ Bard AI:

```php
$answer = $bard->getAnswer('Hello');
```

4. Bạn có thể truy cập vào câu trả lời từ Bard AI thông qua biến `$answer['content']`. Ví dụ:

```php
$content = $bard['content'];
echo $content;
```

## License

Gói này là mã nguồn mở và có sẵn theo [MIT License](https://opensource.org/licenses/MIT).
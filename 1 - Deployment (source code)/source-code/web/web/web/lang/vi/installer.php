<?php


return [
    'title' => 'Trình cài đặt FoodKing',
    'next'  => 'Bước tiếp theo',
    'welcome' => [
        'templateTitle' => 'Chào mừng',
        'title'         => 'Trình cài đặt FoodKing',
        'message'       => 'Trình hướng dẫn cài đặt và thiết lập dễ dàng.',
        'next'          => 'Kiểm tra yêu cầu',
    ],
    'requirement' => [
        'templateTitle' => 'Bước 1 | Yêu cầu hệ thống',
        'title'         => 'Yêu cầu hệ thống',
        'next'          => 'Kiểm tra quyền',
        'version'       => 'phiên bản',
        'required'      => 'bắt buộc'
    ],
    'permission' => [
        'templateTitle'       => 'Bước 2 | Quyền truy cập tệp',
        'title'               => 'Quyền truy cập tệp',
        'next'                => 'Thiết lập giấy phép',
        'permission_checking' => 'Kiểm tra quyền truy cập tệp'
    ],
    'license' => [
        'templateTitle'       => 'Bước 3 | Giấy phép',
        'title'               => 'Thiết lập giấy phép',
        'next'                => 'Thiết lập trang',
        'active_process'      => 'Quá trình kích hoạt',
        'label'               => [
            'license_key' => 'Mã giấy phép',
            'license_code' => 'Mã kích hoạt'
        ]
    ],
    'site'     => [
        'templateTitle' => 'Bước 4 | Thiết lập trang',
        'title'         => 'Thiết lập trang',
        'next'          => 'Thiết lập cơ sở dữ liệu',
        'label'         => [
            'app_name' => 'Tên ứng dụng',
            'app_url'  => 'Đường dẫn ứng dụng',
        ]
    ],
    'database' => [
        'templateTitle' => 'Bước 5 | Thiết lập cơ sở dữ liệu',
        'title'         => 'Cơ sở dữ liệu',
        'next'          => 'Thiết lập hoàn tất',
        'fail_message'  => 'Không thể kết nối đến cơ sở dữ liệu.',
        'label'         => [
            'database_connection' => 'Kết nối CSDL',
            'database_host'       => 'Máy chủ',
            'database_port'       => 'Cổng',
            'database_name'       => 'Tên CSDL',
            'database_username'   => 'Tên người dùng',
            'database_password'   => 'Mật khẩu',
        ]
    ],
    'final'    => [
        'templateTitle'   => 'Bước 6 | Thiết lập hoàn tất',
        'title'           => 'Hoàn tất cài đặt',
        'success_message' => 'Hệ thống đã được cài đặt thành công.',
        'login_info'      => 'Thông tin đăng nhập',
        'email'           => 'Email',
        'password'        => 'Mật khẩu',
        'email_info'      => 'admin@example.com',
        'password_info'   => '123456',
        'next'            => 'Hoàn thành',
    ],
    'installed' => [
        'success_log_message' => 'Trình cài đặt FoodKing đã được CÀI ĐẶT thành công trên ',
        'update_log_message'  => 'Trình cài đặt FoodKing đã được CẬP NHẬT thành công trên ',
    ],
];

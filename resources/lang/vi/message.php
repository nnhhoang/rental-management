<?php

return [
    // General messages
    'success' => 'Thành công!',
    'error' => 'Lỗi!',
    'not_found' => 'Không tìm thấy!',
    'unauthorized' => 'Chưa đăng nhập!',
    'forbidden' => 'Không có quyền truy cập!',
    'locale_changed' => 'Đã thay đổi ngôn ngữ thành công',
    
    // Authentication messages
    'auth' => [
        'registered' => 'Đăng ký người dùng thành công',
        'login_success' => 'Đăng nhập thành công',
        'login_failed' => 'Thông tin đăng nhập không chính xác',
        'logout_success' => 'Đăng xuất thành công',
        'password_reset_link_sent' => 'Đường dẫn đặt lại mật khẩu đã được gửi tới email của bạn',
        'password_reset_success' => 'Đặt lại mật khẩu thành công',
    ],
    
    // Apartment messages
    'apartment' => [
        'created_successfully' => 'Tạo chung cư thành công',
        'updated_successfully' => 'Cập nhật chung cư thành công',
        'deleted_successfully' => 'Xóa chung cư thành công',
        'not_found' => 'Không tìm thấy chung cư',
        'no_permission' => 'Bạn không có quyền truy cập chung cư này',
    ],
    
    // Room messages
    'room' => [
        'created_successfully' => 'Tạo phòng thành công',
        'updated_successfully' => 'Cập nhật phòng thành công',
        'deleted_successfully' => 'Xóa phòng thành công',
        'not_found' => 'Không tìm thấy phòng',
        'no_permission' => 'Bạn không có quyền truy cập phòng này',
    ],
    
    // Tenant messages
    'tenant' => [
        'created_successfully' => 'Tạo người thuê thành công',
        'updated_successfully' => 'Cập nhật người thuê thành công',
        'deleted_successfully' => 'Xóa người thuê thành công',
        'not_found' => 'Không tìm thấy người thuê',
    ],
    
    // Contract messages
    'contract' => [
        'created_successfully' => 'Tạo hợp đồng thành công',
        'updated_successfully' => 'Cập nhật hợp đồng thành công',
        'terminated_successfully' => 'Chấm dứt hợp đồng thành công',
        'deleted_successfully' => 'Xóa hợp đồng thành công',
        'not_found' => 'Không tìm thấy hợp đồng',
        'no_permission' => 'Bạn không có quyền truy cập hợp đồng này',
        'active_contract_exists' => 'Phòng đã có hợp đồng đang hoạt động',
        'no_active_contract' => 'Không tìm thấy hợp đồng đang hoạt động cho phòng này',
        'date_conflict' => 'Thời gian hợp đồng trùng với hợp đồng đã tồn tại từ :start_date đến :end_date',
        'date_not_available' => 'Ngày bắt đầu hợp đồng không khả dụng. Đã có hợp đồng kết thúc vào ngày :end_date',
        'creation_failed' => 'Tạo hợp đồng thất bại',
        'has_fee_collections' => 'Không thể xóa hợp đồng có liên kết với các khoản phí',
    ],
    
    // Fee messages
    'fee' => [
        'created_successfully' => 'Tạo phí thành công',
        'updated_successfully' => 'Cập nhật phí thành công',
        'deleted_successfully' => 'Xóa phí thành công',
        'payment_recorded' => 'Ghi nhận thanh toán thành công',
        'not_found' => 'Không tìm thấy phí',
        'no_permission' => 'Bạn không có quyền truy cập phí này',
    ],
    
    // Utility messages
    'utility' => [
        'created_successfully' => 'Ghi nhận lượng sử dụng thành công',
        'not_found' => 'Không tìm thấy lượng sử dụng cho phòng này',
    ],
];
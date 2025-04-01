import { createI18n } from 'vue-i18n'

// Define messages directly in this file for now
const messages = {
  en: {
    common: {
      previous: 'Previous',
      next: 'Next',
      submit: 'Submit',
      select_option: 'Select an option'
    },
    auth: {
      login_required: 'You need to log in to access this feature',
      session_expired: 'Your session has expired. Please log in again.',
      login_success: 'Successfully logged in',
      login_failed: 'Login failed. Please check your credentials and try again.'
    },
    contract: {
      create_contract: 'Create Contract',
      contract_details: 'Contract Details',
      select_room: 'Select Room',
      tenant_information: 'Tenant Information',
      review_confirm: 'Review and Confirm',
      review_instructions: 'Please review the information below. If everything looks correct, click Submit to create the contract.',
      details: 'Contract Details',
      price: 'Price',
      enter_price: 'Enter price',
      pay_period: 'Payment Period',
      months: 'months',
      number_of_tenant_current: 'Current Number of Tenants',
      note: 'Note',
      enter_note: 'Enter a note (optional)',
      electricity_details: 'Electricity Details',
      electricity_pay_type: 'Electricity Payment Type',
      electricity_price: 'Electricity Price',
      electricity_number_start: 'Initial Electricity Reading',
      water_details: 'Water Details',
      water_pay_type: 'Water Payment Type',
      water_price: 'Water Price',
      water_number_start: 'Initial Water Reading',
      contract_dates: 'Contract Dates',
      start_date: 'Start Date',
      end_date: 'End Date',
      payment_type: 'Payment Type',
      starting_reading: 'Starting Reading',
      per_person: 'Per Person',
      fixed_price: 'Fixed Price',
      by_usage: 'By Usage'
    },
    tenant: {
      create_new: 'Create New Tenant',
      select_existing: 'Select Existing Tenant',
      name: 'Name',
      enter_name: 'Enter name',
      tel: 'Phone',
      enter_tel: 'Enter phone number',
      email: 'Email',
      enter_email: 'Enter email',
      identity_card_number: 'ID Card Number',
      enter_identity_card: 'Enter ID card number',
      search: 'Search Tenants',
      search_placeholder: 'Search by name, phone, or email',
      no_tenants_found: 'No tenants found',
      selected_tenant: 'Selected Tenant',
      information: 'Tenant Information'
    },
    apartment: {
      name: 'Apartment Name',
      select_apartment: 'Select Apartment'
    },
    room: {
      information: 'Room Information',
      select_room: 'Select Room',
      no_available_rooms: 'No rooms available in this apartment',
      room_number: 'Room Number',
      default_price: 'Default Price',
      max_tenant: 'Maximum Tenants',
      selected_room: 'Selected Room'
    }
  },
  vi: {
    common: {
      previous: 'Trước',
      next: 'Tiếp',
      submit: 'Gửi',
      select_option: 'Chọn một tùy chọn'
    },
    auth: {
      login_required: 'Bạn cần đăng nhập để truy cập tính năng này',
      session_expired: 'Phiên đăng nhập của bạn đã hết hạn. Vui lòng đăng nhập lại.',
      login_success: 'Đăng nhập thành công',
      login_failed: 'Đăng nhập thất bại. Vui lòng kiểm tra thông tin đăng nhập và thử lại.'
    },
    contract: {
      create_contract: 'Tạo Hợp Đồng',
      contract_details: 'Chi Tiết Hợp Đồng',
      select_room: 'Chọn Phòng',
      tenant_information: 'Thông Tin Người Thuê',
      review_confirm: 'Xem Lại và Xác Nhận',
      review_instructions: 'Vui lòng kiểm tra thông tin dưới đây. Nếu mọi thứ đều chính xác, nhấp vào Gửi để tạo hợp đồng.',
      details: 'Chi Tiết Hợp Đồng',
      price: 'Giá',
      enter_price: 'Nhập giá',
      pay_period: 'Kỳ Thanh Toán',
      months: 'tháng',
      number_of_tenant_current: 'Số Người Thuê Hiện Tại',
      note: 'Ghi Chú',
      enter_note: 'Nhập ghi chú (tùy chọn)',
      electricity_details: 'Chi Tiết Điện',
      electricity_pay_type: 'Loại Thanh Toán Điện',
      electricity_price: 'Giá Điện',
      electricity_number_start: 'Chỉ Số Điện Ban Đầu',
      water_details: 'Chi Tiết Nước',
      water_pay_type: 'Loại Thanh Toán Nước',
      water_price: 'Giá Nước',
      water_number_start: 'Chỉ Số Nước Ban Đầu',
      contract_dates: 'Ngày Tháng Hợp Đồng',
      start_date: 'Ngày Bắt Đầu',
      end_date: 'Ngày Kết Thúc',
      payment_type: 'Hình Thức Thanh Toán',
      starting_reading: 'Chỉ Số Ban Đầu',
      per_person: 'Theo Người',
      fixed_price: 'Giá Cố Định',
      by_usage: 'Theo Lượng Sử Dụng'
    },
    tenant: {
      create_new: 'Tạo Người Thuê Mới',
      select_existing: 'Chọn Người Thuê Có Sẵn',
      name: 'Tên',
      enter_name: 'Nhập tên',
      tel: 'Số Điện Thoại',
      enter_tel: 'Nhập số điện thoại',
      email: 'Email',
      enter_email: 'Nhập email',
      identity_card_number: 'Số CMND/CCCD',
      enter_identity_card: 'Nhập số CMND/CCCD',
      search: 'Tìm Kiếm Người Thuê',
      search_placeholder: 'Tìm theo tên, số điện thoại hoặc email',
      no_tenants_found: 'Không tìm thấy người thuê',
      selected_tenant: 'Người Thuê Đã Chọn',
      information: 'Thông Tin Người Thuê',
      select: 'Chọn',
    },
    apartment: {
      name: 'Tên Chung Cư',
      select_apartment: 'Chọn Chung Cư'
    },
    room: {
      information: 'Thông Tin Phòng',
      select_room: 'Chọn Phòng',
      no_available_rooms: 'Không có phòng nào trong chung cư này',
      room_number: 'Số Phòng',
      default_price: 'Giá Mặc Định',
      max_tenant: 'Số Người Tối Đa',
      selected_room: 'Phòng Đã Chọn'
    },
    pagination: {
      showing: 'Hiển thị',
      to: 'đến',
      of: 'trong tổng số',
      results: 'kết quả',
      previous: 'Trước',
      next: 'Sau'
    },
    validation: {
      required: 'Trường :attribute là bắt buộc.',
      email: 'Trường :attribute phải là địa chỉ email hợp lệ.',
      tel: {
        invalid_format: 'Số điện thoại không đúng định dạng.'
      },
      id_card: {
        invalid_format: 'Số CMND/CCCD không đúng định dạng.'
      }
    },
  }
};

const i18n = createI18n({
  legacy: false, 
  locale: 'vi', 
  fallbackLocale: 'en',
  messages
});

export default i18n;
<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    */

    'accepted' => ':attribute phải được chấp nhận.',
    'accepted_if' => ':attribute phải được chấp nhận khi :other là :value.',
    'active_url' => ':attribute không phải là một URL hợp lệ.',
    'after' => ':attribute phải là một ngày sau ngày :date.',
    'after_or_equal' => ':attribute phải là một ngày sau hoặc bằng ngày :date.',
    'alpha' => ':attribute chỉ có thể chứa các chữ cái.',
    'alpha_dash' => ':attribute chỉ có thể chứa chữ cái, số, dấu gạch ngang và gạch dưới.',
    'alpha_num' => ':attribute chỉ có thể chứa chữ cái và số.',
    'array' => ':attribute phải là một mảng.',
    'before' => ':attribute phải là một ngày trước ngày :date.',
    'before_or_equal' => ':attribute phải là một ngày trước hoặc bằng ngày :date.',
    'between' => [
        'numeric' => ':attribute phải nằm trong khoảng :min và :max.',
        'file' => ':attribute phải nằm trong khoảng :min và :max kilobytes.',
        'string' => ':attribute phải nằm trong khoảng :min và :max ký tự.',
        'array' => ':attribute phải có từ :min đến :max phần tử.',
    ],
    'boolean' => 'Trường :attribute phải là true hoặc false.',
    'confirmed' => 'Xác nhận :attribute không khớp.',
    'current_password' => 'Mật khẩu không đúng.',
    'date' => ':attribute không phải là định dạng của ngày-tháng.',
    'date_equals' => ':attribute phải là một ngày bằng với :date.',
    'date_format' => ':attribute không giống với định dạng :format.',
    'declined' => ':attribute phải bị từ chối.',
    'declined_if' => ':attribute phải bị từ chối khi :other là :value.',
    'different' => ':attribute và :other phải khác nhau.',
    'digits' => ':attribute phải gồm :digits chữ số.',
    'digits_between' => ':attribute phải nằm trong khoảng :min và :max chữ số.',
    'dimensions' => ':attribute có kích thước không hợp lệ.',
    'distinct' => ':attribute có giá trị trùng lặp.',
    'email' => ':attribute phải là một địa chỉ email hợp lệ.',
    'ends_with' => ':attribute phải kết thúc bằng một trong những giá trị sau: :values',
    'enum' => ':attribute được chọn không hợp lệ.',
    'exists' => ':attribute được chọn không hợp lệ.',
    'file' => ':attribute phải là một tệp tin.',
    'filled' => ':attribute không được bỏ trống.',
    'gt' => [
        'numeric' => ':attribute phải lớn hơn :value.',
        'file' => ':attribute phải lớn hơn :value kilobytes.',
        'string' => ':attribute phải lớn hơn :value ký tự.',
        'array' => ':attribute phải có nhiều hơn :value phần tử.',
    ],
    'gte' => [
        'numeric' => ':attribute phải lớn hơn hoặc bằng :value.',
        'file' => ':attribute phải lớn hơn hoặc bằng :value kilobytes.',
        'string' => ':attribute phải lớn hơn hoặc bằng :value ký tự.',
        'array' => ':attribute phải có :value phần tử trở lên.',
    ],
    'image' => ':attribute phải là định dạng hình ảnh.',
    'in' => ':attribute được chọn không hợp lệ.',
    'in_array' => 'Trường :attribute không có trong :other.',
    'integer' => ':attribute phải là một số nguyên.',
    'ip' => ':attribute phải là một địa chỉ IP.',
    'ipv4' => ':attribute phải là một địa chỉ IPv4.',
    'ipv6' => ':attribute phải là một địa chỉ IPv6.',
    'json' => ':attribute phải là một chuỗi JSON.',
    'lt' => [
        'numeric' => ':attribute phải nhỏ hơn :value.',
        'file' => ':attribute phải nhỏ hơn :value kilobytes.',
        'string' => ':attribute phải nhỏ hơn :value ký tự.',
        'array' => ':attribute phải có ít hơn :value phần tử.',
    ],
    'lte' => [
        'numeric' => ':attribute phải nhỏ hơn hoặc bằng :value.',
        'file' => ':attribute phải nhỏ hơn hoặc bằng :value kilobytes.',
        'string' => ':attribute phải nhỏ hơn hoặc bằng :value ký tự.',
        'array' => ':attribute không được có nhiều hơn :value phần tử.',
    ],
    'mac_address' => ':attribute phải là một địa chỉ MAC hợp lệ.',
    'max' => [
        'numeric' => ':attribute không được lớn hơn :max.',
        'file' => ':attribute không được lớn hơn :max kilobytes.',
        'string' => ':attribute không được lớn hơn :max ký tự.',
        'array' => ':attribute không được có nhiều hơn :max phần tử.',
    ],
    'mimes' => ':attribute phải là một tập tin có định dạng: :values.',
    'mimetypes' => ':attribute phải là một tập tin có định dạng: :values.',
    'min' => [
        'numeric' => ':attribute phải tối thiểu là :min.',
        'file' => ':attribute phải tối thiểu là :min kilobytes.',
        'string' => ':attribute phải tối thiểu là :min ký tự.',
        'array' => ':attribute phải có tối thiểu :min phần tử.',
    ],
    'multiple_of' => ':attribute phải là bội số của :value.',
    'not_in' => ':attribute được chọn không hợp lệ.',
    'not_regex' => ':attribute có định dạng không hợp lệ.',
    'numeric' => ':attribute phải là một số.',
    'password' => 'Mật khẩu không đúng.',
    'present' => 'Trường :attribute phải được cung cấp.',
    'prohibited' => 'Trường :attribute bị cấm.',
    'prohibited_if' => 'Trường :attribute bị cấm khi :other là :value.',
    'prohibited_unless' => 'Trường :attribute bị cấm trừ khi :other là một trong :values.',
    'prohibits' => 'Trường :attribute cấm :other từ thể hiện.',
    'regex' => ':attribute có định dạng không hợp lệ.',
    'required' => 'Trường :attribute không được bỏ trống.',
    'required_array_keys' => 'Trường :attribute phải chứa các mục nhập cho: :values.',
    'required_if' => 'Trường :attribute không được bỏ trống khi trường :other là :value.',
    'required_unless' => 'Trường :attribute không được bỏ trống trừ khi :other là :values.',
    'required_with' => 'Trường :attribute không được bỏ trống khi một trong :values có giá trị.',
    'required_with_all' => 'Trường :attribute không được bỏ trống khi tất cả :values có giá trị.',
    'required_without' => 'Trường :attribute không được bỏ trống khi một trong :values không có giá trị.',
    'required_without_all' => 'Trường :attribute không được bỏ trống khi tất cả :values không có giá trị.',
    'same' => 'Trường :attribute và :other phải giống nhau.',
    'size' => [
        'numeric' => ':attribute phải bằng :size.',
        'file' => ':attribute phải bằng :size kilobytes.',
        'string' => ':attribute phải chứa :size ký tự.',
        'array' => ':attribute phải chứa :size phần tử.',
    ],
    'starts_with' => ':attribute phải được bắt đầu bằng một trong những giá trị sau: :values',
    'string' => ':attribute phải là một chuỗi ký tự.',
    'timezone' => ':attribute phải là một múi giờ hợp lệ.',
    'unique' => ':attribute đã có trong hệ thống.',
    'uploaded' => ':attribute tải lên thất bại.',
    'url' => ':attribute không giống với định dạng một URL.',
    'uuid' => ':attribute phải là một UUID hợp lệ.',

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    */

    'attributes' => [
        // User attributes
        'name' => 'tên',
        'email' => 'địa chỉ email',
        'password' => 'mật khẩu',
        'password_confirmation' => 'xác nhận mật khẩu',

        // Apartment attributes
        'apartment_id' => 'chung cư',
        'address' => 'địa chỉ',
        'province_id' => 'tỉnh/thành phố',
        'district_id' => 'quận/huyện',
        'ward_id' => 'phường/xã',
        'image' => 'hình ảnh',

        // Room attributes
        'room_number' => 'số phòng',
        'default_price' => 'giá mặc định',
        'max_tenant' => 'số người tối đa',

        // Tenant attributes
        'tenant_id' => 'người thuê',
        'tel' => 'số điện thoại',
        'identity_card_number' => 'số CMND/CCCD',

        // Contract attributes
        'tenant_contract_id' => 'hợp đồng',
        'pay_period' => 'kỳ thanh toán',
        'price' => 'giá tiền',
        'electricity_pay_type' => 'loại thanh toán điện',
        'electricity_price' => 'giá điện',
        'electricity_number_start' => 'chỉ số điện ban đầu',
        'water_pay_type' => 'loại thanh toán nước',
        'water_price' => 'giá nước',
        'water_number_start' => 'chỉ số nước ban đầu',
        'number_of_tenant_current' => 'số người hiện tại',
        'note' => 'ghi chú',
        'start_date' => 'ngày bắt đầu',
        'end_date' => 'ngày kết thúc',

        // Fee attributes
        'electricity_number_before' => 'chỉ số điện trước',
        'electricity_number_after' => 'chỉ số điện hiện tại',
        'water_number_before' => 'chỉ số nước trước',
        'water_number_after' => 'chỉ số nước hiện tại',
        'charge_date' => 'ngày tính phí',
        'total_price' => 'tổng tiền',
        'total_paid' => 'số tiền đã thanh toán',
        'amount' => 'số tiền',

        // Utility attributes
        'usage_number' => 'chỉ số sử dụng',
        'input_date' => 'ngày nhập',
    ],

    // Custom validation messages
    'custom' => [
        'apartment' => [
            'name.required' => 'Vui lòng nhập tên chung cư',
            'address.required' => 'Vui lòng nhập địa chỉ chung cư',
        ],
        'room' => [
            'room_number.required' => 'Vui lòng nhập số phòng',
            'default_price.required' => 'Vui lòng nhập giá mặc định',
        ],
        'tenant' => [
            'name.required' => 'Vui lòng nhập tên người thuê',
            'tel.required' => 'Vui lòng nhập số điện thoại',
            'email.required' => 'Vui lòng nhập địa chỉ email',
        ],
        'contract' => [
            'pay_period.required' => 'Vui lòng chọn kỳ thanh toán',
            'price.required' => 'Vui lòng nhập giá hợp đồng',
        ],
        'fee' => [
            'charge_date.required' => 'Vui lòng chọn ngày tính phí',
            'total_price.required' => 'Vui lòng nhập tổng tiền',
        ],
    ],
];

<?php

return [
    'name' => 'Quality',

    // Default inspection sample percentage if not specified
    'default_sample_percentage' => 10.0,

    // Auto-create NCR when inspection status is rejected
    'auto_create_ncr_on_rejection' => true,

    // Severity levels for non-conformance
    'severities' => [
        'minor'    => 'ثانوي (Minor)',
        'major'    => 'رئيسي (Major)',
        'critical' => 'حرج (Critical)',
    ],

    // Root cause categories
    'root_causes' => [
        'raw_material' => 'المواد الخام والموردين (Raw Material)',
        'machinery'    => 'الآلات وخطوط الإنتاج (Machinery & Tooling)',
        'workmanship'  => 'العمالة والتشغيل البشري (Workmanship)',
        'environment'  => 'بيئة العمل والتخزين (Environment & Storage)',
        'design'       => 'المخططات والمواصفات (Design & Specs)',
        'other'        => 'أسباب أخرى (Other)',
    ],
];

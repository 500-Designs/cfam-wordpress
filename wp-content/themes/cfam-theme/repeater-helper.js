// Your data.
(function ($) {
    const data = [
        { label: 'United States', value: '67.86' },
        { label: 'Canada', value: '14.71' },
        { label: 'United Kingdom', value: '8.23' },
        { label: 'Germany', value: '4.40' },
        { label: 'Italy', value: '2.01' },
        { label: 'Spain', value: '1.20' },
        { label: 'Israel', value: '0.85' },
        { label: 'Brazil', value: '0.74' }
    ];
    // For each data item...
    data.forEach((item, index) => {
        // If it's not the first item, click the "Add Row" button.
        $('.acf-field-6462d90f619aa .acf-button').click();

        // Set the label and value for this row.
        $(`.acf-field-6462d90f619aa .acf-row:eq(${index}) .acf-field[data-name="label"] input`).val(item.label);
        $(`.acf-field-6462d90f619aa .acf-row:eq(${index}) .acf-field[data-name="value"] input`).val(item.value);
    });
})(jQuery);

(function ($) {
    // Your data.
    const data = [
        { label: 'Electric Utilities', value: '23.62' },
        { label: 'Independent Power & Renewable Electricity Producers', value: '20.72' },
        { label: 'Energy Infrastructure', value: '19.50' },
        { label: 'Multi-Utilities', value: '11.69' },
        { label: 'Ground Transportation', value: '9.85' },
        { label: 'Water Utilities', value: '3.41' },
        { label: 'Commercial Services & Supply', value: '3.35' },
        { label: 'Machinery', value: '2.48' },
        { label: 'Digital Infrastructure', value: '1.96' },
        { label: 'Financials', value: '1.72' },
        { label: 'Information Technology', value: '0.87' },
        { label: 'Gas Utilities', value: '0.83' }
    ];

    // For each data item...
    data.forEach((item, index) => {
        // If it's not the first item, click the "Add Row" button.
        $('.acf-field-6462d929619ad .acf-button').click();

        // Set the label and value for this row.
        $(`.acf-field-6462d929619ad .acf-row:eq(${index}) .acf-field[data-name="label"] input`).val(item.label);
        $(`.acf-field-6462d929619ad .acf-row:eq(${index}) .acf-field[data-name="value"] input`).val(item.value);
    });
})(jQuery);

(function ($) {
    const data = [
        { investment: 'NextEra Energy, Inc.', infrastructure_type: 'Public Infrastructure Securities', asset_type: 'Utilities', percentage: '4.75%' },
        { investment: 'Canadian Pacific Railway Ltd.', infrastructure_type: 'Public Infrastructure Securities', asset_type: 'Industrials', percentage: '4.04%' },
        { investment: 'Vistra Energy Corp.', infrastructure_type: 'Public Infrastructure Securities', asset_type: 'Utilities', percentage: '3.96%' },
        { investment: 'Atlantica Sustainable Infrastructure PLC', infrastructure_type: 'Public Infrastructure Securities', asset_type: 'Utilities', percentage: '3.75%' },
        { investment: 'Constellation Energy Corp.', infrastructure_type: 'Public Infrastructure Securities', asset_type: 'Utilities', percentage: '3.34%' },
        { investment: 'RWE AG', infrastructure_type: 'Public Infrastructure Securities', asset_type: 'Utilities', percentage: '3.23%' },
        { investment: 'EQT Corp.', infrastructure_type: 'Public Infrastructure Securities', asset_type: 'Energy', percentage: '2.95%' },
        { investment: 'Union Pacific Corp.', infrastructure_type: 'Public Infrastructure Securities', asset_type: 'Industrials', percentage: '2.94%' },
        { investment: 'AES Corp.', infrastructure_type: 'Public Infrastructure Securities', asset_type: 'Utilities', percentage: '2.86%' },
        { investment: 'Cheniere Energy, Inc.', infrastructure_type: 'Public Infrastructure Securities', asset_type: 'Energy', percentage: '2.75%' }
    ];

    // For each data item...
    data.forEach((item, index) => {
        // If it's not the first item, click the "Add Row" button.
        $('.acf-field-6462d9a1b262f .acf-button').click();

        // Set the investment, infrastructure type, asset type, and percentage for this row.
        $(`.acf-field-6462d9a1b262f .acf-row:eq(${index}) .acf-field[data-name="investment"] input`).val(item.investment);
        $(`.acf-field-6462d9a1b262f .acf-row:eq(${index}) .acf-field[data-name="infrastructure_type"] input`).val(item.infrastructure_type);
        $(`.acf-field-6462d9a1b262f .acf-row:eq(${index}) .acf-field[data-name="asset_type"] input`).val(item.asset_type);
        $(`.acf-field-6462d9a1b262f .acf-row:eq(${index}) .acf-field[data-name="percentage"] input`).val(item.percentage);
    });
})(jQuery);

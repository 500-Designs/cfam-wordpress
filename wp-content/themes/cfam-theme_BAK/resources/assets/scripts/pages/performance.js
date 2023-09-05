module.exports = {
    init: function () {

        // START: Hypothetical Growth Chart

        const lineChart = document.getElementById("hypothetical-growth-line-chart");
        ctx = lineChart.getContext('2d');
        ctx.clearRect(0, 0, lineChart.width, lineChart.height);
        const hypotheticalGrowthChart = new Chart(ctx, {
            type: "line",
            data: {
                datasets: [{
                    backgroundColor: "#004b8d",
                    borderColor: "#004b8d",
                    pointRadius: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    tooltip: { enabled: false },
                    legend: { enabled: false } //<-- This makes the legend not to display
                },
                scales: {
                    y: {
                        min: 0,
                        max: 50000,
                        ticks: {
                            stepSize: 10000,
                            /* callback: function(value, index, ticks) {
                                return 'TBD';
                            } */
                        },
                        grid: {
                            drawBorder: false
                        }
                    },
                    x: {
                        ticks: {
                            autoSkip: false,
                            stepSize: 1,
                            // maxTicksLimit: 2,
                            maxRotation: 0,
                            minRotation: 0,
                            padding: 10,
                        },
                        grid: {
                            display: false
                        }
                    }
                }

            }
        });

        function updateHypotheticalGrowthData(shareClass) {

            if (typeof hpgData == 'undefined') {
                console.log("NO  Hypothetical Growth Chart Data found...");
                return;
            }
            if (typeof hpgDataI == 'undefined') {
                console.log("NO  Hypothetical Growth Chart Data for Fund I found...");
                return;
            }
            if (typeof hpgDataC == 'undefined') {
                console.log("NO  Hypothetical Growth Chart Data for Fund C found...");
                return;
            }

            let xValues = hpgData[0];
            var yValues = new Array();

            $.each(hpgData[0], function (index, item) {
                switch (shareClass) {
                    case 'no-load':
                        yValues.push(hpgData[1][index]);
                        break;
                    case 'class-c':
                        // Code to execute when shareClass is "fund-C"
                        xValues = hpgDataC[0];
                        yValues.push(hpgDataC[1][index]);
                        break;
                    case 'class-i':
                        // Code to execute when shareClass is "fund-I"
                        xValues = hpgDataI[0];
                        yValues.push(hpgDataI[1][index]);
                        break;
                    default:
                        yValues.push(hpgData[2][index]);
                        break;
                }
            });

            hypotheticalGrowthChart.data.labels = xValues;

            hypotheticalGrowthChart.data.datasets = [{
                backgroundColor: "#004b8d",
                borderColor: "#004b8d",
                data: yValues,
                pointRadius: 0
            }]

            hypotheticalGrowthChart.options = {
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        min: (shareClass == 'no-load') ? 9000 : 9000,
                        max: (shareClass == 'no-load') ? 12000 : 12000,
                        ticks: {
                            stepSize: (shareClass == 'no-load') ? 500 : 500,
                            // padding: 10,
                        },
                    },
                    x: {
                        grace: '10%',
                        grid: { display: false },
                        ticks: {
                            padding: 10
                        }
                    }
                },
            };

            hypotheticalGrowthChart.update();
        }


        // END: Hypothetical Growth Chart

        // START: Performance and Metrics (Performance Since Inception) Chart and Table

        if (typeof performanceData == 'undefined') {
            console.log("NO Performance and Metrics data found..");
            return;
        }

        const pmData = performanceData['pm_data'];

        const keys = performanceData['keys'];
        const psiDate = new Date(performanceData['report_date']);
        $(".performance-since-inception-date").text(`(As of ${formatDate(psiDate)})`);
        $(".performance-summary-as-of").text(`(As of ${formatDate(psiDate)})`);
        $(".daily-as-of").text(`(As of ${dailyReportDate})`);
        $(".table-as-of-date").text(`As of ${formatDate(psiDate)}`);

        const fixedStringToParse = "TOTAL RETURN INDEX";

        const parseTitle = (val) => {
            if (val.includes("(at Nav)")) {
                return val.replace("(at Nav)", "(at NAV)");
            } else if (val.includes(fixedStringToParse)) {
                return val.replace(fixedStringToParse, "Total Return Index");
            } else if (val.includes("TOTAL RETURN INDEX")) {
                return val.replace("TOTAL RETURN INDEX", "Total Return Index");
            } else if (val.includes("OFFER")) {
                return val.replace("OFFER", "Offer");
            } else {
                return val;
            }
        }

        const hypenIfEmpty = (val) => {
            if (!!val) return val + '%';
            return '-';
        };

        function updatePerformanceTable(shareClass) {
            var performanceTable = $('section.performance-metrics table.performance-table');
            performanceTable.find('tbody').empty();

            var tableRow = '';
            const atNavInd = $("td span.at-nav-label");
            // console.log("atNavInd: ", atNavInd);
            let fundToGet = "59003";
            let navIndicator;

            switch (shareClass) {
                case 'load':
                    fundToGet = '59003';
                    navIndicator = '';
                    break;
                case 'class-c':
                    fundToGet = '59005';
                    navIndicator = '';
                    break;
                case 'class-i':
                    fundToGet = '59001';
                    navIndicator = '';
                    break;
                default:
                    atNavInd.show();
                    navIndicator = "(at NAV)";
                    break;
            }

            let filteredPmData = pmData.filter(row => fundToGet == row[3]);
            const item = filteredPmData[0];

            switch (shareClass) {
                case 'load':
                    // Code to execute when shareClass is "fund-A load"
                    tableRow += '<tr>';
                    tableRow += '<td>Sustainable Infrastructure Fund At Offer</td>';
                    tableRow += '<td>' + hypenIfEmpty(item[keys['R_Fd_1_Month_Load']]) + '</td>';
                    tableRow += '<td>' + hypenIfEmpty(item[keys['R_Fd_3_Month_Load']]) + '</td>';
                    tableRow += '<td>' + hypenIfEmpty(item[keys['R_Fd_6_Month_Load']]) + '</td>';
                    tableRow += '<td>' + hypenIfEmpty(item[keys['R_Fd_1Yr_Load']]) + '</td>';
                    tableRow += '<td>' + hypenIfEmpty(item[keys['R_Fd_ITD_Cum_Load']]) + '</td>';
                    tableRow += '</tr>';
                    break;
                default:
                    // Code to execute when shareClass is "fund-A no-load" (default)
                    tableRow += '<tr>';
                    tableRow += '<td>Sustainable Infrastructure Fund ' + navIndicator + '</td>';
                    tableRow += '<td>' + hypenIfEmpty(item[keys['R_Fd_1_Month_NoLoad']]) + '</td>';
                    tableRow += '<td>' + hypenIfEmpty(item[keys['R_Fd_3_Month_NoLoad']]) + '</td>';
                    tableRow += '<td>' + hypenIfEmpty(item[keys['R_Fd_6_Month_NoLoad']]) + '</td>';
                    tableRow += '<td>' + hypenIfEmpty(item[keys['R_Fd_1Yr_NoLoad']]) + '</td>';
                    tableRow += '<td>' + hypenIfEmpty(item[keys['R_Fd_ITD_Cum_NoLoad']]) + '</td>';
                    tableRow += '</tr>';
                    break;
            }

            $.each(filteredPmData, function (index, item) {
                if (item == '') return;
                let rowTitle = item[keys['Index Name']];
                // if (rowTitle.includes("Sustainable Infrastructure Fund")) return;

                tableRow += '<tr>';
                tableRow += '<td>' + parseTitle(rowTitle) + '</td>';
                tableRow += '<td>' + hypenIfEmpty(item[keys['R_IDX_1_Month']]) + '</td>';
                tableRow += '<td>' + hypenIfEmpty(item[keys['R_IDX_3_Month']]) + '</td>';
                tableRow += '<td>' + hypenIfEmpty(item[keys['R_IDX_6_Month']]) + '</td>';
                tableRow += '<td>' + hypenIfEmpty(item[keys['R_IDX_1YR']]) + '</td>';
                tableRow += '<td>' + hypenIfEmpty(item[keys['R_IDX_ITD_CUM']]) + '</td>';
            });


            performanceTable.find('tbody').append(tableRow);
        }

        var chart_id = $(".performance-metrics-bar-chart").find("canvas").attr("id");
        /* Use chart block id here */
        var labelChart2 = document.getElementById(chart_id).getContext('2d');
        var psiBarsChart = new Chart(labelChart2, {
            type: "bar",
            data: {
                height: 360,
                labels: ["1 Month", "3 Month", "6 Month", "1 Year", "ITD"],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    tooltip: { enabled: false },
                    legend: { //<-- Legend settings
                        display: true,
                        position: 'bottom',
                        labels: {
                            boxWidth: 12
                        }
                    }
                },
                scales: {
                    x: {
                        grid: {
                            display: false,
                        }
                    },
                    y: {
                        min: -20,
                        max: 20,
                        ticks: {
                            stepSize: 5,
                        }
                    }
                }
            }
        });


        // START: Performance multi- bar charts
        function updatePerformanceBarsChart(shareClass) {



            var mydataSets = new Array();
            var backgroundColor = ["#004B8D", "rgb(0, 75, 141, 0.8)", "rgb(0, 75, 141, 0.6)", "rgb(0, 75, 141, 0.4)"]
            var backgroundColorIndex = 1;

            let fundToGet = "59003";
            switch (shareClass) {
                case 'load':
                    fundToGet = '59003';
                    break;
                case 'class-c':
                    fundToGet = '59005';
                    break;
                case 'class-i':
                    fundToGet = '59001';
                    break;
                default:
                    break;
            }
            let filteredPmData = pmData.filter(row => fundToGet == row[3]);
            const item = filteredPmData[0];

            // ADD first BAR "Cantor Fitzgerald Sustainable Infrastructure"
            var firsData = new Object();
            firsData.barPercentage = 1.0;
            firsData.backgroundColor = backgroundColor[0];
            if (shareClass == 'no-load') {
                $('.p-disclaimer.at-nav').show();
                $('.p-disclaimer.at-offer').hide();
                firsData.label = "Sustainable Infrastructure Fund (at NAV)"
                firsData.data = [
                    item[keys["R_Fd_1_Month_NoLoad"]],
                    item[keys["R_Fd_3_Month_NoLoad"]],
                    item[keys["R_Fd_6_Month_NoLoad"]],
                    item[keys["R_Fd_1Yr_NoLoad"]],
                    item[keys["R_Fd_ITD_Cum_NoLoad"]]];
            } else {
                $('.p-disclaimer.at-nav').hide();
                $('.p-disclaimer.at-offer').show();

                firsData.label = "Sustainable Infrastructure Fund At Offer";
                firsData.data = [
                    item[keys["R_Fd_1_Month_Load"]],
                    item[keys["R_Fd_3_Month_Load"]],
                    item[keys["R_Fd_6_Month_Load"]],
                    item[keys["R_Fd_1Yr_Load"]],
                    item[keys["R_Fd_ITD_Cum_Load"]]
                ];
            }
            mydataSets.push(firsData);


            $.each(filteredPmData, function (index, item) {
                var mydata = new Object();
                mydata.barPercentage = 1.0;

                if (index >= 0) {
                    mydata.label = parseTitle(item[keys['Index Name']]);
                    mydata.backgroundColor = backgroundColor[backgroundColorIndex];
                    mydata.data = [
                        filteredPmData[index][keys["R_IDX_1_Month"]],
                        filteredPmData[index][keys["R_IDX_3_Month"]],
                        filteredPmData[index][keys["R_IDX_6_Month"]],
                        filteredPmData[index][keys["R_IDX_1YR"]],
                        filteredPmData[index][keys["R_IDX_ITD_CUM"]]
                    ];

                    backgroundColorIndex++;
                    mydataSets.push(mydata);
                }

            });

            psiBarsChart.data.datasets = mydataSets;

            if (shareClass !== 'no-load') {
                psiBarsChart.data.datasets[0].label = "Cantor Fitzgerald Sustainable Infrastructure Fund (at Offer)"
            }

            psiBarsChart.options.scales = {
                y: {
                    min: (shareClass == 'no-load') ? -15 : -15,
                    max: (shareClass == 'no-load') ? 15 : 15,
                    ticks: {
                        stepSize: (shareClass == 'no-load') ? 5 : 5,
                        callback: function (value) {
                            return value + '%';
                        }
                    }


                }
            }

            psiBarsChart.update();
        }

        // END: Performance and Metrics (Performance Since Inception) Chart and Table


        // START: Monthly Total Returns by Share Class Table

        function updateMonthlyTotalReturnsByShareClass(shareClass) {
            if (typeof shareClassTableData == 'undefined') {
                console.log("NO shareClassTableData found...");
                return;
            }

            var table = $('section.historical-performance table.performance-table');

            var tableBody = table.children('tbody');
            var numberOfColumns = 18;
            var tableFundLabel = $('.table-current-fund-class');
            let tableData = [];

            switch (shareClass) {
                case 'load':
                    tableData = shareClassTableData[1];
                    tableFundLabel.text("CLASS A");
                    break;
                case 'class-c':
                    tableData = shareClassTableData[2];
                    tableFundLabel.text("CLASS C");
                    break;
                case 'class-i':
                    tableData = shareClassTableData[3];
                    tableFundLabel.text("CLASS I");
                    break;
                default:
                    tableData = shareClassTableData[0];
                    tableFundLabel.text("CLASS A");
                    break;
            }

            tableBody.empty();

            for (const key in tableData) {
                var row = $("<tr>");
                Array(numberOfColumns).fill(0).forEach((x, col) => {
                    row.append($("<td>").text("-"));
                });
                row.children("td").eq(0).text(key);

                const rd = tableData[key];

                if (rd["01"]) row.children("td").eq(1).text(rd["01"]).append("%");
                if (rd["02"]) row.children("td").eq(2).text(rd["02"]).append("%");
                if (rd["03"]) row.children("td").eq(3).text(rd["03"]).append("%");
                if (rd["q1"]) row.children("td").eq(4).text(rd["q1"]).append("%");
                if (rd["04"]) row.children("td").eq(5).text(rd["04"]).append("%");
                if (rd["05"]) row.children("td").eq(6).text(rd["05"]).append("%");
                if (rd["06"]) row.children("td").eq(7).text(rd["06"]).append("%");
                if (rd["q2"]) row.children("td").eq(8).text(rd["q2"]).append("%");
                if (rd["07"]) row.children("td").eq(9).text(rd["07"]).append("%");
                if (rd["08"]) row.children("td").eq(10).text(rd["08"]).append("%");
                if (rd["09"]) row.children("td").eq(11).text(rd["09"]).append("%");
                if (rd["q3"]) row.children("td").eq(12).text(rd["q3"]).append("%");
                if (rd["10"]) row.children("td").eq(13).text(rd["10"]).append("%");
                if (rd["11"]) row.children("td").eq(14).text(rd["11"]).append("%");
                if (rd["12"]) row.children("td").eq(15).text(rd["12"]).append("%");
                if (rd["q4"]) row.children("td").eq(16).text(rd["q4"]).append("%");
                if (rd["ytd"]) row.children("td").eq(17).text(rd["ytd"]).append("%");

                tableBody.prepend(row);
            }
        }
        // END: Monthly Total Returns by Share Class Table

        function updatePerformanceSummaryContent(shareClass) {
            let isClassA = true;
            if (typeof psDailyData == 'undefined') {
                console.log("NO psDailyData found...");
                return;
            }
            if (typeof psMonthlyData == 'undefined') {
                console.log("NO psMonthlyData found...");
                return;
            }
            if (typeof arsiApplicable == 'undefined') {
                console.log("NO arsiApplicable found...");
                return;
            }
            let overRideAdr = false;
            if (psDefaultADR && psDefaultADROther && forceDefaultAdr) {
                overRideAdr = true;
            }
            // console.log("psDailyData: ");
            // console.log(psDailyData);
            // console.log(psMonthlyData);
            const sectionWrap = $('.performance-summary-section');
            var percentElement = $('<sup>').text('%');
            switch (shareClass) {
                case 'load':
                    sectionWrap.find('.ps-nav').text(psDailyData.f_59003.NAV);
                    sectionWrap.find('.ps-itd').text(psMonthlyData.f_59003_load.itd);
                    sectionWrap.find('.ps-arsi').text(psMonthlyData.f_59003_load.arsi).append(percentElement);;
                    if (!overRideAdr && psDailyData.f_59003.Annualized_Distribution_Yield && psDailyData.f_59003.Annualized_Distribution_Yield !== "0") {
                        sectionWrap.find('.ps-ady').text(removePercentage(psDailyData.f_59003.Annualized_Distribution_Yield));
                    } else {
                        sectionWrap.find('.ps-ady').text(psDefaultADR);
                    }
                    break;
                case 'class-c':
                    isClassA = false;
                    sectionWrap.find('.ps-nav').text(psDailyData.f_59005.NAV);
                    sectionWrap.find('.ps-itd').text(psMonthlyData.f_59005.itd);
                    sectionWrap.find('.ps-arsi').text(psMonthlyData.f_59005_load.arsi);
                    if (!overRideAdr && psDailyData.f_59005.Annualized_Distribution_Yield && psDailyData.f_59005.Annualized_Distribution_Yield !== "0") {
                        sectionWrap.find('.ps-ady').text(removePercentage(psDailyData.f_59005.Annualized_Distribution_Yield));
                    } else {
                        sectionWrap.find('.ps-ady').text(psDefaultADROther);
                    }
                    break;
                case 'class-i':
                    isClassA = false;
                    sectionWrap.find('.ps-nav').text(psDailyData.f_59001.NAV);
                    sectionWrap.find('.ps-itd').text(psMonthlyData.f_59001.itd);
                    sectionWrap.find('.ps-arsi').text(psMonthlyData.f_59001_load.arsi).append(percentElement);;
                    if (!overRideAdr && psDailyData.f_59001.Annualized_Distribution_Yield && psDailyData.f_59001.Annualized_Distribution_Yield !== "0") {
                        sectionWrap.find('.ps-ady').text(removePercentage(psDailyData.f_59001.Annualized_Distribution_Yield));
                    } else {
                        sectionWrap.find('.ps-ady').text(psDefaultADROther);
                    }
                    break;
                default:
                    sectionWrap.find('.ps-nav').text(psDailyData.f_59003.NAV);
                    sectionWrap.find('.ps-itd').text(psMonthlyData.f_59003.itd);
                    sectionWrap.find('.ps-arsi').text(psMonthlyData.f_59003.arsi).append(percentElement);;
                    if (!overRideAdr && psDailyData.f_59003.Annualized_Distribution_Yield && psDailyData.f_59003.Annualized_Distribution_Yield !== "0") {
                        sectionWrap.find('.ps-ady').text(removePercentage(psDailyData.f_59003.Annualized_Distribution_Yield));
                    } else {
                        sectionWrap.find('.ps-ady').text(psDefaultADR);
                    }
                    break;
            }

            if (arsiApplicable && isClassA) {
                sectionWrap.find('.arsi-na').hide();
                sectionWrap.find('.ps-arsi').show();
            } else {
                sectionWrap.find('.arsi-na').show();
                sectionWrap.find('.ps-arsi').hide();
            }

            function removePercentage(text) {
                if (text.indexOf('%') !== -1) {
                    text = text.replace('%', '');
                }
                return text;
            }
        }

        // START: scripts for inception date CMS editable text content
        const iDate = document.getElementsByClassName('dynamic-id')[0];
        const ger = document.getElementsByClassName('dynamic-ger')[0];
        const ner = document.getElementsByClassName('dynamic-ner')[0];
        const el = document.getElementsByClassName('dynamic-el')[0];
        const ela = document.getElementsByClassName('dynamic-ela')[0];
        const defaultValues = {
            iDate: iDate.textContent,
            ger: ger.textContent,
            ner: ner.textContent,
            el: el.textContent,
            ela: ela.textContent,
        }

        function updateInceptionDateTexts(shareClass) {
            switch (shareClass) {
                case 'class-c':
                    iDate.textContent = "Inception Date: March 20, 2023";
                    ger.textContent = "Gross Expense Ratio: 5.24%";
                    ner.textContent = "Net Expense Ratio: 3.75%";
                    el.textContent = "3.25%";
                    ela.textContent = "July 31, 2024";
                    break;
                case 'class-i':
                    iDate.textContent = "Inception Date: March 20, 2023";
                    ger.textContent = "Gross Expense Ratio: 4.24%";
                    ner.textContent = "Net Expense Ratio: 2.75%";
                    el.textContent = "2.25%";
                    ela.textContent = "July 31, 2024";
                    break;
                default:
                    iDate.textContent = defaultValues.iDate;
                    ger.textContent = defaultValues.ger;
                    ner.textContent = defaultValues.ner;
                    el.textContent = defaultValues.el;
                    ela.textContent = defaultValues.ela;
                    break;
            }
        }

        // END: scripts for inception date CMS editable text content

        // START scripts for: Nav History Bar Chart

        if (ndhData) {
            var xAxis = ndhData[0];
            var yAxisLeft = ndhData[1];
            var yAxisRight = ndhData[2];
        }

        var navDhLineChart = document.getElementById("nav-history-bar-chart").getContext('2d');

        // values from the following arrays are taken for php generated values from historical-performance.php
        if (ndhData && ndhDataI && ndhDataC) {
            console.log("Nav History Bar Chart file sources:");
            console.log(ndhData[3]);
        } else {
            console.log("navDhLineChart has NO fetch values from back-end...");
            var xAxis = new Array();
            var yAxisLeft = new Array();
            var yAxisRight = new Array();
        }

        let isYAxisRightDisabled = false;
        isYAxisRightDisabled = yAxisRight.every((e) => e == 0);
        if (isYAxisRightDisabled) {
            $(".nav-bar-graph-title").text("NAV History");
            $(".nav-bar-graph-legend > div:last-child").hide();
        }
        const navHistoryBarChartData = {
            labels: xAxis,
            datasets: [{
                type: "bar",
                label: "NAV Per share",
                backgroundColor: "#004b8d",
                borderColor: "#004b8d",
                data: yAxisLeft,
                pointRadius: 3,
                order: 2,
                yAxisID: "y"
            },
            {
                type: "line",
                label: "Gross Annualized Distribution Rate\nPer Share",
                borderColor: "#333",
                backgroundColor: "#004b8d",
                borderWidth: 1,
                data: yAxisRight,
                order: 1,
                yAxisID: "y1"
            }]
        };

        const navHistoryBarChart = new Chart(navDhLineChart, {
            data: navHistoryBarChartData,
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    tooltip: {
                        enabled: true,
                        callbacks: {
                            label: function (context) {
                                let label = context.dataset.label || '';

                                if (label) {
                                    label += ': ';
                                }
                                if (context.parsed.y !== null) {
                                    label += new Intl.NumberFormat('en-US', { style: 'currency', currency: 'USD' }).format(context.parsed.y);
                                }
                                return label;
                                // return context.beforeBody
                            }
                        }
                    },
                    // legend: { display: false } //<-- This makes the legend not to display
                    legend: { //<-- Legend settings
                        display: false,
                        // position: 'top',
                        // labels: {
                        //     boxWidth: 12
                        // }
                    },

                },
                scales: {
                    y: {
                        title: {
                            display: true,
                            text: "NAV Per Share",
                            color: "#000000"
                        },
                        min: 0,
                        max: 20,
                        ticks: {
                            stepSize: 5,
                            /* callback: function(value, index, ticks) {
                                return 'TBD';
                            } */
                        },
                        grid: {
                            drawBorder: false
                        }
                    },
                    y1: {
                        title: {
                            display: !isYAxisRightDisabled,
                            text: "Gross Annualized Distribution Rate\nPer Share",
                            color: "#000000"
                        },
                        position: "right",
                        min: 0,
                        max: 20,
                        // grace: '10%',
                        ticks: {
                            stepSize: 5,
                            display: !isYAxisRightDisabled,
                            callback: function (value, index, ticks) {
                                return value + '%';
                            }
                        },
                        grid: {
                            drawBorder: false
                        }
                    },
                    x: {
                        ticks: {
                            maxRotation: 90,
                            minRotation: 90
                        },
                        grace: '0%',
                        grid: {
                            display: false
                        }
                    }
                }

            }
        });

        function updateNavHistoryBarChart(shareClass) {
            switch (shareClass) {
                case 'load':
                    navHistoryBarChart.data.labels = ndhData[0];
                    navHistoryBarChart.data.datasets[0].data = ndhData[1];
                    navHistoryBarChart.data.datasets[1].data = ndhData[2];
                    break;
                case 'class-c':
                    navHistoryBarChart.data.labels = ndhDataC[0];
                    navHistoryBarChart.data.datasets[0].data = ndhDataC[1];
                    navHistoryBarChart.data.datasets[1].data = ndhDataC[2];
                    break;
                case 'class-i':
                    navHistoryBarChart.data.labels = ndhDataI[0];
                    navHistoryBarChart.data.datasets[0].data = ndhDataI[1];
                    navHistoryBarChart.data.datasets[1].data = ndhDataI[2];
                    break;
                default:
                    navHistoryBarChart.data.labels = ndhData[0];
                    navHistoryBarChart.data.datasets[0].data = ndhData[1];
                    navHistoryBarChart.data.datasets[1].data = ndhData[2];
                    break;
            }
            setTimeout(function () {

            }, 100);

            navHistoryBarChart.update();
        }
        // END scripts for: Nav History Bar Chart

        updatePerformanceSummaryContent('no-load');
        updatePerformanceTable('no-load');
        updateHypotheticalGrowthData('no-load');
        updatePerformanceBarsChart('no-load');
        updateMonthlyTotalReturnsByShareClass('no-load');
        updateInceptionDateTexts('no-load');
        updateNavHistoryBarChart('no-load');


        // TOGGLE drodown
        var select = $('.fund-selector');
        select.change(function () {
            const selectedValue = $(this).find(':selected').val();
            updatePerformanceSummaryContent(selectedValue);
            updateHypotheticalGrowthData(selectedValue);
            updatePerformanceBarsChart(selectedValue);
            updatePerformanceTable(selectedValue);
            updateMonthlyTotalReturnsByShareClass(selectedValue);
            updateInceptionDateTexts(selectedValue);
            updateNavHistoryBarChart(selectedValue);

            if (selectedValue == 'load') {
                $('.value-load').show();
                $('.value-no-load').hide();
            } else {
                $('.value-no-load').show();
                $('.value-load').hide();
            }
            select.val(selectedValue);
        });


    },
};


function formatDate(date) {
    const months = [
        "January", "February", "March", "April", "May", "June",
        "July", "August", "September", "October", "November", "December"
    ];

    const day = date.getDate();
    const monthIndex = date.getMonth();
    const year = date.getFullYear();

    return `${months[monthIndex]} ${day}, ${year}`;
}
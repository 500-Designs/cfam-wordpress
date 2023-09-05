function splitLongStringToArray(string) {
    let words = string.split(' ');
    let result = [];
    let temp = [];
    for (let i = 0; i < words.length; i++) {
        temp.push(words[i]);
        if (temp.length === 4 || i === words.length - 1) {
            result.push(temp.join(' '));
            temp = [];
        }
    }
    result[0] = " " + result[0];
    return result;
}

// Asset Chart

var assetCanvas = document.getElementById("asset-chart");
var assetTypeRepeater = document.querySelectorAll('[data-repeater="asset_type"] li');
var labels = [];
var data = [];
// Iterate over the ACF repeater values and extract the label and value
assetTypeRepeater.forEach(function (item) {
    var label = item.querySelector('.label').textContent;
    var value = parseFloat(item.querySelector('.percentage').textContent);

    labels.push(label);
    data.push(value);
});

console.log("data: ", data);
var assetData = {
    labels: labels,
    datasets: [
        {
            data: data,
            backgroundColor: [
                "rgba(0, 51, 102, 1)",
                "rgba(0, 75, 141, 0.8)",
                "rgba(147, 199, 232, 1)",
            ]
        }]
};

// label colors
var backgroundColors = assetData.datasets[0].backgroundColor;
for (var i = 0; i < assetTypeRepeater.length; i++) {
    var colorValue = backgroundColors[i];
    var spanColorElement = assetTypeRepeater[i].querySelector("span.color");
    spanColorElement.style.backgroundColor = colorValue;
}



var pieChart = new Chart(assetCanvas, {
    type: 'doughnut',
    data: assetData,
    options: {
        fontSize: 5,
        aspectRatio: 0.9,
        borderWidth: 2,
        cutout: 60,
        padding: {
            top: 10,
            right: 10,
            bottom: 10,
            left: 10
        },
        scales: {
        },
        plugins: {
            legend: {
                display: false
            },
            tooltip: {
                bodyFont: {
                    size: 12
                },
                callbacks: {
                    label: function (context) {
                        let label = context.label;
                        let value = context.formattedValue;

                        if (!label)
                            label = 'Unknown'

                        label = splitLongStringToArray(label);

                        let sum = 0;
                        let dataArr = context.chart.data.datasets[0].data;
                        dataArr.map(data => {
                            sum += Number(data);
                        });

                        let percentage = value + '%';
                        // let percentage = (value * 100 / sum).toFixed(2) + '%';
                        label[label.length - 1] = label[label.length - 1] + ": " + percentage;
                        return label;
                    }
                }
            },
        }
    },
});

// Geography Chart
var geographyRepeater = document.querySelectorAll('[data-repeater="geography"] li');
var labels = [];
var data = [];
geographyRepeater.forEach(function (item) {
    var label = item.querySelector('.label').textContent;
    var value = parseFloat(item.querySelector('.percentage').textContent);
    labels.push(label);
    data.push(value);
});

var geographyCanvas = document.getElementById("geography-chart");
var geographyData = {
    labels: labels,
    datasets: [
        {
            data: data,
            backgroundColor: [
                "rgba(0, 51, 102, 1)",     // Darkest Blue - #003366
                "rgba(0, 75, 141, 1)", 
                "rgba(0, 75, 141, 0.9)",
                "rgba(0, 75, 141, 0.8)",
                "rgba(0, 75, 141, 0.6)",
                "rgba(0, 75, 141, 0.5)",
                "rgba(88, 130, 168, .5)",
                "rgba(104, 138, 173, .4)",
                "rgba(116, 149, 184, .3)", // less faded lightest color
                "rgba(144, 169, 191, .2)",
            ]
        }]
};

// label colors
var backgroundColors = geographyData.datasets[0].backgroundColor;
for (var i = 0; i < geographyRepeater.length; i++) {
    var colorValue = backgroundColors[i];
    var spanColorElement = geographyRepeater[i].querySelector("span.color");
    spanColorElement.style.backgroundColor = colorValue;
}

var pieChart = new Chart(geographyCanvas, {
    type: 'doughnut',
    data: geographyData,
    options: {
        aspectRatio: 0.9,
        borderWidth: 2,
        cutout: 60,
        scales: {
        },
        plugins: {
            legend: {
                display: false
            },
            tooltip: {
                bodyFont: {
                    size: 12
                },
                callbacks: {
                    label: function (context) {
                        let label = context.label;
                        let value = context.formattedValue;

                        if (!label)
                            label = 'Unknown'
                        let sum = 0;
                        let dataArr = context.chart.data.datasets[0].data;
                        dataArr.map(data => {
                            sum += Number(data);
                        });
                        label = splitLongStringToArray(label);
                        // let percentage = (value * 100 / sum).toFixed(3);
                        // percentage = percentage.slice(0, -1) + '%';
                        let percentage = value + '%';
                        label[label.length - 1] = label[label.length - 1] + ": " + percentage;
                        return label;
                    }
                }
            },
        }
    },
    // plugins: [ChartDataLabels]
});

// Infrastructure Chart
var infrastructureRepeater = document.querySelectorAll('[data-repeater="infrastructure_type"] li');
var labels = [];
var data = [];
infrastructureRepeater.forEach(function (item) {
    var label = item.querySelector('.label').textContent;
    var value = parseFloat(item.querySelector('.percentage').textContent);
    labels.push(label);
    data.push(value);
});
var infrastructureCanvas = document.getElementById("infrastructure-chart");
var infrastructureData = {
    labels: labels,
    datasets: [
        {
            data: data,
            backgroundColor: [
                "rgba(0, 51, 102, 1)",     // Darkest Blue - #003366
                "rgba(0, 61, 122, 1)",
                "rgba(0, 75, 141, 1)", 
                "rgba(0, 75, 141, 0.9)",
                "rgba(0, 75, 141, 0.8)",
                "rgba(0, 75, 141, 0.7)",
                "rgba(0, 75, 141, 0.6)",
                "rgba(0, 75, 141, 0.5)",
                "rgba(0, 75, 141, 0.4)",
                "rgba(88, 130, 168, .5)",
                "rgba(104, 138, 173, .4)",
                "rgba(116, 149, 184, .3)", // less faded lightest color
                "rgba(144, 169, 191, .2)",
            ]
        }]
};

// label colors
var backgroundColors = infrastructureData.datasets[0].backgroundColor;
for (var i = 0; i < infrastructureRepeater.length; i++) {
    var colorValue = backgroundColors[i];
    var spanColorElement = infrastructureRepeater[i].querySelector("span.color");
    spanColorElement.style.backgroundColor = colorValue;
}

var pieChart = new Chart(infrastructureCanvas, {
    type: 'doughnut',
    data: infrastructureData,
    options: {
        aspectRatio: 0.9,
        borderWidth: 2,
        cutout: 60,
        scales: {
        },
        plugins: {
            legend: {
                display: false
            },
            tooltip: {
                bodyFont: {
                    size: 12
                },
                callbacks: {
                    label: function (context) {
                        let label = context.label;
                        let value = context.formattedValue;

                        if (!label)
                            label = 'Unknown'

                        let sum = 0;
                        let dataArr = context.chart.data.datasets[0].data;
                        dataArr.map(data => {
                            sum += Number(data);
                        });

                        label = splitLongStringToArray(label);
                        // let percentage = (value * 100 / sum).toFixed(3);
                        let percentage = value + '%';
                        label[label.length - 1] = label[label.length - 1] + ": " + percentage;
                        return label;
                    }
                }
            },
        }
    },
    // plugins: [ChartDataLabels]
});
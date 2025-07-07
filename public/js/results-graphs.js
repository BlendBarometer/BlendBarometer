let hasSavedImages = false;

const physicalColor = {
    borderColor: '#36A2EB',
    backgroundColor: 'rgba(54,162,235,0.5)',
};
const onlineColor = {
    borderColor: '#FF6384',
    backgroundColor: 'rgba(255,99,132,0.5)',
}

const lessonLevelGraph = document.getElementById('lessonLevel');

Chart.defaults.font.size = 16;
new Chart(lessonLevelGraph, {
    type: 'radar',
    data: {
        labels: lessonLevelSubcategories.map(c => c.name),
        datasets: [
            {
                label: 'Fysieke leeractiviteiten',
                data: lessonLevelDataPhysical,
                borderColor: physicalColor.borderColor,
                backgroundColor: physicalColor.backgroundColor
            },
            {
                label: 'Online leeractiviteiten',
                data: lessonLevelDataOnline,
                borderColor: onlineColor.borderColor,
                backgroundColor: onlineColor.backgroundColor
            }
        ]
    },
    options: {
        responsive: true,
        scale: {
            r: {
                min: 0,
            }
        },
        animation: {
            onComplete: function () {
                const tooltip = this.tooltip;
                const tooltipActive = tooltip && tooltip.opacity !== 0;

                if (!tooltipActive) {
                    const base64Image = this.toBase64Image();

                    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

                    // Send the image to the backend with the CSRF token
                    fetch('/SaveChart', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                        },
                        body: JSON.stringify({image: base64Image, name: 'radar'})
                    }).then(response => {

                    }).catch(error => {

                    });
                }
            }
        },
        scales: {
            r: {
                pointLabels: {
                    font: {
                        size: 16
                    }
                },
                angleLines: {
                    lineWidth: 2,
                },
                grid: {
                    lineWidth: 2,
                }
            }
        },
        scale: {
            ticks: {
                precision: 0
            }
        },
    }
});

let lessonLevelAriaLabel = ""
lessonLevelAriaLabel = "Algemene grafiek op lesniveau. Opgedeeld in fysieke leeractiviteiten en online leeractiviteiten. ";
lessonLevelAriaLabel += "Scores op gebied van fysieke leeractiviteiten: ";
for (let i = 0; i < lessonLevelSubcategories.length; i++) {
    const label = lessonLevelSubcategories.map(c => c.name)[i];
    const data = lessonLevelDataPhysical[i];
    lessonLevelAriaLabel += label + ": " + data + ". ";
}
lessonLevelAriaLabel += "Scores op gebied van online leeractiviteiten: ";
for (let i = 0; i < lessonLevelSubcategories.length; i++) {
    const label = lessonLevelSubcategories.map(c => c.name)[i];
    const data = lessonLevelDataOnline[i];

    lessonLevelAriaLabel += label + ": " + data + ". ";
}
lessonLevelGraph.ariaLabel = lessonLevelAriaLabel;

const lessonLevelDataAllArray = [];
for (const [_, item] of Object.entries(lessonLevelDataAll)) {
    let section = [];
    for (const [_, item2] of Object.entries(item)) {
        section.push(parseInt(item2));
    }
    lessonLevelDataAllArray.push(section);
}

for (const category of lessonLevelSubcategories) {
    const subCatId = category.id;
    const graph = document.getElementById('physical-' + subCatId);
    const categoryLabels = lessonLevelPhysicalQuestions[subCatId] ? lessonLevelPhysicalQuestions[subCatId] : [];

    let categoryData = [];
    if (lessonLevelDataAll && lessonLevelDataAll[subCatId]) {
        categoryData = Object.values(lessonLevelDataAll[subCatId]).map(Number);
    }

    new Chart(graph, {
        type: 'bar',
        data: {
            labels: categoryLabels,
            datasets: [
                {
                    label: 'Punten gescoord',
                    data: categoryData,
                    backgroundColor: physicalColor.backgroundColor
                }
            ]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    display: false
                },
            },
            scales: {
                y: {
                    suggestedMin: 0,
                    suggestedMax: 2
                },
            },
            scale: {
                ticks: {
                    precision: 0
                }
            },
            animation: {
                onComplete: function () {
                    const tooltip = this.tooltip;
                    const tooltipActive = tooltip && tooltip.opacity !== 0;

                    if (!tooltipActive) {
                        const base64Image = this.toBase64Image();

                        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

                        // Send the image to the backend with the CSRF token
                        fetch('/SaveChart', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': csrfToken,
                            },
                            body: JSON.stringify({image: base64Image, name: 'physical' + category.name})
                        }).then(response => {

                        }).catch(error => {

                        });              
                    }
                }
            }
        }
    });
    let graphAriaLabel = "Fysieke leeractiviteiten - ";
    graphAriaLabel += category.name + ". Scores: ";

    for (let i = 0; i < categoryLabels.length; i++) {
        const label = categoryLabels[i];
        const data = categoryData[i];

        graphAriaLabel += label + ": " + data + ". ";
    }

    graph.ariaLabel = graphAriaLabel;
}

for (const category of lessonLevelOnlineSubcategories) {
    const subCatId = category.id;
    const graph = document.getElementById('online-' + subCatId);

    const categoryLabels = lessonLevelOnlineQuestions[subCatId] ? lessonLevelOnlineQuestions[subCatId] : [];
    let categoryData = [];
    if (lessonLevelDataAll && lessonLevelDataAll[subCatId]) {
        categoryData = Object.values(lessonLevelDataAll[subCatId]).map(Number);
    }

    new Chart(graph, {
        type: 'bar',
        data: {
            labels: categoryLabels,
            datasets: [{
                label: 'Punten gescoord',
                data: categoryData,
                backgroundColor: onlineColor.backgroundColor
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    display: false
                },
            },
            scales: {
                y: {
                    suggestedMin: 0,
                    suggestedMax: 2
                }
            },
            scale: {
                ticks: {
                    precision: 0
                }
            },
            animation: {
                onComplete: function () {
                    const tooltip = this.tooltip;
                    const tooltipActive = tooltip && tooltip.opacity !== 0;

                    if (!tooltipActive) {
                        const base64Image = this.toBase64Image();
                        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                        fetch('/SaveChart', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': csrfToken,
                            },
                            body: JSON.stringify({image: base64Image, name: 'online' + category.name})
                        }).then(response => {

                        }).catch(error => {

                        });
                    }
                }
            }
        }
    });
    let graphAriaLabel = "Online leeractiviteiten - ";
    graphAriaLabel += category.name + ". Scores: ";

    for (let i = 0; i < categoryLabels.length; i++) {
        const label = categoryLabels[i];
        const data = categoryData[i];

        graphAriaLabel += label + ": " + data + ". ";
    }

    graph.ariaLabel = graphAriaLabel;
}

const moduleLevelCategoriesGraph = document.getElementById('moduleLevelCategoriesGraph');

let outerLabels = [];
let outerData = [];
let outerColors = ['rgb(221, 221, 221)', 'rgb(235, 235, 235)', 'rgb(248, 248, 248)'];
for (const [key, value] of Object.entries(moduleLevelCategories)) {
    outerLabels.push(key);
    outerData.push(value.length);
}
let removeCount = 0;

let j = 0;
const moduleLevelDataArray = {};
for (const [i, [_, item]] of Object.entries(moduleLevelData).entries()) {

    for (const [_, item2] of Object.entries(item)) {
            moduleLevelDataArray[j] = parseInt(item2);
        j++;
    }
}

let outerLabelsToRemove = [];
let outerDataToKeep = [];
for (i = 0; i < outerData.length; i++) {
    if (outerData[i] == 0) {
        outerLabelsToRemove.push(outerLabels[i]);
    } else {
        outerDataToKeep.push(outerData[i]);
    }
}
outerData = outerDataToKeep;

for (label of outerLabelsToRemove) {
    outerLabels.splice(outerLabels.indexOf(label), 1);
}

if (!outerData.length) {
    const noDataMessage = document.createElement('p');
    noDataMessage.innerText = 'Geen onderdelen van toepassing.';
    noDataMessage.className = 'me-auto';

    const graphImage = document.querySelector('#moduleLevelGraphImage');
    graphImage.className = 'd-none';

    graphImage.parentElement.append(noDataMessage);
}

const moduleLevelDataGraph = document.getElementById('moduleLevelDataGraph');

innerLabels = [];
for ([key, value] of Object.entries(moduleLevelDataArray)) {
    innerLabels.push(parseInt(key) + 1 + ". " + moduleLevelLabels[key]);
}

innerData = [];
innerColors = [];
for ([key, value] of Object.entries(moduleLevelDataArray)) {
    innerData.push(1);
    let color;
    switch (value) {
    case 1:
        color = legendColors[0];
        break;
    case 2:
        color = legendColors[1];
        break;
    case 3:
        color = legendColors[2];
        break;
    case 4:
        color = legendColors[3];
        break;
    case 0:
        color = legendColors[4];
        break;
}
    innerColors.push(color);
}

new Chart(moduleLevelCategoriesGraph, {
    type: 'doughnut',
    data: {
        labels: outerLabels,
        datasets: [{
            data: outerData,
            backgroundColor: outerColors,
        }]
    },
    plugins: [ChartDataLabels],
    options: {
        responsive: true,
        plugins: {
            legend: {
                display: false
            },
            datalabels: {
                color: 'black',
                labels: {
                    title: {
                        font: {
                            weight: 'bold'
                        }
                    },
                },
                anchor: "center", //start, center, end
                rotation: function (ctx) {
                    const valuesBefore = ctx.dataset.data.slice(0, ctx.dataIndex).reduce((a, b) => a + b, 0);
                    const sum = ctx.dataset.data.reduce((a, b) => a + b, 0);
                    const rotation = (valuesBefore + ctx.dataset.data[ctx.dataIndex] / 2) / sum * 360;
                    const pureDegrees = (rotation + 360) % 360;
                    return (pureDegrees >= 90 && pureDegrees < 270) ? pureDegrees + 180 : pureDegrees;
                },
                formatter: function (value, context) {
                    return context.chart.data.labels[context.dataIndex];
                }
            },
            tooltip: {
                enabled: false
            }
        },
        cutout: '75%',
        radius: '76%',
        animation: {
            onComplete: function () {
                const tooltip = this.tooltip;
                const tooltipActive = tooltip && tooltip.opacity !== 0;

                if (!tooltipActive) {
                    const base64Image = this.toBase64Image();
                    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                    fetch('/SaveChart', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                        },
                        body: JSON.stringify({ image: base64Image, name: 'wheelOutside' })
                    }).then(response => {
                    }).catch(error => {
                    });
                }
            }
        },
    }
});

new Chart(moduleLevelDataGraph, {
    type: 'pie',
    data: {
        labels: innerLabels,
        datasets: [{
            data: innerData,
            backgroundColor: innerColors,
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                display: false
            },
            datalabels: {
                color: 'white',
                labels: {
                    title: {
                        font: {
                            weight: 'bold'
                        }
                    },
                },
                anchor: "center", //start, center, end
                rotation: function (ctx) {
                    const valuesBefore = ctx.dataset.data.slice(0, ctx.dataIndex).reduce((a, b) => a + b, 0);
                    const sum = ctx.dataset.data.reduce((a, b) => a + b, 0);
                    const rotation = (valuesBefore + ctx.dataset.data[ctx.dataIndex] / 2) / sum * 360;
                    return rotation < 180 ? rotation - 90 : rotation + 90;
                },
                formatter: function (value, context) {
                    return context.chart.data.labels[context.dataIndex];
                }
            },
            tooltip: {
                enabled: false,
                position: 'nearest',
                external: externalTooltipHandler
            },
        },
        radius: '60%',
        animation: {
            onComplete: function () {
                const tooltip = this.tooltip;
                const tooltipActive = tooltip && tooltip.opacity !== 0;

                if (!tooltipActive) {
                    const base64Image = this.toBase64Image();

                    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                    // Send the image to the backend with the CSRF token
                    fetch('/SaveChart', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                        },
                        body: JSON.stringify({image: base64Image, name: 'wheelInside'})
                    }).then(response => {

                    }).catch(error => {

                    });
                }
            }
        }
    },
    plugins: [ChartDataLabels],
});

hasSavedImages = true;

/**
 * Student Activities Analytics & Visualizations
 * Chart.js Integration for Timeline, Distribution, and Department Comparisons
 */

document.addEventListener("DOMContentLoaded", function() {
    if (typeof Chart === 'undefined') return;

    let stats = window.SA_STATS;
    if (!stats) {
        const dataEl = document.getElementById('sa-stats-data');
        if (dataEl) {
            try { stats = JSON.parse(dataEl.textContent); } catch (e) { stats = {}; }
        }
    }
    stats = stats || {};

    Chart.defaults.font.family = "'Inter', sans-serif";
    Chart.defaults.color = '#718096';
    const palette = ['#1A365D', '#2B6CB0', '#2F855A', '#C05621', '#805AD5', '#319795', '#D69E2E', '#E53E3E'];

    // 1. Timeline Chart
    const tlData = stats.timeline || {};
    const tlCanvas = document.getElementById('timelineChart');
    if (tlCanvas) {
        const ctxTL = tlCanvas.getContext('2d');
        if (ctxTL) {
            new Chart(ctxTL, {
                type: 'line',
                data: {
                    labels: Object.keys(tlData).map(d => {
                        const parts = d.split('-');
                        return new Date(parts[0], parts[1] - 1).toLocaleString('default', { month: 'short', year: 'numeric' });
                    }),
                    datasets: [{
                        label: 'Student Participants',
                        data: Object.values(tlData),
                        borderColor: '#2B6CB0',
                        backgroundColor: 'rgba(43, 108, 176, 0.1)',
                        fill: true,
                        tension: 0.3,
                        pointRadius: 4,
                        pointBackgroundColor: '#FFFFFF',
                        pointBorderColor: '#2B6CB0',
                        pointBorderWidth: 2,
                        borderWidth: 2
                    }]
                },
                options: { 
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: { 
                        x: { grid: { display: false, drawBorder: false } },
                        y: { grid: { borderDash: [4, 4], color: '#E2E8F0', drawBorder: false }, beginAtZero: true, ticks: { precision: 0 } } 
                    }
                }
            });
        }
    }

    // 2. Category Doughnut Chart
    const catData = stats.category_dist || {};
    const catEl = document.getElementById('categoryChart');
    if (catEl) {
        new Chart(catEl, {
            type: 'doughnut',
            data: {
                labels: Object.keys(catData),
                datasets: [{
                    data: Object.values(catData),
                    backgroundColor: palette,
                    borderWidth: 0,
                    hoverOffset: 4
                }]
            },
            options: { 
                responsive: true,
                maintainAspectRatio: false,
                cutout: '72%',
                plugins: { legend: { position: 'bottom', labels: { boxWidth: 10, usePointStyle: true, font: { size: 12 } } } }
            }
        });
    }

    // 3. Event Scope Doughnut Chart
    const scopeData = stats.scope_dist || {};
    const scopeEl = document.getElementById('scopeChart');
    if (scopeEl) {
        new Chart(scopeEl, {
            type: 'doughnut',
            data: {
                labels: Object.keys(scopeData),
                datasets: [{
                    data: Object.values(scopeData),
                    backgroundColor: ['#4F46E5', '#0284C7', '#10B981'],
                    borderWidth: 0,
                    hoverOffset: 4
                }]
            },
            options: { 
                responsive: true,
                maintainAspectRatio: false,
                cutout: '72%',
                plugins: { legend: { position: 'bottom', labels: { boxWidth: 10, usePointStyle: true, font: { size: 12 } } } }
            }
        });
    }

    // 4. Delivery Mode Doughnut
    const modeData = stats.mode_dist || {};
    const modeEl = document.getElementById('modeChart');
    if (modeEl) {
        new Chart(modeEl, {
            type: 'doughnut',
            data: {
                labels: Object.keys(modeData),
                datasets: [{
                    data: Object.values(modeData),
                    backgroundColor: ['#1A365D', '#2B6CB0', '#2F855A', '#D69E2E'],
                    borderWidth: 0,
                    hoverOffset: 4
                }]
            },
            options: { 
                responsive: true,
                maintainAspectRatio: false,
                cutout: '72%',
                plugins: { legend: { position: 'bottom', labels: { boxWidth: 10, usePointStyle: true, font: { size: 12 } } } }
            }
        });
    }

    // 5. Topic Bar Chart
    const topicData = stats.topic_dist || {};
    const topicEl = document.getElementById('topicRadarChart');
    if (topicEl) {
        new Chart(topicEl, {
            type: 'bar',
            data: {
                labels: Object.keys(topicData),
                datasets: [{
                    label: 'Events Count',
                    data: Object.values(topicData),
                    backgroundColor: '#2B6CB0',
                    borderRadius: 4,
                    borderSkipped: false
                }]
            },
            options: { 
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: { 
                    x: { grid: { borderDash: [4, 4], color: '#E2E8F0' }, beginAtZero: true, ticks: { stepSize: 1, precision: 0 } },
                    y: { grid: { display: false } }
                }
            }
        });
    }

    // 6. Department Comparison Bar Chart
    const deptData = stats.dept_compare || {};
    const deptEl = document.getElementById('deptCompareChart');
    if (deptEl) {
        const deptLabels = Object.keys(deptData);
        const deptEvents = deptLabels.map(dept => deptData[dept].events);
        const deptParticipants = deptLabels.map(dept => deptData[dept].participants);

        new Chart(deptEl, {
            type: 'bar',
            data: {
                labels: deptLabels,
                datasets: [
                    {
                        label: 'Departmental Events',
                        data: deptEvents,
                        backgroundColor: '#4F46E5',
                        borderRadius: 4,
                        borderSkipped: false
                    },
                    {
                        label: 'Student Participants',
                        data: deptParticipants,
                        backgroundColor: '#10B981',
                        borderRadius: 4,
                        borderSkipped: false
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { position: 'top', labels: { usePointStyle: true } } },
                scales: { 
                    x: { grid: { display: false } },
                    y: { grid: { borderDash: [4, 4], color: '#E2E8F0' }, beginAtZero: true, ticks: { precision: 0 } } 
                }
            }
        });
    }
});

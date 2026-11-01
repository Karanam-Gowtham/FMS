    const year = window.NBA_CONFIG ? window.NBA_CONFIG.year : '';
    const initialData = window.NBA_CONFIG ? (window.NBA_CONFIG.initialData || {}) : {};
    let levelData = {};
    let currentLevel = '';

    function initData() {
        levelData = initialData.levelData || {};
        const defaultDept = 'dept_' + (window.NBA_CONFIG ? window.NBA_CONFIG.deptId : '');
        if (!levelData[defaultDept]) {
            levelData[defaultDept] = { table4a: {}, t4711: [], t4712: {caym1: [], caym2: [], caym3: []}, t4721: {caym1: [], caym2: [], caym3: []}, t4731: {caym1: [], caym2: [], caym3: []}, t4741: {caym1: [], caym2: [], caym3: []} };
        }
        switchLevel();
    }

    function changeDepartment() {
        const select = document.getElementById('level-select');
        const newDeptId = select.value.replace('dept_', '');
        if (newDeptId != (window.NBA_CONFIG ? window.NBA_CONFIG.deptId : '')) {
            window.location.href = '?route=nba/criterion&id=4&year=' + encodeURIComponent(window.NBA_CONFIG ? window.NBA_CONFIG.year : '') + '&dept_id=' + encodeURIComponent(newDeptId);
        }
    }

    function switchLevel() {
        const select = document.getElementById('level-select');
        const rawVal = select ? select.value : (window.NBA_CONFIG ? window.NBA_CONFIG.deptId : '');
        currentLevel = rawVal.startsWith('dept_') ? rawVal : ('dept_' + rawVal);
        
        if (!levelData[currentLevel]) {
            levelData[currentLevel] = { table4a: {}, t4711: [], t4712: {caym1: [], caym2: [], caym3: []}, t4721: {caym1: [], caym2: [], caym3: []}, t4731: {caym1: [], caym2: [], caym3: []}, t4741: {caym1: [], caym2: [], caym3: []} };
        }
        if (!levelData[currentLevel].table4a) levelData[currentLevel].table4a = {};
        if (!levelData[currentLevel].t4711) levelData[currentLevel].t4711 = [];
        ['t4712', 't4721', 't4731', 't4741'].forEach(k => {
            if (!levelData[currentLevel][k]) levelData[currentLevel][k] = {caym1: [], caym2: [], caym3: []};
        });

        // Populate Table 4A, 4B, and 4C
        const cols = ['cay', 'caym1', 'caym2', 'caym3', 'caym4', 'caym5', 'caym6'];
        const keys4a = ['N', 'N1', 'N2', 'N3', 'N4'];
        const keys4b = ['N52', 'N53', 'N54', 'N61', 'N62', 'N63'];
        
        keys4a.forEach(k => {
            cols.forEach(c => {
                const el = document.getElementById(`t4a_${k}_${c}`);
                if (el) el.value = levelData[currentLevel].table4a[`${k}_${c}`] || '';
            });
        });
        keys4b.forEach(k => {
            cols.forEach(c => {
                const el = document.getElementById(`t4b_${k}_${c}`);
                if (el) el.value = levelData[currentLevel].table4a[`${k}_${c}`] || '';
            });
        });
        cols.forEach(r => {
            for(let i=1; i<=4; i++) {
                const el = document.getElementById(`t4c_${r}_year${i}`);
                if (el) el.value = levelData[currentLevel].table4a[`t4c_${r}_year${i}`] || '';
            }
        });

        // Load 4.3, 4.4, 4.5
        ['t431', 't441', 't451'].forEach(prefix => {
            ['caym1', 'caym2', 'caym3'].forEach(c => {
                ['X', 'Y', 'Z'].forEach(k => {
                    const el = document.getElementById(`${prefix}_${k}_${c}`);
                    if (el) el.value = levelData[currentLevel].table4a[`${prefix}_${k}_${c}`] || '';
                });
            });
        });

        // Load 4.6
        ['caym4', 'caym5', 'caym6'].forEach(c => {
            ['FS', 'X', 'Y', 'Z'].forEach(k => {
                const el = document.getElementById(`t461_${k}_${c}`);
                if (el) el.value = levelData[currentLevel].table4a[`t461_${k}_${c}`] || '';
            });
        });

        calculateAllTotals();
        renderT4711();
        renderT4712();
        renderT4721();
        renderT4731();
        renderT4741();
    }

    function saveCurrentLevel() {
        if (!levelData[currentLevel]) return;
        if (!levelData[currentLevel].table4a) levelData[currentLevel].table4a = {};
        
        const cols = ['cay', 'caym1', 'caym2', 'caym3', 'caym4', 'caym5', 'caym6'];
        const keys4a = ['N', 'N1', 'N2', 'N3', 'N4'];
        const keys4b = ['N52', 'N53', 'N54', 'N61', 'N62', 'N63'];
        
        keys4a.forEach(k => {
            cols.forEach(c => {
                const el = document.getElementById(`t4a_${k}_${c}`);
                if (el) levelData[currentLevel].table4a[`${k}_${c}`] = el.value;
            });
        });
        keys4b.forEach(k => {
            cols.forEach(c => {
                const el = document.getElementById(`t4b_${k}_${c}`);
                if (el) levelData[currentLevel].table4a[`${k}_${c}`] = el.value;
            });
        });
        cols.forEach(r => {
            for(let i=1; i<=4; i++) {
                const el = document.getElementById(`t4c_${r}_year${i}`);
                if (el) levelData[currentLevel].table4a[`t4c_${r}_year${i}`] = el.value;
            }
        });

        ['t431', 't441', 't451'].forEach(prefix => {
            ['caym1', 'caym2', 'caym3'].forEach(c => {
                ['X', 'Y', 'Z'].forEach(k => {
                    const el = document.getElementById(`${prefix}_${k}_${c}`);
                    if (el) levelData[currentLevel].table4a[`${prefix}_${k}_${c}`] = el.value;
                });
            });
        });

        ['caym4', 'caym5', 'caym6'].forEach(c => {
            ['FS', 'X', 'Y', 'Z'].forEach(k => {
                const el = document.getElementById(`t461_${k}_${c}`);
                if (el) levelData[currentLevel].table4a[`t461_${k}_${c}`] = el.value;
            });
        });
    }

    function calculateAllTotals() {
        calculateT4aTotals();
        calculateT4bTotals();
        calculateT4cTotals();
        calculateT411();
        calculateT421();
        calculateAPI('t431');
        calculateAPI('t441');
        calculateAPI('t451');
        calculateT461();
    }

    function calculateT4aTotals() {
        ['cay', 'caym1', 'caym2', 'caym3', 'caym4', 'caym5', 'caym6'].forEach(c => {
            const n1 = parseInt(document.getElementById(`t4a_N1_${c}`)?.value) || 0;
            const n2 = parseInt(document.getElementById(`t4a_N2_${c}`)?.value) || 0;
            const n3 = parseInt(document.getElementById(`t4a_N3_${c}`)?.value) || 0;
            const n4 = parseInt(document.getElementById(`t4a_N4_${c}`)?.value) || 0;
            const el = document.getElementById(`t4a_total_${c}`);
            if (el) el.innerText = (n1 + n2 + n3 + n4);
        });
    }

    function calculateT4bTotals() {
        ['cay', 'caym1', 'caym2', 'caym3', 'caym4', 'caym5', 'caym6'].forEach(c => {
            const n52 = parseInt(document.getElementById(`t4b_N52_${c}`)?.value) || 0;
            const n53 = parseInt(document.getElementById(`t4b_N53_${c}`)?.value) || 0;
            const n54 = parseInt(document.getElementById(`t4b_N54_${c}`)?.value) || 0;
            const totalN5 = document.getElementById(`t4b_total_N5_${c}`);
            if (totalN5) totalN5.innerText = (n52 + n53 + n54);
            
            const n61 = parseInt(document.getElementById(`t4b_N61_${c}`)?.value) || 0;
            const n62 = parseInt(document.getElementById(`t4b_N62_${c}`)?.value) || 0;
            const n63 = parseInt(document.getElementById(`t4b_N63_${c}`)?.value) || 0;
            const totalN6 = document.getElementById(`t4b_total_N6_${c}`);
            if (totalN6) totalN6.innerText = (n61 + n62 + n63);
        });
    }

    function calculateT4cTotals() {
        ['cay', 'caym1', 'caym2', 'caym3', 'caym4', 'caym5', 'caym6'].forEach(c => {
            const n1 = parseInt(document.getElementById(`t4a_N1_${c}`)?.value) || 0;
            const n2 = parseInt(document.getElementById(`t4a_N2_${c}`)?.value) || 0;
            const n3 = parseInt(document.getElementById(`t4a_N3_${c}`)?.value) || 0;
            const n4 = parseInt(document.getElementById(`t4a_N4_${c}`)?.value) || 0;
            const n5 = (parseInt(document.getElementById(`t4b_N52_${c}`)?.value) || 0) + (parseInt(document.getElementById(`t4b_N53_${c}`)?.value) || 0) + (parseInt(document.getElementById(`t4b_N54_${c}`)?.value) || 0);
            const n6 = (parseInt(document.getElementById(`t4b_N61_${c}`)?.value) || 0) + (parseInt(document.getElementById(`t4b_N62_${c}`)?.value) || 0) + (parseInt(document.getElementById(`t4b_N63_${c}`)?.value) || 0);
            const total = Math.max(0, n1 + n2 + n3 + n4 + n5 - n6);
            const el = document.getElementById(`t4c_total_${c}`);
            if (el) el.innerText = total;
        });
    }

    function calculateT411() {
        let totalER = 0, validERs = 0;
        ['cay', 'caym1', 'caym2'].forEach(c => {
            const n = parseInt(document.getElementById(`t4a_N_${c}`)?.value) || 0;
            const n1 = parseInt(document.getElementById(`t4a_N1_${c}`)?.value) || 0;
            const n4 = parseInt(document.getElementById(`t4a_N4_${c}`)?.value) || 0;
            
            const cellN = document.getElementById(`t411_N_${c}`);
            const cellN1 = document.getElementById(`t411_N1_${c}`);
            const cellN4 = document.getElementById(`t411_N4_${c}`);
            const cellER = document.getElementById(`t411_ER_${c}`);
            
            if (cellN) cellN.innerText = n;
            if (cellN1) cellN1.innerText = n1;
            if (cellN4) cellN4.innerText = n4;
            
            if (n > 0) {
                const er = (n1 + n4) / n;
                if (cellER) cellER.innerText = er.toFixed(2);
                totalER += er;
                validERs++;
            } else if (cellER) {
                cellER.innerText = '0.00';
            }
        });
        const cellAvg = document.getElementById(`t411_Avg_ER`);
        if (cellAvg) cellAvg.innerText = validERs > 0 ? (totalER / 3).toFixed(2) : '0.00';
    }

    function calculateT421() {
        let totalSR = 0, validSRs = 0;
        ['caym4', 'caym5', 'caym6'].forEach(c => {
            const n = parseInt(document.getElementById(`t4a_N_${c}`)?.value) || 0;
            const n1 = parseInt(document.getElementById(`t4a_N1_${c}`)?.value) || 0;
            const n2 = parseInt(document.getElementById(`t4a_N2_${c}`)?.value) || 0;
            const n3 = parseInt(document.getElementById(`t4a_N3_${c}`)?.value) || 0;
            const n5 = (parseInt(document.getElementById(`t4b_N52_${c}`)?.value) || 0) + (parseInt(document.getElementById(`t4b_N53_${c}`)?.value) || 0) + (parseInt(document.getElementById(`t4b_N54_${c}`)?.value) || 0);
            const n6 = (parseInt(document.getElementById(`t4b_N61_${c}`)?.value) || 0) + (parseInt(document.getElementById(`t4b_N62_${c}`)?.value) || 0) + (parseInt(document.getElementById(`t4b_N63_${c}`)?.value) || 0);
            
            let a = Math.max(0, n1 + n2 + n3 + n5 - n6);
            if (a < (n + n2)) {
                a = n + Math.min(n2, n * 0.1);
            }
            
            let b = 0;
            for (let i = 1; i <= 4; i++) {
                b += parseInt(document.getElementById(`t4c_${c}_year${i}`)?.value) || 0;
            }
            
            const cellA = document.getElementById(`t421_A_${c}`);
            const cellB = document.getElementById(`t421_B_${c}`);
            const cellSR = document.getElementById(`t421_SR_${c}`);
            if (cellA) cellA.innerText = Math.round(a);
            if (cellB) cellB.innerText = b;
            
            if (a > 0) {
                const sr = (b / a) * 100;
                if (cellSR) cellSR.innerText = sr.toFixed(2);
                totalSR += sr;
                validSRs++;
            }
        });
        const cellAvg = document.getElementById(`t421_Avg_SR`);
        if (cellAvg) cellAvg.innerText = validSRs > 0 ? (totalSR / 3).toFixed(2) : '0.00';
    }

    function calculateAPI(prefix) {
        let totalAPI = 0, validAPIs = 0;
        ['caym1', 'caym2', 'caym3'].forEach(c => {
            const x = parseFloat(document.getElementById(`${prefix}_X_${c}`)?.value) || 0;
            const y = parseInt(document.getElementById(`${prefix}_Y_${c}`)?.value) || 0;
            const z = parseInt(document.getElementById(`${prefix}_Z_${c}`)?.value) || 0;
            const cellAPI = document.getElementById(`${prefix}_API_${c}`);
            if (z > 0) {
                const api = x * (y / z);
                if (cellAPI) cellAPI.innerText = api.toFixed(2);
                totalAPI += api;
                validAPIs++;
            } else if (cellAPI) {
                cellAPI.innerText = '0.00';
            }
        });
        const cellAvg = document.getElementById(`${prefix}_Avg_API`);
        if (cellAvg) cellAvg.innerText = validAPIs > 0 ? (totalAPI / 3).toFixed(2) : '0.00';
    }

    function calculateT461() {
        let totalP = 0, validPs = 0;
        ['caym4', 'caym5', 'caym6'].forEach(c => {
            const fs = parseInt(document.getElementById(`t461_FS_${c}`)?.value) || 0;
            const x = parseInt(document.getElementById(`t461_X_${c}`)?.value) || 0;
            const y = parseInt(document.getElementById(`t461_Y_${c}`)?.value) || 0;
            const z = parseInt(document.getElementById(`t461_Z_${c}`)?.value) || 0;
            const sum = x + y + z;
            const cellSum = document.getElementById(`t461_Sum_${c}`);
            const cellP = document.getElementById(`t461_P_${c}`);
            if (cellSum) cellSum.innerText = sum;
            if (fs > 0) {
                const p = sum / fs;
                if (cellP) cellP.innerText = p.toFixed(2);
                totalP += p;
                validPs++;
            } else if (cellP) {
                cellP.innerText = '0.00';
            }
        });
        const cellAvg = document.getElementById(`t461_Avg_P`);
        if (cellAvg) cellAvg.innerText = validPs > 0 ? (totalP / 3).toFixed(2) : '0.00';
    }

    // --- Dynamic Row Management for 4.7 ---
    function renderT4711() {
        const tbody = document.getElementById('t4711-tbody');
        if (!tbody) return;
        tbody.innerHTML = '';
        (levelData[currentLevel].t4711 || []).forEach((row, index) => {
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td class="cell-center">${index + 1}</td>
                <td><input type="text" value="${row.name || ''}" onchange="levelData[currentLevel].t4711[${index}].name = this.value; saveData(true);"></td>
                <td class="cell-center"><button type="button" onclick="levelData[currentLevel].t4711.splice(${index}, 1); renderT4711(); saveData(true);" style="padding: 4px 8px; background: #dc3545; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 0.8rem;">Remove</button></td>
            `;
            tbody.appendChild(tr);
        });
    }

    function addT4711Row() {
        if (!levelData[currentLevel].t4711) levelData[currentLevel].t4711 = [];
        levelData[currentLevel].t4711.push({ name: '' });
        renderT4711();
        saveData(true);
    }

    function renderT4712() {
        ['caym1', 'caym2', 'caym3'].forEach(year => {
            const tbody = document.getElementById(`t4712-${year}-tbody`);
            if (!tbody) return;
            tbody.innerHTML = '';
            (levelData[currentLevel].t4712?.[year] || []).forEach((row, index) => {
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td class="cell-center">${index + 1}</td>
                    <td><input type="text" value="${row.society || ''}" onchange="levelData[currentLevel].t4712['${year}'][${index}].society = this.value; saveData(true);"></td>
                    <td><input type="text" value="${row.event || ''}" onchange="levelData[currentLevel].t4712['${year}'][${index}].event = this.value; saveData(true);"></td>
                    <td><select onchange="levelData[currentLevel].t4712['${year}'][${index}].level = this.value; saveData(true);" style="width: 100%; border: 1px solid #ced4da; padding: 5px;"><option value="National" ${row.level === 'National' ? 'selected' : ''}>National</option><option value="International" ${row.level === 'International' ? 'selected' : ''}>International</option></select></td>
                    <td><input type="date" value="${row.date || ''}" onchange="levelData[currentLevel].t4712['${year}'][${index}].date = this.value; saveData(true);"></td>
                    <td class="cell-center"><button type="button" onclick="levelData[currentLevel].t4712['${year}'].splice(${index}, 1); renderT4712(); saveData(true);" style="padding: 4px 8px; background: #dc3545; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 0.8rem;">Remove</button></td>
                `;
                tbody.appendChild(tr);
            });
        });
    }

    function addT4712Row(year) {
        if (!levelData[currentLevel].t4712) levelData[currentLevel].t4712 = { caym1: [], caym2: [], caym3: [] };
        if (!levelData[currentLevel].t4712[year]) levelData[currentLevel].t4712[year] = [];
        levelData[currentLevel].t4712[year].push({ society: '', event: '', level: 'National', date: '' });
        renderT4712();
        saveData(true);
    }

    function renderT4721() {
        ['caym1', 'caym2', 'caym3'].forEach(year => {
            const tbody = document.getElementById(`t4721-${year}-tbody`);
            if (!tbody) return;
            tbody.innerHTML = '';
            (levelData[currentLevel].t4721?.[year] || []).forEach((row, index) => {
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td class="cell-center">${index + 1}</td>
                    <td><input type="text" value="${row.student_name || ''}" onchange="levelData[currentLevel].t4721['${year}'][${index}].student_name = this.value; saveData(true);"></td>
                    <td><input type="text" value="${row.event_name || ''}" onchange="levelData[currentLevel].t4721['${year}'][${index}].event_name = this.value; saveData(true);"></td>
                    <td><select onchange="levelData[currentLevel].t4721['${year}'][${index}].level = this.value; saveData(true);" style="width: 100%; border: 1px solid #ced4da; padding: 5px;"><option value="State" ${row.level === 'State' ? 'selected' : ''}>State</option><option value="National" ${row.level === 'National' ? 'selected' : ''}>National</option><option value="International" ${row.level === 'International' ? 'selected' : ''}>International</option></select></td>
                    <td><input type="date" value="${row.date || ''}" onchange="levelData[currentLevel].t4721['${year}'][${index}].date = this.value; saveData(true);"></td>
                    <td><input type="text" value="${row.award || ''}" onchange="levelData[currentLevel].t4721['${year}'][${index}].award = this.value; saveData(true);"></td>
                    <td class="cell-center"><button type="button" onclick="levelData[currentLevel].t4721['${year}'].splice(${index}, 1); renderT4721(); saveData(true);" style="padding: 4px 8px; background: #dc3545; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 0.8rem;">Remove</button></td>
                `;
                tbody.appendChild(tr);
            });
        });
    }

    function addT4721Row(year) {
        if (!levelData[currentLevel].t4721) levelData[currentLevel].t4721 = { caym1: [], caym2: [], caym3: [] };
        if (!levelData[currentLevel].t4721[year]) levelData[currentLevel].t4721[year] = [];
        levelData[currentLevel].t4721[year].push({ student_name: '', event_name: '', level: 'State', date: '', award: '' });
        renderT4721();
        saveData(true);
    }

    function renderT4731() {
        ['caym1', 'caym2', 'caym3'].forEach(year => {
            const tbody = document.getElementById(`t4731-${year}-tbody`);
            if (!tbody) return;
            tbody.innerHTML = '';
            (levelData[currentLevel].t4731?.[year] || []).forEach((row, index) => {
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td class="cell-center">${index + 1}</td>
                    <td><input type="text" value="${row.journal_name || ''}" onchange="levelData[currentLevel].t4731['${year}'][${index}].journal_name = this.value; saveData(true);"></td>
                    <td><input type="text" value="${row.editor || ''}" onchange="levelData[currentLevel].t4731['${year}'][${index}].editor = this.value; saveData(true);"></td>
                    <td><input type="text" value="${row.student_semester || ''}" onchange="levelData[currentLevel].t4731['${year}'][${index}].student_semester = this.value; saveData(true);"></td>
                    <td><input type="number" value="${row.issues || ''}" onchange="levelData[currentLevel].t4731['${year}'][${index}].issues = this.value; saveData(true);"></td>
                    <td><select onchange="levelData[currentLevel].t4731['${year}'][${index}].copy_type = this.value; saveData(true);" style="width: 100%; border: 1px solid #ced4da; padding: 5px;"><option value="Hard Copy" ${row.copy_type === 'Hard Copy' ? 'selected' : ''}>Hard Copy</option><option value="Soft Copy" ${row.copy_type === 'Soft Copy' ? 'selected' : ''}>Soft Copy</option><option value="Both" ${row.copy_type === 'Both' ? 'selected' : ''}>Both</option></select></td>
                    <td class="cell-center"><button type="button" onclick="levelData[currentLevel].t4731['${year}'].splice(${index}, 1); renderT4731(); saveData(true);" style="padding: 4px 8px; background: #dc3545; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 0.8rem;">Remove</button></td>
                `;
                tbody.appendChild(tr);
            });
        });
    }

    function addT4731Row(year) {
        if (!levelData[currentLevel].t4731) levelData[currentLevel].t4731 = { caym1: [], caym2: [], caym3: [] };
        if (!levelData[currentLevel].t4731[year]) levelData[currentLevel].t4731[year] = [];
        levelData[currentLevel].t4731[year].push({ journal_name: '', editor: '', student_semester: '', issues: '', copy_type: 'Hard Copy' });
        renderT4731();
        saveData(true);
    }

    function renderT4741() {
        ['caym1', 'caym2', 'caym3'].forEach(year => {
            const tbody = document.getElementById(`t4741-${year}-tbody`);
            if (!tbody) return;
            tbody.innerHTML = '';
            (levelData[currentLevel].t4741?.[year] || []).forEach((row, index) => {
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td class="cell-center">${index + 1}</td>
                    <td><input type="text" value="${row.student_semester || ''}" onchange="levelData[currentLevel].t4741['${year}'][${index}].student_semester = this.value; saveData(true);"></td>
                    <td><input type="text" value="${row.publisher || ''}" onchange="levelData[currentLevel].t4741['${year}'][${index}].publisher = this.value; saveData(true);"></td>
                    <td><input type="text" value="${row.journal || ''}" onchange="levelData[currentLevel].t4741['${year}'][${index}].journal = this.value; saveData(true);"></td>
                    <td><input type="text" value="${row.volume_issue || ''}" onchange="levelData[currentLevel].t4741['${year}'][${index}].volume_issue = this.value; saveData(true);"></td>
                    <td><input type="text" value="${row.award || ''}" onchange="levelData[currentLevel].t4741['${year}'][${index}].award = this.value; saveData(true);"></td>
                    <td class="cell-center"><button type="button" onclick="levelData[currentLevel].t4741['${year}'].splice(${index}, 1); renderT4741(); saveData(true);" style="padding: 4px 8px; background: #dc3545; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 0.8rem;">Remove</button></td>
                `;
                tbody.appendChild(tr);
            });
        });
    }

    function addT4741Row(year) {
        if (!levelData[currentLevel].t4741) levelData[currentLevel].t4741 = { caym1: [], caym2: [], caym3: [] };
        if (!levelData[currentLevel].t4741[year]) levelData[currentLevel].t4741[year] = [];
        levelData[currentLevel].t4741[year].push({ student_semester: '', publisher: '', journal: '', volume_issue: '', award: '' });
        renderT4741();
        saveData(true);
    }

    async function saveData(silent = false) {
        saveCurrentLevel();
        return await saveAccreditationCriterion(4, { levelData: levelData }, silent);
    }

    document.addEventListener('DOMContentLoaded', initData);
</script>

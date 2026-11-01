/**
 * NBA Criterion 5 Client Controller
 * Faculty Information & SFR / FQI Automation
 */

const nbaConfig = window.NBA_CONFIG || {};
let levelData = {};
let currentLevel = 'dept_' + (nbaConfig.deptId || '');

let initialData = {};
if (nbaConfig.initialData && !Array.isArray(nbaConfig.initialData)) {
    initialData = nbaConfig.initialData;
}

function changeDepartment() {
    const select = document.getElementById('level-select');
    if (!select) return;
    const year = encodeURIComponent(nbaConfig.year || '');
    window.location.href = '?route=nba/criterion&id=5&year=' + year + '&dept_id=' + select.value;
}

const simpleInputs = [
    't51_DS_cay', 't51_AS_cay', 't51_DF_cay', 't51_AF_cay', 't51_FF_cay',
    't51_DS_caym1', 't51_AS_caym1', 't51_DF_caym1', 't51_AF_caym1', 't51_FF_caym1',
    't51_DS_caym2', 't51_AS_caym2', 't51_DF_caym2', 't51_AF_caym2', 't51_FF_caym2',
    
    't52_X_cay', 't52_Y_cay', 't52_X_caym1', 't52_Y_caym1', 't52_X_caym2', 't52_Y_caym2',
    
    't53_prof_avl_CAY', 't53_asso_avl_CAY', 't53_asst_avl_CAY',
    't53_prof_avl_CAYm1', 't53_asso_avl_CAYm1', 't53_asst_avl_CAYm1',
    't53_prof_avl_CAYm2', 't53_asso_avl_CAYm2', 't53_asst_avl_CAYm2',
    
    't55_RF_caym3', 't55_AF_caym3',
    't55_A_caym1', 't55_B_caym1', 't55_C_caym1', 't55_D_caym1', 't55_E_caym1',
    't55_A_caym2', 't55_B_caym2', 't55_C_caym2', 't55_D_caym2', 't55_E_caym2',
    't55_A_caym3', 't55_B_caym3', 't55_C_caym3', 't55_D_caym3', 't55_E_caym3'
];

let visitingFacultyList = [];

function initData() {
    levelData = initialData.levelData || {};
    if (!levelData[currentLevel]) levelData[currentLevel] = {};

    const data = levelData[currentLevel];
    simpleInputs.forEach(id => {
        const el = document.getElementById(id);
        if (el) el.value = data[id] || '';
    });

    visitingFacultyList = data.visitingFaculty || [];
    renderVisitingFaculty();

    calculateAll();
}

function saveCurrentLevel() {
    if (!levelData[currentLevel]) levelData[currentLevel] = {};
    
    simpleInputs.forEach(id => {
        const el = document.getElementById(id);
        if (el) levelData[currentLevel][id] = el.value;
    });

    levelData[currentLevel].visitingFaculty = visitingFacultyList;

    calculateAll();
}

function addVisitingFaculty() {
    visitingFacultyList.push({ year: 'CAYm1', name: '', org: '', course: '', hours: 0 });
    renderVisitingFaculty();
    saveCurrentLevel();
}

function removeVisitingFaculty(idx) {
    visitingFacultyList.splice(idx, 1);
    renderVisitingFaculty();
    saveCurrentLevel();
}

function updateVisiting(idx, field, value) {
    if (visitingFacultyList[idx]) {
        visitingFacultyList[idx][field] = value;
        saveCurrentLevel();
    }
}

function renderVisitingFaculty() {
    const tbody = document.getElementById('visiting-body');
    if (!tbody) return;
    tbody.innerHTML = '';
    let tot1 = 0, tot2 = 0, tot3 = 0;
    
    visitingFacultyList.forEach((vf, idx) => {
        if (vf.year === 'CAYm1') tot1 += parseFloat(vf.hours) || 0;
        if (vf.year === 'CAYm2') tot2 += parseFloat(vf.hours) || 0;
        if (vf.year === 'CAYm3') tot3 += parseFloat(vf.hours) || 0;

        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td style="text-align:center; font-weight:bold;">${idx + 1}</td>
            <td>
                <select onchange="updateVisiting(${idx}, 'year', this.value); renderVisitingFaculty();">
                    <option value="CAYm1" ${vf.year === 'CAYm1' ? 'selected' : ''}>CAYm1</option>
                    <option value="CAYm2" ${vf.year === 'CAYm2' ? 'selected' : ''}>CAYm2</option>
                    <option value="CAYm3" ${vf.year === 'CAYm3' ? 'selected' : ''}>CAYm3</option>
                </select>
            </td>
            <td><input aria-label="Faculty Name" type="text" style="width:100%" value="${vf.name || ''}" onchange="updateVisiting(${idx}, 'name', this.value)"></td>
            <td><input aria-label="Organization" type="text" style="width:100%" value="${vf.org || ''}" onchange="updateVisiting(${idx}, 'org', this.value)"></td>
            <td><input aria-label="Course Delivered" type="text" style="width:100%" value="${vf.course || ''}" onchange="updateVisiting(${idx}, 'course', this.value)"></td>
            <td><input aria-label="Teaching Hours" type="number" min="0" style="width:100%" value="${vf.hours || 0}" onchange="updateVisiting(${idx}, 'hours', this.value); renderVisitingFaculty();"></td>
            <td style="text-align:center;"><button type="button" class="btn-remove" onclick="removeVisitingFaculty(${idx})">&times;</button></td>
        `;
        tbody.appendChild(tr);
    });

    const totEl1 = document.getElementById('t54_tot_caym1');
    const totEl2 = document.getElementById('t54_tot_caym2');
    const totEl3 = document.getElementById('t54_tot_caym3');
    if (totEl1) totEl1.innerText = tot1;
    if (totEl2) totEl2.innerText = tot2;
    if (totEl3) totEl3.innerText = tot3;
}

function calculateAll() {
    let rfMap = {};
    let afMap = {};

    // 5.1 Student-Faculty Ratio
    let totalSFR = 0, validYears = 0;
    ['cay', 'caym1', 'caym2'].forEach(yr => {
        const dsEl = document.getElementById('t51_DS_' + yr);
        const asEl = document.getElementById('t51_AS_' + yr);
        const dfEl = document.getElementById('t51_DF_' + yr);
        const afEl = document.getElementById('t51_AF_' + yr);
        const ffEl = document.getElementById('t51_FF_' + yr);

        const ds = dsEl ? (parseFloat(dsEl.value) || 0) : 0;
        const as = asEl ? (parseFloat(asEl.value) || 0) : 0;
        const df = dfEl ? (parseFloat(dfEl.value) || 0) : 0;
        const af = afEl ? (parseFloat(afEl.value) || 0) : 0;
        const ff = ffEl ? (parseFloat(ffEl.value) || 0) : 0;
        
        const S = ds + as;
        const F = df + af;
        
        const sEl = document.getElementById('t51_S_' + yr);
        const fEl = document.getElementById('t51_F_' + yr);
        if (sEl) sEl.value = S;
        if (fEl) fEl.value = F;

        let rf = S / 20;
        rfMap[yr] = rf;
        afMap[yr] = F;

        const sfrEl = document.getElementById('t51_SFR_' + yr);
        if (sfrEl) {
            if ((F - ff) > 0) {
                let sfr = S / (F - ff);
                sfrEl.value = sfr.toFixed(2);
                totalSFR += sfr;
                validYears++;
            } else {
                sfrEl.value = '0.00';
            }
        }
    });
    const sfrAvgEl = document.getElementById('t51_SFR_avg');
    if (sfrAvgEl) {
        sfrAvgEl.value = validYears > 0 ? (totalSFR / validYears).toFixed(2) : '0.00';
    }

    // 5.2 Faculty Qualification Index (FQI)
    let totalFQI = 0, validFQI = 0;
    ['cay', 'caym1', 'caym2'].forEach(yr => {
        const xEl = document.getElementById('t52_X_' + yr);
        const yEl = document.getElementById('t52_Y_' + yr);
        const x = xEl ? (parseFloat(xEl.value) || 0) : 0;
        const y = yEl ? (parseFloat(yEl.value) || 0) : 0;
        const rf = rfMap[yr] || 0;
        
        const rfEl = document.getElementById('t52_RF_' + yr);
        if (rfEl) rfEl.value = rf.toFixed(2);
        
        const fqiEl = document.getElementById('t52_FQI_' + yr);
        if (fqiEl) {
            if (rf > 0) {
                let fqi = 2.5 * ((10 * x + 4 * y) / rf);
                fqiEl.value = fqi.toFixed(2);
                totalFQI += fqi;
                validFQI++;
            } else {
                fqiEl.value = '0.00';
            }
        }
    });
    const fqiAvgEl = document.getElementById('t52_FQI_avg');
    if (fqiAvgEl) {
        fqiAvgEl.value = validFQI > 0 ? (totalFQI / validFQI).toFixed(2) : '0.00';
    }

    // 5.3 Faculty Cadre Proportion
    ['CAY', 'CAYm1', 'CAYm2'].forEach(yr => {
        const lowerYr = yr.toLowerCase();
        const rf = rfMap[lowerYr] || 0;
        
        const pReq = document.getElementById('t53_prof_req_' + yr);
        const aReq = document.getElementById('t53_asso_req_' + yr);
        const asReq = document.getElementById('t53_asst_req_' + yr);
        if (pReq) pReq.value = (rf / 9).toFixed(2);
        if (aReq) aReq.value = ((rf * 2) / 9).toFixed(2);
        if (asReq) asReq.value = ((rf * 6) / 9).toFixed(2);
    });

    // 5.5 Faculty Retention
    const rfCaym1 = document.getElementById('t55_RF_caym1');
    const rfCaym2 = document.getElementById('t55_RF_caym2');
    if (rfCaym1) rfCaym1.value = (rfMap['caym1'] || 0).toFixed(2);
    if (rfCaym2) rfCaym2.value = (rfMap['caym2'] || 0).toFixed(2);
    
    const afCaym1 = document.getElementById('t55_AF_caym1');
    const afCaym2 = document.getElementById('t55_AF_caym2');
    if (afCaym1) afCaym1.value = (afMap['caym1'] || 0);
    if (afCaym2) afCaym2.value = (afMap['caym2'] || 0);

    let totalFR = 0, validFR = 0;
    ['caym1', 'caym2', 'caym3'].forEach(yr => {
        const rfEl = document.getElementById('t55_RF_' + yr);
        let rf = rfEl ? (parseFloat(rfEl.value) || 0) : 0;
        
        const aEl = document.getElementById('t55_A_' + yr);
        const bEl = document.getElementById('t55_B_' + yr);
        const cEl = document.getElementById('t55_C_' + yr);
        const dEl = document.getElementById('t55_D_' + yr);
        const eEl = document.getElementById('t55_E_' + yr);

        const a = aEl ? (parseFloat(aEl.value) || 0) : 0;
        const b = bEl ? (parseFloat(bEl.value) || 0) : 0;
        const c = cEl ? (parseFloat(cEl.value) || 0) : 0;
        const d = dEl ? (parseFloat(dEl.value) || 0) : 0;
        const e = eEl ? (parseFloat(eEl.value) || 0) : 0;
        
        const frEl = document.getElementById('t55_FR_' + yr);
        if (frEl) {
            if (rf > 0) {
                let fr = (((a*0) + (b*1) + (c*2) + (d*3) + (e*4)) / rf) * 2.50;
                if (fr > 10) fr = 10;
                frEl.value = fr.toFixed(2);
                totalFR += fr;
                validFR++;
            } else {
                frEl.value = '0.00';
            }
        }
    });
    
    const avgFR = validFR > 0 ? (totalFR / validFR) : 0;
    const frAvgEl = document.getElementById('t55_FR_avg');
    if (frAvgEl) {
        frAvgEl.value = (avgFR > 10 ? 10 : avgFR).toFixed(2);
    }
}

async function saveCriterionData(silent = false) {
    saveCurrentLevel();
    if (typeof saveAccreditationCriterion === 'function') {
        return await saveAccreditationCriterion(5, { levelData: levelData }, silent);
    }
    return false;
}

window.onload = function() { initData(); };

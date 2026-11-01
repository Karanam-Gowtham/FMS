    const year = window.NBA_CONFIG ? window.NBA_CONFIG.year : '';
    const initialData = window.NBA_CONFIG ? (window.NBA_CONFIG.initialData || {}) : {};
    let levelData = {};
    let currentLevel = '';

    const sdgOptions = [
        "No Poverty", "Zero Hunger", "Good Health and Well-being", "Quality Education", "Gender Equality",
        "Clean Water and Sanitation", "Affordable and Clean Energy", "Decent Work and Economic Growth",
        "Industry, Innovation and Infrastructure", "Reduced Inequalities", "Sustainable Cities and Communities",
        "Responsible Consumption and Production", "Climate Action", "Life Below Water", "Life on Land",
        "Peace, Justice and Strong Institutions", "Partnerships for the Goals"
    ];

    function initData() {
        levelData = initialData.levelData || {};
        
        const defaultDept = 'dept_' + (window.NBA_CONFIG ? window.NBA_CONFIG.deptId : '');
        if (!levelData[defaultDept]) {
            levelData[defaultDept] = { pdf_3_1: '', pdf_3_2: '', pdf_3_3: '', pdf_3_4: '', pdf_3_5: '', table_3_6: [], pdf_3_7_1: '', pdf_3_7_2: '', table_3_8: { columns: ['PO1','PO2','PO3','PO4','PO5','PO6','PO7','PO8','PO9','PO10','PO11'], rows: [] }, table_3_8_2: { columns: ['PO1','PO2','PO3','PO4','PO5','PO6','PO7','PO8','PO9','PO10','PO11'], rows: [] }, table_3_8_3: { columns: ['PO1','PO2','PO3','PO4','PO5','PO6','PO7','PO8','PO9','PO10','PO11'], rows: [] } };
        }

        switchLevel();
    }

    function changeDepartment() {
        const select = document.getElementById('level-select');
        const newDeptId = select.value.replace('dept_', '');
        if (newDeptId != (window.NBA_CONFIG ? window.NBA_CONFIG.deptId : '')) {
            window.location.href = '?route=nba/criterion&id=3&year=' + encodeURIComponent(window.NBA_CONFIG ? window.NBA_CONFIG.year : '') + '&dept_id=' + encodeURIComponent(newDeptId);
        }
    }

    function switchLevel() {
        const select = document.getElementById('level-select');
        const rawVal = select ? select.value : (window.NBA_CONFIG ? window.NBA_CONFIG.deptId : '');
        currentLevel = rawVal.startsWith('dept_') ? rawVal : ('dept_' + rawVal);
        
        if (!levelData[currentLevel]) {
            levelData[currentLevel] = { pdf_3_1: '', pdf_3_2: '', pdf_3_3: '', pdf_3_4: '', pdf_3_5: '', table_3_6: [], pdf_3_7_1: '', pdf_3_7_2: '', table_3_8: { columns: ['PO1','PO2','PO3','PO4','PO5','PO6','PO7','PO8','PO9','PO10','PO11'], rows: [] }, table_3_8_2: { columns: ['PO1','PO2','PO3','PO4','PO5','PO6','PO7','PO8','PO9','PO10','PO11'], rows: [] }, table_3_8_3: { columns: ['PO1','PO2','PO3','PO4','PO5','PO6','PO7','PO8','PO9','PO10','PO11'], rows: [] } };
        }
        if (!levelData[currentLevel].table_3_6) levelData[currentLevel].table_3_6 = [];
        if (!levelData[currentLevel].table_3_8) levelData[currentLevel].table_3_8 = { columns: ['PO1','PO2','PO3','PO4','PO5','PO6','PO7','PO8','PO9','PO10','PO11'], rows: [] };
        if (!levelData[currentLevel].table_3_8_2) levelData[currentLevel].table_3_8_2 = { columns: ['PO1','PO2','PO3','PO4','PO5','PO6','PO7','PO8','PO9','PO10','PO11'], rows: [] };
        if (!levelData[currentLevel].table_3_8_3) levelData[currentLevel].table_3_8_3 = { columns: ['PO1','PO2','PO3','PO4','PO5','PO6','PO7','PO8','PO9','PO10','PO11'], rows: [] };

        ['3_1', '3_2', '3_3', '3_4', '3_5', '3_7_1', '3_7_2'].forEach(section => {
            updatePDFUI(section);
        });
        
        renderTable3_6();
        renderTable3_8();
        renderTable3_8_2();
        renderTable3_8_3();
    }

    // --- Table 3.8.1 Functions (Direct) ---
    function renderTable3_8() {
        const table = document.getElementById('table-3-8');
        const data = levelData[currentLevel].table_3_8;
        
        let html = '<thead><tr><th scope="col">Course</th>';
        data.columns.forEach((col, cIdx) => {
            html += `<th scope="col">
                        ${col}
                        ${cIdx >= 11 ? `<br><button class="btn-remove" style="padding: 2px 5px; font-size: 0.7rem;" onclick="removeColumn3_8(${cIdx})">œ&bull;</button>` : ''}
                     </th>`;
        });
        html += '<th scope="col">Action</th></tr></thead><tbody>';
        
        const sums = {};
        const counts = {};
        data.columns.forEach(col => { sums[col] = 0; counts[col] = 0; });

        data.rows.forEach((row, rIdx) => {
            html += `<tr>
                <td style="padding: 4px;">
                    <input aria-label="Text Input" type="text" value="${row.course || ''}" onchange="updateRow3_8(${rIdx}, 'course', this.value)" style="width: 100px; padding: 4px; box-sizing: border-box;" placeholder="e.g. C101">
                </td>`;
            
            data.columns.forEach(col => {
                let val = row.values ? (row.values[col] || '') : '';
                if (val !== '' && !isNaN(val)) {
                    sums[col] += parseFloat(val);
                    counts[col]++;
                }
                html += `
                    <td style="padding: 4px;">
                        <input aria-label="Number Input" type="number" min="0" step="0.01" value="${val}" onchange="updateCell3_8(${rIdx}, '${col}', this.value)" class="matrix-input">
                    </td>
                `;
            });

            html += `
                <td style="padding: 4px;">
                    <button type="button" class="btn-remove" onclick="removeRow3_8(${rIdx})">Delete</button>
                </td>
            </tr>`;
        });

        // Averages row
        html += `<tr><td style="font-weight: bold; background: #f8f9fa;">Direct Attainment</td>`;
        data.columns.forEach(col => {
            let avg = counts[col] > 0 ? (sums[col] / counts[col]).toFixed(2) : '-';
            html += `<td style="font-weight: bold; background: #f8f9fa;" id="direct_avg_${col}">${avg}</td>`;
        });
        html += `<td style="background: #f8f9fa;"></td></tr>`;

        html += '</tbody>';
        table.innerHTML = html;
        
        // Trigger 3.8.3 render as direct affects overall
        if (document.getElementById('table-3-8-3')) {
            renderTable3_8_3();
        }
    }

    function addRow3_8() {
        levelData[currentLevel].table_3_8.rows.push({ course: '', values: {} });
        renderTable3_8();
    }

    function removeRow3_8(rIdx) {
        if(confirm("Delete this row?")) {
            levelData[currentLevel].table_3_8.rows.splice(rIdx, 1);
            renderTable3_8();
            saveData(true);
        }
    }

    function addColumn3_8() {
        const colName = prompt("Enter new column name (e.g., PSO1, PSO2):");
        if(colName && colName.trim() !== '') {
            const cleanName = colName.trim();
            if (levelData[currentLevel].table_3_8.columns.includes(cleanName)) {
                alert("Column already exists in Direct table!");
                return;
            }
            levelData[currentLevel].table_3_8.columns.push(cleanName);
            // Auto add to indirect table as well
            if (!levelData[currentLevel].table_3_8_2.columns.includes(cleanName)) {
                levelData[currentLevel].table_3_8_2.columns.push(cleanName);
            }
            renderTable3_8();
            renderTable3_8_2();
            saveData(true);
        }
    }

    function removeColumn3_8(cIdx) {
        if(confirm("Delete this column from all tables?")) {
            const col = levelData[currentLevel].table_3_8.columns[cIdx];
            levelData[currentLevel].table_3_8.columns.splice(cIdx, 1);
            
            levelData[currentLevel].table_3_8.rows.forEach(row => {
                if (row.values && row.values[col] !== undefined) delete row.values[col];
            });
            
            // Remove from 3.8.2 as well
            const cIdx2 = levelData[currentLevel].table_3_8_2.columns.indexOf(col);
            if (cIdx2 !== -1) {
                levelData[currentLevel].table_3_8_2.columns.splice(cIdx2, 1);
                levelData[currentLevel].table_3_8_2.rows.forEach(row => {
                    if (row.values && row.values[col] !== undefined) delete row.values[col];
                });
            }
            
            renderTable3_8();
            renderTable3_8_2();
            saveData(true);
        }
    }

    function updateRow3_8(rIdx, field, value) {
        levelData[currentLevel].table_3_8.rows[rIdx][field] = value;
        renderTable3_8();
    }

    function updateCell3_8(rIdx, col, value) {
        if (!levelData[currentLevel].table_3_8.rows[rIdx].values) {
            levelData[currentLevel].table_3_8.rows[rIdx].values = {};
        }
        levelData[currentLevel].table_3_8.rows[rIdx].values[col] = value;
        renderTable3_8();
        saveData(true);
    }

    // --- Table 3.8.2 Functions ---
    function renderTable3_8_2() {
        const table = document.getElementById('table-3-8-2');
        const data = levelData[currentLevel].table_3_8_2;
        
        let html = '<thead><tr><th scope="col">Name of the Survey</th>';
        data.columns.forEach((col, cIdx) => {
            html += `<th scope="col">
                        ${col}
                        ${cIdx >= 11 ? `<br><button class="btn-remove" style="padding: 2px 5px; font-size: 0.7rem;" onclick="removeColumn3_8_2(${cIdx})">œ&bull;</button>` : ''}
                     </th>`;
        });
        html += '<th scope="col">Action</th></tr></thead><tbody>';
        
        const sums = {};
        const counts = {};
        data.columns.forEach(col => { sums[col] = 0; counts[col] = 0; });

        data.rows.forEach((row, rIdx) => {
            html += `<tr>
                <td style="padding: 4px;">
                    <input aria-label="Text Input" type="text" value="${row.survey || ''}" onchange="updateRow3_8_2(${rIdx}, 'survey', this.value)" style="width: 150px; padding: 4px; box-sizing: border-box;" placeholder="e.g. Survey 1">
                </td>`;
            
            data.columns.forEach(col => {
                let val = row.values ? (row.values[col] || '') : '';
                if (val !== '' && !isNaN(val)) {
                    sums[col] += parseFloat(val);
                    counts[col]++;
                }
                html += `
                    <td style="padding: 4px;">
                        <input aria-label="Number Input" type="number" min="0" step="0.01" value="${val}" onchange="updateCell3_8_2(${rIdx}, '${col}', this.value)" class="matrix-input">
                    </td>
                `;
            });

            html += `
                <td style="padding: 4px;">
                    <button type="button" class="btn-remove" onclick="removeRow3_8_2(${rIdx})">Delete</button>
                </td>
            </tr>`;
        });

        // Averages row
        html += `<tr><td style="font-weight: bold; background: #f8f9fa;">Indirect Attainment</td>`;
        data.columns.forEach(col => {
            let avg = counts[col] > 0 ? (sums[col] / counts[col]).toFixed(2) : '-';
            html += `<td style="font-weight: bold; background: #f8f9fa;" id="indirect_avg_${col}">${avg}</td>`;
        });
        html += `<td style="background: #f8f9fa;"></td></tr>`;

        html += '</tbody>';
        table.innerHTML = html;
        
        // Trigger 3.8.3 render as indirect affects overall
        if (document.getElementById('table-3-8-3')) {
            renderTable3_8_3();
        }
    }

    function addRow3_8_2() {
        levelData[currentLevel].table_3_8_2.rows.push({ survey: '', values: {} });
        renderTable3_8_2();
    }

    function removeRow3_8_2(rIdx) {
        if(confirm("Delete this survey row?")) {
            levelData[currentLevel].table_3_8_2.rows.splice(rIdx, 1);
            renderTable3_8_2();
            saveData(true);
        }
    }

    function addColumn3_8_2() {
        const colName = prompt("Enter new column name (e.g., PSO1, PSO2):");
        if(colName && colName.trim() !== '') {
            const cleanName = colName.trim();
            if (levelData[currentLevel].table_3_8_2.columns.includes(cleanName)) {
                alert("Column already exists in Indirect table!");
                return;
            }
            levelData[currentLevel].table_3_8_2.columns.push(cleanName);
            // Auto add to direct table as well
            if (!levelData[currentLevel].table_3_8.columns.includes(cleanName)) {
                levelData[currentLevel].table_3_8.columns.push(cleanName);
            }
            renderTable3_8();
            renderTable3_8_2();
            saveData(true);
        }
    }

    function removeColumn3_8_2(cIdx) {
        if(confirm("Delete this column from all tables?")) {
            const col = levelData[currentLevel].table_3_8_2.columns[cIdx];
            levelData[currentLevel].table_3_8_2.columns.splice(cIdx, 1);
            
            levelData[currentLevel].table_3_8_2.rows.forEach(row => {
                if (row.values && row.values[col] !== undefined) delete row.values[col];
            });
            
            // Remove from 3.8 as well
            const cIdx1 = levelData[currentLevel].table_3_8.columns.indexOf(col);
            if (cIdx1 !== -1) {
                levelData[currentLevel].table_3_8.columns.splice(cIdx1, 1);
                levelData[currentLevel].table_3_8.rows.forEach(row => {
                    if (row.values && row.values[col] !== undefined) delete row.values[col];
                });
            }
            
            renderTable3_8();
            renderTable3_8_2();
            saveData(true);
        }
    }

    function updateRow3_8_2(rIdx, field, value) {
        levelData[currentLevel].table_3_8_2.rows[rIdx][field] = value;
        renderTable3_8_2();
    }

    function updateCell3_8_2(rIdx, col, value) {
        if (!levelData[currentLevel].table_3_8_2.rows[rIdx].values) {
            levelData[currentLevel].table_3_8_2.rows[rIdx].values = {};
        }
        levelData[currentLevel].table_3_8_2.rows[rIdx].values[col] = value;
        renderTable3_8_2();
        saveData(true);
    }

    // --- Table 3.8.3 Functions (Overall) ---
    function renderTable3_8_3() {
        const table = document.getElementById('table-3-8-3');
        if (!table) return;
        
        const data = levelData[currentLevel].table_3_8_3;
        
        let html = '<thead><tr><th scope="col">Assessment</th>';
        data.columns.forEach((col, cIdx) => {
            html += `<th scope="col">
                        ${col}
                        ${cIdx >= 11 ? `<br><button class="btn-remove" style="padding: 2px 5px; font-size: 0.7rem;" onclick="removeColumn3_8_3(${cIdx})">œ&bull;</button>` : ''}
                     </th>`;
        });
        html += '<th scope="col">Action</th></tr></thead><tbody>';

        data.rows.forEach((row, rIdx) => {
            html += `<tr>
                <td style="padding: 4px;">
                    <input aria-label="Text Input" type="text" value="${row.assessment || ''}" onchange="updateRow3_8_3(${rIdx}, 'assessment', this.value)" style="width: 150px; padding: 4px; box-sizing: border-box;" placeholder="e.g. Direct Attainment">
                </td>`;
            
            data.columns.forEach(col => {
                let val = row.values ? (row.values[col] || '') : '';
                html += `
                    <td style="padding: 4px;">
                        <input aria-label="Number Input" type="number" min="0" step="0.01" value="${val}" onchange="updateCell3_8_3(${rIdx}, '${col}', this.value)" class="matrix-input">
                    </td>
                `;
            });

            html += `
                <td style="padding: 4px;">
                    <button type="button" class="btn-remove" onclick="removeRow3_8_3(${rIdx})">Delete</button>
                </td>
            </tr>`;
        });

        html += '</tbody>';
        table.innerHTML = html;
    }

    function addRow3_8_3() {
        levelData[currentLevel].table_3_8_3.rows.push({ assessment: '', values: {} });
        renderTable3_8_3();
    }

    function removeRow3_8_3(rIdx) {
        if(confirm("Delete this row?")) {
            levelData[currentLevel].table_3_8_3.rows.splice(rIdx, 1);
            renderTable3_8_3();
            saveData(true);
        }
    }

    function addColumn3_8_3() {
        const colName = prompt("Enter new column name (e.g., PSO1, PSO2):");
        if(colName && colName.trim() !== '') {
            const cleanName = colName.trim();
            if (levelData[currentLevel].table_3_8_3.columns.includes(cleanName)) {
                alert("Column already exists!");
                return;
            }
            levelData[currentLevel].table_3_8_3.columns.push(cleanName);
            renderTable3_8_3();
            saveData(true);
        }
    }

    function removeColumn3_8_3(cIdx) {
        if(confirm("Delete this column?")) {
            const col = levelData[currentLevel].table_3_8_3.columns[cIdx];
            levelData[currentLevel].table_3_8_3.columns.splice(cIdx, 1);
            
            levelData[currentLevel].table_3_8_3.rows.forEach(row => {
                if (row.values && row.values[col] !== undefined) delete row.values[col];
            });
            
            renderTable3_8_3();
            saveData(true);
        }
    }

    function updateRow3_8_3(rIdx, field, value) {
        levelData[currentLevel].table_3_8_3.rows[rIdx][field] = value;
        renderTable3_8_3();
    }

    function updateCell3_8_3(rIdx, col, value) {
        if (!levelData[currentLevel].table_3_8_3.rows[rIdx].values) {
            levelData[currentLevel].table_3_8_3.rows[rIdx].values = {};
        }
        levelData[currentLevel].table_3_8_3.rows[rIdx].values[col] = value;
        renderTable3_8_3();
        saveData(true);
    }

    function importExcel(input, tableKey, firstColumnKey) {
        const file = input.files[0];
        if (!file) return;

        const reader = new FileReader();
        reader.onload = function(e) {
            try {
                const data = new Uint8Array(e.target.result);
                const workbook = XLSX.read(data, {type: 'array'});
                const firstSheetName = workbook.SheetNames[0];
                const worksheet = workbook.Sheets[firstSheetName];
                
                // Convert to 2D array
                const json = XLSX.utils.sheet_to_json(worksheet, {header: 1, defval: ''});
                
                if (json.length === 0) {
                    alert("Excel file is empty!");
                    return;
                }

                // The first row is the header, e.g., ["Course", "PO1", "PO2", ...]
                let headers = json[0].map(h => String(h).trim()).filter(h => h !== '');
                
                if (headers.length === 0) {
                    alert("No headers found in the Excel file.");
                    return;
                }

                // Remove the very first column name ("Course", "Name of the Survey", "Assessment")
                headers.shift();

                // Setup levelData table structure
                levelData[currentLevel][tableKey].columns = headers;
                levelData[currentLevel][tableKey].rows = [];
                
                // Loop through data rows (skip the first row)
                for (let i = 1; i < json.length; i++) {
                    let rowArray = json[i];
                    // Check if row is just empty strings
                    if (rowArray.every(val => val === '')) continue;
                    
                    let firstColValue = rowArray[0] ? String(rowArray[0]).trim() : '';
                    if (!firstColValue) continue;

                    let rowObj = { values: {} };
                    rowObj[firstColumnKey] = firstColValue;
                    
                    for (let j = 0; j < headers.length; j++) {
                        let val = rowArray[j + 1];
                        if (val !== undefined && val !== '') {
                            rowObj.values[headers[j]] = val;
                        }
                    }
                    
                    levelData[currentLevel][tableKey].rows.push(rowObj);
                }
                
                // Re-render based on tableKey
                if (tableKey === 'table_3_8') renderTable3_8();
                if (tableKey === 'table_3_8_2') renderTable3_8_2();
                if (tableKey === 'table_3_8_3') renderTable3_8_3();
                
                saveData(true);
                alert("Excel data successfully imported!");
            } catch (err) {
                console.error(err);
                alert("Error importing Excel file. Please check the file format.");
            }
        };
        reader.readAsArrayBuffer(file);
        
        // Reset input
        input.value = '';
    }

    function renderTable3_6() {
        const table = document.getElementById('table-3-8');
        const data = levelData[currentLevel].table_3_8;
        
        let html = '<thead><tr><th scope="col">Course</th>';
        data.columns.forEach((col, cIdx) => {
            html += `<th scope="col">
                        ${col}
                        ${cIdx >= 11 ? `<br><button class="btn-remove" style="padding: 2px 5px; font-size: 0.7rem;" onclick="removeColumn3_8(${cIdx})">œ&bull;</button>` : ''}
                     </th>`;
        });
        html += '<th scope="col">Action</th></tr></thead><tbody>';
        
        const sums = {};
        const counts = {};
        data.columns.forEach(col => { sums[col] = 0; counts[col] = 0; });

        data.rows.forEach((row, rIdx) => {
            html += `<tr>
                <td style="padding: 4px;">
                    <input aria-label="Text Input" type="text" value="${row.course || ''}" onchange="updateRow3_8(${rIdx}, 'course', this.value)" style="width: 100px; padding: 4px; box-sizing: border-box;" placeholder="e.g. C101">
                </td>`;
            
            data.columns.forEach(col => {
                let val = row.values ? (row.values[col] || '') : '';
                if (val !== '' && !isNaN(val)) {
                    sums[col] += parseFloat(val);
                    counts[col]++;
                }
                html += `
                    <td style="padding: 4px;">
                        <input aria-label="Number Input" type="number" min="0" step="0.01" value="${val}" onchange="updateCell3_8(${rIdx}, '${col}', this.value)" class="matrix-input">
                    </td>
                `;
            });

            html += `
                <td style="padding: 4px;">
                    <button type="button" class="btn-remove" onclick="removeRow3_8(${rIdx})">Delete</button>
                </td>
            </tr>`;
        });

        // Averages row
        html += `<tr><td style="font-weight: bold; background: #f8f9fa;">Direct Attainment</td>`;
        data.columns.forEach(col => {
            let avg = counts[col] > 0 ? (sums[col] / counts[col]).toFixed(2) : '-';
            html += `<td style="font-weight: bold; background: #f8f9fa;">${avg}</td>`;
        });
        html += `<td style="background: #f8f9fa;"></td></tr>`;

        html += '</tbody>';
        table.innerHTML = html;
    }

    function addRow3_8() {
        levelData[currentLevel].table_3_8.rows.push({ course: '', values: {} });
        renderTable3_8();
    }

    function removeRow3_8(rIdx) {
        if(confirm("Delete this row?")) {
            levelData[currentLevel].table_3_8.rows.splice(rIdx, 1);
            renderTable3_8();
            saveData(true);
        }
    }

    function addColumn3_8() {
        const colName = prompt("Enter new column name (e.g., PSO1, PSO2):");
        if(colName && colName.trim() !== '') {
            if (levelData[currentLevel].table_3_8.columns.includes(colName.trim())) {
                alert("Column already exists!");
                return;
            }
            levelData[currentLevel].table_3_8.columns.push(colName.trim());
            renderTable3_8();
            saveData(true);
        }
    }

    function removeColumn3_8(cIdx) {
        if(confirm("Delete this column?")) {
            const col = levelData[currentLevel].table_3_8.columns[cIdx];
            levelData[currentLevel].table_3_8.columns.splice(cIdx, 1);
            // Cleanup values
            levelData[currentLevel].table_3_8.rows.forEach(row => {
                if (row.values && row.values[col] !== undefined) {
                    delete row.values[col];
                }
            });
            renderTable3_8();
            saveData(true);
        }
    }

    function updateRow3_8(rIdx, field, value) {
        levelData[currentLevel].table_3_8.rows[rIdx][field] = value;
        renderTable3_8();
    }

    function updateCell3_8(rIdx, col, value) {
        if (!levelData[currentLevel].table_3_8.rows[rIdx].values) {
            levelData[currentLevel].table_3_8.rows[rIdx].values = {};
        }
        levelData[currentLevel].table_3_8.rows[rIdx].values[col] = value;
        renderTable3_8();
        saveData(true);
    }

    function renderTable3_6() {
        const tbody = document.getElementById('table-3-6-body');
        tbody.innerHTML = '';
        
        levelData[currentLevel].table_3_6.forEach((row, index) => {
            let sdgSelect = `<select onchange="updateRow3_6(${index}, 'sdg', this.value)" style="width: 100%; padding: 4px;">`;
            sdgSelect += `<option value="">Select SDG...</option>`;
            sdgOptions.forEach(opt => {
                sdgSelect += `<option value="${opt}" ${row.sdg === opt ? 'selected' : ''}>${opt}</option>`;
            });
            sdgSelect += `</select>`;

            let pdfHtml = '';
            if (row.pdf_path) {
                pdfHtml = `
                    <div style="font-size: 0.8rem; background: #e8f5e9; padding: 4px; border-radius: 4px; display: flex; align-items: center; justify-content: space-between; margin-top: 5px;">
                        <a href="/" target="_blank" style="color: #28a745; text-decoration: none; overflow: hidden; text-overflow: ellipsis; max-width: 100px; white-space: nowrap;">View</a>
                        <button type="button" onclick="removeRowPDF3_6(${index})" style="background: none; border: none; color: #dc3545; cursor: pointer; padding: 0;">œ&bull;</button>
                    </div>
                `;
            } else {
                pdfHtml = `
                    <div style="font-size: 0.8rem; color: #6c757d; margin-top: 5px;" id="row_uploading_${index}"></div>
                    <button type="button" class="btn-add" style="padding: 4px 8px; font-size: 0.8rem;" onclick="document.getElementById('row_pdf_${index}').click()">Upload PDF</button>
                    <input aria-label="Row Pdf ${index}" type="file" id="row_pdf_${index}" style="display: none;" accept="application/pdf" onchange="handleRowPDFUpload(this, ${index})">
                `;
            }

            tbody.innerHTML += `
                <tr>
                    <td style="padding: 4px;">
                        <select onchange="updateRow3_6(${index}, 'type', this.value)" style="width: 100%; padding: 4px;">
                            <option value="">Select...</option>
                            <option value="Research work" ${row.type === 'Research work' ? 'selected' : ''}>Research work</option>
                            <option value="Project work" ${row.type === 'Project work' ? 'selected' : ''}>Project work</option>
                            <option value="Student activities" ${row.type === 'Student activities' ? 'selected' : ''}>Student activities</option>
                        </select>
                    </td>
                    <td style="padding: 4px;">
                        <input aria-label="Text Input" type="text" value="${row.title || ''}" onchange="updateRow3_6(${index}, 'title', this.value)" style="width: 100%; padding: 4px; box-sizing: border-box;" placeholder="Enter title">
                    </td>
                    <td style="padding: 4px; text-align: left;">
                        ${sdgSelect}
                    </td>
                    <td style="padding: 4px;">
                        ${pdfHtml}
                    </td>
                    <td style="padding: 4px;">
                        <button type="button" class="btn-remove" onclick="removeRow3_6(${index})">Delete</button>
                    </td>
                </tr>
            `;
        });
    }

    function addTableRow3_6() {
        levelData[currentLevel].table_3_6.push({ type: '', title: '', sdg: '', pdf_path: '' });
        renderTable3_6();
    }

    function updateRow3_6(index, field, value) {
        levelData[currentLevel].table_3_6[index][field] = value;
    }

    function removeRow3_6(index) {
        if(confirm("Delete this row?")) {
            levelData[currentLevel].table_3_6.splice(index, 1);
            renderTable3_6();
            saveData(true);
        }
    }

    async function handleRowPDFUpload(input, index) {
        const file = input.files[0];
        if (!file) return;

        if (file.type !== 'application/pdf') {
            alert('Only PDF files are allowed.');
            input.value = '';
            return;
        }

        document.getElementById('row_uploading_' + index).innerText = 'Uploading...';

        const deptSelect = document.getElementById('level-select');
        const activeDeptId = deptSelect ? deptSelect.value.replace('dept_', '') : (window.NBA_CONFIG ? window.NBA_CONFIG.deptId : '');

        const formData = new FormData();
        formData.append('_csrf_token', window.NBA_CONFIG ? window.NBA_CONFIG.csrfToken : '');
        formData.append('dept_id', activeDeptId);
        formData.append('pdf_file', file);
        formData.append('section', '3_6_row_' + index);

        try {
            const baseUrl = window.NBA_CONFIG ? window.NBA_CONFIG.baseUrl : ''; const response = await fetch(baseUrl + '/public/index.php?route=api/nba/upload_pdf&id=3', {
                method: 'POST',
                body: formData
            });
            const result = await response.json();

            if (result.status === 'success') {
                levelData[currentLevel].table_3_6[index].pdf_path = result.file_path;
                renderTable3_6();
                saveData(true);
            } else {
                alert('Upload failed: ' + result.message);
                document.getElementById('row_uploading_' + index).innerText = '';
            }
        } catch (error) {
            console.error('Error:', error);
            alert('An error occurred during upload.');
            document.getElementById('row_uploading_' + index).innerText = '';
        }
        input.value = '';
    }

    function removeRowPDF3_6(index) {
        if(confirm("Remove this document?")) {
            levelData[currentLevel].table_3_6[index].pdf_path = '';
            renderTable3_6();
            saveData(true);
        }
    }

    function updatePDFUI(section) {
        const path = levelData[currentLevel]['pdf_' + section];
        const preview = document.getElementById('preview_' + section);
        const link = document.getElementById('link_' + section);
        const dropzone = document.getElementById('pdfInput_' + section).parentElement;

        if (path) {
            link.href = (window.NBA_CONFIG ? window.NBA_CONFIG.baseUrl : '') + '/' + path;
            preview.style.display = 'flex';
            dropzone.style.display = 'none';
        } else {
            preview.style.display = 'none';
            dropzone.style.display = 'block';
        }
    }

    async function handlePDFUpload(input, section) {
        const file = input.files[0];
        if (!file) return;
        const overlay = document.getElementById('overlay_' + section);
        if (overlay) overlay.style.display = 'flex';

        const path = await uploadAccreditationPdf(3, section, file);
        if (overlay) overlay.style.display = 'none';
        if (path) {
            levelData[currentLevel]['pdf_' + section] = path;
            updatePDFUI(section);
            saveData(true);
        }
        input.value = '';
    }

    function removePDF(section) {
        if (confirm("Are you sure you want to remove this document?")) {
            levelData[currentLevel]['pdf_' + section] = '';
            updatePDFUI(section);
            saveData(true);
        }
    }

    async function saveData(silent = false) {
        return await saveAccreditationCriterion(3, { levelData: levelData }, silent);
    }


    // Initialize
    document.addEventListener('DOMContentLoaded', initData);

</script>
